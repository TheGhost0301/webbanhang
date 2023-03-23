<?php

namespace Keywordrush\AffiliateEgg;

/**
 * NeboruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class NeboruParser extends MicrodataShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//ul[@class='products-list']//a[@class='item__link url']/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://nebo.ru' . $url;
        }
        return $urls;
    }
    
    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }        

    public function parseImg()
    {
        return $this->xpathScalar(".//div[@class='slide']/img/@src");
    }    
    
    public function parseOldPrice()
    {
        return $this->xpathScalar(".//div[@class='product_price product_price_old']");
    }


}
