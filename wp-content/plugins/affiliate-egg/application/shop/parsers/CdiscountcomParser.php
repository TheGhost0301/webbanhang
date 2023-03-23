<?php

namespace Keywordrush\AffiliateEgg;

/**
 * CdiscountcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com> 
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class CdiscountcomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EUR';

    public function parseCatalog($max)
    {
        $urls = $this->xpathArray(".//a[@class='jsQs']/@href");
        if (!$urls)
            $urls = $this->xpathArray(".//*[@class='prdtBILDetails']/a/@href");
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//p[@itemprop='description']");
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//*[@itemprop='price']/@content");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[contains(@class, 'fpPriceBloc')]//*[contains(@class, 'fpStriked')]/text()");
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@itemprop='brand']//*[@itemprop='name']");
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//*[@property='twitter:image']/@content");
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra['features'] = array();
        $names = $this->xpathArray(".//table[@class='fpDescTb fpDescTbPub']//td[1]");
        $values = $this->xpathArray(".//table[@class='fpDescTb fpDescTbPub']//td[2]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!trim($names[$i]))
                continue;
            if (!empty($values[$i]))
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[@class='detMainRating']//*[@class='infoCli']/p");
        $users = $this->xpathArray(".//*[@class='detMainRating']//*[@itemprop='author']");
        for ($i = 0; $i < count($comments); $i++)
        {
            $c = \sanitize_text_field($comments[$i]);
            if (!$c)
                continue;
            $comment['comment'] = $c;
            if (!empty($users[$i]))
                $comment['name'] = \sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
            {
                $r_parts = explode('/', $ratings[$i]);
                $comment['rating'] = TextHelper::ratingPrepare($r_parts[1] / 2);
            }
            $extra['comments'][] = $comment;
        }

        $extra['rating'] = TextHelper::ratingPrepare(str_replace(',', '.', $this->xpathScalar(".//*[@itemprop='ratingValue']")));
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
