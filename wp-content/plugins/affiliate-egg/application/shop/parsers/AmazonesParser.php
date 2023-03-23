<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AmazonesParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
require_once dirname(__FILE__) . '/AmazoncomParser.php';

class AmazonesParser extends AmazoncomParser {
    protected $canonical_domain = 'https://www.amazon.es';
    protected $currency = 'EUR';
    protected $user_agent = array('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36');

}
