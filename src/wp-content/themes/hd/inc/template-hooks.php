<?php
/**
 * Template Filters
 *
 * @author WEBHD
 */
\defined('\WPINC') || die;

use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;
use Webhd\Helpers\Url;
use Webhd\Themes\SVG_Icons;

// -------------------------------------------------------------
// wp_head
// -------------------------------------------------------------

add_action('wp_head', '__critical_inline_css', 1);
add_action('wp_head', '__extra_header', 10);

/**
 * @return void
 */
function __critical_inline_css() {
	$inline_css = '';
	if ( $inline_css ) {
		echo '<style id="w-inline-css">' . $inline_css . '</style>';
	}
}

// -------------------------------------------------------------

/**
 * @return void
 */
function __extra_header() {
	//echo '<link rel="preconnect" href="https://fonts.gstatic.com">';

	$theme_color = get_theme_mod_ssl( 'theme_color_setting' );
	if ( $theme_color ) {
		echo '<meta name="theme-color" content="' . $theme_color . '" />';
	}

	$fb_appid = get_theme_mod_ssl( 'fb_menu_setting' );
	if ( $fb_appid ) {
		echo '<meta property="fb:app_id" content="' . $fb_appid . '" />';
	}
}

// -------------------------------------------------------------
// wp_footer
// -------------------------------------------------------------

//add_action('wp_footer', '__extra_footer', 99);
//function __extra_footer() {}

// -------------------------------------------------------------
// off_canvas
// -------------------------------------------------------------

add_action( 'off_canvas', '__off_canvas_button', 10 );

/**
 * @return void
 */
function __off_canvas_button() {
	// mobile navigation
	$position = get_theme_mod_ssl( 'offcanvas_menu_setting' );
	if ( ! in_array( $position, [ 'left', 'right', 'top', 'bottom' ] ) ) {
		$position = 'left';
	}

	get_template_part( 'template-parts/header/navigation-' . $position . '-offcanvas' );
}

// -------------------------------------------------------------
// before_header
// -------------------------------------------------------------

// before_header actions
add_action( 'before_header', '__before_header_extra', 14 );

/**
 * @return void
 */
function __before_header_extra() {
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}

	?>
    <a class="skip-link screen-reader-text" href="#main-content"><?php echo __( 'Skip to main content', 'hd' ); ?></a>
	<?php
}

// -------------------------------------------------------------
// header
// -------------------------------------------------------------

// header
add_action( 'header', '__topheader', 10 );
add_action( 'header', '__header', 11 );
add_action( 'header', '__bottomheader', 12 );

/**
 * @return void
 */
function __topheader() {

	$top_header_cols      = (int) get_theme_mod_ssl( 'top_header_setting' );
	$top_header_container = (int) get_theme_mod_ssl( 'top_header_container_setting' );

    if ( $top_header_cols > 0 ) :

    ?>
    <div class="top-header" id="top-header">
	    <?php
        if ( $top_header_container ) echo '<div class="grid-container width-extra">';
        else echo '<div class="grid-container width-extra widescreen">';

        for ( $i = 1; $i <= $top_header_cols; $i++ ) :
            if ( is_active_sidebar( 'w-topheader-' . $i . '-sidebar' )) :
	            echo '<div class="top-header-inner cell-' . $i . '">';
	            dynamic_sidebar( 'w-topheader-' . $i . '-sidebar' );
	            echo '</div>';
            endif;
        endfor;

        echo '</div>';
        ?>
    </div>
    <?php endif;
}

// -------------------------------------------------------------

/**
 * @return void
 */
