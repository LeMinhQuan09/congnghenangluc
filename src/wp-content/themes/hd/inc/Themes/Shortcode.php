<?php

namespace Webhd\Themes;

use Webhd\Helpers\Str;
use Webhd\Helpers\Url;

\defined( '\WPINC' ) || die;

final class Shortcode {

	// ------------------------------------------------------

	public function __construct() {
		add_shortcode( 'safe_mail', [ &$this, 'safe_mailto_shortcode' ], 11 );
		add_shortcode( 'logo', [ &$this, 'logo_shortcode' ], 11 );
		add_shortcode( 'site_logo', [ &$this, 'site_logo_shortcode' ], 11 );

		add_shortcode( 'horizontal_menu', [ &$this, 'horizontal_menu_shortcode' ], 11 );
		add_shortcode( 'vertical_menu', [ &$this, 'vertical_menu_shortcode' ], 11 );

		add_shortcode( 'mega_menu', [ &$this, 'mega_menu_shortcode' ], 11 );

		add_shortcode( 'social_menu', [ &$this, 'social_menu_shortcode' ], 11 );
		add_shortcode( 'mobile_menu', [ &$this, 'mobile_menu_shortcode' ], 11 );
		add_shortcode( 'main_menu', [ &$this, 'main_menu_shortcode' ], 11 );
		add_shortcode( 'second_menu', [ &$this, 'second_menu_shortcode' ], 11 );
		add_shortcode( 'term_menu', [ &$this, 'term_menu_shortcode' ], 11 );
		add_shortcode( 'productcat_menu', [ &$this, 'productcat_menu_shortcode' ], 11 );
		
		add_shortcode( 'page_title_theme', [ &$this, 'page_title_theme_shortcode' ], 11 );

		add_shortcode( 'inline-search', [ &$this, 'inline_search_shortcode' ], 11 );
		add_shortcode( 'dropdown-search', [ &$this, 'dropdown_search_shortcode' ], 11 );
		add_shortcode( 'copyright', [ &$this, 'copyright_shortcode' ], 11 );

		add_shortcode( 'product_cat_thumb', [ &$this, 'product_cat_thumb_shortcode' ], 11 );
	}

	// ------------------------------------------------------

