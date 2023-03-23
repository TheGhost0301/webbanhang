<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AsoscomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2014 keywordrush.com
 */
class AsoscomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';	
    protected $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';
    protected $headers = array(
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-us,en;q=0.5',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    );
    protected $js_content = '';

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//div[@class='items']//li/div/a/@href"), 0, $max);
        if (!$urls)
            $urls = array_slice($this->xpathArray(".//div[contains(@class,'three-grid')]//li/a/@href"), 0, $max);

        foreach ($urls as $key => $url)
        {
            $parts = parse_url($url);
            $path = explode('/', $parts['path']);
            if (isset($path[4]) && $path[4] == 'prod')
                $path[3] = urlencode($path[3]);

            $urls[$key] = $parts['scheme'] . '://' . $parts['host'] . join('/', $path) . '?' . $parts['query'];
        }
        return $urls;
    }

    protected function parseJsContent()
    {
        $data = $this->xpathScalar(".//script[starts-with(normalize-space(text()),\"require(['Pages/FullProduct\")]");
        if (!$data)
            return false;
        if (!preg_match('/view\((.+?)\);/', $data, $matches))
            return false;
        $data = trim($matches[1], "\'");
        $data = trim(preg_replace('/\s+/', ' ', $data));

        $this->js_content = $data;
    }

    public function parseTitle()
    {
        $this->parseJsContent();
        if ($this->js_content)
        {
            if (preg_match('/"name":"(.+?)"/', $this->js_content, $matches))
                return $matches[1];
        }

        $title = trim($this->xpathScalar(".//*[@id='aside-content']//h1"));
        $brand = $this->parseManufacturer();
        $title = str_replace($brand, '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        return $title;
    }

    public function parseDescription()
    {
        $res = $this->xpathScalar(".//div[@class='about-me']");
        $res = str_replace('ABOUT ME', '', $res);
        return $res;
    }

    public function parsePrice()
    {
        if (preg_match('/"price":{"current":(.+?),/', $this->js_content, $matches))
            return $matches[1];
    }

    public function parseOldPrice()
    {
        if (preg_match('/,"previous":(.+?),/', $this->js_content, $matches))
            return $matches[1];
    }

    public function parseManufacturer()
    {

        if (preg_match('/"brandName":"(.+?)"/', $this->js_content, $matches))
            return $matches[1];
    }

    public function parseImg()
    {
        if (preg_match('/"images":\[{"productId":\d+,"url":"(.+?)",/', $this->js_content, $matches))
            return $matches[1];
    }

    public function parseImgLarge()
    {
        if ($this->item['orig_img'])
            return $this->item['orig_img'] . '?$XXL$&wid=513&fit=constrain';
    }

    public function parseExtra()
    {
        
    }

    public function isInStock()
    {
        $res = $this->xpath->evaluate("boolean(.//div[@class='outofstock'])");
        return ($res) ? false : true;
    }

    public function getCurrency()
    {
        if (preg_match('/,"currency":"(\w+?)"/', $this->js_content, $matches))
            return $matches[1];
        else
            return $this->currency;
    }

}
