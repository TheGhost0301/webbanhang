<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: Nguyenkim.com
  URI: http://nguyenkim.com
  Icon: http://www.google.com/s2/favicons?domain=nguyenkim.com
  SEARCH URI: https://www.nguyenkim.com/tim-kiem.html?subcats=Y&pcode_from_q=Y&pshort=Y&pfull=Y&pname=Y&pkeywords=Y&search_performed=Y&q=%KEY+WORD%
 *  
 */

/**
 * NguyenkimcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class NguyenkimcomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@id='grid-viewmore-page' or @class='grid-list-category']//a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray("//*[contains(@class, 'nk-product-img')]/../../a/@href"), 0, $max);
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h2[@class='product_info_name']");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//*[@itemprop='description']/@content");
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//*[@class='product_infdo_price_value-final' or @class='product_info_price_value-sock-final']");
    }

    public function parseOldPrice()
    {
        $price = $this->xpathScalar(".//*[@class='strike']/*[@class='list-price nowrap']");
        if (!$price)
            $price = $this->xpathScalar(".//*[@class='product_insfo_price_value-real' or @class='product_info_price_value-sock-real']");
        return $price;
    }

    public function parseManufacturer()
    {
        return $this->xpathScalar(".//*[@itemprop='brand']/@content");
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//*[@itemprop='image']/@content");
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = array();

        $names = $this->xpathArray(".//*[@class='productSpecification_table']//td[1]");
        $values = $this->xpathArray(".//*[@class='productSpecification_table']//td[2]");
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
        
        $extra['comments'] = array();
        $comments = $this->xpathArray(".//*[@class='NkReview_body']//*[@class='post post-main']/*[@class='post_content']");
        $users = $this->xpathArray(".//*[@class='NkReview_body']//*[@class='post_info-left']/*[@class='post_author-name']");
        $dates = $this->xpathArray(".//*[@class='NkReview_body']//*[@class='post_info-left']/*[@class='post_date']");
        $ratings = $this->xpathArray(".//*[@class='NkReview_body']//*[@class='post_info-left']/*[contains(@class, 'post_stars stars-345')]");
        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($ratings[$i]))
                $comment['rating'] = TextHelper::ratingPrepare($ratings[$i]);
            if (!empty($dates[$i]))
                $comment['date'] = strtotime($dates[$i]);
            $extra['comments'][] = $comment;
        }        

        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
