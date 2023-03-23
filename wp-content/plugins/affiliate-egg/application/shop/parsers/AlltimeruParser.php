<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AlltimeruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class AlltimeruParser extends ShopParser {

    protected $charset = 'windows-1251';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@class='bc-catalog']//*[@class='bcc-title']//a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.alltime.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//*[@id='item-title']");
    }

    public function parseDescription()
    {
        return $this->xpathScalar("(.//div[@class='product-accordion']/div)[1]");
    }

    public function parsePrice()
    {
        return $this->xpathScalar("(.//div[@class='control-price']//strong)[1]");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//div[@class='control-price']//strong[contains(@class, 'old')]");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@class='brand-photo']//img/@alt");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//*[@id='js-current-gallery-image']/@src");
        if ($img && !preg_match('/^http:\/\//', $img))
            $img = 'http://www.alltime.ru' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@class='product-accordion']/div[2]//th");
        $values = $this->xpathArray(".//div[@class='product-accordion']/div[2]//td");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = sanitize_text_field(str_replace(":", "", $names[$i]));
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//*[@class='product-thumb']//img/@data-full-src");
        foreach ($results as $i => $res)
        {
            if ($i == 1)
                continue;
            if (!preg_match('/^http:\/\//', $res))
                $extra['images'][] = 'http://www.alltime.ru' . $res;
            else
                $extra['images'][] = $res;
        }
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
