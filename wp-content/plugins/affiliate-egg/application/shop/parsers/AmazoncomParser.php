<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AmazoncomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class AmazoncomParser extends ShopParser {

    protected $canonical_domain = 'https://www.amazon.com';
    protected $charset = 'utf-8';
    protected $currency = 'USD';
    //protected $user_agent = array('DuckDuckBot', 'facebot', 'ia_archiver');
    protected $user_agent = array('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:70.0) Gecko/20100101 Firefox/70.0');
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );

    public function restPostGet($url, $fix_encoding = true)
    {
        \add_action('http_api_curl', array(__CLASS__, '_setCurlOptions'), 10, 3);

        $body = parent::restPostGet($url, false);
        // fix
        $body = preg_replace('/<table id="HLCXComparisonTable".+?<\/table>/uims', '', $body);
        return $this->decodeCharset($body, $fix_encoding);
    }

    static public function _setCurlOptions($handle, $r, $url)
    {
        curl_setopt($handle, CURLOPT_ENCODING, '');
    }

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@class='aok-inline-block zg-item']/a[@class='a-link-normal']/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//h3[@class='newaps']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[@id='resultsCol']//a[contains(@class,'s-access-detail-page')]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[@class='zg_title']/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[@id='rightResultsATF']//a[contains(@class,'s-access-detail-page')]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[@id='atfResults']/ul//li//div[contains(@class,'a-column')]/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[@id='mainResults']//li//a[@title]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@id='zg_centerListWrapper']//a[@class='a-link-normal' and not(@title)]/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//h5/a[@class='a-link-normal a-text-normal']/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//*[@data-component-type='s-product-image']//a[@class='a-link-normal']/@href"), 0, $max);
        // Today's Deals
        if (!$urls)
            $urls = $this->parseGoldBoxDeals();

        if (!$urls)
            return array();

        foreach ($urls as $i => $url)
        {
            if (!preg_match('/^https?:\/\//', $url))
                $urls[$i] = $this->canonical_domain . $url;
        }

        // picassoRedirect fix
        foreach ($urls as $i => $url)
        {
            if (!strstr($url, '/gp/slredirect/picassoRedirect.html/'))
                continue;
            $parts = parse_url($url);
            if (empty($parts['query']))
                continue;
            parse_str($parts['query'], $output);
            if (empty($output['url']))
                continue;

            $urls[$i] = $output['url'];
        }
        $url_parts = parse_url($urls[0]);

        // fix urls. prevent duplicates for autobloging
        foreach ($urls as $key => $url)
        {
            $asin = self::parseAsinFromUrl($url);
            if (!$asin)
                continue;

            // build url
            $urls[$key] = $url_parts['scheme'] . '://' . $url_parts['host'] . '/dp/' . $asin . '/';
        }
        return $urls;
    }

    protected function parseGoldBoxDeals()
    {

        if (!strstr($this->getUrl(), 'amazon.com/gp/goldbox'))
            return array();

        $request_url = 'https://www.amazon.com/xa/dealcontent/v2/GetDeals?nocache=' . time();
        $response = \wp_remote_post($request_url, array(
            'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
            'body' => '{"requestMetadata":{"marketplaceID":"ATVPDKIKX0DER","clientID":"goldbox_mobile_pc","sessionID":"147-0111701-3832735"},"dealTargets":[{"dealID":"27929040"},{"dealID":"2dfcb07b"},{"dealID":"6727cdb5"},{"dealID":"676b0c2d"},{"dealID":"7aeb0274"},{"dealID":"7ca1692e"},{"dealID":"a6614039"},{"dealID":"af1e3631"},{"dealID":"b3db4ae7"},{"dealID":"e2b741c7"},{"dealID":"eb7ca674"},{"dealID":"f5a1f5c0"}],"responseSize":"ALL","itemResponseSize":"DEFAULT_WITH_PREEMPTIVE_LEAKING","widgetContext":{"pageType":"Landing","subPageType":"hybrid-batch-btf","deviceType":"pc","refRID":"KH2KVAGJESZ5EF3NCNGD","widgetID":"f3f63185-46c5-4297-bc5f-35921b3fb364","slotName":"merchandised-search-8"},"customerContext":{"languageOfPreference":"en_US"}}',
            'method' => 'POST'
        ));

        if (\is_wp_error($response) || !$body = \wp_remote_retrieve_body($response))
            return array();
        $js_data = json_decode($body, true);

        if (!$js_data || !isset($js_data['dealDetails']))
            return array();

        $urls = array();
        foreach ($js_data['dealDetails'] as $hit)
        {
            if (strstr($hit['egressUrl'], '/dp/'))
                $urls[] = $hit['egressUrl'];
        }
        return $urls;
    }

    public function parseTitle()
    {
        $title = $this->xpathScalar(".//h1[@id='title']/span");
        if (!$title)
            $title = $this->xpathScalar(".//*[@id='fine-ART-ProductLabelArtistNameLink']");
        if (!$title)
            $title = $this->xpathScalar(".//h1");
        return $title;
    }

    public function parseDescription()
    {
        $descr = '';
        $results = $this->xpathScalar(".//script[contains(.,'iframeContent')]");
        preg_match('/iframeContent\s=\s"(.+?)"/msi', $results, $match);
        if (isset($match[1]))
        {
            $res = urldecode($match[1]);
            preg_match('/class="productDescriptionWrapper">(.+?)</msi', $res, $match);
            if (isset($match[1]))
                $descr = trim($match[1]);
        }
        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='bookDescription_feature_div']/noscript/div");

        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='productDescription']//*[@class='productDescriptionWrapper']");
        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='productDescription']/p/*[@class='btext']");
        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='productDescription']/p");
        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='bookDescription_feature_div']/noscript");
        if (!$descr)
            $descr = $this->xpathScalar(".//*[@class='dv-simple-synopsis dv-extender']");

        if (!$descr)
            $descr = $this->xpathScalar(".//*[@id='bookDescription_feature_div']//noscript/div");

        if (!$descr)
        {
            $results = $this->xpathArray(".//div[@id='featurebullets_feature_div']//span[@class='a-list-item']");
            $descr = implode(";\n", $results);
        }
        if (!$descr)
        {
            if (preg_match('/bookDescEncodedData = "(.+?)",/', $this->dom->saveHTML(), $matches))
                $descr = html_entity_decode(urldecode($matches[1]));
        }
        if (!$descr)
        {
            $results = $this->xpathArray(".//div[@id='featurebullets_feature_div']//li");
            $descr = implode(";\n", $results);
        }

        return $descr;
    }

    public function parsePrice()
    {
        $price = $this->xpathScalar(".//*[@id='priceblock_dealprice']");
        if (!$price && $price = $this->xpathScalar(".//span[@id='priceblock_ourprice']//*[@class='buyingPrice' or @class='price-large']"))
        {
            $cent = $this->xpathScalar(".//span[@id='priceblock_ourprice']//*[@class='verticalAlign a-size-large priceToPayPadding' or @class='a-size-small price-info-superscript']");
            if ($cent)
                $price = $price . '.' . $cent;
        }


        if (!$price)
            $price = $this->xpathScalar(".//span[@id='priceblock_ourprice']");
        if (!$price)
            $price = $this->xpathScalar(".//span[@id='priceblock_saleprice']");
        if (!$price)
            $price = $this->xpathScalar(".//input[@name='displayedPrice']/@value");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='buyNewSection']//*[contains(@class, 'offer-price')]");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='unqualifiedBuyBox']//*[@class='a-color-price']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@class='dv-button-text']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='cerberus-data-metrics']/@data-asin-price");
        if (!$price)
            $price = $this->xpathScalar(".//*[@class='a-price']/*[@class='a-offscreen']");

        if (strstr($price, ' - '))
        {
            $tprice = explode('-', $price);
            $price = $tprice[0];
        }

        $price = preg_replace('/[^0-9\.,]/', '', $price);
        return $price;
    }

    public function parseOldPrice()
    {
        // Gold Box Deals?
        $price = $this->xpathScalar(".//*[@id='price']//span[@class='a-text-strike']");
        if (!$price)
            $price = $this->xpathScalar(".//div[@id='price']//td[contains(@class,'a-text-strike')]");
        if (!$price)
            $price = $this->xpathScalar("(.//*[@id='price']//span[@class='a-text-strike'])[2]");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='buyBoxInner']//*[contains(@class, 'a-text-strike')]");
        if (!$price)
            $price = $this->xpathScalar(".//*[@id='price']//span[contains(@class, 'priceBlockStrikePriceString')]");

        $price = preg_replace('/[^0-9\.,]/', '', $price);
        return $price;
    }

    public function parseManufacturer()
    {
        $brand = $this->xpathScalar(".//a[@id='brand']");
        if (!$brand)
            $brand = $this->xpathScalar(".//*[@id='byline']//*[contains(@class, 'contributorNameID')]");
        return $brand;
    }

    public function parseImg()
    {
        $img = $this->xpathScalar(".//img[@id='miniATF_image']/@src");
        if (!$img)
        {
            $img = $this->xpathScalar(".//img[@id='landingImage']/@data-old-hires");
            $img = str_replace('._SL1200_.jpg', '._SY300_.jpg', $img);
        }
        if (!$img)
        {
            $img = $this->xpathScalar(".//img[@id='landingImage']/@data-a-dynamic-image");
            if (preg_match('/(https?:\/.+?)"/', $img, $matches))
                return $this->rewriteSslImageUrl($matches[1]);
        }

        if (!$img)
        {
            $img = $this->xpathScalar(".//img[@id='landingImage']/@data-a-dynamic-image");
            $img = str_replace('{"', '', $img);
            $img = str_replace('":[350,350]}', '', $img);
        }

        if (!$img)
            $img = $this->xpathScalar(".//img[@id='landingImage']/@src");
        if (!$img)
        {
            $img = $this->xpathScalar(".//img[@id='imgBlkFront']/@src");
            if (preg_match('/^data:image/', $img))
                $img = '';
        }
        if (!$img)
            $img = $this->xpathScalar(".//img[@id='ebooksImgBlkFront']/@src");
        if (!$img)
            $img = $this->xpathScalar(".//*[@id='fine-art-landingImage']/@src");
        if (!$img)
            $img = $this->xpathScalar(".//*[@class='dv-dp-packshot js-hide-on-play-left']//img/@src");
        if (!$img)
        {
            $img = $this->xpathScalar(".//*[contains(@class, 'imageThumb thumb')]/img/@src");
            $img = preg_replace('/\._.+?\_.jpg/', '.jpg', $img);
        }
        if (!$img)
            $img = $this->xpathScalar(".//*[@id='main-image']/@src");

        $img = str_replace('._SL1500_.', '._SY300_.', $img);
        $img = str_replace('._SL1200_.', '._SY300_.', $img);
        $img = str_replace('._SL1000_.', '._SY300_.', $img);
        return $this->rewriteSslImageUrl($img);
    }

    public function parseImgLarge()
    {
        // return str_replace('._SY300_.jpg', '.jpg', $this->parseImg());
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[contains(@data-hook, 'review-body')]//div[@data-hook]");
        if ($comments)
        {
            $users = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@class='a-profile-name']");
            $dates = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@data-hook='review-date']");
            $ratings = $this->xpathArray(".//*[contains(@class, 'reviews-content')]//*[@data-hook='review-star-rating']");
        } else
        {

            $comments = $this->xpathArray(".//*[@id='revMH']//*[contains(@id, 'revData-dpReviewsMostHelpful')]/div[@class='a-section']");
            $users = $this->xpathArray(".//*[@id='revMH']//a[@class='noTextDecoration']");
            $dates = $this->xpathArray(".//*[@id='revMH']//span[@class='a-color-secondary']/span[@class='a-color-secondary']");
            $ratings = $this->xpathArray(".//*[@id='revMH']//span[@class='a-icon-alt']");
        }

        for ($i = 0; $i < count($comments); $i++)
        {
            if (isset($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            $date = str_replace("on ", "", $dates[$i]);
            $comment['date'] = strtotime($date);
            $comment['comment'] = sanitize_text_field($comments[$i]);
            $comment['comment'] = preg_replace('/Read\smore$/', '', $comment['comment']);
            if (isset($ratings[$i]))
                $comment['rating'] = $this->prepareRating($ratings[$i]);
            $extra['comments'][] = $comment;
        }
        preg_match("/\/dp\/(.+?)\//msi", $this->getUrl(), $match);
        $extra['item_id'] = isset($match[1]) ? $match[1] : '';

        $extra['features'] = array();
        $names = $this->xpathArray(".//*[@id='productDetails_techSpec_section_1']//th");
        $values = $this->xpathArray(".//*[@id='productDetails_techSpec_section_1']//td");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && $names[$i] != 'Condition:' && $names[$i] != 'Brand:')
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        $features2 = $this->xpathArray(".//*[@id='productDetailsTable']//li[not(@id) and not(@class)]");
        for ($i = 0; $i < count($features2); $i++)
        {
            $parts = explode(':', $features2[$i]);
            if (count($parts) != 2)
                continue;
            $feature['name'] = sanitize_text_field($parts[0]);
            $feature['value'] = sanitize_text_field($parts[1]);
            $feature['features'][] = $feature;
        }

        if (!$extra['features'])
        {
            $extra['features'] = array();
            $names = $this->xpathArray(".//*[contains(@id, 'technicalSpecifications_section')]//th");
            $values = $this->xpathArray(".//*[contains(@id, 'technicalSpecifications_section')]//td");
            $feature = array();
            for ($i = 0; $i < count($names); $i++)
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        if (!$extra['features'])
        {
            $extra['features'] = array();
            $names = $this->xpathArray(".//*[@id='prodDetails']//td[@class='label']");
            $values = $this->xpathArray(".//*[@id='prodDetails']//td[@class='value']");
            $feature = array();
            for ($i = 0; $i < count($names); $i++)
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }
        if (!$extra['features'])
        {
            $extra['features'] = array();
            $names = $this->xpathArray(".//*[contains(@id, 'technicalSpecifications_section')]//th");
            $values = $this->xpathArray(".//*[contains(@id, 'technicalSpecifications_section')]//td");
            $feature = array();
            for ($i = 0; $i < count($names); $i++)
            {
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = sanitize_text_field($values[$i]);
                $extra['features'][] = $feature;
            }
        }

        if (!$extra['features'])
        {
            $results = $this->xpathArray(".//div[@id='technical-data']//li");
            if ($results)
            {
                foreach ($results as $res)
                {
                    $expl = explode(":", $res, 2);
                    if (count($expl) == 2)
                    {
                        $feature['name'] = sanitize_text_field($expl[0]);
                        $feature['value'] = sanitize_text_field($expl[1]);
                        $extra['features'][] = $feature;
                    }
                }
            }
        }

        if (!$extra['features'])
        {
            $results = $this->xpathArray(".//div[@id='detail-bullets']//li");
            if ($results)
            {
                foreach ($results as $res)
                {
                    $expl = explode(":", $res, 2);
                    if (count($expl) == 2 && $expl[0] != 'Amazon Best Sellers Rank' && $expl[0] != 'Average Customer Review' && $expl[0] != 'ASIN')
                    {
                        $feature['name'] = sanitize_text_field($expl[0]);
                        $feature['value'] = sanitize_text_field($expl[1]);
                        $extra['features'][] = $feature;
                    }
                }
            }
        }

        $extra['images'] = array();
        $results = $this->xpathArray(".//div[@id='altImages']//ul/li//span[contains(@data-thumb-action, 'image')]//img/@src");
        foreach ($results as $i => $res)
        {
            if ($i == 0)
                continue;
            if ($res)
            {
                $res = preg_replace('/,\d+_\.jpg/', '.jpg', $res);
                $res = preg_replace('/\._.+?_\.jpg/', '.jpg', $res);
                $extra['images'][] = $this->rewriteSslImageUrl($res);
            }
        }

        $extra['rating'] = $this->prepareRating($this->xpathScalar(".//*[@id='summaryStars']//i/span"));
        if (!$extra['rating'])
            $extra['rating'] = $this->prepareRating((float) $this->xpathScalar(".//*[@id='acrPopover']//i[contains(@class, 'a-icon a-icon-star')]"));

        $extra['ratingDecimal'] = (float) $this->xpathScalar(".//*[@id='acrPopover']//i[contains(@class, 'a-icon a-icon-star')]");

        $extra['ratingCount'] = (int) str_replace(',', '', $this->xpathScalar(".//*[@id='acrCustomerReviewText']"));
        $extra['reviewUrl'] = '';
        if ($asin = self::parseAsinFromUrl($this->getUrl()))
        {
            $url_parts = parse_url($this->getUrl());
            $extra['reviewUrl'] = $url_parts['scheme'] . '://' . $url_parts['host'] . '/product-reviews/' . $asin . '/';
        }
        return $extra;
    }

    public function isInStock()
    {
        if ($this->xpathScalar(".//div[@id='availability']/span[contains(@class,'a-color-success')]"))
            return true;

        $availability = trim($this->xpathScalar(".//div[@id='availability']/span"));
        if ($availability == 'Currently unavailable.')
            return false;

        return true;
    }

    private function prepareRating($rating_str)
    {
        $rating_parts = explode(' ', $rating_str);
        return TextHelper::ratingPrepare($rating_parts[0]);
    }

    private function rewriteSslImageUrl($img)
    {
        return str_replace('http://ecx.images-amazon.com', 'https://images-na.ssl-images-amazon.com', $img);
    }

    public static function parseAsinFromUrl($url)
    {
        $regex = '~(?:www\.)?ama?zo?n\.(?:com|ca|co\.uk|co\.jp|de|fr|in|it|es|com\.mx)/(?:exec/obidos/ASIN/|o/|gp/product/|(?:(?:[^"\'/]*)/)?dp/|)(B[A-Z0-9]{9})(?:(?:/|\?|\#)(?:[^"\'\s]*))?~isx';
        if (preg_match($regex, $url, $matches))
            return $matches[1];
        else
            return '';
    }

}
