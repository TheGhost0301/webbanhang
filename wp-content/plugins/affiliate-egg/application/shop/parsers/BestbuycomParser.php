<?php

namespace Keywordrush\AffiliateEgg;

/**
 * BestbuycomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class BestbuycomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';
    protected $user_agent = 'Wget';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@id='main-results']//h4/a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.bestbuy.com' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        $desc = $this->xpathScalar(".//*[@itemprop='description']");
        if (!$desc)
            $desc = $this->xpathScalar(".//*[@id='long-description']");
        return $desc;
    }

    public function parsePrice()
    {
        $price = $this->xpathScalar(".//*[@itemprop='price']/@content");
        if (!$price)
            $price = $this->xpathScalar(".//*[@class='pb-hero-price pb-purchase-price']");
        if (!$price)
            $price = $this->xpathScalar(".//*[contains(@class, 'priceView-hero-price')]/span");

        return $price;
    }

    public function parseOldPrice()
    {
  
        $price = str_replace('Was', '', $this->xpathScalar(".//*[@class='pricing-price__regular-price']"));

        return $price;
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//meta[@id='schemaorg-brand-name']/@content");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        return str_replace(';maxHeight=210;maxWidth=210', '', $img);
    }

    public function parseImgLarge()
    {
        $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        return str_replace(';maxHeight=210;maxWidth=210', ';maxHeight=1000;maxWidth=1000', $img);
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['images'] = $this->xpathArray(".//*[@id='carousel-main']//div[@data-slide-number][position() > 1]//img/@data-img-path");

        $extra['comments'] = array();
        $users = $this->xpathArray(".//*[@id='reviews-content']//*[@itemprop='author']");
        $comments = $this->xpathArray(".//*[@id='reviews-content']//*[@itemprop='description']");
        $ratings = $this->xpathArray(".//*[@id='reviews-content']//*[@itemprop='ratingValue']");
        $dates = $this->xpathArray(".//*[@id='reviews-content']//*[@itemprop='datePublished']");

        for ($i = 0; $i < count($comments); $i++)
        {
            $comment = array();
            $comment['comment'] = sanitize_text_field($comments[$i]);
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
                $comment['rating'] = TextHelper::ratingPrepare(str_replace('Average rating:', '', $ratings[$i]));
            if (!empty($dates[$i]))
                $comment['date'] = strtotime($dates[$i]);
            $extra['comments'][] = $comment;
        }

        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='ratingValue']"));
        if (!$extra['rating'])
            $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@class='c-review-average']"));
        return $extra;
    }

    public function isInStock()
    {
        if ($this->xpathScalar(".//*[@itemprop='availability']/@content") == 'OutOfStock')
            return false;
        else
            return true;
    }

}
