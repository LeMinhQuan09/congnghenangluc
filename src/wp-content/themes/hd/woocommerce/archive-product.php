<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 */

defined('ABSPATH') || exit;

// header
get_header('shop');

$is_sidebar = FALSE;
if (is_active_sidebar('w-products-sidebar') && !is_search()) $is_sidebar = TRUE;

//the_page_title_theme();

// thanh điều hướng
get_template_part( 'template-parts/parts/archive-product-title', null, [ 'css_class' => 'archive-product' ] );
/**
 * Hook: woocommerce_before_main_content.
 *
 * @see woocommerce_breadcrumb - 20 - removed
 * @see WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');

/**
 * Hook: woocommerce_archive_description.
 *
 * @hooked woocommerce_taxonomy_archive_description - 10
 * @hooked woocommerce_product_archive_description - 10
 */
//do_action( 'woocommerce_archive_description' );

//woocommerce_product_loop_start();

if ( ! is_search() ) :
	if ( ( @is_shop() ) && is_active_sidebar( 'w-shop-sidebar' ) ) :
		wc_get_template_part( 'shop', 'product' );
	endif;

	// Product Attributes
	if ( is_active_sidebar( 'w-product-attributes-sidebar' ) ) :
		echo '<section class="section product-attributes"><div class="grid-container width-extra"><div class="product-attributes-inner">';
		dynamic_sidebar( 'w-product-attributes-sidebar' );
		echo '</div></div></section>';
	endif;
endif;

