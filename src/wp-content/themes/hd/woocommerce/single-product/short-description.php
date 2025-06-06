<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 */

use Webhd\Helpers\Str;

\defined( 'ABSPATH' ) || exit;

global $post;

$excerpt = false;
if (is_active_sidebar('w-product-excerpt-sidebar')) :
    $excerpt = true;
endif;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
if ( $short_description || $excerpt ) :

?>
<div class="woocommerce-product-details__short-description">
    <?php
    if (Str::stripSpace($short_description)) :
        echo '<div class="woocommerce-description">';
        echo $short_description; // WPCS: XSS ok.
        echo '</div>';
    endif;

    if ($excerpt) :
        echo '<div class="woocommerce-excerpt">';
        dynamic_sidebar('w-product-excerpt-sidebar');
        echo '</div>';
    endif;
    ?>
</div>
<?php endif;