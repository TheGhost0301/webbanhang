<?php

namespace Keywordrush\AffiliateEgg;

/**
 * EldoradoruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class EldoradoruParser extends MicrodataShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[@itemprop='name']/a/@href"), 0, $max);
        return $urls;
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='bigPriceContainer']//*[@class='product-box-price__old-el']");
    }

}
