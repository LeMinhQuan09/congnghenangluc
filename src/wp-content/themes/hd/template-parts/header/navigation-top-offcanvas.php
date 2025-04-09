<?php

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Url;

/**
 * Displays navigation mobile
 *
 * @package WordPress
 */

$txt_logo = get_option('blogname');
//$img_logo = get_theme_mod_ssl('alternative_logo');
$img_logo = false;

if (!$img_logo) :
    $html = sprintf('<a href="%1$s" class="mobile-logo-link" rel="home">%2$s</a>', Url::home(), $txt_logo);
else :
    $image = '<img src="' . $img_logo . '" alt="mobile logo">';
    $html = sprintf('<a href="%1$s" class="mobile-logo-link" rel="home">%2$s</a>', Url::home(), $image);
endif;

?>
<div class="off-canvas position-top white-color" id="offCanvasMenu" data-off-canvas data-content-scroll="false">
    <div class="menu-heading-outer">
        <button class="menu-lines" aria-label="Close" type="button" data-close>
            <span class="line line-1"></span>
            <span class="line line-2"></span>
        </button>
        <div class="title-bar-title"><?php echo $html; ?></div>
        <?php
        if (is_active_sidebar('w-language-sidebar'))
            dynamic_sidebar('w-language-sidebar');
        ?>
    </div>
    <div class="menu-outer">
        <?php echo do_shortcode('[inline-search]'); ?>
        <?php echo mobile_nav(); ?>
    </div>
</div>
