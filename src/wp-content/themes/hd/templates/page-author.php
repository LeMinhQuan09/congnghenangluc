<?php

/**
 * The template for displaying author page
 * Template Name: Author
 * Template Post Type: page
 */

use Webhd\Helpers\Cast;

\defined( '\WPINC' ) || die;

get_header();

echo '<main role="main">';

if (have_posts()) the_post();
if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

$ID = $post->ID ?? false;
$ACF = Cast::toObject( get_fields( $ID ) );

$sub_title =  $ACF->acf_sub_title ?? '';
$name =  $ACF->acf_name ?? '';
$title = $ACF->acf_title ?? '';
$thumbs = $ACF->acf_thumbs ?? '';
$css_class = $ACF->acf_css_class ?? '';

?>
<section class="section single single-nhasanglap-page <?=$css_class?>">
	<div class="grid-container">
        <?php if ( $sub_title ) : ?>
        <div class="page-sub-title"><?= $sub_title ?></div>
        <?php endif; ?>
        <h1 class="page-heading-title h2"><?= get_the_title(); ?></h1>
		<div class="grid-x">
            <?php if ( $thumbs ) : ?>
			<div class="cell thumbnails">
				<span class="cover after-overlay">
                    <?php echo wp_get_attachment_image( $thumbs, 'post-thumbnail' );?>
                </span>
			</div>
            <?php endif; ?>
			<div class="cell contents">
				<div class="contents-inner">
                    <?php
                    // post content
                    the_content();

                    get_template_part( 'template-parts/parts/inline-share' );

                    ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php

echo '</main>';

get_footer('about');