function __bottomheader() {

	$header_cols = (int) get_theme_mod_ssl( 'header_setting' );

	$bottom_header_cols      = (int) get_theme_mod_ssl( 'bottom_header_setting' );
	$bottom_header_container = (int) get_theme_mod_ssl( 'bottom_header_container_setting' );

    if ( $bottom_header_cols  > 0 ) :

    ?>
    <div class="bottom-header header-content" id="bottom-header">
	    <?php
        if ( $bottom_header_container ) echo '<div class="grid-container width-extra">';
        else echo '<div class="grid-container width-extra widescreen">';

	    for ( $i = 1; $i <= $bottom_header_cols; $i++ ) :
		    if ( is_active_sidebar( 'w-bottomheader-' . $i . '-sidebar' )) :
			    echo '<div class="cell-' . $i . '">';
			    dynamic_sidebar( 'w-bottomheader-' . $i . '-sidebar' );
			    echo '</div>';
		    endif;
	    endfor;

        echo '</div>';
        ?>
    </div>
    <?php
    endif;
}

// -------------------------------------------------------------

/**
 * @return void
 */
function __header() {

	$top_header_cols = (int) get_theme_mod_ssl( 'top_header_setting' );

	$header_cols = (int) get_theme_mod_ssl( 'header_setting' );
	$header_container = (int) get_theme_mod_ssl( 'header_container_setting' );

	$sticky_data = 'data-sticky data-options="marginTop:0;" data-top-anchor="top-header:bottom"';
    if ( ! $top_header_cols ) {
            $sticky_data = 'data-sticky data-options="marginTop:0;"';
    }

	// $sticky_data = '';

    ?>
    <div class="inside-header" id="inside-header" <?=$sticky_data?>>
	    <?php
        if ( $header_container ) echo '<div class="grid-container width-extra">';
        else echo '<div class="grid-container width-extra widescreen">';

        ?>
        <div class="header-inner">
	        <?php
	        for ( $i = 1; $i <= $header_cols; $i++ ) :
		        if ( is_active_sidebar( 'w-header-' . $i . '-sidebar' )) :
			        echo '<div class="header-inner-cell cell-' . $i . '">';
			        dynamic_sidebar( 'w-header-' . $i . '-sidebar' );
			        echo '</div>';
		        endif;
	        endfor;
	        ?>
        </div>

	    <?php echo '</div>'; ?>
    </div>
    <?php
}

// -------------------------------------------------------------
// footer
// -------------------------------------------------------------

add_action( 'w_footer', '__footer_widgets', 10 );
add_action( 'footer', '__footer_credit', 11 );

/**
 * @return void
 */
function __footer_widgets() {
	$rows    = Cast::toInt( get_theme_mod_ssl( 'footer_row_setting' ) );
	$regions = Cast::toInt( get_theme_mod_ssl( 'footer_col_setting' ) );

    $footer_container = get_theme_mod_ssl( 'footer_container_setting' );

    ?>
    <footer id="colophon" class="footer-widgets has-bg" role="contentinfo">
        <?php
        for ($row = 1; $row <= $rows; $row++) :

            // Defines the number of active columns in this footer row.
            for ($region = $regions; 0 < $region; $region--) {
                if (is_active_sidebar('w-footer-' . esc_attr($region + $regions * ($row - 1)))) {
                    $columns = $region;
                    break;
                }
            }

	        if ( isset( $columns ) ) :

            ?>
            <div class="rows row-<?php echo $row; ?>">
                <?php
                if ( $footer_container ) echo '<div class="grid-container width-extra">';
                else echo '<div class="grid-container width-extra widescreen">';

                ?>
                <div class="grid-x">
                    <?php
                    for ($column = 1; $column <= $columns; $column++) :
                        $footer_n = $column + $regions * ($row - 1);
                        if (is_active_sidebar('w-footer-' . esc_attr($footer_n))) :
                            ?>
                            <div class="cell footer-widget footer-widget-<?php echo esc_attr($column); ?>">
                                <?php dynamic_sidebar('w-footer-' . esc_attr($footer_n)); ?>
                            </div>
                        <?php
                        endif;
                    endfor;
                    ?>
                </div>
	            <?php echo '</div>'; ?>
            </div>
            <?php
                unset($columns);
            endif;
        endfor;
        ?>
    </footer><!-- #colophon-->
    <?php
}

