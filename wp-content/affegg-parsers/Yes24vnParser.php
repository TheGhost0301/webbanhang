<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: Yes24.vn
  URI: http://yes24.vn
  Icon: http://www.google.com/s2/favicons?domain=yes24.vn
  SEARCH URI: https://www.yes24.vn/tim-kiem?q=%KEYWORD%
 * 
 */

/**
 * Yes24vnParser class file
 *
 * @author khotainguyen.com <contact@khotainguyen.com>
 * @link https://khotainguyen.com
 * @copyright Copyright &copy; 2018 khotainguyen.com
 */
class Yes24vnParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';

    public function parseCatalog($max)
    {   
        $urls = array_slice($this->xpathArray(".//*[@class='th-product-item']/a/@href"), 0, $max);
        
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//*[contains(@class, 'full left tr-prd-info-content')]");
    }

    public function parsePrice()
    {
        $price = $this->xpathScalar(".//*[@itemprop='price']/@content");
        if (!$price)
            $price = $this->xpathScalar(".//*[@class='th-detail-price']");
        return $price;
    }

    public function parseOldPrice()
    {
        $price = $this->xpathScalar(".//*[contains(@class, 'tr-prd-price')]//*[@class='linethrough-text']");
        return $price;
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@class='tr-thuonghieu-reg']/a[1]");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//*[@property='og:image']/@content");
        return $img;
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='ratingValue']/@content"));
        if (!$extra['rating'])
        {
            $rating = $this->xpathScalar(".//*[@class='full left tr-star-ranknum']");
            $parts = explode('/', $rating);
            if (count($parts) == 2)
                $extra['rating'] = TextHelper::ratingPrepare($parts[0]);
        }

        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
