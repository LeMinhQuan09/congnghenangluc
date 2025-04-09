<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 */

use Webhd\Helpers\Str;

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

?>
<article <?php wc_product_class( 'item', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @see woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @see woocommerce_show_product_loop_sale_flash - 10
	 * @see woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

    /** */

	$ACF           = \get_fields( $product->get_id() ) ?? '';
	$label_txt     = $ACF['label_txt'] ?? '';
	$label_color   = $ACF['label_color'] ?? '';
	$label_bgcolor = $ACF['label_bgcolor'] ?? '';

	$_hot_label = '';
	if ( $label_txt ) {
		$_css = '';
		if ( $label_bgcolor ) {
			$_css .= 'background-color:' . $label_bgcolor . ';';
		}
		if ( $label_color ) {
			$_css .= 'color:' . $label_color . ';';
		}
		if ( $_css ) {
			$_css = ' style="' . $_css . '"';
		}

		$_hot_label = '<sup class="hot-label" ' . $_css . '>' . $label_txt . '</sup>';
	}

	echo '<p class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . $_hot_label . '</p>';

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @see woocommerce_template_loop_rating - 5
	 * @see woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @see woocommerce_template_loop_product_link_close - 5
	 * @see woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</article>

