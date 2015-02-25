<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name: Categories Layered Navigation For Woocommerce
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: This plug in adds a Widget that lets you add filters for taxonomies like Product Categories to the Layered Nav in Woocommerce. Requires Woocommerce.
 * Version: 1.0
 * Author: oscb
 * Author URI: http://oscarbazaldua.com
 * License: GPL2
 * WC requires at least: 2.2
 * WC tested up to: 2.3
 *
 * @author oscb (Oscar Bazaldua)
 * @requires Woocommerce
 */

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	// Woocommerce is active

	// Define Constants
	define( 'OB_PRODUCTS_CATEGORY_TAXONOMY', 'product_cat' );

	/**
	 * ob_add_categories_filter function
	 *
	 * Sets the global $chosen_attributes to include the Product Categories
	 *
	 * @param array $filtered_posts
	 *
	 * @return array $filtered_posts
	 */
	function ob_add_categories_filter( $filtered_posts ) {
		global $_chosen_attributes;

		$taxonomy        = wc_sanitize_taxonomy_name( OB_PRODUCTS_CATEGORY_TAXONOMY );
		$name            = 'filter_' . OB_PRODUCTS_CATEGORY_TAXONOMY;
		$query_type_name = 'query_type_' . OB_PRODUCTS_CATEGORY_TAXONOMY;

		if ( ! empty( $_GET[ $name ] ) && taxonomy_exists( $taxonomy ) ) {

			$_chosen_attributes[ $taxonomy ]['terms'] = explode( ',', $_GET[ $name ] );

			if ( empty( $_GET[ $query_type_name ] ) || ! in_array( strtolower( $_GET[ $query_type_name ] ), array(
					'and',
					'or'
				) )
			) {
				$_chosen_attributes[ $taxonomy ]['query_type'] = apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
			} else {
				$_chosen_attributes[ $taxonomy ]['query_type'] = strtolower( $_GET[ $query_type_name ] );
			}

		}

		return $filtered_posts;
	}

	/**
	 * ob_replace_layered_nav_widget
	 *
	 * Loads the Widget Class after the Woocommmerce plugins are loaded so it
	 * can extend the Layered Nav Plugin
	 *
	 * @return void
	 */
	function ob_replace_layered_nav_widget() {
		unregister_widget( 'WC_Widget_Layered_Nav' );
		include_once( 'widgets/OB_Widget_Layered_Nav_Categories.php' );
		register_widget( 'OB_Widget_Layered_Nav_Categories' );
	}

	// Set Actions and Filters
	add_action( 'widgets_init', 'ob_replace_layered_nav_widget', 11 );

	add_filter( 'loop_shop_post_in', 'ob_add_categories_filter', 5, 1 );

}