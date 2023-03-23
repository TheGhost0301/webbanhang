<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('GSPB_GreenShift_Settings')) {

	class GSPB_GreenShift_Settings
	{
		private $allowed_font_ext = [
			'woff2',
			'woff',
			'tiff',
			'ttf',
		];

		public function __construct()
		{
			add_action('admin_menu', array($this, 'greenshift_admin_page'));
			if (!defined('REHUB_ADMIN_DIR')) {
				//Show Reusable blocks column
				add_action('registered_post_type', array($this, 'gspb_template_menu_display'), 10, 2);
				add_filter('manage_wp_block_posts_columns', array($this, 'gspb_template_screen_add_column'));
				add_action('manage_wp_block_posts_custom_column', array($this, 'gspb_template_screen_fill_column'), 1000, 2);
				// Force Block editor for Reusable Blocks even when Classic editor plugin is activated
				add_filter('use_block_editor_for_post', array($this, 'gspb_template_gutenberg_post'), 1000, 2);
				add_filter('use_block_editor_for_post_type', array($this, 'gspb_template_gutenberg_post_type'), 1000, 2);
				//Shortcode output for reusable blocks
				add_shortcode('wp_reusable_render', array($this, 'gspb_template_shortcode_function'));
				//Ajax render action
				add_action('wp_ajax_gspb_el_reusable_load', array($this, 'gspb_el_reusable_load'));
				add_action('wp_ajax_nopriv_gspb_el_reusable_load', array($this, 'gspb_el_reusable_load'));
				//settings fonts actions
				add_action('wp_ajax_gspb_settings_add_font', array($this, 'gspb_settings_add_font'));
			}
		}

		public function greenshift_admin_page()
		{

			$parent_slug = 'greenshift_dashboard';

			add_menu_page(
				'GreenShift',
				'GreenShift',
				'manage_options',
				$parent_slug,
				array($this, 'welcome_page'),
				plugin_dir_url(__FILE__) . 'libs/gspbLogo.svg',
				20
			);

			add_submenu_page(
				$parent_slug,
				esc_html__('Settings', 'greenshift'),
				esc_html__('Settings', 'greenshift'),
				'manage_options',
				'greenshift',
				array($this, 'settings_page')
			);
		}

		public function welcome_page()
		{
?>
			<div class="wrap gspb_welcome_div_container">
				<style>
					.wrap {
						background: white;
						max-width: 900px;
						margin: 2.5em auto;
						border: 1px solid #dbdde2;
						box-shadow: 0 10px 20px #ececec;
						text-align: center
					}

					.wrap .notice,
					.wrap .error {
						display: none
					}

					.wrap h2 {
						font-size: 1.5em;
						margin-bottom: 1em;
						font-weight: bold
					}

					.gs-introtext {
						font-size: 14px;
						max-width: 500px;
						margin: 0 auto 30px auto
					}

					.gs-intro-video iframe {
						box-shadow: 10px 10px 20px rgb(0 0 0 / 15%);
					}

					.gs-intro-video {
						margin-bottom: 15px
					}

					.wrap h1 {
						text-align: left;
						padding: 15px 20px;
						margin: -1px -1px 0 -1px;
						font-size: 13px;
						font-weight: bold;
						text-transform: uppercase;
						box-shadow: 0 3px 8px rgb(0 0 0 / 5%);
					}

					.wrap .fs-notice {
						margin: 0 25px 25px 25px !important
					}

					.wrap .fs-plugin-title {
						display: none !important
					}

					.gridrows {
						display: grid;
						grid-template-columns: repeat(2, minmax(0, 1fr));
						gap: 20px
					}

					.gridrows div {
						padding: 20px;
						border: 1px solid #e4eff9;
						background: #f3f8ff;
						font-size: 16px
					}

					.gridrows div a {
						text-decoration: none
					}

					.gs-padd {
						padding: 25px
					}
				</style>
				<h1><?php esc_html_e("Getting Started", 'greenshift'); ?></h1>
				<div class="gs-padd">
					<p><img src="<?php echo GREENSHIFT_DIR_URL . 'libs/logo_300.png'; ?>" height="100" width="100" /></p>
					<p class="gs-introtext"><?php esc_html_e("Thank you for using Greenshift. For any bug report, please, contact us ", 'greenshift'); ?> <a href="<?php echo admin_url('admin.php?page=greenshift_dashboard-contact'); ?>"><?php esc_html_e("through the contact form", 'greenshift'); ?></a></p>
					<div class="gs-intro-video"><iframe width="560" height="315" src="https://www.youtube.com/embed/3xbQcQ5LDEc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
					<div class="gs-news" style="max-width: 560px;text-align: left;margin: 0 auto 30px auto;padding: 15px;box-sizing: border-box;border: 1px dashed #ccc;display: flex;justify-content: space-between;">
						<div>
							<div class="title" style="font-weight: bold;font-size: 20px;margin-bottom: 5px;"><?php esc_html_e("Don’t miss our new Features ", 'greenshift'); ?></div>
							<div style="opacity: 0.7;"><?php esc_html_e("Tips and tricks, new functions and news", 'greenshift'); ?></div>
						</div>
						<div>
							<a href="https://twitter.com/GreenshiftWP" target="_blank">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 37.203" enable-background="new 0 0 122.88 37.203" xml:space="preserve" style="width: 120px;">
									<g>
										<path fill-rule="evenodd" clip-rule="evenodd" fill="#1DA1F2" d="M120.115,0H2.766C1.243,0,0,1.243,0,2.765v31.673 c0,1.522,1.243,2.766,2.766,2.766h117.35c1.522,0,2.765-1.243,2.765-2.766V2.765C122.88,1.243,121.638,0,120.115,0L120.115,0 L120.115,0L120.115,0z"></path>
										<polygon fill="#1A91DA" points="33.263,36.232 33.263,0 35.075,0 35.075,36.232 33.263,36.232"></polygon>
										<path fill="#056CAB" d="M0.426,33.681c0,0.702,0.287,1.34,0.749,1.802c0.461,0.463,1.1,0.75,1.803,0.75h116.924 c0.702,0,1.341-0.287,1.803-0.748c0.462-0.464,0.749-1.102,0.749-1.804l0.426-31.339v31.339c0,0.819-0.335,1.564-0.874,2.104 c-0.54,0.539-1.285,0.874-2.104,0.874H2.979c-0.819,0-1.564-0.335-2.104-0.875C0.335,35.245,0,34.5,0,33.681V2.342L0.426,33.681 L0.426,33.681z"></path>
										<path fill="#1A72AD" d="M51.718,19.243l-0.175,2.331l-2.108-0.096h-0.192l-0.08,4.52l0.127,3.353h-3.194l0.16-3.033l-0.08-4.839 h-0.208l-2.124,0.096l-0.192-0.224l0.176-2.331h7.713L51.718,19.243L51.718,19.243z M63.358,29.351h-3.385l-0.974-3.736h-0.191 l-0.894,3.736h-3.401L52.085,19.02h3.098l0.16,1.597l1.006,5.525h0.191l1.198-4.711l-0.543-2.411h3.226l0.224,1.916l1.085,5.206 h0.191l1.262-5.334l0.287-1.788h2.938L63.358,29.351L63.358,29.351z M70.305,25.998l0.128,3.353h-3.21l0.159-3.033l-0.159-7.298 h3.258L70.305,25.998L70.305,25.998z M79.678,19.243l-0.176,2.331l-2.107-0.096h-0.191l-0.08,4.52l0.128,3.353h-3.194l0.16-3.033 l-0.08-4.839H73.93l-2.124,0.096l-0.191-0.224l0.176-2.331h7.712L79.678,19.243L79.678,19.243z M87.965,19.243l-0.175,2.331 l-2.108-0.096H85.49l-0.08,4.52l0.128,3.353h-3.193l0.159-3.033l-0.079-4.839h-0.208l-2.124,0.096l-0.191-0.224l0.176-2.331h7.713 L87.965,19.243L87.965,19.243z M96.173,26.796l0.176,0.224l-0.191,2.331h-7.282l0.16-3.033l-0.16-7.298h7.409l0.176,0.224 l-0.207,2.331l-2.539-0.096h-1.645l-0.032,1.453h1.581l1.66-0.048l0.176,0.224l-0.191,2.331l-1.853-0.048h-1.438l-0.016,0.607 l0.032,0.894h1.533L96.173,26.796L96.173,26.796z M104.316,25.279c0.767,1.192,1.533,2.299,2.3,3.321l-0.048,0.352 c-1.086,0.33-2.156,0.526-3.21,0.591l-0.303-0.256l-0.192-0.399c-0.085-0.17-0.255-0.529-0.511-1.077 c-0.256-0.549-0.479-1.063-0.671-1.541h-1.069l0.111,3.081h-3.209l0.159-3.033l-0.159-7.298l4.902-0.016 c1.16,0,2.054,0.273,2.683,0.822c0.628,0.548,0.941,1.338,0.941,2.371c0,0.606-0.151,1.177-0.455,1.709 S104.859,24.896,104.316,25.279L104.316,25.279z M102.672,22.517c0-0.426-0.109-0.742-0.327-0.95 c-0.219-0.207-0.572-0.322-1.063-0.343l-0.574,0.048l-0.064,2.666l1.166,0.064c0.298-0.139,0.517-0.325,0.655-0.56 C102.603,23.209,102.672,22.899,102.672,22.517L102.672,22.517z"></path>
										<path fill="#1A72AD" d="M45.244,9.258l-0.028,1.376h0.743l1.027-0.027l0.083,0.11l-0.092,1.101L45.85,11.79h-0.651l-0.01,0.321 l0.056,1.899h-1.496l0.083-1.752l-0.083-4.165h3.706l0.083,0.11l-0.101,1.101l-1.431-0.055L45.244,9.258L45.244,9.258z M50.796,8.001c0.844,0,1.495,0.252,1.954,0.757c0.459,0.504,0.688,1.222,0.688,2.151c0,1.015-0.251,1.803-0.752,2.362 s-1.208,0.839-2.119,0.839c-0.838,0-1.486-0.259-1.945-0.775s-0.688-1.249-0.688-2.197c0-1.002,0.249-1.776,0.748-2.32 C49.18,8.273,49.885,8.001,50.796,8.001L50.796,8.001z M50.668,9.193c-0.288,0-0.517,0.057-0.688,0.17 c-0.171,0.113-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032c0,0.52,0.04,0.928,0.119,1.225 c0.08,0.297,0.207,0.509,0.381,0.638s0.411,0.193,0.711,0.193c0.293,0,0.526-0.057,0.697-0.17c0.171-0.113,0.295-0.298,0.371-0.555 c0.077-0.257,0.115-0.608,0.115-1.055c0-0.514-0.04-0.917-0.12-1.211c-0.079-0.294-0.205-0.503-0.376-0.628 C51.212,9.256,50.974,9.193,50.668,9.193L50.668,9.193z M57.954,12.9l-0.101,1.11h-3.33l0.083-1.752l-0.083-4.165h1.514 l-0.083,4.018l0.027,0.679h1.881L57.954,12.9L57.954,12.9z M62.332,12.9l-0.101,1.11h-3.331l0.083-1.752l-0.083-4.165h1.514 l-0.083,4.018l0.028,0.679h1.88L62.332,12.9L62.332,12.9z M65.499,8.001c0.844,0,1.495,0.252,1.954,0.757s0.688,1.222,0.688,2.151 c0,1.015-0.251,1.803-0.753,2.362c-0.501,0.56-1.207,0.839-2.119,0.839c-0.838,0-1.485-0.259-1.944-0.775s-0.688-1.249-0.688-2.197 c0-1.002,0.249-1.776,0.748-2.32C63.883,8.273,64.588,8.001,65.499,8.001L65.499,8.001z M65.371,9.193 c-0.288,0-0.517,0.057-0.688,0.17c-0.171,0.113-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032 c0,0.52,0.039,0.928,0.119,1.225c0.079,0.297,0.206,0.509,0.381,0.638c0.174,0.128,0.411,0.193,0.711,0.193 c0.294,0,0.525-0.057,0.697-0.17c0.171-0.113,0.295-0.298,0.371-0.555c0.077-0.257,0.115-0.608,0.115-1.055 c0-0.514-0.04-0.917-0.119-1.211c-0.08-0.294-0.205-0.503-0.377-0.628C65.915,9.256,65.677,9.193,65.371,9.193L65.371,9.193z M74.877,14.01h-1.569l-0.742-2.55h-0.092l-0.734,2.55h-1.577l-1.551-5.917h1.449L70.152,8.9l0.854,3.596h0.092l0.871-2.99 l-0.367-1.413h1.505l0.128,0.954l0.891,3.449h0.091l1.001-3.514l0.155-0.89h1.376L74.877,14.01L74.877,14.01z M84.119,11.093 l-0.009,0.853c0.006,0.691-0.207,1.223-0.638,1.596c-0.432,0.373-1.044,0.56-1.839,0.56c-0.771,0-1.359-0.184-1.767-0.55 c-0.406-0.367-0.601-0.893-0.582-1.578l0.018-0.899l-0.036-2.981h1.486l-0.064,3.679c-0.006,0.391,0.073,0.679,0.238,0.862 s0.422,0.275,0.771,0.275c0.355,0,0.616-0.09,0.784-0.27c0.169-0.181,0.25-0.458,0.243-0.831l-0.063-3.715h1.514L84.119,11.093 L84.119,11.093z M87.69,8.001c0.526,0,1.058,0.104,1.597,0.312l-0.239,1.248l-0.22,0.083c-0.232-0.153-0.465-0.272-0.697-0.358 c-0.232-0.085-0.435-0.128-0.605-0.128c-0.202,0-0.362,0.05-0.481,0.151c-0.119,0.101-0.179,0.219-0.179,0.354 c0,0.153,0.074,0.283,0.225,0.39c0.149,0.107,0.387,0.234,0.711,0.381c0.342,0.159,0.619,0.304,0.83,0.436 c0.211,0.131,0.395,0.307,0.55,0.527c0.156,0.22,0.234,0.489,0.234,0.807c0,0.355-0.097,0.676-0.289,0.963 s-0.467,0.515-0.821,0.684c-0.354,0.168-0.764,0.252-1.229,0.252c-0.617,0-1.229-0.122-1.834-0.367l0.211-1.33l0.156-0.091 c0.256,0.214,0.536,0.38,0.839,0.5c0.303,0.119,0.564,0.179,0.784,0.179c0.232,0,0.408-0.052,0.527-0.156 c0.12-0.104,0.18-0.227,0.18-0.367c0-0.166-0.077-0.303-0.229-0.413c-0.153-0.11-0.392-0.239-0.716-0.386 c-0.33-0.153-0.601-0.295-0.812-0.426c-0.212-0.131-0.394-0.308-0.546-0.527c-0.153-0.221-0.229-0.489-0.229-0.808 c0-0.361,0.097-0.687,0.289-0.977s0.461-0.518,0.807-0.684C86.848,8.084,87.244,8.001,87.69,8.001L87.69,8.001z M94.759,8.001 c0.844,0,1.495,0.252,1.954,0.757c0.458,0.504,0.688,1.222,0.688,2.151c0,1.015-0.251,1.803-0.753,2.362 c-0.501,0.56-1.208,0.839-2.119,0.839c-0.838,0-1.486-0.259-1.944-0.775c-0.459-0.517-0.688-1.249-0.688-2.197 c0-1.002,0.249-1.776,0.748-2.32C93.143,8.273,93.848,8.001,94.759,8.001L94.759,8.001z M94.63,9.193 c-0.287,0-0.517,0.057-0.688,0.17s-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032c0,0.52,0.039,0.928,0.119,1.225 c0.079,0.297,0.206,0.509,0.381,0.638c0.174,0.128,0.411,0.193,0.711,0.193c0.293,0,0.525-0.057,0.697-0.17 c0.171-0.113,0.295-0.298,0.371-0.555c0.076-0.257,0.114-0.608,0.114-1.055c0-0.514-0.039-0.917-0.119-1.211 c-0.079-0.294-0.205-0.503-0.376-0.628C95.175,9.256,94.937,9.193,94.63,9.193L94.63,9.193z M103.586,12.139l0.055,1.871h-1.614 l-2.101-3.66h-0.092l-0.009,1.578l0.055,2.082h-1.349l0.073-1.743l-0.073-4.174h1.605l2.11,3.661h0.091l-0.036-3.605l1.367-0.083 L103.586,12.139L103.586,12.139z"></path>
										<path fill="#FFFFFF" d="M45.244,8.917l-0.028,1.376h0.743l1.027-0.027l0.083,0.11l-0.092,1.101l-1.128-0.027h-0.651l-0.01,0.321 l0.056,1.899h-1.496l0.083-1.752l-0.083-4.165h3.706l0.083,0.11l-0.101,1.101l-1.431-0.055L45.244,8.917L45.244,8.917z M50.796,7.66c0.844,0,1.495,0.252,1.954,0.757c0.459,0.505,0.688,1.222,0.688,2.151c0,1.015-0.251,1.803-0.752,2.362 s-1.208,0.839-2.119,0.839c-0.838,0-1.486-0.258-1.945-0.775c-0.458-0.517-0.688-1.249-0.688-2.197 c0-1.003,0.249-1.777,0.748-2.321C49.18,7.933,49.885,7.66,50.796,7.66L50.796,7.66z M50.668,8.853 c-0.288,0-0.517,0.057-0.688,0.169c-0.171,0.113-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032 c0,0.52,0.04,0.928,0.119,1.225c0.08,0.297,0.207,0.509,0.381,0.638c0.174,0.129,0.411,0.193,0.711,0.193 c0.293,0,0.526-0.057,0.697-0.17s0.295-0.298,0.371-0.555c0.077-0.257,0.115-0.608,0.115-1.055c0-0.514-0.04-0.917-0.12-1.211 c-0.079-0.293-0.205-0.503-0.376-0.628S50.974,8.853,50.668,8.853L50.668,8.853z M57.954,12.559l-0.101,1.11h-3.33l0.083-1.752 l-0.083-4.165h1.514l-0.083,4.018l0.027,0.679h1.881L57.954,12.559L57.954,12.559z M62.332,12.559l-0.101,1.11h-3.331l0.083-1.752 l-0.083-4.165h1.514l-0.083,4.018l0.028,0.679h1.88L62.332,12.559L62.332,12.559z M65.499,7.66c0.844,0,1.495,0.252,1.954,0.757 c0.459,0.505,0.688,1.222,0.688,2.151c0,1.015-0.251,1.803-0.753,2.362c-0.501,0.56-1.207,0.839-2.119,0.839 c-0.838,0-1.485-0.258-1.944-0.775c-0.459-0.517-0.688-1.249-0.688-2.197c0-1.003,0.249-1.777,0.748-2.321 C63.883,7.933,64.588,7.66,65.499,7.66L65.499,7.66z M65.371,8.853c-0.288,0-0.517,0.057-0.688,0.169 c-0.171,0.113-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032c0,0.52,0.039,0.928,0.119,1.225 c0.079,0.297,0.206,0.509,0.381,0.638c0.174,0.129,0.411,0.193,0.711,0.193c0.294,0,0.525-0.057,0.697-0.17 c0.171-0.113,0.295-0.298,0.371-0.555c0.077-0.257,0.115-0.608,0.115-1.055c0-0.514-0.04-0.917-0.119-1.211 c-0.08-0.293-0.205-0.503-0.377-0.628C65.915,8.916,65.677,8.853,65.371,8.853L65.371,8.853z M74.877,13.669h-1.569l-0.742-2.55 h-0.092l-0.734,2.55h-1.577l-1.551-5.917h1.449l0.092,0.808l0.854,3.596h0.092l0.871-2.99l-0.367-1.413h1.505l0.128,0.954 l0.891,3.449h0.091l1.001-3.513l0.155-0.89h1.376L74.877,13.669L74.877,13.669z M84.119,10.752l-0.009,0.853 c0.006,0.691-0.207,1.223-0.638,1.596c-0.432,0.374-1.044,0.56-1.839,0.56c-0.771,0-1.359-0.184-1.767-0.55 c-0.406-0.367-0.601-0.893-0.582-1.578l0.018-0.899l-0.036-2.981h1.486l-0.064,3.679c-0.006,0.392,0.073,0.679,0.238,0.862 s0.422,0.275,0.771,0.275c0.355,0,0.616-0.09,0.784-0.271c0.169-0.181,0.25-0.458,0.243-0.831l-0.063-3.715h1.514L84.119,10.752 L84.119,10.752z M87.69,7.66c0.526,0,1.058,0.104,1.597,0.312L89.048,9.22l-0.22,0.083c-0.232-0.153-0.465-0.272-0.697-0.357 c-0.232-0.086-0.435-0.128-0.605-0.128c-0.202,0-0.362,0.05-0.481,0.151c-0.119,0.101-0.179,0.218-0.179,0.353 c0,0.153,0.074,0.283,0.225,0.39c0.149,0.107,0.387,0.234,0.711,0.381c0.342,0.159,0.619,0.304,0.83,0.436 c0.211,0.131,0.395,0.307,0.55,0.527c0.156,0.22,0.234,0.489,0.234,0.807c0,0.355-0.097,0.676-0.289,0.963 c-0.192,0.288-0.467,0.515-0.821,0.684c-0.354,0.168-0.764,0.252-1.229,0.252c-0.617,0-1.229-0.122-1.834-0.367l0.211-1.331 l0.156-0.091c0.256,0.214,0.536,0.38,0.839,0.5c0.303,0.119,0.564,0.179,0.784,0.179c0.232,0,0.408-0.052,0.527-0.156 c0.12-0.104,0.18-0.227,0.18-0.367c0-0.165-0.077-0.303-0.229-0.413c-0.153-0.11-0.392-0.239-0.716-0.386 c-0.33-0.153-0.601-0.295-0.812-0.426c-0.212-0.131-0.394-0.308-0.546-0.527c-0.153-0.22-0.229-0.489-0.229-0.808 c0-0.361,0.097-0.687,0.289-0.977s0.461-0.518,0.807-0.683S87.244,7.66,87.69,7.66L87.69,7.66z M94.759,7.66 c0.844,0,1.495,0.252,1.954,0.757c0.458,0.505,0.688,1.222,0.688,2.151c0,1.015-0.251,1.803-0.753,2.362 c-0.501,0.56-1.208,0.839-2.119,0.839c-0.838,0-1.486-0.258-1.944-0.775c-0.459-0.517-0.688-1.249-0.688-2.197 c0-1.003,0.249-1.777,0.748-2.321C93.143,7.933,93.848,7.66,94.759,7.66L94.759,7.66z M94.63,8.853 c-0.287,0-0.517,0.057-0.688,0.169c-0.171,0.113-0.297,0.297-0.376,0.551c-0.08,0.253-0.119,0.598-0.119,1.032 c0,0.52,0.039,0.928,0.119,1.225c0.079,0.297,0.206,0.509,0.381,0.638c0.174,0.129,0.411,0.193,0.711,0.193 c0.293,0,0.525-0.057,0.697-0.17c0.171-0.113,0.295-0.298,0.371-0.555c0.076-0.257,0.114-0.608,0.114-1.055 c0-0.514-0.039-0.917-0.119-1.211c-0.079-0.293-0.205-0.503-0.376-0.628S94.937,8.853,94.63,8.853L94.63,8.853z M103.586,11.798 l0.055,1.871h-1.614l-2.101-3.66h-0.092l-0.009,1.578l0.055,2.082h-1.349l0.073-1.743l-0.073-4.174h1.605l2.11,3.661h0.091 l-0.036-3.605l1.367-0.083L103.586,11.798L103.586,11.798z"></path>
										<path fill="#FFFFFF" d="M51.718,18.776l-0.175,2.332l-2.108-0.096h-0.192l-0.08,4.519l0.127,3.354h-3.194l0.16-3.034l-0.08-4.838 h-0.208l-2.124,0.096l-0.192-0.224l0.176-2.332h7.713L51.718,18.776L51.718,18.776z M63.358,28.884h-3.385l-0.974-3.736h-0.191 l-0.894,3.736h-3.401l-2.427-10.332h3.098l0.16,1.597l1.006,5.524h0.191l1.198-4.71l-0.543-2.412h3.226l0.224,1.917l1.085,5.205 h0.191l1.262-5.333l0.287-1.789h2.938L63.358,28.884L63.358,28.884z M70.305,25.53l0.128,3.354h-3.21l0.159-3.034l-0.159-7.297 h3.258L70.305,25.53L70.305,25.53z M79.678,18.776l-0.176,2.332l-2.107-0.096h-0.191l-0.08,4.519l0.128,3.354h-3.194l0.16-3.034 l-0.08-4.838H73.93l-2.124,0.096l-0.191-0.224l0.176-2.332h7.712L79.678,18.776L79.678,18.776z M87.965,18.776l-0.175,2.332 l-2.108-0.096H85.49l-0.08,4.519l0.128,3.354h-3.193l0.159-3.034l-0.079-4.838h-0.208l-2.124,0.096l-0.191-0.224l0.176-2.332h7.713 L87.965,18.776L87.965,18.776z M96.173,26.329l0.176,0.224l-0.191,2.331h-7.282l0.16-3.034l-0.16-7.297h7.409l0.176,0.224 l-0.207,2.332l-2.539-0.096h-1.645l-0.032,1.453h1.581l1.66-0.048l0.176,0.224l-0.191,2.331l-1.853-0.048h-1.438l-0.016,0.606 l0.032,0.895h1.533L96.173,26.329L96.173,26.329z M104.316,24.812c0.767,1.192,1.533,2.3,2.3,3.321l-0.048,0.352 c-1.086,0.33-2.156,0.527-3.21,0.591l-0.303-0.255l-0.192-0.399c-0.085-0.171-0.255-0.529-0.511-1.078 c-0.256-0.548-0.479-1.062-0.671-1.541h-1.069l0.111,3.082h-3.209l0.159-3.034l-0.159-7.297l4.902-0.016 c1.16,0,2.054,0.274,2.683,0.823c0.628,0.548,0.941,1.338,0.941,2.371c0,0.606-0.151,1.176-0.455,1.708 S104.859,24.429,104.316,24.812L104.316,24.812z M102.672,22.05c0-0.426-0.109-0.743-0.327-0.95 c-0.219-0.208-0.572-0.322-1.063-0.344l-0.574,0.048l-0.064,2.667l1.166,0.063c0.298-0.138,0.517-0.324,0.655-0.559 C102.603,22.741,102.672,22.433,102.672,22.05L102.672,22.05z"></path>
										<path fill="#FFFFFF" d="M26.478,12.671c-0.705,0.315-1.462,0.523-2.252,0.619c0.812-0.486,1.431-1.254,1.724-2.172 c-0.758,0.448-1.601,0.774-2.493,0.95c-0.715-0.764-1.735-1.233-2.867-1.233c-2.167,0-3.923,1.756-3.923,3.923 c0,0.304,0.032,0.603,0.102,0.892c-3.262-0.166-6.149-1.724-8.087-4.099c-0.347,0.598-0.528,1.281-0.528,1.969l0,0 c0,1.361,0.688,2.562,1.745,3.261c-0.64-0.021-1.249-0.197-1.777-0.491v0.048c0,1.9,1.351,3.491,3.149,3.848 c-0.331,0.091-0.678,0.139-1.036,0.139c-0.25,0-0.497-0.026-0.742-0.069c0.501,1.559,1.948,2.69,3.662,2.728 c-1.345,1.052-3.032,1.682-4.874,1.682c-0.32,0-0.63-0.021-0.934-0.059c1.74,1.115,3.8,1.762,6.016,1.762 c7.216,0,11.167-5.979,11.167-11.167c0-0.17-0.005-0.341-0.011-0.507C25.288,14.15,25.95,13.461,26.478,12.671L26.478,12.671 L26.478,12.671L26.478,12.671L26.478,12.671L26.478,12.671z"></path>
									</g>
								</svg>
							</a>
						</div>
					</div>
					<div style="text-align:left; padding-top:30px; border-top:1px solid #eee;">
						<h2><?php esc_html_e("More tutorials", 'greenshift'); ?></h2>
						<div class="gridrows">
							<div><a href="https://www.youtube.com/watch?v=hwzSWXvvJXU4" target="_blank"><?php esc_html_e("Row and section Options", 'greenshift'); ?></a></div>
							<div><a href="https://www.youtube.com/watch?v=00ebtAX-a34" target="_blank"><?php esc_html_e("Overview of design options", 'greenshift'); ?></a></div>
							<div><a href="https://www.youtube.com/watch?v=ijo7sBKGPIQ" target="_blank"><?php esc_html_e("In depth overview of unique options", 'greenshift'); ?></a></div>
							<div><a href="https://www.youtube.com/watch?v=5g51fLFtpmc" target="_blank"><?php esc_html_e("How to Add carousels to any block", 'greenshift'); ?></a></div>
							<div><a href="https://www.youtube.com/watch?v=pIz5U5eq2bQ" target="_blank"><?php esc_html_e("How to Use Presets", 'greenshift'); ?></a></div>
							<div><a href="https://youtu.be/Qj5uk7e4vpM" target="_blank"><?php esc_html_e("How to make floating toolbars", 'greenshift'); ?></a></div>
							<div><a href="https://youtu.be/gksGsf1VEBs" target="_blank"><?php esc_html_e("How to improve Query Loop with Query Addon", 'greenshift'); ?></a></div>
							<div><a href="https://youtube.com/playlist?list=PLIEKo1RENmYxbs3yL3nMuOzJJ0AY6GvQ2" target="_blank"><?php esc_html_e("Animation Addon overview", 'greenshift'); ?></a></div>
						</div>
					</div>
				</div>
			</div>
		<?php
		}

		public function settings_page()
		{

			if (!current_user_can('manage_options')) {
				wp_die('Unauthorized user');
			}

			// Get the active tab from the $_GET param
			$default_tab = null;
			$tab         = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

		?>
			<div class="wrap">
				<style>
					.wrap {
						background: white;
						max-width: 900px;
						margin: 2.5em auto;
						border: 1px solid #dbdde2;
						box-shadow: 0 10px 20px #ececec;
						text-align: center
					}

					.wrap .notice,
					.wrap .error {
						display: none
					}

					.wrap h2 {
						font-size: 1.5em;
						margin-bottom: 1em;
						font-weight: bold;
						padding: 15px;
						background: #f4f4f4;
					}

					.gs-introtext {
						font-size: 14px;
						max-width: 500px;
						margin: 0 auto 50px auto
					}

					.gs-intro-video iframe {
						box-shadow: 10px 10px 20px rgb(0 0 0 / 15%);
					}

					.gs-intro-video {
						margin-bottom: 40px
					}

					.wrap h1 {
						text-align: left;
						padding: 15px 20px;
						margin: -1px -1px 60px -1px;
						font-size: 13px;
						font-weight: bold;
						text-transform: uppercase;
						box-shadow: 0 3px 8px rgb(0 0 0 / 5%);
					}

					.gs-padd {
						padding: 25px;
						text-align: left;
						background-color: #fbfbfb
					}

					.rtl .gs-padd {
						text-align: right
					}

					.wp-core-ui .button-primary {
						background-color: #2184f9
					}

					.nav-tab-active,
					.nav-tab-active:focus,
					.nav-tab-active:focus:active,
					.nav-tab-active:hover {
						border-bottom: 1px solid #fbfbfb;
						background: #fbfbfb;
					}

					.nav-tab-wrapper {
						padding-left: 20px
					}

					.wrap .fs-notice {
						margin: 0 25px 35px 25px !important
					}

					.wrap .fs-plugin-title {
						display: none !important
					}
				</style>
				<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
				<!-- Here are our tabs -->
				<nav class="nav-tab-wrapper">
					<a href="?page=greenshift" class="nav-tab 
				<?php
				if ($tab === null) :
				?>
					nav-tab-active<?php endif; ?>"> <?php esc_html_e("General", 'greenshift'); ?> </a>
					<a href="?page=greenshift&tab=save_css" class="nav-tab 
				<?php
				if ($tab === 'save_css') :
				?>
					nav-tab-active<?php endif; ?>"><?php esc_html_e("Save CSS", 'greenshift'); ?></a>
					<a href="?page=greenshift&tab=breakpoints" class="nav-tab 
				<?php
				if ($tab === 'breakpoints') :
				?>
					nav-tab-active<?php endif; ?>"><?php esc_html_e("Breakpoints", 'greenshift'); ?></a>
				</nav>

				<div class="tab-content gs-padd">
					<?php
					switch ($tab):
						case 'save_css':
							if (isset($_POST['gspb_save_settings'])) {
								if (!wp_verify_nonce($_POST['gspb_settings_field'], 'gspb_settings_page_action')) {
									esc_html_e("Sorry, your nonce did not verify.", 'greenshift');
									return;
								}
								update_option('gspb_css_save', sanitize_text_field($_POST['gspb_settings_option']));
							}

							$css_tsyle_option = get_option('gspb_css_save');
					?>
							<div class="gspb_settings_form">
								<form method="POST">
									<?php wp_nonce_field('gspb_settings_page_action', 'gspb_settings_field'); ?>
									<table class="form-table">
										<tr>
											<th> <label for="css_system"><?php esc_html_e("Css location", 'greenshift'); ?></label> </th>
											<td>
												<select name="gspb_settings_option">
													<option value="inline" <?php selected($css_tsyle_option, 'inline'); ?>><?php esc_html_e("Inline in Head", 'greenshift'); ?> </option>
													<option value="file" <?php selected($css_tsyle_option, 'file'); ?>> <?php esc_html_e("File system", 'greenshift'); ?> </option>
													<option value="inlineblock" <?php selected($css_tsyle_option, 'inlineblock'); ?>> <?php esc_html_e("Inline in block", 'greenshift'); ?> </option>
												</select>
											</td>
										</tr>
									</table>
									<div style="margin-bottom:15px"><?php esc_html_e("Use Inline in block only if you have some issues with not updating styles of blocks or cache. Once saved as inline in block, styles can be overwritten only when you update post with blocks", 'greenshift'); ?></div>

									<input type="submit" name="gspb_save_settings" value="<?php esc_html_e("Save settings"); ?>" class="button button-primary button-large">
								</form>
							</div>
						<?php
							break;
						case 'breakpoints':
							$global_settings = get_option('gspb_global_settings');

							if (isset($_POST['gspb_save_settings']) && isset($_POST['gspb_settings_field']) && wp_verify_nonce($_POST['gspb_settings_field'], 'gspb_settings_page_action')) {
								$breakpoints = array(
									"mobile" =>  sanitize_text_field($_POST['mobile']),
									"tablet" =>  sanitize_text_field($_POST['tablet']),
									"desktop" =>  sanitize_text_field($_POST['desktop']),
									"row" =>  sanitize_text_field($_POST['row']),
								);
								$global_settings['breakpoints'] = $breakpoints;
								update_option('gspb_global_settings', $global_settings);
							}
						?>
							<form method="POST" class="greenshift_form">
								<?php wp_nonce_field('gspb_settings_page_action', 'gspb_settings_field'); ?>
								<table class="form-table">

									<tr>
										<td> <?php esc_html_e("Mobile", 'greenshift'); ?> </td>
										<td>
											<input name="mobile" type="text" value="<?php if (isset($global_settings['breakpoints']['mobile'])) {
																						echo esc_attr($global_settings['breakpoints']['mobile']);
																					}  ?>" placeholder="576" />
										</td>
									</tr>
									<tr>
										<td> <?php esc_html_e("Tablet", 'greenshift'); ?> </td>
										<td>
											<input name="tablet" type="text" value="<?php if (isset($global_settings['breakpoints']['tablet'])) {
																						echo esc_attr($global_settings['breakpoints']['tablet']);
																					} ?>" placeholder="768" />
										</td>
									</tr>
									<tr>
										<td> <?php esc_html_e("Desktop", 'greenshift'); ?> </td>
										<td>
											<input name="desktop" type="text" value="<?php if (isset($global_settings['breakpoints']['desktop'])) {
																							echo esc_attr($global_settings['breakpoints']['desktop']);
																						} ?>" placeholder="992" />
										</td>
									</tr>
									<tr>
										<td> <?php esc_html_e("Default Row Content Width", 'greenshift'); ?> </td>
										<td>
											<input name="row" type="text" value="<?php if (isset($global_settings['breakpoints']['row'])) {
																						echo esc_attr($global_settings['breakpoints']['row']);
																					} ?>" placeholder="1200" />
										</td>
									</tr>
								</table>
								<input type="submit" name="gspb_save_settings" value="Save" class="button button-primary button-large">
							</form>
						<?php
							break;
						default:
							wp_enqueue_style('gsadminsettings');
							wp_enqueue_script('gsadminsettings');
							if (isset($_POST['gspb_save_settings_general']) && isset($_POST['gspb_settings_field']) && wp_verify_nonce($_POST['gspb_settings_field'], 'gspb_settings_page_action')) {
								$this->gspb_save_general_form($_POST, $_FILES);
							}
						?>
							<h2><?php esc_html_e("General Settings", 'greenshift'); ?></h2>
							<?php esc_html_e("You can assign global presets and other settings in Post edit area when you click on G button in header toolbar", 'greenshift'); ?>
							<h2><?php esc_html_e("Local Font Loader", 'greenshift'); ?></h2>
							<?php esc_html_e("Attention! Local font is global option and it can reduce performance in some cases, please, check", 'greenshift'); ?> <a href="https://greenshiftwp.com/how-to-use-local-fonts-in-greenshift-for-gdpr/" target="_blank"><?php esc_html_e("Documentation", 'greenshift'); ?></a>
							<?php
							$allowed_font_ext = $this->allowed_font_ext;
							require_once GREENSHIFT_DIR_PATH . 'templates/admin/settings_general_form.php'; ?>
					<?php
							break;
					endswitch;
					?>
				</div>
			</div>
