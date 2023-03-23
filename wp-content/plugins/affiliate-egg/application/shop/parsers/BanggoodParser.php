<?php

namespace Keywordrush\AffiliateEgg;

/**
 * BanggoodParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class BanggoodParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//dd[@class='name']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//span[@class='title']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@class='hot_others_box_c']//a[1]/@href"), 0, $max);

        foreach ($urls as $i => $url)
        {
            $replace = array(
                '?rmmds=search',
                '?rmmds=search-bottom-lookingfor',
                '?rmmds=category',
            );
            $urls[$i] = str_replace($replace, '', $url);
        }
        return $urls;
    }

    public function parseLdJson()
    {
        if (parent::parseLdJson())
            $this->ld_json;

        $lds = $this->xpathArray(".//script[@type='application/ld+json']", true);

        // fix
        if (isset($lds[1]))
            $lds = array($lds[1]);
        foreach ($lds as $ld)
        {
            $ld = TextHelper::fixHiddenCharacters($ld);
            $ld = preg_replace('/\/\*.+?\*\//', '', $ld);
            $ld = trim($ld, ";");
            if (!$data = json_decode($ld, true))
                continue;
            if (isset($data['mainEntity']))
                $data = $data['mainEntity'];

            if (isset($data['@graph']))
            {
                foreach ($data['@graph'] as $d)
                {
                    if (isset($d['@type']) && (in_array($d['@type'], $this->product_types) || in_array(ucfirst(strtolower($d['@type'])), $this->product_types)))
                        $data = $d;
                }
            } elseif (isset($data[0]) && isset($data[1]))
            {
                foreach ($data as $d)
                {
                    if (isset($d['@type']) && (in_array($d['@type'], $this->product_types) || in_array(ucfirst(strtolower($d['@type'])), $this->product_types)))
                        $data = $d;
                }
            } elseif (isset($data[0]))
                $data = $data[0];

            if (isset($data['@type']) && (in_array($data['@type'], $this->product_types) || in_array(ucfirst(strtolower($data['@type'])), $this->product_types)))
            {
                $this->ld_json = $data;
                return $this->ld_json;
            }
        }
        return false;
    }

    public function parseTitle()
    {
        if ($title = parent::parseTitle())
            return $title;
        else
            return $this->xpathScalar(".//div[@class='title_hd']//h2/strong");
    }

    public function parseDescription()
    {
        return '';
    }

    public function parsePrice()
    {
        // variations fix
        $p = $this->xpathScalar(".//*[@class='item_now_price']");
        if (strstr($p, 'US$'))
            return $p;
        $description = $this->xpathScalar(".//meta[@property='og:description']/@content");
        if (preg_match('/Only\sUS\$(.+?),\s/', $description, $matches))
            return $matches[1];

        if ($p = parent::parsePrice())
            return $p;

        $price = $this->xpathScalar(".//*[@itemprop='price']/@content");
        if (!$price)
            $price = $this->xpathScalar(".//*[@itemprop='price']");
        if (!$price)
            $price = $this->xpathScalar(".//div[@class='good_main']//span[@itemprop='price']");
        if (!$price)
            $price = $this->xpathScalar(".//div[@class='good_main']//div[@class='now']/@oriprice");
        return $price;
    }

    public function parseOldPrice()
    {
        $price = $this->xpathScalar(".//span[@class='price_old']");
        if (!$price)
            $price = $this->xpathScalar(".//div[@class='good_main']//span[@class='azs_hidd'][1]");
        if (!$price)
            $price = $this->xpathScalar(".//div[@class='good_main']//div[@class='old']/@oriprice");
        if ($price > $this->item['price'])
            return $price;
        else
            return '';
    }

    public function parseManufacturer()
    {
        return '';
    }

    public function parseImg()
    {
        if ($i = parent::parseImg())
            return $i;

        $img = trim($this->xpathScalar(".//meta[@property='og:image']/@content"));
        if (!$img)
            $img = $this->xpathScalar(".//*[@class='pro_img_box']//img/@src");
        return $img;
    }

    public function parseImgLarge()
    {
        $img = $this->parseImg();
        if (!$img)
            return '';
        return str_replace('/thumb/view/upload/', '/thumb/large/upload/', $img);
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();
        return $extra;
    }

    public function isInStock()
    {
        if (strstr($this->xpathScalar(".//title"), 'Banggood.com sold out'))
            return false;
        if ($this->xpathScalar(".//*[@class='addToCartBtn_box']//a[contains(@class, 'arrivalnotice')]") == 'In Stock Alert')
            return false;

        return parent::isInStock();
    }

}
