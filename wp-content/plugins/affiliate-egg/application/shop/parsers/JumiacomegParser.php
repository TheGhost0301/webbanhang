<?php

namespace Keywordrush\AffiliateEgg;

/**
 * JumiacomegParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2018 keywordrush.com
 */
class JumiacomegParser extends LdShopParser {

    protected $charset = 'utf-8';
    protected $currency = 'EGP';

    /**
     * Site scaping is permited IF the user-agent is clearly identify it as a bot and
     * the bot owner and is using less than 200 request per minute
     */
    protected function getUserAgent()
    {
        return 'Mozilla/5.0 (compatible; AEBot; +' . \get_home_url() . ')';
    }

    public function parseCatalog($max)
    {
        return $this->xpathArray(".//a[@class='link']/@href");
    }

    public function parseTitle()
    {
        if ($t = parent::parseTitle())
            return $t;
        else
            return $this->xpathScalar(".//title"); //for outofstock products
    }

    public function parsePrice()
    {
        if ($p = $this->xpathScalar(array("(.//*[@class='price-box']//*/@data-price)[1]", ".//span[@class='-b -ltr -tal -fs24']")))
            return $p;
        else
            return parent::parsePrice();
    }

    public function parseOldPrice()
    {
        return $this->xpathScalar(array(".//*[@class='price-box']//*/@data-price)[2]", ".//*[@class='row card _no-g -fh -pas']//span[@data-price-old]"));
    }

    public function parseImg()
    {
        return $this->xpathScalar(".//meta[@property='og:image']/@content");
    }

    public function parseImgLarge()
    {
        
    }

    public function parseExtra()
    {
        $extra = parent::parseExtra();
        $names = $this->xpathArray(".//*[@id='product-details']//*[contains(@class, 'osh-row')]/div[1]");
        $values = $this->xpathArray(".//*[@id='product-details']//*[contains(@class, 'osh-row')]/div[2]");
        $feature = array();
        for ($i = 0; $i < count($names); $i++)
        {
            if (!empty($values[$i]) && !empty($names[$i]))
            {
                $value = \sanitize_text_field($values[$i]);
                if (!$value || $value == '-')
                    continue;
                $feature['name'] = sanitize_text_field($names[$i]);
                $feature['value'] = $value;
                $extra['features'][] = $feature;
            }
        }

        $extra['comments'] = array();
        $users = $this->xpathArray(".//div[@class='reviews']//address[@class='author word-wrap']");
        $comments = $this->xpathArray(".//div[@class='reviews']//div[@class='title']");
        $comments2 = $this->xpathArray(".//div[@class='reviews']//div[@class='detail truncate-txt']/text()");
        $comments2 = array_values(array_filter($comments2));
        $ratings = $this->xpathArray(".//div[@class='reviews']//*[@class='stars']/@style");

        for ($i = 0; $i < count($comments); $i++)
        {
            $comment = array();
            $comment['comment'] = \sanitize_text_field($comments[$i]);
            if (!empty($comments2[$i]))
                $comment['comment'] .= '. ' . \sanitize_text_field($comments2[$i]);
            if (!empty($users[$i]))
                $comment['name'] = \sanitize_text_field($users[$i]);

            if (!empty($ratings[$i]))
            {
                $rating_parts = explode(':', $ratings[$i]);
                if (count($rating_parts) == 2)
                    $comment['rating'] = TextHelper::ratingPrepare((int) $rating_parts[1] / 20);
            }
            $extra['comments'][] = $comment;
        }

        return $extra;
    }

    public function isInStock()
    {
        // redirect for index page for outofstock products
        //if ($this->xpathScalar(".//meta[@property='og:image']/@content") == 'https://eg.jumia.is/cms/Banners/jumia-image-logo.png')
        if (!$this->parsePrice())
            return false;
        else
            return parent::isInStock();
    }

}
