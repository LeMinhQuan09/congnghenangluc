<?php
defined( 'ABSPATH' ) || exit;

$terms = $args['terms'] ?? null;
if ($terms) :

?>
<h2 class="h5"><?php echo __( 'Chọn chi nhánh', 'hd' ) ?></h2>
<div class="location-options select-options select-style">
	<div class="trigger">
		<?php
		$_cookie = $_COOKIE['location-options'] ?? false;
		if ( $_cookie ) :
			$_term = get_term_by( 'id', (int) $_cookie, 'location_category' );
			if ( $_term ) $trigger_name = $_term->name;
			unset( $_term );
		else :
			$trigger_name = $terms[0]->name;

            if ( ! headers_sent() ) :
                setcookie('location-options', $terms[0]->term_id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
            endif;

		endif;
		echo '<a href="javascript:;" data-toggle="location-options-dropdown" class="trigger-pane">' . $trigger_name . '</a>';
		//var_dump($_cookie);
		?>
	</div>
	<div class="dropdown-pane" id="location-options-dropdown" data-dropdown data-auto-focus="true">
		<ul>
			<?php foreach ( $terms as $term ) :
				$class = ' class="lvl0"';
				if ( $_cookie && (int) $_cookie === $term->term_id )
					$class = ' class="active lvl0"';
			?>
			<li<?=$class?> data-src="<?=$term->term_id?>"><?=$term->name?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>
