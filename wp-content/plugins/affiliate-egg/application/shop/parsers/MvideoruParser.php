<?php

namespace Keywordrush\AffiliateEgg;

/**
 * MvideoruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class MvideoruParser extends ShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//a[contains(@class,'product-tile-title-link')]/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'https://www.mvideo.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[contains(@class, 'product-title')]");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//*[@class='o-about-product']");
    }

    public function parsePrice()
    {
        $price = $this->xpathScalar(".//*[contains(@class, 'c-pdp-price__current')]");
        if (!$price)
            return $this->xpathScalar(".//*[@itemprop='price']/@content");
        return $price;
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='c-pdp-price__old']");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//img[contains(@class,'brand-logo')]/@alt");
    }

    public function parseImg()
    {
        $res = $this->xpathScalar(".//meta[@property='og:image']/@content");
        if ($res && preg_match('/^\/\//', $res))
            $res = 'https:' . $res;
        return $res;
    }

    public function parseImgLarge()
    {
        return '';
    }

    public function parseExtra()
    {
        $extra = array();


        $extra['rating'] = TextHelper::ratingPrepare(str_replace($this->xpathScalar(".//meta[@itemprop='ratingValue']/@content"), ',', '.'));
        
        $extra['images'] = array();
        $results = $this->xpathArray(".//div[contains(@class,'list-carousel')]//li[not(@class)]/a/@data-src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res && preg_match('/^\/\//', $res))
                $res = 'https:' . $res;
            if (!in_array($res, $extra['images']))
                $extra['images'][] = $res;
        }

        $extra['comments'] = array();
        $users = $this->xpathArray(".//div[@class='product-review-area']//strong[@class='product-review-author-name']");
        $dates = $this->xpathArray(".//div[@class='product-review-area']//span[@class='product-review-date']");
        $comments = $this->xpathArray(".//div[@class='product-review-area']//div[@class='product-review-description']/p");
        for ($i = 0; $i < count($comments); $i++)
        {
            if (!empty($comments[$i]))
            {

                $comment['name'] = (isset($users[$i])) ? trim($users[$i], ', ') : '';
                $comment['date'] = '';
                if (isset($dates[$i]))
                {
                    $date = explode('.', $dates[$i]);
                    if (count($date) == 3)
                        $comment['date'] = strtotime(trim($date[1]) . '/' . trim($date[0]) . '/' . $date[2]);
                }

                $comment['comment'] = sanitize_text_field($comments[$i]);
                $extra['comments'][] = $comment;
            }
        }
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//div[contains(@class,'product-tile-out-of-stock')])");
        if ($res)
            return false;
        return true;
    }

}
