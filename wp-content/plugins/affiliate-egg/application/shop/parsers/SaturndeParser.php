<?php

namespace Keywordrush\AffiliateEgg;

/**
 * SaturndeParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class SaturndeParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';

    public function parseCatalog($max)
    {
        // redirect from search to product page
        if ($this->parseTitle() && $url = $this->xpathScalar(".//link[@rel='canonical']/@href"))
        {
            return array($url);
        }	
	
        $urls = array_slice($this->xpathArray(".//div[@class='product-wrapper']//h2/a/@href"), 0, $max);
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']");
    }

    public function parseDescription()
    {
        $descr = '';
        $results = $this->xpathScalar(".//article[@id='produktbeschreibung']/p");
        $descr = sanitize_text_field($results);
        return $descr;
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//meta[@property='product:price:amount']/@content");
    }

    public function parseOldPrice()
    {
        return '';
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//meta[@property='product:brand']/@content");
    }

    public function parseImg()
    {
        $imageurl = $this->xpathScalar(".//meta[@property='og:image']/@content");
        $imageurl = $imageurl . '.png';
        return $imageurl;
    }

    public function parseImgLarge()
    {
        $img = $this->parseImg();
        return str_replace('/fee_325_225_png/', '/fee_786_587_png/', $img);
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@id='features']//dl[@class='specification']/dt");
        $values = $this->xpathArray(".//div[@id='features']//dl[@class='specification']/dd");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = str_replace(":", "", $names[$i]);
                $feature['value'] = $values[$i];
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//aside[@id='product-sidebar']/ul[@class='thumbs']/li[position() >=2]/a/@data-magnifier");

        foreach ($results as $i => $res)
        {
            $extra['images'][] = 'http:' . $res;
        }
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