$object = get_queried_object();
$title = '';
if ( function_exists( 'is_shop' ) && is_shop() ) {
	$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
} else {
	$title = $object->label ?? '';
	if ( ! $title ) {
		$title = $object->name ?? '';
	}
}
if(is_product_category()){
    $current_term = get_queried_object();
    $id_taxonomy = get_queried_object()->term_id;
    // print_r($id_taxonomy);
}
?>
<section class="section archive-products">
    <div class="grid-page">
        <?php echo '<div class="sidebars-col">';
        if ( is_active_sidebar( 'w-fix-sidebarleft-sidebar' ) ) :
            echo '<div class="fixed-sidebar left">';
            dynamic_sidebar( 'w-fix-sidebarleft-sidebar' );
            echo '</div>';
        endif;
        echo '</div>'; ?>
        <div class="main-col grid-container width-extra">
            <?php
            /**
             * Hook: woocommerce_before_archive.
             */
            do_action('woocommerce_before_archive');
            ?>
            <div class="grid-x">
                <?php if (TRUE === $is_sidebar) : ?>
                <div class="cell sidebar-col medium-12 large-3">
                    <div class="sidebar--wrap">
                        <?php dynamic_sidebar('w-products-sidebar'); ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="archive-products-inner cell<?php if (TRUE === $is_sidebar) echo ' medium-12 large-9 has-sidebar'; ?>">
                    <!-- GET PRODUCT CAT -->
                    <?php
                        if(is_tax()){
                            $display_cat_child = get_field('display_cat_child','product_cat_'. $id_taxonomy);
                            $terms_tax = get_terms('product_cat',array(
                                'hide_empty' => false,
                                'parent' => $id_taxonomy,
                            ));
                            if($terms_tax){
                                foreach($terms_tax as $val) {
                                $val_id = get_term_by('id', $val->term_id ,'product_cat');
                                $icon_true = get_field('img_or_icon',$val_id); } ?>
                                <div class="productcat_wrapper">
                                    <div class="list_productcat swiper-section <?php if($icon_true == 1){ echo 'list_producat_icon'; } ?>">
                                        <?php 
                                        $_data = [];
                                        if($icon_true == 1){
                                            $_data["desktop"] = 8;
                                        } else {
                                            $_data["desktop"] = 8;
                                        }
                                        $_data["tablet"] = 3;
                                        $_data["mobile"] = 2;
                                        $_data["loop"] = true;
                                        $_data["navigation"] = true;
                                        $_data["autoplay"] = true;
                                        $_data["delay"] = 5000;
                                        $_data["speed"] = 700;
                                        $_data["smallgap"] = 15;
                                        $_data = json_encode($_data, JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
                                        ?>
                                        <div class="w-swiper swiper">
                                            <div class="swiper-wrapper" data-options='<?= $_data;?>'>
                                                <?php
                                                foreach($terms_tax as $term) {
                                                $cat_id = get_term_by('id', $term->term_id ,'product_cat');
                                                $img_or_icon = get_field('img_or_icon',$cat_id); ?>
                                                <div class="swiper-slide">
                                                    <a href="<?php echo get_term_link($term->term_id);?>" class="link">
                                                        <div class="cover">
                                                            <span class="after-overlay res scale ar-4-3">
                                                                <?php
                                                                $icon_productcat = get_field('icon_productcat', $cat_id );
                                                                $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true); 
                                                                $image_src = wp_get_attachment_image_src($thumbnail_id, 'full'); ?>
                                                                <?php if ($img_or_icon == 1) {
                                                                    if (!empty($icon_productcat) && isset($icon_productcat['url'])) { ?>
                                                                        <img src="<?php echo esc_url($icon_productcat['url']); ?>" alt="image">
                                                                    <?php } else { ?>
                                                                        <img src="<?php echo esc_url(get_field('icon_default', 'option')['url']); ?>" alt="image">
                                                                    <?php }
                                                                } else {
                                                                    if (!empty($image_src) && isset($image_src[0])) { ?>
                                                                        <img src="<?php echo esc_url($image_src[0]); ?>" alt="image">
                                                                    <?php } else { ?>
                                                                        <img src="<?php echo esc_url(get_field('img-default', 'option')['url']); ?>" alt="image">
                                                                    <?php }
                                                                } ?>
                                                            </span>
                                                        </div>
                                                        <p class="title"><?php echo $term->name; ?></p>
                                                    </a>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            <?php } else {
                            $parent_term_id = $current_term->parent;
                            if ($parent_term_id != 0) {
                                $parent_terms_tax = get_terms('product_cat', array(
                                    'hide_empty' => false,
                                    'parent' => $parent_term_id,
                                ));
                                if (!empty($parent_terms_tax) && !is_wp_error($parent_terms_tax)) {
                                    foreach ($parent_terms_tax as $parent_term) {
                                        //print_r($parent_term);
                                        $val_id = get_term_by('id', $parent_term ->term_id,'product_cat');
                                        $icon_true = get_field('img_or_icon',$val_id); } ?>
                                        <div class="productcat_wrapper">
                                            <div class="list_productcat swiper-section <?php if($icon_true == 1){ echo 'list_producat_icon'; } ?>">
                                                <?php 
                                                $_data = [];
                                                if($icon_true == 1){
                                                    $_data["desktop"] = 8;
                                                } else {
                                                    $_data["desktop"] = 8;
                                                }
                                                $_data["tablet"] = 3;
                                                $_data["mobile"] = 2;
                                                $_data["loop"] = true;
                                                $_data["navigation"] = true;
                                                $_data["autoplay"] = true;
                                                $_data["delay"] = 5000;
                                                $_data["speed"] = 700;
                                                $_data["smallgap"] = 15;
                                                $_data = json_encode($_data, JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
                                                ?>
                                                <div class="w-swiper swiper">
                                                    <div class="swiper-wrapper" data-options='<?= $_data;?>'>
                                                        <?php
                                                        foreach($parent_terms_tax as $term) {
                                                        $cat_id = get_term_by('id', $term->term_id ,'product_cat');
                                                        $img_or_icon = get_field('img_or_icon',$cat_id); ?>
                                                        <div class="swiper-slide">
                                                            <a href="<?php echo get_term_link($term->term_id);?>" class="link <?php if($term->term_id == $id_taxonomy){echo 'active';} ?>">
                                                                <div class="cover">
                                                                    <span class="after-overlay res scale ar-4-3">
                                                                        <?php
                                                                        $icon_productcat = get_field('icon_productcat', $cat_id );
                                                                        $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true); 
                                                                        $image_src = wp_get_attachment_image_src($thumbnail_id, 'full'); ?>
                                                                        <?php if ($img_or_icon == 1) {
                                                                            if (!empty($icon_productcat) && isset($icon_productcat['url'])) { ?>
                                                                                <img src="<?php echo esc_url($icon_productcat['url']); ?>" alt="image">
                                                                            <?php } else { ?>
                                                                                <img src="<?php echo esc_url(get_field('icon_default', 'option')['url']); ?>" alt="image">
                                                                            <?php }
                                                                        } else {
                                                                            if (!empty($image_src) && isset($image_src[0])) { ?>
                                                                                <img src="<?php echo esc_url($image_src[0]); ?>" alt="image">
                                                                            <?php } else { ?>
                                                                                <img src="<?php echo esc_url(get_field('img-default', 'option')['url']); ?>" alt="image">
                                                                            <?php }
                                                                        } ?>
                                                                    </span>
                                                                </div>
                                                                <p class="title"><?php echo $term->name; ?></p>
                                                            </a>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    <?php }
                                }
                            }
                        } ?>
                    <?php $desc_productcat = get_field('desc_productcat','product_cat_'. $id_taxonomy);
                    //print_r($desc_productcat);
                    if($desc_productcat){
                        echo '<div class="desc_produccat">';
                        echo $desc_productcat;
                        echo '</div>';
                    } ?>
                    <!-- END GET PRODUCT CAT -->
                    <?php
                    if ( woocommerce_product_loop() ) :

                        /**
                         * Hook: woocommerce_before_shop_loop.
                         *
                         * @see woocommerce_output_all_notices - 8
                         * @see woocommerce_result_count - 20
                         * @see woocommerce_catalog_ordering - 30
                         */
                        do_action( 'woocommerce_before_shop_loop' );

                        $i = 0;
                        $term_banner = null;
                        $term_banner_link = null;

                        if ( !is_search() && is_post_type_archive( 'product' ) && 0 === absint( get_query_var( 'paged' ) )) {
                            $shop_page = get_post( wc_get_page_id( 'shop' ) );
                            if ( $shop_page && function_exists('get_fields') ) {
                                $ACF = get_fields($shop_page->ID);

                                $term_banner = $ACF['term_banner'] ?? null;
                                $term_banner_link = $ACF['term_banner_link'] ?? null;

                                if ($term_banner && !$term_banner_link) {
                                    $term_banner_link = get_permalink( wc_get_page_id( 'shop' ) );
                                }
                            }
                        }
                        else if ( !is_search() && is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) )) {
                            $term = get_queried_object();
                            if ( $term && function_exists( 'get_fields' ) ) {

                                $ACF = get_fields($term);

                                $term_banner = $ACF['term_banner'] ?? null;
                                $term_banner_link = $ACF['term_banner_link'] ?? null;

                                if ($term_banner && !$term_banner_link) {
                                    $term_banner_link = get_term_link($term, 'product_cat');
                                }
                            }
                        }
                        ?>
                        <div class="grid-products grid-x columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?><?php if ( is_search() ) echo ' main-content-search'; ?>">
                        <?php
                        //...
                        if ( wc_get_loop_prop( 'total' ) ) :
                            // Start the Loop.
                            while (have_posts()) : the_post();

                                //...
    //                            if (0 === $i && $term_banner) {
    //                                echo '<div class="cell banner-cell">';
    //                                echo '<figure>';
    //                                if ($term_banner_link) echo '<a class="after-overlay _blank" href="' . $term_banner_link . '" title>';
    //                                echo wp_get_attachment_image($term_banner, 'medium');
    //                                if ($term_banner_link) echo '</a>';
    //                                echo '</figure>';
    //                                echo '</div>';
    //                            }

                                echo '<div class="cell cell-' . $i . '">';
                                wc_get_template_part('content', 'product');
                                echo '</div>';

                                // End the loop.
                                ++$i;
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                        </div>
                        <?php

                        /**
                         * Hook: woocommerce_after_shop_loop.
                         *
                         * @see woocommerce_pagination - 10
                         */
                        do_action('woocommerce_after_shop_loop');
                    else :
                        // @see wc_no_products_found - 10
                        do_action('woocommerce_no_products_found');
                    endif;

                    ?>
                </div>
        </div>
        <?php

        // echo '<div class="grid-container width-extra">';
        //     /**
        //      * Hook: woocommerce_after_shop.
        //      *
        //      * @see woocommerce_output_recently_viewed_products - 19
        //      */
        //     do_action('woocommerce_after_shop');
        // echo '</div>';

        /**
         * Hook: woocommerce_archive_description.
         *
         * @see woocommerce_taxonomy_archive_description - 10
         * @see woocommerce_product_archive_description - 10
         */
        //do_action('woocommerce_archive_description');
        ?>
        </div>
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

/**
 * Hook: woocommerce_after_main_content.
 *
 */
do_action('woocommerce_after_main_content');
// footer
get_footer('shop');