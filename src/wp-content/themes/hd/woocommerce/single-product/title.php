<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

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

?>
<h1 class="h4 product_title entry-title"><?php echo get_the_title() . $_hot_label; ?></h1>
