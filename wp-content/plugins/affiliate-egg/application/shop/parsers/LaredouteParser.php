<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: Laredoute.ru
  URI: http://www.laredoute.ru
  Icon: http://www.google.com/s2/favicons?domain=laredoute.ru
  CPA: gdeslon
 */

/**
 * LaredouteParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class LaredouteParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'RUB';
    protected $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0';
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'Accept-Encoding' => 'identity',
    );

    function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//li[contains(@data-cerberus, 'area_plpProduit_product')]/a/@href"), 0, $max);
        return $urls;
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(array(".//*[@class='price-block']//*[@class='price-info line-through']", ".//div[@class='price']/span[@class='sale-price-before']"));
    }

}
