<?php
/**
 * The template for displaying homepage
 * Template Name: Trang chủ
 * Template Post Type: page
 */

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Str;

get_header('homepage');

global $post;

echo '<main role="main">';

$content = $post->post_content;
if ( Str::stripSpace( $content ) ) {
	echo '<section class="section homepage-section">';
	echo '<div class="grid-container">';
	echo '<div class="content clearfix">';

	// post content
	the_content();
	echo '</div></div>';
	echo '</section>';
} else {
	the_content();
}

if (have_posts()) the_post();
if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;
echo '<div class="grid-page">';
echo '<div class="sidebars-col">';
if ( is_active_sidebar( 'w-fix-sidebarleft-sidebar' ) ) :
	echo '<div class="fixed-sidebar left">';
	dynamic_sidebar( 'w-fix-sidebarleft-sidebar' );
	echo '</div>';
endif;
echo '</div>';

if ( is_active_sidebar( 'w-homepage-top-sidebar' ) || is_active_sidebar( 'w-homepage-center-sidebar' ) || is_active_sidebar( 'w-homepage-bottom-sidebar' ) ) {
	echo '<div class="main-col grid-container width-extra">';
}
// top
if ( is_active_sidebar( 'w-homepage-top-sidebar' ) ) :
	echo '<div class="homepage-inner homepage-top">';
	dynamic_sidebar( 'w-homepage-top-sidebar' );
	echo '</div>';
endif;

// center
if ( is_active_sidebar( 'w-homepage-center-sidebar' ) ) :
	echo '<div class="homepage-inner homepage-center">';
	dynamic_sidebar( 'w-homepage-center-sidebar' );
	echo '</div>';
endif;

// bottom
if ( is_active_sidebar( 'w-homepage-bottom-sidebar' ) ) :
	echo '<div class="homepage-inner homepage-bottom">';
	dynamic_sidebar( 'w-homepage-bottom-sidebar' );
	echo '</div>';
endif;

if ( is_active_sidebar( 'w-homepage-top-sidebar' ) || is_active_sidebar( 'w-homepage-center-sidebar' ) || is_active_sidebar( 'w-homepage-bottom-sidebar') ) {
	echo '</div>';
}
// Fix Sidebar Right
echo '<div class="sidebars-col">';
if ( is_active_sidebar( 'w-fix-sidebarright-sidebar' ) ) :
	echo '<div class="fixed-sidebar right">';
	dynamic_sidebar( 'w-fix-sidebarright-sidebar' );
	echo '</div>';
endif;
echo '</div>';
echo '</div>';
echo '</main>';

get_footer('homepage');
