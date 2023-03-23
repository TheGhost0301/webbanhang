<?php

namespace Keywordrush\AffiliateEgg;

/*
  Name: Shopee.vn
  URI: http://shopee.vn
  Icon: http://www.google.com/s2/favicons?domain=shopee.vn
  CPA:
  SEARCH URI: https://shopee.vn/search/?keyword=%KEYWORD%
 * 
 */

/**
 * ShopeevnParser class file
 *
 * @author keywordrush.com <support@keywordrush.com> 
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class ShopeevnParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'VND';
    private $_product;

    public function parseCatalog($max)
    {
        if (!preg_match('~keyword=(.+)~', $this->getUrl(), $matches))
            return array();

        $keyword = $matches[1];
        try
        {
            $result = $this->requestGet('https://shopee.vn/api/v2/search_items/?by=relevancy&keyword=' . $keyword . '&limit=50&newest=0&official_mall=1&order=desc&page_type=search', false);
        } catch (\Exception $e)
        {
            return array();
        }
        $result = json_decode($result, true);
        if (!$result || !isset($result['items']))
            return false;
        $urls = array();
        foreach ($result['items'] as $item)
        {
            $url = 'https://shopee.vn/' . str_replace(' ', '-', $item['name']) . '-i.' . $item['shopid'] . '.' . $item['itemid'];
            $urls[] = $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        $this->_getProduct();
        if (!$this->_product)
            return;
        if (isset($this->_product['name']))
            return $this->_product['name'];
    }

    public function _getProduct()
    {
        if (!preg_match('~\-i\.(\d+\.\d+)~', $this->getUrl(), $matches))
            return false;
        $ids = $matches[1];
        $ids = explode('.', $ids);
        try
        {
            $result = $this->requestGet('https://shopee.vn/api/v1/item_detail/?item_id=' . urlencode($ids[1]) . '&shop_id=' . urlencode($ids[0]), false);
        } catch (\Exception $e)
        {
            return false;
        }
        $result = json_decode($result, true);
        if (!$result)
            return false;

        $this->_product = $result;
        return $this->_product;
    }

    public function parseDescription()
    {
        if (isset($this->_product['description']))
            return $this->_product['description'];
    }

    public function parsePrice()
    {
        if (isset($this->_product['price']))
            return $this->_product['price'] / 100000;
    }

    public function parseOldPrice()
    {

        if (isset($this->_product['price_before_discount']))
            return $this->_product['price_before_discount'] / 100000;
    }

    public function parseManufacturer()
    {
        if (isset($this->_product['brand']))
            return $this->_product['brand'];
    }

    public function parseImg()
    {
        if (isset($this->_product['image']))
            return 'https://cf.shopee.vn/file/' . $this->_product['image'];
    }

    public function parseExtra()
    {
        $extra = array();
        if (isset($this->_product['rating_star']))
            $extra['rating'] = TextHelper::ratingPrepare($this->_product['rating_star']);
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
