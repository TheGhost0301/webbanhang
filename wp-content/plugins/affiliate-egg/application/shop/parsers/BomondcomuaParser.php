<?php

namespace Keywordrush\AffiliateEgg;

/**
 * BomondcomuaParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class BomondcomuaParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'UAH';

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//a[@class='product-image']/@href");
    }

}
