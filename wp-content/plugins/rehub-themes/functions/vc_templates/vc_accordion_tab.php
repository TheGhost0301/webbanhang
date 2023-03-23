<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$output = $title = $title = $el_id = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts ); 

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_accordion_section group', $this->settings['base'], $atts );

$output = '
	<div ' . ( isset( $el_id ) && ! empty( $el_id ) ? "id='" . esc_attr( $el_id ) . "'" : '' ) . 'class="' . esc_attr( $css_class ) . '">
		<h3 class="wpb_accordion_header ui-accordion-header wpsm-accordion-trigger"><a href="#' . sanitize_title( $title ) . '">' . $title . '</a></h3>
		<div class="wpb_accordion_content ui-accordion-content vc_clearfix">
			' . ( ( '' === trim( $content ) ) ? esc_html__( 'Empty section. Edit page to add content here.', 'rehub-theme' ) : wpb_js_remove_wpautop( $content ) ) . '
		</div>
	</div>
';

echo ''.$output;