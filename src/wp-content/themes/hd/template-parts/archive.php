<?php

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Str;

$post_page_id = get_option( 'page_for_posts' );
$term = get_queried_object();

$desc = '';
if ( $post_page_id && isset($term->ID) && $post_page_id == $term->ID ) { // is posts page
	$desc = post_excerpt( $term, null );
} else if ( isset($term->term_id)) {
	$desc = term_excerpt( $term, null );
}

// template-parts/parts/page-title.php
the_page_title_theme();

/** */
$display_type = 'items_1';
if (function_exists('get_field') && $term) {
    $display_type = get_field('display-type', $term);
}

?>
<section class="section archives archive-posts <?=$display_type?>">
    <div class="grid-page">
        <?php 
        echo '<div class="sidebars-col">';
        if ( is_active_sidebar( 'w-fix-sidebarleft-sidebar' ) ) :
            echo '<div class="fixed-sidebar left">';
            dynamic_sidebar( 'w-fix-sidebarleft-sidebar' );
            echo '</div>';
        endif;
        echo '</div>'; ?>
        <?php if ( Str::stripSpace( $desc ) ) : ?>
        <div class="grid-container width-extra">
            <div class="archive-desc heading-desc" data-glyph=""><?= $desc ?></div>
        </div>
        <?php endif; ?>
        <div class="main-col grid-container width-extra">
            <div class="grid-container width-extra">
                <!-- <div class="feature-posts">
                    <?php //get_template_part( 'template-parts/posts/feature-post' ); ?>
                </div> -->
                <?php get_template_part( 'template-parts/posts/grid-blog' ); ?>
            </div>
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
