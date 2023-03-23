<?php

namespace Keywordrush\AffiliateEgg;

/**
 * CookiesConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class CookiesConfig extends Config {

    public function page_slug()
    {
        return 'affiliate-egg-cookies-settings';
    }

    public function option_name()
    {
        return 'affegg_cookies';
    }

    public function add_admin_menu()
    {
        \add_submenu_page('options.php', __('Custom Cookies', 'affegg') . ' &lsaquo; Affiliate Egg', __('Custom Cookies', 'affegg'), 'manage_options', $this->page_slug, array($this, 'settings_page'));
    }

    protected function options()
    {
        $options = array();
        $shops = ShopManager::getInstance()->getActiveItems(true);
        
        foreach ($shops as $id => $shop)
        {
            $name = '';
            if ($shop->ico)
            {
                $name .= '<a target="_blank" href="' . esc_attr($shop->uri) . '">';
                $name .= '<img src="' . esc_attr($shop->ico) . '" alt="' . esc_attr($shop->name) . '" />';
                $name .= '</a>&nbsp;&nbsp;';
            }
            $name .= $shop->getName();

            $option = array(
                'title' => $name,
                'description' => '',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    'allow_empty',
                ),
                'default' => '',
            );
            $options[$shop->id] = $option;
        }
        return $options;
    }

    public function settings_page()
    {
        AffiliateEggAdmin::getInstance()->render('settings', array('page_slug' => $this->page_slug()));
    }

}
