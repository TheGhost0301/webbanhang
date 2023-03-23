<?php

namespace Keywordrush\AffiliateEgg;

/**
 * AmazonfrParser class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
require_once dirname(__FILE__) . '/AmazoncomParser.php';

class AmazonfrParser extends AmazoncomParser {
    protected $canonical_domain = 'https://www.amazon.fr';
    protected $currency = 'EUR';
    
    

}
