<?php

namespace Keywordrush\AffiliateEgg;

/**
 * TargetcomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com> 
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class TargetcomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';
    protected $user_agent = 'wget';    
    private $_product;
    private $_id;

    public function restPostGet($url, $fix_encoding = true)
    {
        if (preg_match('/A\-(\d+)/', $url))
            return '<html></html>';
        else
            return parent::restPostGet($url, $fix_encoding);
    }

    public function parseCatalog($max)
    {
        return array();
    }

    public function parseTitle()
    {
        $this->_getProduct();
        if (!$this->_product)
            return;

        if (isset($this->_product['item']['product_description']['title']))
            return $this->_product['item']['product_description']['title'];
    }

    public function _getProduct()
    {
        if (!preg_match('/A\-(\d+)/', $this->getUrl(), $matches))
            return false;
        $id = $matches[1];
        $this->_id = $id;
        try
        {
            $result = $this->requestGet('https://redsky.target.com/v2/pdp/tcin/' . urlencode($id) . '?excludes=taxonomy', false);
        } catch (\Exception $e)
        {
            return false;
        }
        $result = json_decode($result, true);
        if (!$result || !isset($result['product']))
            return false;

        $this->_product = $result['product'];
        return $this->_product;
    }

    public function parseDescription()
    {
        if (isset($this->_product['item']['product_description']['downstream_description']))
            return $this->_product['item']['product_description']['downstream_description'];
    }

    public function parsePrice()
    {
        if (!empty($this->_product['price']['offerPrice']['price']))
            return $this->_product['price']['offerPrice']['price'];
        elseif (isset($this->_product['price']['offerPrice']['formattedPrice']))
            return $this->_product['price']['offerPrice']['formattedPrice'];
    }

    public function parseOldPrice()
    {
        if (!empty($this->_product['price']['listPrice']['price']))
            return $this->_product['price']['listPrice']['price'];
        elseif (isset($this->_product['price']['listPrice']['formattedPrice']))
            return $this->_product['price']['listPrice']['formattedPrice'];
    }

    public function parseManufacturer()
    {
        if (isset($this->_product['item']['product_brand']['brand']))
            return $this->_product['item']['product_brand']['brand'];
    }

    public function parseImg()
    {
        if (isset($this->_product['item']['enrichment']['images'][0]['base_url']))
            return $this->_product['item']['enrichment']['images'][0]['base_url'] . $this->_product['item']['enrichment']['images'][0]['primary'] . '?wid=488&hei=488&fmt=pjpeg';
    }

    public function parseExtra()
    {
        $extra = array();
        if (isset($this->_product['rating_and_review_statistics']['result'][$this->_id]['coreStats']['AverageOverallRating']))
            $extra['rating'] = TextHelper::ratingPrepare($this->_product['rating_and_review_statistics']['result'][$this->_id]['coreStats']['AverageOverallRating']);
        if (isset($this->_product['rating_and_review_statistics']['result'][$this->_id]['coreStats']['TotalReviewCount']))
            $extra['ratingCount'] = (int) $this->_product['rating_and_review_statistics']['result'][$this->_id]['coreStats']['TotalReviewCount'];

        if (isset($this->_product['item']['product_description']['bullet_description']))
        {
            $extra['features'] = array();
            foreach ($this->_product['item']['product_description']['bullet_description'] as $f)
            {
                $f = strip_tags($f);
                $f_parts = explode(':', $f);
                if (count($f_parts == 2))
                {
                    $feature = array();
                    $feature['name'] = \sanitize_text_field(trim($f_parts[0]));
                    $feature['value'] = \sanitize_text_field(trim($f_parts[1]));
                    $extra['features'][] = $feature;
                }
            }
        }
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
