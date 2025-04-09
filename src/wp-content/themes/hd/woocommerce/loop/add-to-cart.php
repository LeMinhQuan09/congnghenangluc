<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$glyph = '';
if ( $product->get_price() && ( $product->managing_stock() || $product->is_in_stock() ) ) {
    $glyph = '';

	echo apply_filters(
		'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
		sprintf(
			'<a href="%s" data-quantity="%s" class="%s add-to-cart product_button" title="%s" data-glyph-after="%s" %s><span>%s</span>
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
<path d="M0 0.785714C0 0.57733 0.0842863 0.377481 0.234317 0.230131C0.384347 0.0827805 0.587832 0 0.800007 0H3.20003C3.37848 4.8418e-05 3.55179 0.0586939 3.6924 0.16661C3.83301 0.274527 3.93285 0.425518 3.97604 0.595571L4.62404 3.14286H23.2002C23.3217 3.14289 23.4416 3.17011 23.5509 3.22245C23.6601 3.2748 23.7557 3.35089 23.8305 3.44495C23.9053 3.53901 23.9573 3.64857 23.9825 3.76531C24.0078 3.88206 24.0056 4.00291 23.9762 4.11871L21.5762 13.5473C21.533 13.7173 21.4332 13.8683 21.2926 13.9762C21.152 14.0842 20.9786 14.1428 20.8002 14.1429H6.40006C6.22161 14.1428 6.0483 14.0842 5.90768 13.9762C5.76707 13.8683 5.66724 13.7173 5.62405 13.5473L2.57602 1.57143H0.800007C0.587832 1.57143 0.384347 1.48865 0.234317 1.3413C0.0842863 1.19395 0 0.994099 0 0.785714ZM5.02405 4.71429L5.82405 7.85714H8.00007V4.71429H5.02405ZM9.60009 4.71429V7.85714H12.8001V4.71429H9.60009ZM14.4001 4.71429V7.85714H17.6002V4.71429H14.4001ZM19.2002 4.71429V7.85714H21.3762L22.1762 4.71429H19.2002ZM20.9762 9.42857H19.2002V12.5714H20.1762L20.9762 9.42857ZM17.6002 9.42857H14.4001V12.5714H17.6002V9.42857ZM12.8001 9.42857H9.60009V12.5714H12.8001V9.42857ZM8.00007 9.42857H6.22406L7.02407 12.5714H8.00007V9.42857ZM8.00007 17.2857C7.57572 17.2857 7.16875 17.4513 6.86869 17.746C6.56863 18.0407 6.40006 18.4404 6.40006 18.8571C6.40006 19.2739 6.56863 19.6736 6.86869 19.9683C7.16875 20.263 7.57572 20.4286 8.00007 20.4286C8.42442 20.4286 8.83139 20.263 9.13146 19.9683C9.43152 19.6736 9.60009 19.2739 9.60009 18.8571C9.60009 18.4404 9.43152 18.0407 9.13146 17.746C8.83139 17.4513 8.42442 17.2857 8.00007 17.2857ZM4.80004 18.8571C4.80004 18.0236 5.13719 17.2242 5.73731 16.6348C6.33743 16.0454 7.15137 15.7143 8.00007 15.7143C8.84877 15.7143 9.66271 16.0454 10.2628 16.6348C10.863 17.2242 11.2001 18.0236 11.2001 18.8571C11.2001 19.6907 10.863 20.4901 10.2628 21.0795C9.66271 21.6689 8.84877 22 8.00007 22C7.15137 22 6.33743 21.6689 5.73731 21.0795C5.13719 20.4901 4.80004 19.6907 4.80004 18.8571ZM19.2002 17.2857C18.7758 17.2857 18.3689 17.4513 18.0688 17.746C17.7687 18.0407 17.6002 18.4404 17.6002 18.8571C17.6002 19.2739 17.7687 19.6736 18.0688 19.9683C18.3689 20.263 18.7758 20.4286 19.2002 20.4286C19.6245 20.4286 20.0315 20.263 20.3316 19.9683C20.6316 19.6736 20.8002 19.2739 20.8002 18.8571C20.8002 18.4404 20.6316 18.0407 20.3316 17.746C20.0315 17.4513 19.6245 17.2857 19.2002 17.2857ZM16.0001 18.8571C16.0001 18.0236 16.3373 17.2242 16.9374 16.6348C17.5375 16.0454 18.3515 15.7143 19.2002 15.7143C20.0489 15.7143 20.8628 16.0454 21.4629 16.6348C22.0631 17.2242 22.4002 18.0236 22.4002 18.8571C22.4002 19.6907 22.0631 20.4901 21.4629 21.0795C20.8628 21.6689 20.0489 22 19.2002 22C18.3515 22 17.5375 21.6689 16.9374 21.0795C16.3373 20.4901 16.0001 19.6907 16.0001 18.8571Z" fill="white"/>
</svg>
			</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $args['quantity'] ?? 1 ),
			esc_attr( $args['class'] ?? 'button' ),
			esc_html( $product->add_to_cart_text() ),
			$glyph,
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() )
		),
		$product,
		$args
	);
} else {

	echo apply_filters(
		'woocommerce_loop_view_link', // WPCS: XSS ok.
		sprintf(
			'<a href="%s" class="%s product_button loop-view-details" title="%s" data-glyph-after="%s"><span>%s</span></a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $args['class'] ?? 'button' ),
			__( 'View details', 'hd' ),
			$glyph,
			__( 'View details', 'hd' )
		),
		$product,
		$args
	);
}
