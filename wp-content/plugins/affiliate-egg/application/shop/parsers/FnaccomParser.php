<?php

namespace Keywordrush\AffiliateEgg;

/**
 * FnaccomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class FnaccomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';
    protected $headers = array(
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'Accept-Encoding' => 'identity',
    );

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//p[@class='Article-desc']/a/@href");
    }

    public function parseOldPrice()
    {
        $price = $this->xpathScalar(".//*[contains(@class, 'f-priceBox-price--old')]", true);
        $price = str_replace('&euro;', '.', $price);
        return $price;
    }

    public function parseManufacturer()
    {
        if (isset($this->_ld['brand']['name']))
            return $this->_ld['brand']['name'];
    }


    public function parseExtra()
    {
        $extra = parent::parseExtra();
        $extra['features'] = array();
        $names = $this->xpathArray(".//*[@class='f-productDetails-table']//td[1]");
        $values = $this->xpathArray(".//*[@class='f-productDetails-table']//td[2]");
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

        return $extra;
    }


}