<?php
		}

		// settings fonts
		public function gspb_settings_add_font()
		{
			$i = $_POST['i'];
			$allowed_font_ext = $this->allowed_font_ext;
			ob_start();
			require_once GREENSHIFT_DIR_PATH . 'templates/admin/settings_general_font_item.php';
			$html = ob_get_contents();
			ob_get_clean();
			wp_send_json(['html' => $html]);
		}

		public function gspb_save_general_form($data, $files)
		{
			$global_settings = get_option('gspb_global_settings');

			$fonts_urls = $this->gspb_save_files($files);
			$arr = [];
			for ($i = 0; (int)$data['fonts_count'] > $i; $i++) {
				//$item_arr = ['label' => sanitize_text_field($data['font_specific_style_name'][$i])];
				foreach ($this->allowed_font_ext as $ext) {
					$item_arr[$ext] = !empty($fonts_urls[$i][$ext]) ? $fonts_urls[$i][$ext] : sanitize_text_field($data[$ext][$i]);
				}
				$arr[sanitize_text_field($data['font_family_name'][$i])] = $item_arr;
			}
			$new_localfont = json_encode($arr);
			$global_settings['localfont'] = $new_localfont;

			$localfontcss = '';
			if (!empty($arr)) {
				foreach ($arr as $i => $value) {
					$localfontcss .= '@font-face {';
					$localfontcss .= 'font-family: "' . $i . '";';
					$localfontcss .= 'src: ';
					if (!empty($value['woff2'])) {
						$localfontcss .= 'url(' . $value["woff2"] . ') format("woff2"), ';
					}
					if (!empty($value['woff'])) {
						$localfontcss .= 'url(' . $value["woff"] . ') format("woff"), ';
					}
					if (!empty($value['ttf'])) {
						$localfontcss .= 'url(' . $value["ttf"] . ') format("ttf"), ';
					}
					if (!empty($value['tiff'])) {
						$localfontcss .= 'url(' . $value["tiff"] . ') format("tiff"), ';
					}
					$localfontcss .= ';';
					$localfontcss .= 'font-display: swap;}';
				}
				$localfontcss = str_replace(', ;', ';', $localfontcss);
				$global_settings['localfontcss'] = $localfontcss;

				$gs_global_css = (!empty($global_settings['globalcss'])) ? $global_settings['globalcss'] : '';
				$upload_dir = wp_upload_dir();

				require_once ABSPATH . 'wp-admin/includes/file.php';
				global $wp_filesystem;
				$dir = trailingslashit($upload_dir['basedir']) . 'GreenShift/'; // Set storage directory path

				WP_Filesystem(); // WP file system

				if (!$wp_filesystem->is_dir($dir)) {
					$wp_filesystem->mkdir($dir);
				}

				$gspb_css_filename = 'globalstyle.css';

				$gs_global_css = str_replace('!important', '', $gs_global_css);

				if (!$wp_filesystem->put_contents($dir . $gspb_css_filename, $gs_global_css . $localfontcss)) {
					throw new Exception(__('CSS not saved due the permission!!!', 'greenshift'));
				}
			}
			update_option('gspb_global_settings', $global_settings);
		}

		public function gspb_save_files($files)
		{
			$result = [];
			$upload = wp_upload_dir();
			$upload_dir = $upload['basedir'] . '/GreenShift/fonts';
			$upload_url = $upload['baseurl'] . '/GreenShift/fonts';

			foreach (array_keys($files) as $filename) {
				foreach ($files[$filename]["error"] as $key => $error) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $files[$filename]["tmp_name"][$key];
						$name = basename($files[$filename]["name"][$key]);
						$ext = pathinfo($name, PATHINFO_EXTENSION);
						$font_dir = $upload_dir . '/font_' . ($key + 1) . '/' . $ext;

						$this->gspb_rm_rec($font_dir); //clean up dir before download

						if (!wp_mkdir_p($font_dir)) {
							return false;
						}

						if (move_uploaded_file($tmp_name, "$font_dir/$name")) {
							$result[$key][$ext] = $upload_url . '/font_' . ($key + 1) . '/' . $ext . '/' . $name;
						}
					}
				}
			}

			return $result;
		}

		public function gspb_rm_rec($path)
		{
			if (is_file($path)) return unlink($path);
			if (is_dir($path)) {
				foreach (scandir($path) as $p) if (($p != '.') && ($p != '..'))
					$this->gspb_rm_rec($path . '/' . $p);
				return rmdir($path);
			}
			return false;
		}

		//Function to display Reusable section in menu
		function gspb_template_menu_display($type, $args)
		{
			if ('wp_block' !== $type) {
				return;
			}
			$args->show_in_menu = true;
			$args->_builtin = false;
			$args->labels->name = esc_html__('Block template', 'greenshift');
			$args->labels->menu_name = esc_html__('Reusable templates', 'greenshift');
			$args->menu_icon = 'dashicons-screenoptions';
			$args->menu_position = 58;
		}

		//Columns in Reusable section
		function gspb_template_screen_add_column($columns)
		{
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => esc_html__('Block title', 'greenshift'),
				'gs-reusable-preview' => esc_html__('Usage', 'greenshift'),
			);
			return $columns;
		}

		//Render function for Columns in Reusable Sections
		function gspb_template_screen_fill_column($column, $ID)
		{
			global $post;
			switch ($column) {

				case 'gs-reusable-preview':

					echo '<p><input type="text" style="width:350px" value="[wp_reusable_render id=\'' . $ID . '\']" readonly=""></p>';
					echo '<p>' . esc_html__('If you use template inside other dynamic ajax blocks', 'greenshift') . '<br><input type="text" style="width:350px" value="[wp_reusable_render inlinestyle=1 id=\'' . $ID . '\']" readonly="">';
					echo '<p>' . esc_html__('Shortcode for Ajax render:', 'greenshift') . '<br><input type="text" style="width:350px" value="[wp_reusable_render ajax=1 height=100px id=\'' . $ID . '\']" readonly="">';
					echo '<p>' . esc_html__('Hover trigger:', 'greenshift') . ' <code>gs-el-onhover load-block-' . $ID . '</code>';
					echo '<p>' . esc_html__('Click trigger:', 'greenshift') . ' <code>gs-el-onclick load-block-' . $ID . '</code>';
					echo '<p>' . esc_html__('On view trigger:', 'greenshift') . ' <code>gs-el-onview load-block-' . $ID . '</code>';
					break;

				default:
					break;
			}
		}

		//Render shortcode function

		function gspb_template_shortcode_function($atts)
		{
			extract(shortcode_atts(
				array(
					'id' => '',
					'ajax' => '',
					'height' => '',
					'inlinestyle' => ''
				),
				$atts
			));
			if (!isset($id) || empty($id)) {
				return '';
			}
			if (!is_numeric($id)) {
				$postget = get_page_by_path($id, OBJECT, array('wp_block'));
				$id = $postget->ID;
			}
			if (!empty($ajax)) {
				wp_enqueue_style('wp-block-library');
				wp_enqueue_style('gspreloadercss');
				wp_enqueue_script('gselajaxloader');
				$scriptvars = array(
					'reusablenonce' => wp_create_nonce('gsreusable'),
					'ajax_url' => admin_url('admin-ajax.php', 'relative'),
				);
				wp_localize_script('gselajaxloader', 'gsreusablevars', $scriptvars);
				$content = '<div class="gs-ajax-load-block gs-ajax-load-block-' . $id . '"></div>';

				$content_post = get_post($id);
				$contentpost = $content_post->post_content;
				$style = '';
				if (has_blocks($contentpost)) {
					$blocks = parse_blocks($contentpost);
					$style .= '<style scoped>';
					$style .= gspb_get_inline_styles_blocks($blocks);
					$style .= '</style>';
				}
				if (!empty($height)) {
					$content = '<div style="min-height:' . $height . '">' . $content . $style . '</div>';
				} else {
					$content = '<div>' . $content . $style . '</div>';
				}
			} else {
				$content_post = get_post($id);
				$content = $content_post->post_content;
				$style = '';
				if ($inlinestyle) {
					if (has_blocks($content)) {
						$blocks = parse_blocks($content);
						$style .= '<style scoped>';
						$style .= gspb_get_inline_styles_blocks($blocks);
						$style .= '</style>';
					}
				}
				$content = do_blocks($content);
				$content = do_shortcode($content);
				$content = preg_replace('%<p>&nbsp;\s*</p>%', '', $content);
				$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);
				$content = $content . $style;
			}
			return $content;
		}

		//Load reusable Ajax function
		function gspb_el_reusable_load()
		{
			check_ajax_referer('gsreusable', 'security');
			$post_id = intval($_POST['post_id']);
			$content_post = get_post($post_id);
			$content = $content_post->post_content;
			$content = apply_filters('the_content', $content);
			$content = str_replace('strokewidth', 'stroke-width', $content);
			$content = str_replace('strokedasharray', 'stroke-dasharray', $content);
			$content = str_replace('stopcolor', 'stop-color', $content);
			$content = str_replace('loading="lazy"', '', $content);
			if ($content) {
				wp_send_json_success($content);
			} else {
				wp_send_json_success('fail');
			}
			wp_die();
		}

		//Show gutenberg editor on reusable section even if Classic editor plugins enabled
		function gspb_template_gutenberg_post($use_block_editor, $post)
		{
			if (empty($post->ID)) return $use_block_editor;
			if ('wp_block' === get_post_type($post->ID)) return true;
			return $use_block_editor;
		}
		function gspb_template_gutenberg_post_type($use_block_editor, $post_type)
		{
			if ('wp_block' === $post_type) return true;
			return $use_block_editor;
		}
	}
}

