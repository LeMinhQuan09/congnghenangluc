<?php

/**
 * Theme functions and definitions
 *
 * @package hd
 */

use Webhd\Themes\Theme;

// This theme requires WordPress 5.3 or later.
if ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) {
	require __DIR__ . '/inc/back-compat.php';
}

$theme_version = ( $theme_version = wp_get_theme()->get( 'Version' ) ) ? $theme_version : false;
$theme_author  = ( $theme_author = wp_get_theme()->get( 'Author' ) ) ? $theme_author : 'WebHD Team';

defined( 'W_THEME_VERSION' ) || define( 'W_THEME_VERSION', $theme_version );
defined( 'W_AUTHOR' ) || define( 'W_AUTHOR', $theme_author );
defined( 'W_THEME_PATH' ) || define( 'W_THEME_PATH', untrailingslashit( get_template_directory() ) ); // /**/wp-content/themes/hd
defined( 'W_THEME_URL' ) || define( 'W_THEME_URL', untrailingslashit( esc_url( get_template_directory_uri() ) ) ); // https://**/wp-content/themes/hd

const W_THEME_INC_PATH = W_THEME_PATH . DIRECTORY_SEPARATOR . 'inc';
const W_THEME_INC_URL  = W_THEME_URL . '/inc';

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	wp_die( __( 'Error locating autoloader. Please run <code>composer install</code>.', 'hd' ) );
}

require __DIR__ . '/vendor/autoload.php';

// Initialize theme settings.
( new Theme() );

function woocommerce_ajax_search() {
    $query = sanitize_text_field($_POST['query']);
    $args = array(
        'post_type' => array('product','post'),
        'posts_per_page' => 10,
        's' => $query
    );
    $search_query = new WP_Query($args);
    if ($search_query->have_posts()) {
        $suggestions = array();
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $post_id = get_the_ID();
			$post_type = get_post_type();
			// Neu la 'product'
			if ($post_type === 'product') {
				$product = wc_get_product($post_id);
				$price = $product->get_price();
				$regular_price = $product->get_regular_price(); 
				$sale_price = $product->get_sale_price();
				// format
				$formatted_price = $price ? wc_price($price) : 'Liên hệ';
				$formatted_regular_price = $regular_price ? wc_price($regular_price) : 'Liên hệ';
				$formatted_sale_price = $sale_price ? wc_price($sale_price) : 'Liên hệ';
				$suggestions[] = array(
					'type' => 'product',
					'title' => get_the_title(),
					'permalink' => get_permalink(),
					'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
					'price' => $formatted_price, 
					'regular_price' => $formatted_regular_price,
					'sale_price' => $formatted_sale_price,
				);
			}
			// Neu la 'post'
            if ($post_type === 'post') {
                $suggestions[] = array(
                    'type' => 'post',
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                );
            }
        }
        wp_reset_postdata();
        echo json_encode($suggestions);
    } else {
        echo json_encode(array('message' => 'No products found'));
    }
    wp_die();
}
add_action('wp_ajax_woocommerce_ajax_search', 'woocommerce_ajax_search');
add_action('wp_ajax_nopriv_woocommerce_ajax_search', 'woocommerce_ajax_search');