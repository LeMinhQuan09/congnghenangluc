<?php

\defined( '\WPINC' ) || die;

$term = get_queried_object();

// template-parts/parts/page-title.php
the_page_title_theme();


?>
<section class="section archives archive-posts archive-author">
    <div class="grid-container width-extra">
        <?php
        get_template_part( 'template-parts/posts/grid-author' );

        ?>
    </div>
</section>
