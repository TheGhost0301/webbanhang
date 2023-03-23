<?php

namespace Keywordrush\AffiliateEgg;

/**
 * TigerfitnesscomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
class TigerfitnesscomParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'USD';

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//a[@class='productitem--image-link']/@href");
    }

    public function restPostGet($url, $fix_encoding = true)
    {
        $body = parent::restPostGet($url, false);
        // fix wrong LD description
        $body = preg_replace('/"description": ".+?",/', '"description": "",', $body);
        return $this->decodeCharset($body, $fix_encoding);
    }

    public function parseLdJson()
    {
        if ($p = parent::parseLdJson())
            return $p;
        
        $lds = $this->xpathArray(".//script[@type='application/ld+json']", true);
        foreach ($lds as $ld)
        {
            $ld = TextHelper::fixHiddenCharacters($ld);
            $ld = preg_replace('/\/\*.+?\*\//', '', $ld);
            $ld = trim($ld, ";");
            
            //fix
            $ld = preg_replace('/\},\s+\}$/ims', '} }', $ld);
            
            if (!$data = json_decode($ld, true))
                continue;
            
            if (isset($data['mainEntity']))
                $data = $data['mainEntity'];

            if (isset($data['@graph']))
            {
                foreach ($data['@graph'] as $d)
                {
                    if (isset($d['@type']) && (in_array($d['@type'], $this->product_types) || in_array(ucfirst(strtolower($d['@type'])), $this->product_types)))
                        $data = $d;
                }
            } elseif (isset($data[0]) && isset($data[1]))
            {
                foreach ($data as $d)
                {
                    if (isset($d['@type']) && (in_array($d['@type'], $this->product_types) || in_array(ucfirst(strtolower($d['@type'])), $this->product_types)))
                        $data = $d;
                }
            } elseif (isset($data[0]))
                $data = $data[0];

            if (isset($data['@type']) && (in_array($data['@type'], $this->product_types) || in_array(ucfirst(strtolower($data['@type'])), $this->product_types)))
            {
                $this->ld_json = $data;
                return $this->ld_json;
            }
        }
        return false;
    }

}
