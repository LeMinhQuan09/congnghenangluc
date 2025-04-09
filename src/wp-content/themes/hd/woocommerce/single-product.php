<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 */

defined('ABSPATH') || exit;

get_header('shop');

//the_page_title_theme();
get_template_part( 'template-parts/parts/single-product-title', null, [ 'css_class' => 'single-product' ] );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @see woocommerce_breadcrumb - 20 - removed
 * @see WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');

?>
<section class="section single single-product single-section">
    <div class="grid-page">
        <?php 
        echo '<div class="sidebars-col">';
        if ( is_active_sidebar( 'w-fix-sidebarleft-sidebar' ) ) :
            echo '<div class="fixed-sidebar left">';
            dynamic_sidebar( 'w-fix-sidebarleft-sidebar' );
            echo '</div>';
        endif;
        echo '</div>'; ?>
        <?php
        /* Start the Loop */
        while (have_posts()) : the_post();
            setup_postdata($GLOBALS['post_product'] = &$post);
            wc_get_template_part('content', 'single-product');

            // end of the loop.
        endwhile;
        wp_reset_postdata();
        ?>
        <?php 
        echo '<div class="sidebars-col">';
        if ( is_active_sidebar( 'w-fix-sidebarright-sidebar' ) ) :
            echo '<div class="fixed-sidebar right">';
            dynamic_sidebar( 'w-fix-sidebarright-sidebar' );
            echo '</div>';
        endif;
        echo '</div>'; ?>
    </div>
</section>
<?php

get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
