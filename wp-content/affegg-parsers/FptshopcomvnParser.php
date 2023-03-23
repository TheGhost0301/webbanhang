<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: fptshop.com.vn
  URI: http://fptshop.com.vn
  Icon: http://www.google.com/s2/favicons?domain=fptshop.com.vn
  SEARCH URI: https://fptshop.com.vn/tim-kiem/%KEY-WORD%
 *  
 */

/**
 * FptshopcomvnParser class file
 *
 * @author khotainguyen.com <contact@khotainguyen.com>
 * @link https://khotainguyen.com
 * @copyright Copyright &copy; 2018 khotainguyen.com
 */
class FptshopcomvnParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@class='fs-lpitem']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@class='fs-lpil']/a/@href"), 0, $max);

        $host = parse_url($this->getUrl(), PHP_URL_HOST);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'https://' . $host . $url;
        }
        
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//meta[@property='og:description']/@content");
    }

    public function parsePrice()
    {
        $price = explode("₫", $this->xpathScalar(".//*[@class='fs-dtprice ']"));
        return $price[0];
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='fs-dtprice ']/del"); 
    }

    public function parseManufacturer()
    {
        
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        if ($img && !preg_match('/^https:/', $img))
            $img = 'https:' . $img;
        
        return $img;
    }

    public function parseImgLarge()
    {
        $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        if ($img && !preg_match('/^https:/', $img))
            $img = 'https:' . $img;
        
        return $img;
    }

    public function parseExtra()
    {
        $extra = array();

        $names = $this->xpathArray(".//*[@class='fs-dttsktul']//li/label");
        $values = $this->xpathArray(".//*[@class='fs-dttsktul']//li/span");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        $rating = explode("/", $this->xpathScalar(".//*[@class='fs-dtrt-col fs-dtrt-c1']//h5"));
        if (count($rating) == 2)
            $extra['rating'] = TextHelper::ratingPrepare($rating[0]);

        return $extra;
    }

    public function isInStock()
    {
        if ($this->parsePrice())
            return true;
        else
            return false;
    }

}
