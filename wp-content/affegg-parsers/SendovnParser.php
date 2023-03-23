<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: Sendo.vn
  URI: http://www.sendo.vn
  Icon: http://www.google.com/s2/favicons?domain=sendo.vn
  CPA:
  SEARCH URI: https://www.sendo.vn/tim-kiem/?q=%KEYWORD% 
 * 
 */

/**
 * SendovnParser class file
 *
 * @author khotainguyen.com <contact@khotainguyen.com>
 * @link https://khotainguyen.com
 * @copyright Copyright &copy; 2018 khotainguyen.com
 */
class SendovnParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@class='item']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@class='content_item content_item_hover']//*[@class='name_product fullname']/@href"), 0, $max);

        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'https://www.sendo.vn' . $host . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//meta[@name='description' or @property='og:description']/@content");
    }

    public function parsePrice()
    {
        $price = explode("₫", $this->xpathScalar(".//*[@class='currentPrice_2zpf']"));

        return str_replace(',', '', $price[0]);
    }

    public function parseOldPrice()
    {
        $orgPrice = explode("₫", $this->xpathScalar(".//*[@class='oldPrice_119m']"));
        return str_replace(',', '', $orgPrice[0]);
    }

    public function parseManufacturer()
    {
        
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//*[@id='product_gallery']//img[@data-zoom-image]/@src");
        if (!$img)
            $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        if ($img)
            return $img;

        if (!$data = $this->xpathScalar(".//script[starts-with(normalize-space(text()),\"var productJsonMedias\")]"))
            return '';
        if (!preg_match('/var productJsonMedias = (\[{.+}\]);/', $data, $matches))
            return '';
        if (!$json = json_decode($matches[1], true))
            return '';

        if (isset($json[0]['carouselUrl']) && isset($json[0]['type']) && $json[0]['type'] == 'IMAGE')
            return $json[0]['carouselUrl'];
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $names = $this->xpathArray(".//*[@class='attrs-block']//li//*[1]"); 
        $values = $this->xpathArray(".//*[@class='attrs-block']//li//*[2]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = \sanitize_text_field($names[$i]);
                $feature['value'] = \sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        
        if ($rating = $this->xpathScalar(".//*[@class='rated-star']/@style"))
        {
            $rating_parts = explode(':', $rating);
            if (count($rating_parts) == 2)
                $extra['rating'] = TextHelper::ratingPrepare((int) $rating_parts[1] / 20);
        }        
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
