<?php

if ( have_posts() ) :
	$i = 0;

	// Load slides loop.
	while ( have_posts() && $i < 1) : the_post();
		get_template_part('template-parts/posts/loop-blog', null, [ 'thumbnail_size' => 'post-thumbnail', 'no_scale' => true ] );
		++ $i;
	endwhile;
	wp_reset_postdata();
endif;
