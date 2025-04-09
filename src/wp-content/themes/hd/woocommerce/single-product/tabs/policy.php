<?php

\defined( '\WPINC' ) || die;// Exit if accessed directly.

if (!function_exists('get_field')) {
    return;
}

global $product;

$heading = apply_filters( 'woocommerce_product_policy_heading', __( 'Chính sách', 'hd' ) );

?>
<div class="product-policy-inner product-tabs-content">
    <?php if ( $heading ) : ?>
    <h2 class="tab-heading-title"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>
    <?php echo $policy = get_field('policy', $product->get_id());; ?>
</div>
