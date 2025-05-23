<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var object $cross_sells
 */
if ( $cross_sells ) :

?>
<div class="cross-sells">
    <?php
    $heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) );
    if ( $heading ) :
    ?>
        <h2 class="heading-title h3"><span><?php echo esc_html( $heading ); ?></span></h2>
    <?php endif; ?>
    <div class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
        <?php foreach ( $cross_sells as $cross_sell ) : ?>
            <?php
                $post_object = get_post( $cross_sell->get_id() );
                setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
                wc_get_template_part( 'content', 'product' );
            ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
endif;
wp_reset_postdata();