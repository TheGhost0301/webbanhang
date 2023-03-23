<?php

namespace Keywordrush\AffiliateEgg;

/**
 * FoxtrotcomuaParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class FoxtrotcomuaParser extends MicrodataShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'UAH';

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//*[@class='listing-item__info']//a/@href");
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }
    
    public function parseDescription()
    {
        return '';
    }

    public function parsePrice()
    {
        if ($p = $this->xpathScalar(".//*[@class='price']//*[@class='price__relevant']"))
            return $p;
        else
            return parent::parsePrice();
    }

    public function parseImg()
    {
        if ($p = $this->xpathScalar(".//*[@class='product-store-information__slider-block']//img/@data-src"))
            return $p;
        else
            return parent::parseImg();
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@class='price']//*[@class='price__not-relevant']");
    }

}
