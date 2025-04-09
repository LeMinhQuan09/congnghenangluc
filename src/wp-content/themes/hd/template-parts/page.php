<?php

\defined( '\WPINC' ) || die;

global $post;

if (have_posts()) the_post();
if (post_password_required()) :
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
endif;

// template-parts/parts/page-title.php
the_page_title_theme();

?>
<section class="section single single-post single-page">
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
			<?php //get_template_part('template-parts/parts/sharing'); ?>
			<div class="single-content">
				<div class="col-content cell">
					<h1 class="h3 single-title text-center hide"><?php echo get_the_title( $post ); ?></h1>
					<?php echo post_excerpt( $post, 'excerpt', true ); ?>
					<div class="content clearfix">
						<?php

						// post content
						$content = get_the_content();
						if(!empty($content)){
							the_content();
						} else {
							echo '<p>Đang cập nhật nội dung...</p>';
						}

						// after post sidebar
						if ( is_active_sidebar( 'w-after-post-sidebar' ) ) {
							dynamic_sidebar( 'w-after-post-sidebar' );
						}

						//get_template_part( 'template-parts/parts/inline-share' );
						get_template_part( 'template-parts/parts/upseo' );

						// If comments are open or we have at least one comment, load up the comment template.
						the_comment_html();

						?>
					</div>
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
