<?php

namespace Keywordrush\AffiliateEgg;

/**
 * CromacomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class CromacomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'INR';

    public function restPostGet($url, $fix_encoding = true)
    {
        //fix incorrect json
        $html = parent::restPostGet($url, $fix_encoding = true);
        $html = str_replace('"availability": inStock', '"availability": "inStock"', $html);
        $html = preg_replace('/"description": ".+?",/', '', $html);
        return $html;
    }

    public function parseCatalog($max)
    {
        return $this->xpathArray(array(".//a[@class='product__list--name']/@href", ".//a[@class='productMainLink']/@href", ".//a[@class='productMainLink']/@href"));
    }

    public function parseOldPrice()
    {
        
    }

    public function parseImg()
    {
        if ($img = $this->xpathScalar(".//img[@class='_pdp_im']/@src"))
            return $img;
        else
            return parent::parseImg();
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();
        $extra['features'] = array();

        $names = $this->xpathArray(".//*[@class='product-classifications']//li/span[@class='attrib']");
        $values = $this->xpathArray(".//*[@class='product-classifications']//li/span[@class='attribvalue']");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (empty($values[$i]))
                continue;

            $feature['name'] = sanitize_text_field($names[$i]);
            $feature['value'] = sanitize_text_field($values[$i]);
            $extra['features'][] = $feature;
        }
        return $extra;
    }

}