	/**
	 * @param $atts
	 *
	 * @return void
	 */
	public function product_cat_thumb_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'catid' => 0,
				'class' => 'menu-product-cat-thumb',
				'id'    => esc_attr( uniqid( 'menu-cat-thumb-' ) ),
			],

			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		?>
        <div class="mega-media-thumb <?php echo $a['class']; ?>" id="<?php echo $a['id']; ?>">
			<?php
			$term = get_term( $a['catid'] );

			$caption     = '';
			$description = '';

			if ( $term && isset( $term->term_id ) ) :
				$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				//$image_src = wp_get_attachment_image_src( $thumbnail_id, 'medium' );

				$attachment_meta = wp_get_attachment( $thumbnail_id );
				if ( $attachment_meta->caption ?? '' ) {
					$caption = $attachment_meta->caption;
				}
				if ( $attachment_meta->description ?? '' ) {
					$description = $attachment_meta->description;
				}

				?>
                <figure style="width: 768px" class="wp-caption alignnone">
                    <a class="thumb" href="<?php echo get_term_link( $term ) ?>"
                       aria-label="<?php echo esc_attr( $term->name ) ?>">
						<?php echo wp_get_attachment_image( $thumbnail_id, 'medium' ); ?>
                    </a>
					<?php //if ( $caption || $description ) :
					?>
                    <figcaption class="wp-caption-text">
                        <span class="caption"><?= $caption; ?></span>
                        <span class="desc"><?= $description ?></span>
                    </figcaption>
					<?php //endif;
					?>
                </figure>
			<?php endif; ?>
        </div>
		<?php
	}

	// ------------------------------------------------------

	/**
	 * @param $atts
	 *
	 * @return void
	 */
	public function copyright_shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'title' => '',
				'class' => 'copyright-inner',
				'id'    => esc_attr( uniqid( 'search-' ) ),
			],
			$atts,
			'dropdown-search'
		);

		?>
        <div class="copyright <?php echo $atts['class']; ?>">
            <p>
                <span class="cr">Copyright &copy; <?= date( 'Y' ) ?>&nbsp;<?= get_bloginfo( 'name' ) ?>, All rights reserved.</span>
                <span class="hd">&nbsp;<?php echo sprintf( '<a class="_blank" href="https://webhd.vn" title="%1$s">%1$s</a>', __( 'HD Agency', 'hd' ) ) ?></span>
            </p>
			<?php
			$GPKD = get_theme_mod_ssl( 'GPKD_setting' );
			if ( Str::stripSpace( $GPKD ) )
				echo '<p class="gpkd" data-glyph="">' . $GPKD . '</p>'
			?>
        </div>
		<?php
	}

	// ------------------------------------------------------

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	public function dropdown_search_shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'title' => '',
				'class' => 'search-dropdown--wrap',
				'id'    => esc_attr( uniqid( 'search-' ) ),
			],
			$atts,
			'dropdown-search'
		);

		$title             = $atts['title'] ?: __( 'Search', 'hd' );
		$title_for         = __( 'Search for', 'hd' );
		$placeholder_title = __( 'Search ...', 'hd' );
		$close_title       = __( 'Close', 'hd' );
		$id                = $atts['id'] ?: esc_attr( uniqid( 'search-' ) );

		ob_start();

		?>
        <a class="trigger-s" title="<?= esc_attr( $title ); ?>" href="javascript:;"
           data-toggle="dropdown-<?= $id; ?>" data-glyph="">
            <span><?php echo $title; ?></span>
        </a>
        <div role="search" class="dropdown-pane" id="dropdown-<?= $atts['id']; ?>" data-dropdown
             data-auto-focus="true">
            <form role="form" action="<?= Url::home(); ?>" class="frm-search" method="get" accept-charset="UTF-8"
                  data-abide novalidate>
                <div class="frm-container">
                    <label for="<?= $id; ?>" class="screen-reader-text"><?= esc_attr( $title_for ); ?></label>
                    <input id="<?= $id; ?>" required pattern="^(.*\S+.*)$" type="search" name="s"
                           value="<?php echo get_search_query(); ?>"
                           placeholder="<?php echo esc_attr( $placeholder_title ); ?>">
                    <button class="btn-s" type="submit" data-glyph="">
                        <span><?php echo $title; ?></span>
                    </button>
                    <button class="trigger-s-close" type="button" data-glyph="">
                        <span><?php echo esc_attr( $close_title ); ?></span>
                    </button>
                </div>
				<?php if ( class_exists( '\WooCommerce' ) ) : ?>
                    <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
            </form>
        </div>
		<?php

		return '<div class="dropdown-search ' . $atts['class'] . '">' . ob_get_clean() . '</div>';
	}

	// ------------------------------------------------------

	/**
	 * @param $atts
	 *
	 * @return void
	 */
	public function inline_search_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'class' => 'inline-search',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		$_unique_id        = esc_attr( uniqid( 'search-form-' ) );
		$title             = __( 'Search', 'hd' );
		$title_for         = __( 'Search for', 'hd' );
		$placeholder_title = esc_attr( __( 'Search ...', 'hd' ) );

		if ( class_exists( '\WooCommerce' ) ) :
			$title             = __( 'Tìm kiếm sản phẩm', 'hd' );
			$title_for         = __( 'Tìm kiếm sản phẩm', 'hd' );
			$placeholder_title = esc_attr( __( 'Tìm kiếm sản phẩm', 'hd' ) );
		endif;

		?>
        <div class="inside-search <?php echo $a['class']; ?>">
            <form role="search" action="<?php echo Url::home(); ?>" class="frm-search" method="get"
                  accept-charset="UTF-8" data-abide novalidate>
                <label for="<?php echo $_unique_id; ?>" class="screen-reader-text"><?php echo $title_for; ?></label>
                <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search"
                       autocomplete="off" name="s" value="<?php echo get_search_query(); ?>"
                       placeholder="<?php echo $placeholder_title; ?>">
                <button type="submit" data-glyph="">
                    <span><?php echo $title; ?></span>
                </button>
				<?php if ( class_exists( '\WooCommerce' ) ) : ?>
                    <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
            </form>
        </div>
		<?php
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return false|string
	 */
	public function page_title_theme_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts( [], array_change_key_case( (array) $atts, CASE_LOWER ) );

		ob_start();
		the_page_title_theme();

		return ob_get_clean();
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function social_menu_shortcode( $atts = [] ) {

		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'social-nav',
				'menu_class' => 'social-menu menu conn-lnk',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return social_nav( $a['location'], $a['menu_class'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function mobile_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'mobile-nav',
				'menu_class' => 'mobile-menu',
				'menu_id'    => 'mobile-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return mobile_nav( $a['location'], $a['menu_class'], $a['menu_id'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function term_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'policy-nav',
				'menu_class' => 'terms-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return term_nav( $a['location'], $a['menu_class'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function main_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'main-nav',
				'menu_class' => 'desktop-menu',
				'menu_id'    => 'main-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return main_nav( $a['location'], $a['menu_class'], $a['menu_id'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function second_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'second-nav',
				'menu_class' => 'desktop-menu second-menu',
				'menu_id'    => 'second-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return main_nav( $a['location'], $a['menu_class'], $a['menu_id'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function mega_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'mega-nav',
				'menu_class' => 'desktop-menu',
				'menu_id'    => 'main-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return mega_nav( $a['location'], $a['menu_class'], $a['menu_id'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function vertical_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'id'       => '',
				'class'    => 'mobile-menu',
				'location' => 'mobile-nav',
				'depth'    => 4,
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return vertical_nav( [
			'menu_id'        => $a['id'],
			'menu_class'     => 'vertical menu ' . $a['class'],
			'theme_location' => $a['location'],
			'depth'          => $a['depth'],
			'echo'           => false,
		] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function horizontal_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'id'       => '',
				'class'    => 'desktop-menu',
				'location' => 'main-nav',
				'depth'    => 4,
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return horizontal_nav( [
			'menu_id'        => $a['id'],
			'menu_class'     => 'dropdown menu horizontal horizontal-menu ' . $a['class'],
			'theme_location' => $a['location'],
			'depth'          => $a['depth'],
			'echo'           => false,
		] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return bool|string
	 */
	public function productcat_menu_shortcode( $atts = [] ) {
		// override default attributes
		$a = shortcode_atts(
			[
				'location'   => 'productcat-nav',
				'menu_class' => 'desktop-menu productcat-menu',
				'menu_id'    => 'productcat-menu',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return main_nav( $a['location'], $a['menu_class'], $a['menu_id'] );
	}

	// ------------------------------------------------------

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	public function logo_shortcode( $atts = [] ): string {

		// override default attributes
		$a = shortcode_atts(
			[
				'class' => 'site-logo',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		ob_start();

		?>
        <div class="<?= $a['class'] ?>">
			<?php site_title_or_logo(); ?>
        </div>
		<?php
		return ob_get_clean();
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function site_logo_shortcode( $atts = [] ): string {

		// override default attributes
		$a = shortcode_atts(
			[
				'theme' => 'default',
				'class' => 'site-logo',
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		return site_logo( $a['theme'], $a['class'] );
	}

	// ------------------------------------------------------

	/**
	 * @param array $atts
	 *
	 * @return string
	 */
	public function safe_mailto_shortcode( $atts = [] ): string {

		// override default attributes
		$a = shortcode_atts(
			[
				'email'      => 'info@webhd.vn',
				'title'      => '',
				'attributes' => '',
				'class'      => '',
				'id'         => esc_attr( uniqid( 'mail-' ) ),
			],
			array_change_key_case( (array) $atts, CASE_LOWER )
		);

		$_attr = [];
		if ( $a['id'] ) {
			$_attr['id'] = $a['id'];
		}

		if ( $a['class'] ) {
			$_attr['class'] = $a['class'];
		}

		if ( empty( $a['title'] ) ) {
			$a['title'] = esc_attr( $a['email'] );
		}

		$_attr['title'] = $a['title'];

		if ( $a['attributes'] ) {
			$a['attributes'] = array_merge( $_attr, (array) $a['attributes'] );
		} else {
			$a['attributes'] = $_attr;
		}

		return safe_mailto( $a['email'], $a['title'], $a['attributes'] );
	}
}
