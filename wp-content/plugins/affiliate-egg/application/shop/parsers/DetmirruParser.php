<?php

namespace Keywordrush\AffiliateEgg;

/**
 * DetmirruParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class DetmirruParser extends ShopParser {

    protected $charset = 'utf-8';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[@class='goods_prod_list']//div[@class='caption']/a/@href"), 0, $max);
        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = 'http://www.detmir.ru' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//div[@class='product_block']/h1");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//div[@class='g_block']//div[@class='igh_inner']");
    }

    public function parsePrice()
    {
        return (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//div[@class='product_card__price']/div[@class='price']/text()"));
    }

    public function parseOldPrice()
    {
        return (float) preg_replace('/[^0-9,]/', '', $this->xpathScalar(".//div[@class='product_card__price']/div[@class='old_price']/text()"));
    }

    public function parseManufacturer()
    {
        return trim($this->xpathScalar(".//div[@class='g_block']//div[@class='ijh_inner']/p/text()[contains(., 'Бренд:')]/following::a"));
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//div[@class='product_block']//div[@class='imageContents']/img/@src");
    }

    public function parseImgLarge()
    {
        return '';
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();

        $results = $this->xpathArray(".//div[@class='g_block']//div[@class='ijh_inner']/p");
        $feature = array();
        foreach ($results as $res)
        {
            $expl = explode(":", $res, 2);
            if (count($expl) == 2 && $expl[0] != 'Бренд' && $expl[0] != 'Инструкция')
            {
                $feature['name'] = $expl[0];
                $feature['value'] = trim($expl[1]);
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@class='product_carousel']//ul/li/div/img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
                $extra['images'][] = str_replace('/60x60/', '/600x600/', $res);
        }
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//div[@class='basket_buttons to_notice'])");
        return ($res) ? false : true;
    }

}
