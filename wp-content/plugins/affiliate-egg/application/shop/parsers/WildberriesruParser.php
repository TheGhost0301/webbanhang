<?php

namespace Keywordrush\AffiliateEgg;

/**
 * WildberriesruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class WildberriesruParser extends MicrodataShopParser {

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//a[@class='ref_goods_n_p']/@href");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//*[@id='description']//p");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='inner-price']//del");
    }

}
