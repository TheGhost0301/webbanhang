<?php

namespace Keywordrush\AffiliateEgg;

/**
 * VangoldruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class VangoldruParser extends ShopParser {

    protected $charset = 'UTF-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[contains(@class,'product-content')]//div[@class='product-item-img']/a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.vangold.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return sanitize_text_field($this->xpathScalar(".//div[@class='columns nine omega']//h2"));
    }

    public function parseDescription()
    {
        return '';
    }

    public function parsePrice()
    {
        return preg_replace('/[^0-9]/', '', $this->xpathScalar(".//div[@class='catalog_price_right']"));
    }

    public function parseOldPrice()
    {
        return '';
    }

    public function parseManufacturer()
    {
        return 'Vangold';
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//div[@class='product-content-photo-big']/img/@src");
        if (!preg_match('/^http:\/\//', $img))
            $img = 'http://www.vangold.ru' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        return "";
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $names = $this->xpathArray(".//table[@class='product-content-table']/tbody/tr/td[1]");
        $values = $this->xpathArray(".//table[@class='product-content-table']/tbody/tr/td[2]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && $values[$i] != '-')
            {
                $feature['name'] = sanitize_text_field(str_replace(":", "", $names[$i]));
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//div[@class='columns nine omega']//p[@class='product-content-title' and text()='Нет в наличии'])");
        return ($res) ? false : true;
    }

}
