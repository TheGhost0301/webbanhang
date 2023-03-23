<?php

namespace Keywordrush\AffiliateEgg;

/**
 * HoffruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class HoffruParser extends ShopParser {

    protected $charset = 'UTF-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[@class='ba-img-wrapper']/h3/a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.hoff.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//div[@class='header-wrapper']/h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//span[@itemprop='description']");
    }

    public function parsePrice()
    {
        return preg_replace('/[^0-9]/', '', $this->xpathScalar(".//div[@class='product-price']//span[@itemprop='price']"));
    }

    public function parseOldPrice()
    {
        return preg_replace('/[^0-9]/', '', $this->xpathScalar(".//div[@class='product-price']//span[@class='product-old-price']"));
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//ul[@class='product-properties']//span[@itemprop='brand']");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//div[@class='product-big-image']/div/img/@src");
        if (!preg_match('/^http:\/\//', $img))
            $img = 'http://www.hoff.ru' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        return '';
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@class='product-properties']/div/div[1]");
        $values = $this->xpathArray(".//div[@class='product-properties']/div/div[3]");

        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($names[$i]))
            {
                $feature['name'] = sanitize_text_field(str_replace(":", "", $names[$i]));
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@class='product-big-image']/div/img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if (!preg_match('/^http:\/\//', $res))
                $res = 'http://www.hoff.ru' . $res;
            $extra['images'][] = $res;
        }
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//span[@class='product-order-text'][contains(.,'Товара нет в наличии')])");
        return ($res) ? false : true;
    }

}
