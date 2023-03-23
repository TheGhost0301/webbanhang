<?php

namespace Keywordrush\AffiliateEgg;

/**
 * MytoysruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class MytoysruParser extends ShopParser {

    protected $charset = 'UTF-8';

    public function parseCatalog($max)
    {
        return array_slice($this->xpathArray(".//span[contains(@class, 'prod_title')]/a/@href"), 0, $max);
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//*[@id='content1']//*[@itemprop='description']");
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//meta[@itemprop='price']/@content");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@itemprop='offers']//*[@class='price--canceled']");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@itemprop='brand']");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//*[@class='pdp__pic']//img/@src");
        $img = str_replace('.jpg?$rtf_mt_pdp_mp_xl$', '.jpg', $img);
        return $img;
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $results = $this->xpathArray(".//div[@class='infoArea']//div[contains(@id,'_product_details_')]/ul/li");
        $feature = array();
        foreach ($results as $res)
        {
            $expl = explode(":", $res, 2);
            if (count($expl) == 2)
            {
                $feature['name'] = $expl[0];
                $feature['value'] = $expl[1];
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@class='imageView']//div[@class='views']//li/span/a/img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
            {
                $extra['images'][] = str_replace('-u.gif', '-w.jpg', $res);
            }
        }
        return $extra;
    }

    public function isInStock()
    {
        return $this->xpath->evaluate("boolean(.//div[@class='versand']//*[contains(.,'Есть в наличии') or contains(.,'Количество ограничено')])");
    }

}
