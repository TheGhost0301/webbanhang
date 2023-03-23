<?php

namespace Keywordrush\AffiliateEgg;

/**
 *  NotikruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class NotikruParser extends MicrodataShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//table[contains(@class,'goods_list_view')]//tr[contains(@class,'good_list_title')]/following::tr[contains(@cn-nam,'list_list_')][1]/td/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//table[contains(@class,'accessoryList')]//td/a[1]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//ul[contains(@class,'resultNotesList')]/li//tr[@class='noteComplectation'][1]/td[1]//strong/a/@href"), 0, $max);
        if (!$urls)
            $urls = $this->xpathArray(".//*[@class='title']/a/@href");

        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@class='goodtitlemain']/text()");
    }
    public function parseDescription()
    {
        return '';
    }

    public function parsePrice()
    {
        if ($price = (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//a[contains(., 'Купить со скидкой!')]/following::span")))
            return $price;
        if ($price = (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//table[@class='parametersInCard']//td[@class='cell1'][contains(., 'Цена:')]/following::td")))
            return $price;
        return parent::parsePrice();
    }

    public function parseOldPrice()
    {
        $price = (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//a[contains(., 'Купить со скидкой!')]/following::span"));
        if ($price)
            $price = (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//table[@class='parametersInCard']//td[@class='cell1'][contains(., 'Цена:')]/following::td"));
        return $price;
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//a/@ecbrand");
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();

        $extra['features'] = array();

        $names = str_replace(":", "", $this->xpathArray(".//table[@class='parametersInCard']//td[@class='cell1']/strong/text()"));
        $values = $this->xpathArray(".//table[@class='parametersInCard']//td[@class='cell1']/following::td[1]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && trim($names[$i]) != "Цена" && trim($names[$i]) != "Цена безнал")
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@id='pictures-scroll-list']//a/@href");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
            {
                $extra['images'][] = 'http://www.notik.ru' . $res;
            }
        }

        $extra['comments'] = array();
        $users = $this->xpathArray(".//div[@class='commentsBox']//span[@class='username']");
        $dates = $this->xpathArray(".//div[@class='commentsBox']//span[@class='thedate']");
        $comments = $this->xpathArray(".//div[@class='commentsBox']//div[@class='comment-content']");
        for ($i = 0; $i < count($comments); $i++)
        {
            if (!empty($comments[$i]))
            {
                $comment['name'] = sanitize_text_field($users[$i]);
                $date = explode('.', $dates[$i]);
                if (count($date) == 3)
                    $comment['date'] = strtotime($date[1] . '/' . $date[0] . '/' . $date[2]);
                else
                    $comment['date'] = '';

                $comment['comment'] = sanitize_text_field($comments[$i]);
                $extra['comments'][] = $comment;
            }
        }

        return $extra;
    }

}
