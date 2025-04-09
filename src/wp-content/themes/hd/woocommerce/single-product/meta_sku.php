<?php

defined( 'ABSPATH' ) || exit;

global $product;

if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) :
?>
    <div class="product_meta product_sku">
        <?php do_action( 'woocommerce_product_meta_start' ); ?>
        <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'hd' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
        <?php do_action( 'woocommerce_product_meta_end' ); ?>
    </div>
<?php endif;