// -------------------------------------------------------------

/**
 * @return void
 */
function __footer_credit() {
	$footer_container = get_theme_mod_ssl( 'footer_container_setting' );
    ?>
    <footer class="footer-credit">
	    <?php
        if ( $footer_container ) echo '<div class="grid-container width-extra">';
        else echo '<div class="grid-container width-extra widescreen">';

        ?>
        <div class="align-middle grid-x grid-padding-x align-justify">
            <?php if ( has_nav_menu( 'social-nav' ) ) : ?>
            <div class="cell medium-shrink nav">
                <span class="title">KẾT NỐI VỚI CHÚNG TÔI</span>
                <?php echo social_nav(); ?>
            </div>
            <?php endif; ?>
            <div class="cell medium-shrink copyright">
                <div class="copyright-inner">
                    <p>
                        <span class="cr">Copyright &copy; <?= date('Y') ?>&nbsp;<a href="<?= Url::home() ?>" aria-label="<?= get_bloginfo('name') ?>"><?= get_bloginfo('name') ?></a></span>
                        <span class="hd">&nbsp;<?php echo sprintf('<a class="_blank" href="https://webhd.vn" title="%1$s">%1$s</a>', __('HD Agency', 'hd')) ?></span>
                    </p>
                    <?php
                    $GPKD = get_theme_mod_ssl('GPKD_setting');
                    if (Str::stripSpace($GPKD))
                        echo '<p class="gpkd">' . $GPKD . '</p>'
                    ?>
                </div>
            </div>
            
        </div>
	    <?php echo '</div>'; ?>
    </footer>
    <?php
}

// -------------------------------------------------------------
// before_footer
// -------------------------------------------------------------

add_action( 'before_footer', '__before_footer_extra', 32 );
//add_action('before_footer', '__before_footer', 31);

/**
 * @return void
 */
function __before_footer_extra() {
	$topfooter_1 = is_active_sidebar( 'w-topfooter-1' );
	$topfooter_2 = is_active_sidebar( 'w-topfooter-2' );

    if ( $topfooter_1 || $topfooter_2 ) {
    ?>
    <div class="footer-before before-footer">
        <?php if ( $topfooter_1 ) : ?>
        <div class="before-footer-inner top-footer-1">
            <?php dynamic_sidebar('w-topfooter-1'); ?>
        </div>
        <?php endif; ?>
        <?php if ( $topfooter_2 ) : ?>
        <div class="before-footer-inner top-footer-2">
            <?php dynamic_sidebar('w-topfooter-2'); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    }
}

/**
 * @return void
 */
function __before_footer() {
?>
    <div class="footer-infos">
        <div class="grid-container grid-extra">
            <div class="align-middle grid-x grid-padding-x align-justify">
                <div class="cell">
                    <div class="footer-logo">
                        <?php
                        echo site_logo();

                        $logo_title = get_theme_mod_ssl('logo_title_setting');
                        $logo_slogan = get_theme_mod_ssl('logo_slogan_setting');

                        if (Str::stripSpace($logo_title) || Str::stripSpace($logo_slogan)) :
                            echo '<div class="site-title"><div class="site-title-inner">';
                            if (Str::stripSpace($logo_title)) :
                                echo '<div class="logo-title">';
                                echo '<span class="txt-logo">' . $logo_title . '</span>';
                                echo '</div>';
                            endif;
                            if (Str::stripSpace($logo_slogan)) :
                                echo '<div class="logo-slogan">';
                                echo '<span class="txt-slogan">' . $logo_slogan . '</span>';
                                echo '</div>';
                            endif;
                            echo '</div></div>';
                        endif;

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}

// -------------------------------------------------------------
// after_footer
// -------------------------------------------------------------

add_action( 'after_footer', '__after_footer_extra', 32 );
add_action( 'after_footer', '__fixed_contact_button', 31 );

/**
 * @return void
 */
