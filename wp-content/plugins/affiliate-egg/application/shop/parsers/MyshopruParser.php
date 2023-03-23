<?php

namespace Keywordrush\AffiliateEgg;

/**
 * MyshopruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class MyshopruParser extends ShopParser {

    protected $charset = 'windows-1251';

    public function parseCatalog($max)
    {
        $urls = array();
        $urls = array_slice($this->xpathArray(".//*[@data-o='listgeneral']//td[1]/div/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//td[contains(@class,'w730')]//table//td[2]/a[1]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//td[contains(@class,'w740s')]//div[@class='tal']/div[1]/a[1]/@href"), 0, $max);

        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://my-shop.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//td[@itemprop='description']");
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//table[@class='w100p']//td[contains(@class,'bgcolor_2 list_border')]/b");
    }

    public function parseOldPrice()
    {
        return '';
    }

    public function parseManufacturer()
    {
        return trim($this->xpathScalar(".//table[@width='550']//td[contains(.,'Производитель:') or contains(.,'Издательство:')]/a"));
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//table[@width='550']//img/@src");
        if ($img && !preg_match('/^https?:\/\//', $img))
            $img = 'https:' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        $img = $this->xpathScalar(".//div[@class='fotorama']/a/img/@src");
        if ($img && !preg_match('/^https?:\/\//', $img))
            $img = 'https:' . $img;
        return $img;        
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $results = $this->xpathArray(".//div[@class='small1']//td[contains(@class,'small1')]/text()");
        $feature = array();
        foreach ($results as $res)
        {
            $expl = explode(":", $res, 2);
            if (count($expl) == 2 && !empty($expl[1]))
            {
                $feature['name'] = $expl[0];
                $feature['value'] = $expl[1];
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        return $extra;
    }

    public function isInStock()
    {
        return $this->xpath->evaluate("boolean(.//table[@class='w100p']//td[contains(@class,'bgcolor_2 list_border')][contains(.,'в наличии')])");
    }

}
