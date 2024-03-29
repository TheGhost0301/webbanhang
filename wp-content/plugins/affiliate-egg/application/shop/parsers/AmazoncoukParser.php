<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AmazoncoukParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
require_once dirname(__FILE__) . '/AmazoncomParser.php';

class AmazoncoukParser extends AmazoncomParser {

    protected $canonical_domain = 'https://www.amazon.co.uk';
    protected $user_agent = array('DuckDuckBot', 'facebot', 'ia_archiver');
    protected $currency = 'GBP';
    
}
