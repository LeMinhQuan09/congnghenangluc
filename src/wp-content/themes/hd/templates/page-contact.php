<?php

/**
 * The template for displaying contact page
 * Template Name: Liên hệ
 * Template Post Type: page
 */

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;

get_header('contact');

global $post;

echo '<main role="main">';

if (have_posts()) the_post();
if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

// template-parts/parts/page-title.php
the_page_title_theme();

$ID = $post->ID ?? false;
$ACF = Cast::toObject( get_fields( $ID ) );

$contact_title = $ACF->contact_title ?? '';
$contact_desc = $ACF->contact_desc ?? '';
$contact_form = $ACF->contact_form ?? '';
$contact_map = $ACF->contact_map ?? '';
$css_class = $ACF->css_class ?? '';
$sub_title = $ACF->sub_title ?? '';
$second_title = $ACF->second_title ?? '';
$title_icon = $ACF->title_icon ?? '';
$working_hours = $ACF->working_hours ?? '';
$email = $ACF->email ?? '';
$phone = $ACF->phone ?? '';
$branding = $ACF->branding ?? [];
$desc = $ACF->desc ?? '';

?>
<section class="section single single-post single-page single-contactpage <?=$css_class?>">
    <div class="grid-page">
        <?php 
        echo '<div class="sidebars-col">';
        if ( is_active_sidebar( 'w-fix-sidebarleft-sidebar' ) ) :
            echo '<div class="fixed-sidebar left">';
            dynamic_sidebar( 'w-fix-sidebarleft-sidebar' );
            echo '</div>';
        endif;
        echo '</div>'; ?>
        <div class="main-col grid-container width-extra">
            <div class="single-content">
                <?php if ( $contact_title ) : ?>
                <div class="top-info">
                    <?php if ( $title_icon ) echo $title_icon; ?>
                    <h1 class="heading-title"><?=$contact_title?></h1>
                    <?php if ( $desc ) : ?>
                    <div class="desc"><?=$desc?></div>
                    <?php endif; ?>
                </div>
                <?php endif;?>

                <div class="form-info">
                    <div class="form-inner">
                        <?php if ( $sub_title ) : ?>
                        <p class="form-sub-title"><?=$sub_title?></p>
                        <?php endif; ?>

                        <?php if ( $second_title ) : ?>
                        <p class="form-title"><?=$second_title?></p>
                        <?php endif; ?>

                        <?php
                        if ( $contact_form ) :
                            $form = get_post( $contact_form );
                            echo do_shortcode( '[contact-form-7 id="' . $form->ID . '" title="' . esc_attr( $form->post_title ) . '"]' );
                        endif;
                        ?>
                    </div>
                    <?php if ( $contact_desc ) : ?>
                    <div class="form-card">
                        <div class="form-card-inner">

                            <?=$contact_desc?>

                            <?php echo post_excerpt( $post, 'short', false ); ?>

                            <?php if ( $working_hours ) echo '<p class="working-hours">' . $working_hours . '</p>'; ?>
                            <?php if ( $email ) echo '<a class="line" href="mailto:' . $email . '" title="' . $email . '">Email: ' . $email . '</a>'; ?>
                            <?php if ( $phone ) echo '<a class="line" href="tel:' . $phone . '" title="' . $phone . '">Điện thoại: ' . $phone . '</a>'; ?>

                            <?php echo do_shortcode( '[social_menu]' );?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="branding-info">
                    <div class="content-inner">
                        <?php the_content(); ?>
                    </div>

                    <?php if ( $branding ) : ?>
                    <div class="branding-inner">
                        <?php
                        foreach ( $branding as $brand ) :
                            $re_title = $brand['re_title'] ?? '';
                            $re_address = $brand['re_address'] ?? '';
                            $re_phone = $brand['re_phone'] ?? '';
                            $re_email = $brand['re_email'] ?? '';
                            $re_bg = $brand['re_bg'] ?? '';

                            $_bg = '';
                            if ( $re_bg ) $_bg = ' style="background-image:url(' . attachment_image_src( $re_bg, 'medium' ) . '")';
                        ?>
                        <div class="item" <?=$_bg?>>
                            <?php if ( $re_title ) : ?>
                            <p class="brand-title"><?=$re_title?></p>
                            <?php endif; ?>

                            <?php if ( $re_address ) : ?>
                            <address><?=$re_address?></address>
                            <?php endif; ?>

                            <?php if ( $re_email ) echo '<a class="line" href="mailto:' . $re_email . '" title="' . $re_email . '">' . $re_email . '</a>'; ?>
                            <?php if ( $re_phone ) echo '<a class="line" href="tel:' . $re_phone . '" title="' . $re_phone . '">' . $re_phone . '</a>'; ?>

                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            
            <?php if ( Str::stripSpace( $contact_map, false ) ) : ?>
            <div class="gmap-section">
                <div class="res res-map">
                    <?php echo $contact_map; ?>
                </div>
            </div>
            <?php endif; ?>
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
<?php

echo '</main>';

get_footer('contact');
