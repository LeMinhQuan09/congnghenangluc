<?php
/**
 * The template for displaying about page
 * Template Name: Giới thiệu
 * Template Post Type: page
 */

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;

get_header('about');

echo '<main role="main">';

if (have_posts()) the_post();
if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

// template-parts/parts/page-title.php
the_page_title_theme();

$ID = $post->ID ?? false;
$ACF = \get_fields( $ID ) ?? '';

$acf_title = $ACF['acf_title'] ?? '';
$acf_content_extra = $ACF['acf_content_extra'] ?? '';
$css_class = $ACF['css_class'] ?? '';

?>
<section class="section single single-post single-page single-about-page <?=$css_class?>">
    <div class="grid-container">
        <div class="col-content">
            <?php if ( $acf_title ) : ?>
            <h2 class="heading-title h1"><?=$acf_title?></h2>
            <?php endif; ?>
            <?php if ( Str::stripSpace( $post->post_content ) ) echo '<div class="content clearfix">'; ?>
		        <?php the_content(); ?>
	        <?php if ( Str::stripSpace( $post->post_content ) ) echo '</div>'; ?>

            <?php if ($acf_content_extra) : ?>
            <div class="teams-outer">
                <div class="teams">
                    <?php foreach ($acf_content_extra as $item) :
                        $re_img = $item['re_img'] ?? '';
                        $re_title = $item['re_title'] ?? '';
                        $re_content = $item['re_content'] ?? '';
                    ?>
                    <div class="teams-inner">
                        <?php if ( $re_img ) : ?>
                        <div class="team-bg">
                            <span class="cover after-overlay">
                                <?php echo wp_get_attachment_image($re_img, 'large');?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <div class="team-content">
                            <?php if ( $re_title ) : ?>
                            <h2><?php echo $re_title; ?></h2>
                            <?php endif; ?>
                            <?php if ( $re_content ) : ?>
                            <div class="desc"><?php echo $re_content; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php

echo '</main>';

get_footer('about');
