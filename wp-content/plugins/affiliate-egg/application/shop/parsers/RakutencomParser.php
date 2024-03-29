<?php

namespace Keywordrush\AffiliateEgg;

/**
 * RakutencomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com> 
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class RakutencomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//div[@class='product']/a/@href");
    }

    public function parsePrice()
    {
        if (preg_match('/"summary_new_best_price":{"value":"(.+?)"}/', $this->dom->saveHTML(), $matches))
            return $matches[1];
        
        if ($p = $this->xpathScalar(array(".//*[@id='prdBuyBoxV2']//*[contains(@class, 'price')]", ".//*[@id='prdBuyBoxV2']//p[@class='price typeNew spacerBottomXs']")))
            return $p;
        else
            return parent::parsePrice();
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//section[@id='prdBuyBoxV2']//span[contains(@class, 'oldPrice')]");
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();

        $names = $this->xpathArray(".//table[@class='spec_table_ctn']//th");
        $values = $this->xpathArray(".//table[@class='spec_table_ctn']//td");
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
