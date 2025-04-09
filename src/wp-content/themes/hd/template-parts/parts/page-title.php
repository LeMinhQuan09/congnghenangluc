<?php

\defined( '\WPINC' ) || die;

$_class        = '';
$breadcrumb_bg = get_theme_mod_ssl( 'breadcrumb_bg_setting' );
if ( $breadcrumb_bg ) {
    $_class .= ' has-background';
}

if ( isset( $args['css_class'] ) ) {
    $_class .= ' ' . trim( $args['css_class'] );
}

$object = get_queried_object();

$title = '';
if ( function_exists( 'is_shop' ) && is_shop() ) {
    $title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
} else {
    $title = $object->label ?? '';
    if ( ! $title ) {
        $title = $object->name ?? '';
    }
    if ( ! $title ) {
        $title = $object->post_title ?? '';
    }
}

if ( is_search() ) {
    $title = sprintf( __( 'Search Results for: %s', 'hd' ), get_search_query() );
}

if ( is_single() ) {
    $primary_term = primary_term( $object );
    $title        = $primary_term->name ?? '';
}

if ( is_author() ) {
    $author_data = $object->data;
    $title       = $author_data->display_name ?? $author_data->user_nicename ?? $author_data->user_login;
}

$tag = ( is_single() or is_page() ) ? 'div' : 'h1';

?>
<section class="section section-title<?= $_class ?>" tabindex="-1">
    <div class="title-bg parallax-bg">
        <span class="cover"><img src="<?=$breadcrumb_bg?>" alt="<?php echo esc_attr( $title )?>"></span>
    </div>
    <div class="archive-header">
        <div class="title-inner">
            <<?=$tag?> class="title"><?php echo $title; ?></<?=$tag?>>
        </div>
        <div class="breadcrumbs-container">
			<?php
			if ( function_exists( 'the_breadcrumbs' ) ) :
				the_breadcrumbs();
            elseif ( function_exists( 'woocommerce_breadcrumb' ) ) :
				woocommerce_breadcrumb();
            elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) :
				rank_math_the_breadcrumbs();
			endif;
			?>
        </div>
    </div>
</section>
