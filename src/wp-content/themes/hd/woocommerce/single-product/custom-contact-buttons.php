<?php

use Webhd\Helpers\Str;

\defined( 'ABSPATH' ) || exit;

global $post;

$hotline = get_theme_mod_ssl( 'hotline_setting' );
if ( $hotline ) :
	$_tel = Str::stripSpace($hotline);
?>
<div class="custom-contact-button hide">
	<div class="group-inner">
		<a rel="nofollow" href="tel:<?=$_tel?>" title="<?php echo esc_attr( $hotline )?>" class="btn contact-product-button"><?php echo __( 'Liên hệ tư vấn', 'hd' ) ?></a>
	</div>
</div>
<?php endif;
