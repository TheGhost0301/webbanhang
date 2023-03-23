<?php

namespace Keywordrush\AffiliateEgg;

/**
 * RendezvousruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class RendezvousruParser extends ShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[contains(@class,'goods_block')]/div[@class='cat_item']/a[1]/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.rendez-vous.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return trim($this->xpathScalar(".//div[@class='good_heading']"));
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//div[@class='details']//div[@class='display'][2]");
    }

    public function parsePrice()
    {
        return preg_replace('/[^0-9]/', '', $this->xpathScalar(".//div[@class='price']/span[2]"));
    }

    public function parseOldPrice()
    {
        return preg_replace('/[^0-9]/', '', $this->xpathScalar(".//div[@class='price']/span[1]"));
    }

    public function parseManufacturer()
    {
        return '';
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//div[@class='image']/a/img/@src");
        if ($img && !preg_match('/^http:\/\//', $img))
            $img = 'http://www.rendez-vous.ru' . $img;
        return $img;
    }

    public function parseImgLarge()
    {
        $img = $this->xpathScalar(".//div[@class='image']/a/@data-image");
        if ($img && !preg_match('/^http:\/\//', $img))
            $img = 'http://www.rendez-vous.ru' . $img;
        return $img;
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $results = $this->xpathArray(".//div[@class='details']//div[@class='display'][1]//div[@class='details_col']/div[not(contains(@class,'title'))]");

        $feature = array();
        foreach ($results as $res)
        {
            $expl = explode(":", $res, 2);
            if (count($expl) == 2)
            {
                $feature['name'] = trim($expl[0]);
                $feature['value'] = trim($expl[1]);
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//ul[@class='gallery_carousel']/li/@data-image");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res && !preg_match('/^http:\/\//', $res))
                $res = 'http://www.rendez-vous.ru' . $res;

            $extra['images'][] = $res;
        }
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpathScalar(".//div[@class='to_bin']/button/@data-bin");
        if ($res && $res == 1)
            return true;
        else
            return false;
    }

}
