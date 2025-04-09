<?php
/**
 * Sidebars functions
 *
 * @author   WEBHD
 */
\defined( '\WPINC' ) || die;

if ( ! function_exists( '__register_sidebars' ) ) {
	/**
	 * Register widget area.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
	 */
	function __register_sidebars() {

		//----------------------------------------------------------

		// homepage
		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-homepage-center-sidebar',
				'name'          => __( 'Home Center', 'hd' ),
				'description'   => __( 'Widgets added here will appear in homepage.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-homepage-top-sidebar',
				'name'          => __( 'Home Top', 'hd' ),
				'description'   => __( 'Widgets added here will appear in homepage.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-homepage-bottom-sidebar',
				'name'          => __( 'Home Bottom', 'hd' ),
				'description'   => __( 'Widgets added here will appear in homepage.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		//----------------------------------------------------------
		// Header
		//----------------------------------------------------------

		$top_header_cols = (int) get_theme_mod_ssl( 'top_header_setting' );
		$header_cols = (int) get_theme_mod_ssl( 'header_setting' );
		$bottom_header_cols = (int) get_theme_mod_ssl( 'bottom_header_setting' );

		if ( $top_header_cols > 0 ) {
			for ( $i = 1; $i <= $top_header_cols; $i++ ) {
				$_name = sprintf( __( 'Top-Header %d', 'hd' ), $i );
				register_sidebar(
					[
						'container'     => false,
						'id'            => 'w-topheader-' . $i . '-sidebar',
						'name'          => $_name ,
						'description'   => __( 'Widgets added here will appear in top header.', 'hd' ),
						'before_widget' => '<div class="header-widgets %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<span>',
						'after_title'   => '</span>',
					]
				);
			}
		}

		if ( $header_cols > 0 ) {
			for ( $i = 1; $i <= $header_cols; $i++ ) {
				$_name = sprintf( __( 'Header %d', 'hd' ), $i );
				register_sidebar(
					[
						'container'     => false,
						'id'            => 'w-header-' . $i . '-sidebar',
						'name'          => $_name ,
						'description'   => __( 'Widgets added here will appear in header.', 'hd' ),
						'before_widget' => '<div class="header-widgets %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<span>',
						'after_title'   => '</span>',
					]
				);
			}
		}

		if ( $bottom_header_cols > 0 ) {
			for ( $i = 1; $i <= $bottom_header_cols; $i++ ) {
				$_name = sprintf( __( 'Bottom-Header %d', 'hd' ), $i );
				register_sidebar(
					[
						'container'     => false,
						'id'            => 'w-bottomheader-' . $i . '-sidebar',
						'name'          => $_name ,
						'description'   => __( 'Widgets added here will appear in bottom header.', 'hd' ),
						'before_widget' => '<div class="header-widgets %2$s">',
						'after_widget'  => '</div>',
						'before_title'  => '<span>',
						'after_title'   => '</span>',
					]
				);
			}
		}

		//----------------------------------------------------------

//        register_sidebar(
//            [
//                'container'     => false,
//                'id'            => 'w-language-sidebar',
//                'name'          => __( 'Language', 'hd' ),
//                'description'   => __( 'Widgets added here will appear in language.', 'hd' ),
//                'before_widget' => '<div class="language-widgets %2$s">',
//                'after_widget'  => '</div>',
//                'before_title'  => '<span>',
//                'after_title'   => '</span>',
//            ]
//        );

		// Fixed sidebar
		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-fixed-sidebar',
				'name'          => __( 'Fixed Sidebar', 'hd' ),
				'description'   => __( 'Widgets added here will appear in fixed sidebar.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		// news sidebar
//        register_sidebar(
//            [
//                'container'     => false,
//                'id'            => 'w-news-sidebar',
//                'name'        => __('News Sidebar', 'hd'),
//                'description' => __('Widgets added here will appear in news sidebar.', 'hd'),
//                'before_widget' => '<aside class="sidebar %2$s">',
//                'after_widget'  => '</aside>',
//                'before_title'  => '<h6 class="sidebar-title">',
//                'after_title'   => '</h6>',
//            ]
//        );

		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-topfooter-1',
				'name'          => __( 'Before Footer 1', 'hd' ),
				'description'   => __( 'Widgets added here will appear in before footer sidebar.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		//----------------------------------------------------------
		// Footer
		//----------------------------------------------------------

		$footer_args = [];

		$rows    = (int) get_theme_mod_ssl( 'footer_row_setting' );
		$regions = (int) get_theme_mod_ssl( 'footer_col_setting' );
		for ( $row = 1; $row <= $rows; $row ++ ) {
			for ( $region = 1; $region <= $regions; $region ++ ) {
				$footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
				$footer   = sprintf( 'footer_%d', $footer_n );
				if ( 1 === $rows ) {

					/* translators: 1: column number */
					$footer_region_name = sprintf( __( 'Footer Column %1$d', 'hd' ), $region );

					/* translators: 1: column number */
					$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', 'hd' ), $region );
				} else {

					/* translators: 1: row number, 2: column number */
					$footer_region_name = sprintf( __( 'Footer Row %1$d - Column %2$d', 'hd' ), $row, $region );

					/* translators: 1: column number, 2: row number */
					$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'hd' ), $region, $row );
				}

				$footer_args[ $footer ] = [
					'name'        => $footer_region_name,
					'id'          => sprintf( 'w-footer-%d', $footer_n ),
					'description' => $footer_region_description,
				];
			}
		}

		foreach ( $footer_args as $args ) {
			$footer_tags = [
				'container'     => false,
				'before_widget' => '<div class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<p class="widget-title h6">',
				'after_title'   => '</p>',
			];

			register_sidebar( $args + $footer_tags );
		}

		//----------------------------------------------------------
		// Custom page
		//----------------------------------------------------------

		// After post
		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-after-post-sidebar',
				'name'          => __( 'After Posts', 'hd' ),
				'description'   => __( 'Widgets added here will appear in after single posts.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		// Fix Sidebar Left
		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-fix-sidebarleft-sidebar',
				'name'          => __( 'Fixed Sidebar Left', 'hd' ),
				'description'   => __( 'Widgets added here will appear in fixed sidebar left.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);

		// Fix Sidebar Right
		register_sidebar(
			[
				'container'     => false,
				'id'            => 'w-fix-sidebarright-sidebar',
				'name'          => __( 'Fixed Sidebar Right', 'hd' ),
				'description'   => __( 'Widgets added here will appear in fixed sidebar left.', 'hd' ),
				'before_widget' => '<div class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span>',
				'after_title'   => '</span>',
			]
		);
	}

	add_action( 'widgets_init', '__register_sidebars', 10 );
}
