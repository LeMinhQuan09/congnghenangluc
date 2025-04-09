<?php

\defined( '\WPINC' ) || die;

$title = '';
$_class = '';

if ( isset( $args['css_class'] ) ) {
	$_class .= ' ' . trim( $args['css_class'] );
}

$breadcrumb_bg = get_theme_mod_ssl( 'breadcrumb_bg_setting' );
$term_banner_link = '';

$object = get_queried_object();
if ( 'product' == $object->post_type ) {
    $primary_term = primary_term( $object->ID, 'product_cat' );

	if ( 'product_cat' == $primary_term->taxonomy ) {
		$title = $primary_term->name ?? '';
		$ACF = \get_fields( $primary_term );

		$term_banner = $ACF['term_banner'] ?? '';
		$term_banner_link = $ACF['term_banner_link'] ?? '';

		if ( $term_banner ) {
			$breadcrumb_bg = wp_get_attachment_image_src( $term_banner, 'full' )[0];
		}
	}
}

if ( $title ) :

?>
<section class="section section-title<?= $_class ?>" tabindex="-1">
    <?php if ( $breadcrumb_bg ) : ?>
    <div class="title-bg parallax-bg">
        <span class="cover"><img src="<?=$breadcrumb_bg?>" alt="<?php echo esc_attr( $title )?>"></span>
        <?php if ( $term_banner_link ) : ?>
        <a class="title-bg-trigger" href="<?=$term_banner_link?>" aria-label="<?php echo esc_attr( $title )?>"></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="archive-header">
        <div class="title-inner">
            <div class="title h3"><?php echo $title; ?></div>
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
<?php endif;
