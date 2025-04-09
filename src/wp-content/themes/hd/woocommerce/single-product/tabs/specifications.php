<?php

\defined( '\WPINC' ) || die;// Exit if accessed directly.

if (!function_exists('get_field')) {
    return;
}

global $product;

$heading = apply_filters( 'woocommerce_product_specifications_heading', __( 'Thông số kỹ thuật', 'hd' ) );

?>
<div class="product-specifications-inner product-tabs-content">
    <?php if ( $heading ) : ?>
        <h2 class="tab-heading-title"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>
    <?php echo $components = get_field('specifications', $product->get_id());; ?>
</div>