add_filter('block_editor_settings_all', 'gspb_generate_anchor_headings', 10, 2);

function gspb_generate_anchor_headings($settings, $block_editor_context)
{
	$settings['generateAnchors'] = true;
	return $settings;
}

function gspb_get_inline_styles_blocks($blocks)
{
	$inlinestyle = '';
	foreach ($blocks as $block) {
		if (!empty($block['attrs']['inlineCssStyles'])) {
			$dynamic_style = $block['attrs']['inlineCssStyles'];
			$dynamic_style = gspb_get_final_css($dynamic_style);
			$dynamic_style = gspb_quick_minify_css($dynamic_style);
			$dynamic_style = htmlspecialchars_decode($dynamic_style);
			$inlinestyle .= $dynamic_style;
		}
		gspb_greenShift_block_script_assets('', $block);
		if (function_exists('greenShiftGsap_block_script_assets')) {
			greenShiftGsap_block_script_assets('', $block);
		}
		if (!empty($block['innerBlocks'])) {
			$blocks = $block['innerBlocks'];
			$inlinestyle .= gspb_get_inline_styles_blocks($blocks);
		}
	}
	return $inlinestyle;
}

//////////////////////////////////////////////////////////////////
// File Manager
//////////////////////////////////////////////////////////////////

if (!function_exists('greenshift_download_file_localy')) {
	function  greenshift_download_file_localy($file_uri, $save_dir, $file_name, $file_ext = null, $check_type = '')
	{
		$file_path = trailingslashit($save_dir) . $file_name;
		if (file_exists($file_path)) {
			return $file_name;
		}
		$args = array(
			'timeout' => 30,
			'httpversion' => '1.1',
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36',
			'sslverify'   => true,
		);

		$response = wp_remote_get($file_uri, $args);

		if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
			return false;
		}

		if ($file_ext === null) {
			$headers = wp_remote_retrieve_headers($response);
			if (empty($headers['content-type'])) return false;

			$types = array_search($headers['content-type'], wp_get_mime_types());

			if (!$types) return false;

			$exts = explode('|', $types);
			$file_ext = $exts[0];
			$file_name .= '.' . $file_ext;
		}

		$file_name = wp_unique_filename($save_dir, $file_name);

		if ($check_type) {
			$filetype = wp_check_filetype($file_name, null);
			if (substr($filetype['type'], 0, 5) != $check_type)
				return false;
		}

		$image_string = wp_remote_retrieve_body($response);
		if (!file_put_contents($file_path, $image_string))
			return false;

		return $file_name;
	}
}

