<?php

\defined( '\WPINC' ) || die;// Exit if accessed directly.

if (!function_exists('get_field')) {
    return;
}

global $product;

$heading = apply_filters( 'woocommerce_product_certificate_heading', __('Chứng chỉ', 'hd') );

?>
<div class="product-certificate-inner product-tabs-content">
    <?php if ( $heading ) : ?>
        <h2 class="tab-heading-title"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>
    <?php echo $certificate = get_field('certificate', $product->get_id());; ?>
</div>
