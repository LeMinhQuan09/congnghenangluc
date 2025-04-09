<?php

\defined( '\WPINC' ) || die;

?>
<div class="grid-news-outer">
    <div class="col-sidebar"></div>
    <div class="col-content">
        <?php if ( have_posts() ) : ?>
        <div class="section grid-posts grid-x">
            <?php

            // Start the Loop.
            while ( have_posts() ) : the_post();

                echo "<div class=\"cell\">";
                get_template_part( 'template-parts/posts/loop-blog' );
                echo "</div>";

                // End the loop.
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
        <?php
            // Previous/next page navigation.
            pagination_links();
        else :
            get_template_part( 'template-parts/parts/content-none' );
        endif;
        ?>
    </div>
</div>
