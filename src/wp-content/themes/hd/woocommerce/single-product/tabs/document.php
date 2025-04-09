<?php

\defined( '\WPINC' ) || die;// Exit if accessed directly.

if (!function_exists('get_field')) {
    return;
}

global $product;

$heading = apply_filters( 'woocommerce_product_document_heading', __('Tài liệu', 'hd') );

?>
<div class="product-document-inner product-tabs-content">
    <?php if ( $heading ) : ?>
        <h2 class="tab-heading-title"><?php echo esc_html( $heading ); ?></h2>
    <?php endif; ?>
    <?php echo $document = get_field('document', $product->get_id());; ?>
</div>
