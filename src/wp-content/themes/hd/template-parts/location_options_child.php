<?php

defined( 'ABSPATH' ) || exit;

?>
<div class="location-options-child select-options-child select-style">
	<div class="trigger">
        <a href="javascript:;" data-toggle="location-options-child-dropdown" class="trigger-pane">-- Phường/xã --</a>
	</div>
	<div class="dropdown-pane" id="location-options-child-dropdown" data-dropdown data-auto-focus="true">
		<ul>
            <?php
            $_cookie = $_COOKIE['location-options'] ?? false;
            if ( $_cookie ) :
	            $terms_child = get_terms(
		            [
			            'parent' => (int) $_cookie,
			            'taxonomy'   => 'location_category',
			            'hide_empty' => false,
			            'order'      => 'ASC',
			            'orderby'    => 'meta_value',
			            'meta_key'   => 'term_order',
		            ]
	            );

                foreach ( $terms_child as $term ) :
				    $class = ' class="lvl1"';
			?>
            <li<?=$class?> data-src="<?=$term->term_id?>"><?=$term->name?></li>
			<?php endforeach; endif; ?>
		</ul>
	</div>
</div>
