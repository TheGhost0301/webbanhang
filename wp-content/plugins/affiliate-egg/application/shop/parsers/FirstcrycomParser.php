<?php

namespace Keywordrush\AffiliateEgg;

/**
 * FirstcrycomParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class FirstcrycomParser extends ShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'INR';
    protected $json_data = array();

    public function parseCatalog($max)
    {
        $urls = array_slice($this->xpathArray(".//*[@id='maindiv']//div[@class='list_img wifi']/a/@href"), 0, $max);
        foreach ($urls as $key => $url)
        {
            if (!preg_match('/^https?:/', $url))
                $urls[$key] = 'http://www.firstcry.com' . $url;
        }
        return $urls;
    }

    public function parseTitle()
    {
        return $this->xpathScalar(".//h1[@itemprop='name']");
    }

    public function parseDescription()
    {
        return $this->xpathScalar(".//div[@itemprop='description']");
    }

    public function parsePrice()
    {
        return $this->xpathScalar(".//*[@itemprop='price']");
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(".//*[contains(@class, 'p_price_save')][1]");
    }

    public function parseManufacturer()
    {
        
    }

    public function parseImg()
    {
		if ($img = $this->xpathScalar(".//meta[@property='og:image']/@content"))
			$img = str_replace('/165x200/', '/438x531/', $img);
	
		if (!$img)
			$img = $this->xpathScalar(".//*[@id='inZoom']/img/@src");
			
        if (!preg_match('/^https?:/', $img))
            $img = 'https:' . $img;
        return $img;
		
    }

    public function parseImgLarge()
    {
        return str_replace('/438x531/', '/zoom/', $this->parseImg());
    }

    public function parseExtra()
    {
        $extra = array();

        $extra['images'] = array();
        $images = $this->xpathArray(".//*[@class='thumb-ctr']//span[position() > 1]/img/@src");
        foreach ($images as $img)
        {
            $extra['images'][] = str_replace('/80x95/', '/438x531/', $img);
        }

        $extra['rating'] = TextHelper::ratingPrepare($this->xpathScalar(".//*[@itemprop='ratingValue']"));

        $extra['comments'] = array();
        $users = $this->xpathArray(".//*[@id='reviewdatadiv']//*[contains(@class, 'p_c_name')]");
        $comments = $this->xpathArray(".//*[@id='reviewdatadiv']//*[contains(@class, 'p_c_title_des')]");
        $ratings = $this->xpathArray(".//*[@id='reviewdatadiv']//*[contains(@class, 'small_st1')]/@style");
        $dates = $this->xpathArray(".//*[@id='reviewdatadiv']//*[contains(@class, 'p_c_cmt_date')]");
        for ($i = 0; $i < count($comments); $i++)
        {
            $comment['comment'] = sanitize_text_field($comments[$i]);
            if (!empty($users[$i]))
                $comment['name'] = sanitize_text_field($users[$i]);
            if (!empty($dates[$i]))
                $comment['date'] = strtotime($dates[$i]);
            if (!empty($ratings[$i]))
            {
                $rating_parts = explode(':', $ratings[$i]);
                if (count($rating_parts) == 2)
                {
                    $comment['rating'] = TextHelper::ratingPrepare((int) $rating_parts[1] / 12);
                }
            }
            $extra['comments'][] = $comment;
        }
        return $extra;
    }

    public function isInStock()
    {
        return true;
    }

}
