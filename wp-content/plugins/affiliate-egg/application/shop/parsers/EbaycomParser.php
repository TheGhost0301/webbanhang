<?php

namespace Keywordrush\AffiliateEgg;

/**
 * EbaycomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class EbaycomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//ul[@id='ListViewInner']//h3/a/@href"), 0, $max);
        // ebay deals
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[contains(@class, 'grid-gutter')]//h3/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//h3/a[@itemprop='url']/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//h3[@class='lvtitle']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//a[@class='s-item__link']/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//h3/a/@href"), 0, $max);
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']/text()");
    }

    public function parseDescription()
    {
        return '';
        /*
          $res = $this->xpathArray(".//div[@id='desc_div']/node()[normalize-space()][not(contains(., 'http://'))]");
          return sanitize_text_field(implode(' ', $res));
         * 
         */
    }

    public function parsePrice()
    {
        $res = $this->xpathScalar(".//span[@itemprop='price']");
        if (!$res)
            $res = $this->xpathScalar(".//span[@id='mm-saleDscPrc']");
        return $res;
    }

    public function parseOldPrice()
    {
        $res = $this->xpathScalar(".//span[@id='mm-saleOrgPrc']");
        if (!$res)
            $res = $this->xpathScalar(".//*[@id='orgPrc']");
        return $res;
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//h2[@itemprop='brand']");
    }

    public function parseImg()
    {
        $res = $this->xpathScalar(".//*[@id='icImg']/@src");
        if (!$res)
        {
            $results = $this->xpathScalar(".//script[contains(.,'image.src=')]");
            preg_match("/image\.src=\s+?'(.+?)'/msi", $results, $match);
            if (isset($match[1]))
                $res = $match[1];
        }
        return $res;
    }

    public function parseImgLarge()
    {
        $res = '';
        if ($this->item['orig_img'])
            $res = preg_replace('/\/\$_\d+\./', '/$_57.', $this->item['orig_img']);
        return $res;
    }

    public function parseExtra()
    {
        $extra = array();

        preg_match("/\/(\d{12})/msi", $this->getUrl(), $match);
        $extra['item_id'] = isset($match[1]) ? $match[1] : '';

        $extra['features'] = array();

        $names = $this->xpathArray(".//div[@class='itemAttr']//tr/td[@class='attrLabels']");
        $values = $this->xpathArray(".//div[@class='itemAttr']//tr/td[position() mod 2 = 0]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && $names[$i] != 'Condition:' && $names[$i] != 'Brand:')
            {
                $feature['name'] = str_replace(":", "", $names[$i]);
                $feature['value'] = $values[$i];
                $extra['features'][] = $feature;
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@id='vi_main_img_fs_slider']//img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
            {
                $new_res = preg_replace('/\/\$_\d+\./', '/$_57.', $res);
                if ($new_res !== $res)
                {
                    $extra['images'][] = $new_res;
                }
            }
        }

        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='reviewBody']");
        $users = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='author']");
        $dates = $this->xpathArray(".//*[@class='reviews']//*[@itemprop='datePublished']");
        $ratings = $this->xpathArray(".//*[@class='reviews']//*[@class='ebay-star-rating']/@aria-label");
        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
                $comment['rating'] = TextHelper::ratingPrepare((float) $ratings[$i]);
            if (!empty($dates[$i]))
                $comment['date'] = strtotime($dates[$i]);
            $extra['comments'][] = $comment;
        }
        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='aggregateRating']//*[@itemprop='ratingValue']/@content"));
        return $extra;
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//span[@class='msgTextAlign'][contains(.,'This listing has ended')])");
        if (!$res)
            $res = $this->xpath->evaluate("boolean(.//span[@class='msgTextAlign'][contains(.,'This Buy It Now listing has ended')])");
        return ($res) ? false : true;
    }

    public function getCurrency()
    {
        $currency = $this->xpathScalar(".//span[@itemprop='priceCurrency']/@content");
        if (!$currency)
            $currency = $this->currency;
        return $currency;
    }

}