if (!function_exists('greenshift_save_file_localy')) {
	function greenshift_save_file_localy($file_uri, $img_title = '', $check_type = '')
	{
		$newfilename = basename($file_uri);
		$ext = pathinfo(basename($file_uri), PATHINFO_EXTENSION);

		$ext = ($ext) ? $ext : null;

		if (empty($newfilename)) {
			$newfilename = preg_replace('/[^a-zA-Z0-9\-]/', '', $newfilename);
			$newfilename = strtolower($newfilename);
		}

		$uploads = wp_upload_dir();

		if ($newfilename = greenshift_download_file_localy($file_uri, $uploads['path'], $newfilename, $ext, $check_type)) {
			return $newfilename;
		} else {
			return false;
		}
	}
}

if (!function_exists('greenshift_replace_ext_images')) {
	function greenshift_replace_ext_images($content)
	{
		$pattern = '#https?://[^/\s]+/\S+\.(jpg|png|gif|webp|svg|jpeg)#';
		$content = json_decode($content, true);
		$result = preg_replace_callback($pattern, function ($match) {
			if (is_array($match)) {
				$url = $match[0];
				if ($url) {
					$urlnew = greenshift_save_file_localy($url);
					$uploads = wp_upload_dir();
					$image = trailingslashit($uploads['url']) . $urlnew;
					return $image;
				}
			}
		}, $content);
		$result = json_encode($result);
		return $result;
	}
}

?>