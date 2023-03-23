<?php

namespace Keywordrush\AffiliateEgg;

/**
 * TatacliqcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class TatacliqcomParser extends ShopParser {

    protected $currency = 'INR';
    private $_product;

    public function parseCatalog($max)
    {
        // search page
        if (!$query = parse_url($this->getUrl(), PHP_URL_QUERY))
            return array();
        parse_str($query, $arr);
        if (!isset($arr['text']))
            return array();
        $keyword = $arr['text'];
        try
        {
            $result = $this->requestGet('https://www.tataque.com/marketplacewebservices/v2/mpl/products/searchProducts/?searchText=' . urlencode($keyword) . '&isTextSearch=false&isFilter=false&page=0&isPwa=true&pageSize=40&typeID=all', false);
        } catch (\Exception $e)
        {
            return array();
        }
        $result = json_decode($result, true);
        if (!$result || !isset($result['searchresult']))
            return array();
        $urls = array();
        foreach ($result['searchresult'] as $item)
        {
            if (isset($item['webURL']))
                $urls[] = $item['webURL'];
        }
        return $urls;
    }

    public function parseTitle()
    {
        $this->_getProduct();
        if (!$this->_product)
            return;
        if (isset($this->_product['productName']))
            return $this->_product['productName'];
    }

    public function _getProduct()
    {
        if (!preg_match('~/p-(mp\d+)~i', $this->getUrl(), $matches))
            return false;
        $id = strtoupper($matches[1]);
        try
        {
            $result = $this->requestGet('https://www.tataque.com/marketplacewebservices/v2/mpl/products/productDetails/' . urlencode($id) . '?isPwa=true', false);
        } catch (\Exception $e)
        {
            return false;
        }
        $result = json_decode($result, true);
        if (!$result || !isset($result['status']) || $result['status'] != 'SUCCESS')
            return false;

        $this->_product = $result;
        return $this->_product;
    }

    public function parseDescription()
    {
        if (isset($this->_product['productDescription']))
            return $this->_product['productDescription'];
    }

    public function parsePrice()
    {
        if (isset($this->_product['winningSellerPrice']['doubleValue']))
            return $this->_product['winningSellerPrice']['doubleValue'];
    }

    public function parseOldPrice()
    {

        if (isset($this->_product['mrpPrice']['doubleValue']))
            return $this->_product['mrpPrice']['doubleValue'];
    }

    public function parseManufacturer()
    {
        if (isset($this->_product['brandName']))
            return $this->_product['brandName'];
    }

    public function parseImg()
    {
        if (isset($this->_product['galleryImagesList'][0]['galleryImages']))
            return $this->_product['galleryImagesList'][0]['galleryImages'][0]['value'];
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['features'] = array();
        if (isset($this->_product['classifications']))
        {
            $feature = array();
            foreach ($this->_product['classifications'] as $c)
            {
                if ($c['groupName'] == 'Warranty')
                    continue;
                foreach ($c['specifications'] as $s)
                {

                    $feature['name'] = \sanitize_text_field($s['key']);
                    $feature['value'] = \sanitize_text_field($s['value']);
                    $extra['features'][] = $feature;
                }
            }
        }
        return $extra;
    }

}
