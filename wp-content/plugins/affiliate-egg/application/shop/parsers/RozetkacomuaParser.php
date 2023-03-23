<?php

namespace Keywordrush\AffiliateEgg;

/**
 * RozetkacomuaParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class RozetkacomuaParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'UAH';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@id='block_with_goods' or @id='block_with_search']//div[contains(@class,'g-i-tile-i-title') or contains(@class, 'g-i-list-title')]/a/@href"), 0, $max);
        if ($urls)
        {
            return $urls;
        } elseif ($this->parseTitle())
        {
            // redirect from search to product page
            return array($this->xpathScalar(".//meta[@property='og:url']/@content"));
        }
        return array();
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']");
    }

    public function parseDescription()
    {
        $description = $this->xpathScalar(".//div[@itemprop='description']");
        if (!$description)
        {
            $description = $this->xpathArray(".//*[@id='short_text']/p");
            $description = join("\r\n", $description);
        }
        return $description;
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//*[@itemprop='price']/@content");
    }

    public function parseOldPrice()
    {
        if ($p = $this->xpathScalar(".//*[@class='detail-price-old ng-star-inserted']"))
            return $p;
        
        $html = $this->dom->saveHTML();
        if (!preg_match('/pricerawjson\s=\s"(.+?)";/', $html, $matches))
            return;
        if (!$data = json_decode(urldecode($matches[1]), true))
            return;
        if (isset($data['old_price']))
            return $data['old_price'];
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//li[@itemprop='itemListElement'][last()]//*[@class='breadcrumbs-title']");
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//meta[@name='og:image']/@content");
    }

    public function parseImgLarge()
    {
        return $this->xpathScalar(".//*[@id='preview_details']/div[position()= 1]/a/@href");
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();
        $names_values = $this->xpathArray(".//*[@class='detail-col-description']//td[@class='chars-t-cell']", true);
        //$feature_names = $this->xpathArray(".//*[@class='detail-col-description']//td[@class='chars-t-cell']//*[@class='chars-title']", true);
        //$feature_values = $this->xpathArray(".//*[@class='detail-col-description']//td[2]", true);

        $feature_names = $feature_values = array();
        for ($i = 0; $i < count($names_values); $i = $i + 2)
        {
            $feature_names[$i] = $names_values[$i];
            $feature_values[$i] = $names_values[$i + 1];
            $feature = array();

            $feature_values[$i] = html_entity_decode($feature_values[$i]);
            preg_match_all('/<span class="glossary-term">.+?<\/span>/ims', $feature_values[$i], $matches);
            if (!$matches[0])
                preg_match_all('/<span class="chars-value.+?">.+?<\/span>/ims', $feature_values[$i], $matches);
            if (!$matches[0])
                continue;
            $v = '';
            foreach ($matches[0] as $m)
            {
                if ($v)
                    $v .= '; ';
                $v .= strip_tags($m);
            }

            $feature_names[$i] = html_entity_decode($feature_names[$i]);
            preg_match('/<span class="glossary-term">(.+?)<\/span>/ims', $feature_names[$i], $matches);
            if ($matches)
                $n = $matches[1];
            else
                $n = strip_tags($feature_names[$i]);

            $feature['name'] = sanitize_text_field($n);
            $feature['value'] = sanitize_text_field($v);
            $extra['features'][] = $feature;
        }
        $extra['images'] = $this->xpathArray(".//*[@id='preview_details']/div[position() > 1]/a/@data-accord-url");

        if (!$extra['images'])
        {
            if (preg_match_all('/&q;src&q;:&q;(https:\/\/[a-z0-9_\.\/]+?\.jpg)&q;,&q;width/ims', $this->dom->saveHTML(), $matches))
                $images = $matches[1];
            
            $extra['images'] = array();
            for ($i = 0; $i < count($images); $i = $i + 4)
            {
                if (substr_count($images[$i], 'https') > 1)
                    continue;
                $extra['images'][] = $images[$i];
            }
        }

        $extra['comments'] = array();
        $users = $this->xpathArray(".//*[@id='comments']//*[@itemprop='review']//*[@itemprop='author']");
        $comments = $this->xpathArray(".//*[@id='comments']//*[@itemprop='review']//*[@class='pp-review-text']");
        $ratings = $this->xpathArray(".//*[@id='comments']//*[@itemprop='review']//*[@itemprop='ratingValue']/@content");
        $dates = $this->xpathArray(".//*[@id='comments']//*[@itemprop='review']//*[@itemprop='datePublished']");

        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            $comment['comment'] = preg_replace('/\.\.\..+?Еще/', '', $comment['comment']);

            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
                $comment['rating'] = TextHelper::ratingPrepare($ratings[$i]);
            if (!empty($dates[$i]))
                $comment['date'] = strtotime($dates[$i]);
            $extra['comments'][] = $comment;
        }
        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//meta[@itemprop='ratingValue']/@content"));

        return $extra;
    }

    public function isInStock()
    {
        $availability = $this->xpathScalar(".//*[@itemprop='availability']/@href");
        if (!$availability || $availability == 'http://schema.org/OutOfStock')
            return false;
        else
            return true;
    }

}
