<?php

\defined( '\WPINC' ) || die;

global $post;

if ( have_posts() ) the_post();
if ( post_password_required() ) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

//$is_sidebar = FALSE;
//if ( is_active_sidebar( 'w-news-sidebar' ) && !is_search() ) $is_sidebar = TRUE;

// template-parts/parts/page-title.php
the_page_title_theme();

?>
<section class="section single single-post">
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
            <div class="grid-container width-extra">
                <?php //get_template_part('template-parts/parts/sharing'); ?>
                <div class="single-content">
                    <div class="col-content cell">
                        <?php if ( has_post_thumbnail( $post ) ) : ?>
                        <!-- <div class="single-thumbnail">
                            <?php //echo get_the_post_thumbnail( $post, 'post-thumbnail' )?>
                        </div> -->
                        <?php endif; ?>
                        <div class="meta">
                            <div class="time" data-glyph=""><?php echo humanize_time(); ?></div>
                            <?php echo post_terms( $post->ID ); ?>
                            <?php echo post_author( $post->ID ); ?>

                            <?php if ( function_exists( 'kk_star_ratings' ) ) echo kk_star_ratings(); ?>
                        </div>
                        <h1 class="h3 single-title"><?php echo get_the_title( $post ); ?></h1>
                        <?php //get_template_part('template-parts/parts/inline-share'); ?>
                        <?php echo post_excerpt( $post, 'excerpt', true ); ?>

                        <div class="content clearfix">
                            <?php
                            // post content
                            the_content();

                            the_hashtags();

                            // after post sidebar
                            if ( is_active_sidebar( 'w-after-post-sidebar' ) ) {
                                echo '<div class="after-posts">';
                                dynamic_sidebar( 'w-after-post-sidebar' );
                                echo '</div>';
                            }
                            echo '<div class="form_quote" style="margin-top: 15px">';
                            echo '<p class="title">Câu hỏi</p>';
                            echo do_shortcode('[contact-form-7 id="a72a328" title="Form Đăng ký báo giá"]');
                            echo '</div>';

                            //get_template_part( 'template-parts/parts/inline-share' );
                            get_template_part( 'template-parts/parts/pagination-nav' );

                            get_template_part( 'template-parts/parts/upseo' );

                            // If comments are open or we have at least one comment, load up the comment template.
                            the_comment_html();

                            ?>
                        </div>
                    </div>
                    <?php //if (TRUE === $is_sidebar) : ?>
                    <!--<div class="col-sidebar cell">
                        <div class="sidebar--wrap">
                            <?php /*dynamic_sidebar('w-news-sidebar'); */?>
                        </div>
                    </div>-->
                    <?php //endif; ?>
                </div>
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
