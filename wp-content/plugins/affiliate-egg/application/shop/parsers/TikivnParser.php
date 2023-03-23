<?php

namespace Keywordrush\AffiliateEgg;

/**
 * TikivnParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class TikivnParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[contains(@class, 'product-item')]//a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@class='search-a-product-item']"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//p[@class='title']/a/@href"), 0, $max);
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1");
    }

    public function parseDescription()
    {
        $pieces = $this->xpathArray(".//*[@class='top-feature-item bullet-wrap']/p");
        $description = join('; ', $pieces);
        if (!$description)
            $description = $this->xpathScalar(".//*[@itemprop='description']");
        return $description;
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//*[@itemprop='price']/@content");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[@id='span-list-price']");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@itemprop='brand']/*[@itemprop='name']/@content");
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//meta[@property='og:image']/@content");
        if (!$img)
            $img = $this->xpathScalar(".//*[@class='product-image']//a/@data-image");
        $img = str_replace(' ', '%20', $img);
        return $img;
    }

    public function parseImgLarge()
    {
        return $this->xpathScalar(".//*[@class='product-image']//a/@data-zoom-image");
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['images'] = $this->xpathArray(".//*[@id='product-images']//a/@data-image");

        $names = $this->xpathArray(".//*[@id='chi-tiet']//td[@rel]"); 
        $values = $this->xpathArray(".//*[@id='chi-tiet']//td[@class]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]))
            {
                $feature['name'] = \sanitize_text_field($names[$i]);
                $feature['value'] = \sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='aggregateRating']//*[@itemprop='ratingValue']/@content"));

        return $extra;
    }

    public function isInStock()
    {
        if ($this->xpathScalar(".//*[@itemprop='availability']/@href") == 'https://schema.org/OutOfStock')
            return false;
        else
            return true;

    }

}