function __after_footer_extra() {
	if ( is_active_sidebar( 'w-fixed-sidebar' ) ) {
		dynamic_sidebar( 'w-fixed-sidebar' );
	}
}

/**
 * @return void
 */
function __fixed_contact_button() {
	$btn_title     = get_theme_mod_ssl( 'btn_contact_setting' );
	$btn_popup     = get_theme_mod_ssl( 'btn_contact_popup_setting' );
	$btn_shortcode = get_theme_mod_ssl( 'btn_contact_shortcode_setting' );
	$btn_url       = get_theme_mod_ssl( 'btn_contact_link_setting' );
	$btn_window    = get_theme_mod_ssl( 'btn_contact_window_setting' );

	if ( Str::stripSpace( $btn_title, true ) ) {

    ?>
    <div class="section fixed-contact-section">
        <div class="fixed-contact-inner">
            <div class="contact-item">
                <?php
                $url = 'javascript:;';
                $class = 'contact-link';
                if ($btn_popup) $class .= ' contact-popup';
                if ($btn_window) $class .= ' _black';
                if (Str::stripSpace( $btn_url, true )) $url = $btn_url;

                ?>
                <a class="<?=$class?>" href="<?=$url?>" title="<?=esc_attr($btn_title)?>"><?=$btn_title?></a>
            </div>
        </div>
        <div class="reveal-wrapper reveal-contact-popup">
            <div class="reveal-inner">
                <h2><?php echo $btn_title; ?></h2>
                <?php echo do_shortcode($btn_shortcode); ?>
                <button class="close-button" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <?php
    }
}

// -------------------------------------------------------------
// before_content
// -------------------------------------------------------------

add_action('before_content', '__before_content_extra', 10);

/**
 * @return void
 */
function __before_content_extra() {}

// ------------------------------------------------------
// ------------------------------------------------------

/**
 * @param $item_output
 * @param $item
 * @param $depth
 * @param $args
 *
 * @return string|string[]
 */
add_filter( 'walker_nav_menu_start_el', function ( $item_output, $item, $depth, $args ) {

	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'social-nav' === $args->theme_location && class_exists( SVG_Icons::class ) ) {
		$svg = SVG_Icons::get_social_link_svg( $item->url, 24 );
		if ( $svg ) {
			$item_output = str_replace( $args->link_before, $svg, $item_output );
		}
	}

	return $item_output;
}, 12, 4 );

// -------------------------------------------------------------

/**
 * @param array $args
 *
 * @return array
 */
add_filter( 'widget_tag_cloud_args', function ( array $args ) {
	$args['smallest'] = '10';
	$args['largest']  = '19';
	$args['unit']     = 'px';
	$args['number']   = 12;

	return $args;
} );

// -------------------------------------------------------------

// add class to anchor link
add_filter( 'nav_menu_link_attributes', function ( $atts ) {
	//$atts['class'] = "nav-link";
	return $atts;
}, 100, 1 );

// -------------------------------------------------------------
// optimize load
// -------------------------------------------------------------

add_filter( 'defer_script_loader_tag', function ( $arr ) {
	$arr = [
		'woo-variation-swatches' => 'defer',
		'wc-single-product'      => 'defer',
		'wc-add-to-cart'         => 'defer',
		'google-recaptcha'       => 'defer',
		'wpcf7-recaptcha'        => 'defer',
		'contact-form-7'         => 'defer',
		'video-js'               => 'defer',

		'comment-reply' => 'delay',
		'wp-embed'      => 'delay',
		'backtop'       => 'delay',
		'shares'        => 'delay',
		'draggable'     => 'delay',
	];

	return $arr;
}, 10, 1 );

// -------------------------------------------------------------

add_filter( 'defer_style_loader_tag', function ( $arr ) {
	$arr = [
		'dashicons',
		'contact-form-7',
		'photoswipe',
		'photoswipe-default-skin',
		'woo-variation-swatches',
		'rank-math',
	];

	return $arr;
}, 10, 1 );
