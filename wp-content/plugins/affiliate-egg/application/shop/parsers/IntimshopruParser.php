<?php

namespace Keywordrush\AffiliateEgg;

/**
 * IntimshopruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class IntimshopruParser extends ShopParser {

    protected $charset = 'windows-1251';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[contains(@class,'grid')]//div[@class='item_picture']/a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.intimshop.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return trim($this->xpathScalar(".//h1[@itemprop='name']"));
    }

    public function parseDescription()
    {
        return trim($this->xpathScalar(".//*[@id='full_descr']/following::p[1]"));
    }

    public function parsePrice()
    {
        // do not remove (float)
        return (float) $this->xpathScalar(".//meta[@itemprop='price']/@content");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='item_price']//span[@class='old_price']");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//p[@class='producer_badge']/a/img/@alt");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//img[@itemprop='image']/@src");
        if (!preg_match('/^https?:\/\//', $img))
            $img = 'http://www.intimshop.ru' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        return '';
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[@itemtype='http://schema.org/Review']//*[@itemprop='description']");
        $users = $this->xpathArray(".//*[@itemtype='http://schema.org/Review']//*[@class='user_title']/a");

        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            $comment['comment'] = trim(str_replace(' Покупала здесь', '', $comment['comment']));
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            $extra['comments'][] = $comment;
        }

        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
