<?php

namespace BlueBillywigPlugin\Admin;

/**
 *  Shortcode class
 */
class Shortcode {


	/**
	 * Initialize
	 */
	public function init() {
		// Hooks.
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register shortcode
	 */
	public function register() {
		add_shortcode( 'blue-billywig-embed', array( $this, 'render_shortcode' ) );
	}


	/**
	 * Renders embed shortcode
	 *
	 * @param  array $atts Shortcode attributes.
	 */
	public function render_shortcode( $atts ) {
		if ( isset( $atts['videoid'] ) ) {
			$entity_id         = $atts['videoid'];
			$bb_embedcode_data = apply_filters( 'blue_billywig_get_embedcode', get_option( 'blue-billywig-api-secret' ), get_option( 'blue-billywig-api-id' ), get_option( 'blue-billywig-publication' ), $entity_id );
			return $bb_embedcode_data['body'];
		} else {
			return '';
		}
	}
}
