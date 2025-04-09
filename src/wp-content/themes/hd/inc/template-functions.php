<?php

/**
 * Template Functions
 *
 * @author WEBHD
 */
\defined( '\WPINC' ) || die;

use Webhd\Helpers\Str;
use Webhd\Helpers\Url;
use Webhd\Helpers\Cast;
use Webhd\Helpers\Arr;

use Webhd\Walkers\Horizontal_Menu_Walker;
use Webhd\Walkers\Vertical_Menu_Walker;

// ------------------------------------------------------

if ( ! function_exists( 'get_pages_by_template' ) ) {
	/**
	 * @param string $post_type
	 * @param string $meta_value
	 *
	 * @return false|WP_Post[]
	 */
	function get_pages_by_template( string $post_type = 'page', string $meta_value = '' ) {
		return get_pages( [
			'post_type'    => $post_type,
			'meta_key'     => '_wp_page_template',
			'hierarchical' => 0,
			'meta_value'   => $meta_value,
		] );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'redirect' ) ) {
	/**
	 * Header Redirect
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @param string $uri URL
	 * @param string $method Redirect method
	 *            'auto', 'location' or 'refresh'
	 * @param int $code HTTP Response status code
	 *
	 * @return    void
	 *
	 * @override
	 */
	function redirect( string $uri = '', string $method = 'auto', $code = null ) {
		if ( ! preg_match( '#^(\w+:)?//#i', $uri ) ) {
			$uri = site_url( $uri );
		}

		if ( ! headers_sent() ) {
			// IIS environment likely? Use 'refresh' for better compatibility
			if ( $method === 'auto' && isset( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) !== false ) {
				$method = 'refresh';
			} elseif ( $method !== 'refresh' && ( empty( $code ) or ! is_numeric( $code ) ) ) {
				if ( isset( $_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD'] ) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1' ) {
					$code = ( $_SERVER['REQUEST_METHOD'] !== 'GET' )
						? 303    // reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
						: 307;
				} else {
					$code = 302;
				}
			}

			switch ( $method ) {
				case 'refresh':
					header( 'Refresh:0;url=' . $uri );
					break;
				default:
					header( 'Location: ' . $uri, true, $code );
					break;
			}
		} else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="' . $uri . '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=' . $uri . '" />';
			echo '</noscript>';
		}

		exit;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'do_shortcode' ) ) {
	/**
	 * Call a shortcode function by tag name.
	 *
	 * @param string $tag The shortcode whose function to call.
	 * @param array $atts The attributes to pass to the shortcode function. Optional.
	 * @param array|null $content The shortcode's content. Default is null (none).
	 *
	 * @return false|mixed False on failure, the result of the shortcode on success.
	 */
	function do_shortcode( $tag, array $atts = [], $content = null ) {
		global $shortcode_tags;
		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'get_image_id' ) ) {
	/**
	 * @param $image_url
	 *
	 * @return false|mixed
	 */
	function get_image_id( $image_url ) {
		global $wpdb;

		$sql        = 'SELECT ID FROM ' . $wpdb->prefix . "posts WHERE post_type LIKE 'attachment' AND guid LIKE '%" . esc_sql( $image_url ) . "';";
		$attachment = $wpdb->get_col( $sql );
		$img_id     = reset( $attachment );
		if ( ! $img_id ) {
			if ( str_contains( $image_url, '-scaled.' ) ) {
				$image_url = str_replace( '-scaled.', '.', $image_url );
				$img_id    = get_image_id( $image_url );
			}
		}

		return $img_id;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'wp_get_attachment' ) ) {
	/**
	 * @param      $attachment_id
	 * @param bool $return_object
	 *
	 * @return array|object
	 */
	function wp_get_attachment( $attachment_id, bool $return_object = true ) {
		$attachment = get_post( $attachment_id );
		$_return    = [
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'href'        => get_permalink( $attachment->ID ),
			'src'         => $attachment->guid,
			'title'       => $attachment->post_title,
		];

		if ( true === $return_object ) {
			$_return = Cast::toObject( $_return );
		}

		return $_return;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'lazy_script_tag' ) ) {
	/**
	 * @param array $arr_parsed [ $handle: $value ] -- $value[ 'defer', 'delay' ]
	 * @param string $tag
	 * @param string $handle
	 * @param string $src
	 *
	 * @return array|string|string[]|null
	 */
	function lazy_script_tag( array $arr_parsed, string $tag, string $handle, string $src ) {
		foreach ( $arr_parsed as $str => $value ) {
			if ( str_contains( $handle, $str ) ) {
				if ( 'defer' === $value ) {
					$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );

					return preg_replace( '/\s+src=/', ' defer src=', $tag );
				} elseif ( 'delay' === $value ) {
					$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );

					return preg_replace( '/\s+src=/', ' defer data-type=\'lazy\' data-src=', $tag );
				}
			}
		}

		return $tag;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'lazy_style_tag' ) ) {
	/**
	 * @param array $arr_styles [ $handle ]
	 * @param string $html
	 * @param string $handle
	 *
	 * @return array|string|string[]|null
	 */
	function lazy_style_tag( array $arr_styles, string $html, string $handle ) {
		foreach ( $arr_styles as $style ) {
			if ( str_contains( $handle, $style ) ) {
				return preg_replace( '/media=\'all\'/', 'media=\'print\' onload=\'this.media="all"\'', $html );
			}
		}

		return $html;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'get_theme_mod_ssl' ) ) {
	/**
	 * @param             $mod_name
	 * @param string|bool $default
	 *
	 * @return mixed|string|string[]
	 */
	function get_theme_mod_ssl( $mod_name, $default = false ) {
		static $_is_loaded;
		if ( empty( $_is_loaded ) ) {

			// references cannot be directly assigned to static variables, so we use an array
			$_is_loaded[0] = [];
		}

		if ( $mod_name ) {
			if ( ! isset( $_is_loaded[0][ strtolower( $mod_name ) ] ) ) {
				$_mod = get_theme_mod( $mod_name, $default );
				if ( is_ssl() ) {
					$_is_loaded[0][ strtolower( $mod_name ) ] = str_replace( [ 'http://' ], 'https://', $_mod );
				} else {
					$_is_loaded[0][ strtolower( $mod_name ) ] = str_replace( [ 'https://' ], 'http://', $_mod );
				}
			}

			return $_is_loaded[0][ strtolower( $mod_name ) ];
		}

		return $default;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'query_by_term' ) ) {
	/**
	 * @param object|array $term
	 * @param string $post_type
	 * @param bool $include_children
	 *
	 * @param int $posts_per_page
	 * @param array $orderby
	 * @param bool|string $strtotime_recent
	 *
	 * @return bool|WP_Query
	 */
	function query_by_term( $term, string $post_type = 'any', bool $include_children = false, int $posts_per_page = 0, $orderby = [], $strtotime_recent = false ) {
		if ( ! $term ) {
			return false;
		}

		$_args = [
			'post_type'              => $post_type ?: 'post',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page ?: 10,
			'tax_query'              => [ 'relation' => 'AND' ],
		];

		//...
		if ( ! is_object( $term ) ) {
			$term = Cast::toObject( $term );
		}

		//
		if ( isset( $term->taxonomy ) && isset( $term->term_id ) ) {
			$_args['tax_query'][] = [
				'taxonomy'         => $term->taxonomy,
				'terms'            => [ $term->term_id ],
				'include_children' => (bool) $include_children,
				'operator'         => 'IN',
			];
		}

		if ( is_array( $orderby ) ) {
			$orderby = Arr::removeEmptyValues( $orderby );
		} else {
			$orderby = [ 'date' => 'DESC' ];
		}

		$_args['orderby'] = $orderby;

		// ...
		if ( $strtotime_recent ) {

			// constrain to just posts in $strtotime_recent
			$recent = strtotime( $strtotime_recent );
			if ( $recent ) {
				$_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && class_exists( '\WooCommerce' ) ) {

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();

			$_args['tax_query'][] = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				],
			]; // WPCS: slow query ok.
		}

		$_query = new WP_Query( $_args );
		if ( ! $_query->have_posts() ) {
			return false;
		}

		return $_query;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'query_by_terms' ) ) {
	/**
	 * @param array|string $term_ids
	 * @param string $taxonomy
	 * @param string $post_type
	 * @param bool $include_children
	 * @param int $posts_per_page
	 * @param bool|string $strtotime_str
	 *
	 * @return bool|WP_Query
	 */
	function query_by_terms( $term_ids = [], string $taxonomy = 'category', string $post_type = 'any', bool $include_children = false, int $posts_per_page = 10, $strtotime_str = false ) {
		if ( ! $term_ids ) {
			return false;
		}

		$_args = [
			'post_type'              => $post_type ?: 'post',
			'post_status'            => 'publish',
			'orderby'                => [ 'date' => 'DESC' ],
			'tax_query'              => [ 'relation' => 'AND' ],
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'posts_per_page'         => $posts_per_page ?: 10,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];

		if ( ! is_array( $term_ids ) ) {
			$term_ids = Cast::toArray( $term_ids );
		}

		if ( ! $taxonomy ) {
			$taxonomy = 'category';
		}

		//...
		$_args['tax_query'][] = [
			'taxonomy'         => $taxonomy,
			'terms'            => $term_ids,
			'include_children' => (bool) $include_children,
			'operator'         => 'IN',
		];

		// ...
		if ( $strtotime_str ) {

			// constrain to just posts in $strtotime_str
			$recent = strtotime( $strtotime_str );
			if ( $recent ) {
				$_args['date_query'] = [
					'after' => [
						'year'  => date( 'Y', $recent ),
						'month' => date( 'n', $recent ),
						'day'   => date( 'j', $recent ),
					],
				];
			}
		}

		// woocommerce_hide_out_of_stock_items
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && class_exists( '\WooCommerce' ) ) {
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$_args['tax_query'][]        = [
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['outofstock'],
					'operator' => 'NOT IN',
				],
			]; // WPCS: slow query ok.
		}

		// query
		$r = new WP_Query( $_args );
		if ( ! $r->have_posts() ) {
			return false;
		}

		return $r;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'horizontal_nav' ) ) {
	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param array $args
	 *
	 * @return bool|false|string|void
	 */
	function horizontal_nav( array $args = [] ) {
		$args = wp_parse_args(
			(array) $args,
			[
				'container'      => false,
				'menu_id'        => '',
				'menu_class'     => 'dropdown menu horizontal horizontal-menu',
				'theme_location' => '',
				'depth'          => 4,
				'fallback_cb'    => false,
				'walker'         => new Horizontal_Menu_Walker(),
				'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-dropdown-menu>%3$s</ul>',
				'echo'           => false,
			]
		);

		if ( true === $args['echo'] ) {
			echo wp_nav_menu( $args );
		} else {
			return wp_nav_menu( $args );
		}
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'vertical_nav' ) ) {
	/**
	 * @param array $args
	 *
	 * @return bool|false|string|void
	 */
	function vertical_nav( array $args = [] ) {
		$args = wp_parse_args(
			(array) $args,
			[
				'container'      => false, // Remove nav container
				'menu_id'        => '',
				'menu_class'     => 'vertical menu accordion-menu',
				'theme_location' => '',
				'depth'          => 4,
				'fallback_cb'    => false,
				'walker'         => new Vertical_Menu_Walker(),
				'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s" data-accordion-menu data-submenu-toggle="true" data-multi-open="false">%3$s</ul>',
				'echo'           => false,
			]
		);

		if ( true === $args['echo'] ) {
			echo wp_nav_menu( $args );
		} else {
			return wp_nav_menu( $args );
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'main_nav' ) ) {
	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|string
	 */
	function main_nav( string $location = 'main-nav', string $menu_class = 'desktop-menu', string $menu_id = 'main-menu' ) {
		return horizontal_nav( [
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class . ' dropdown menu horizontal horizontal-menu',
			'theme_location' => $location,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'second_nav' ) ) {
	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|string
	 */
	function second_nav( string $location = 'second-nav', string $menu_class = 'desktop-menu second-menu', string $menu_id = 'second-menu' ) {
		return horizontal_nav( [
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class . ' dropdown menu horizontal horizontal-menu',
			'theme_location' => $location,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'mega_nav' ) ) {
	/**
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|string|null
	 */
	function mega_nav( string $location = 'mega-nav', string $menu_class = 'desktop-menu', string $menu_id = 'main-menu' ) {
		return horizontal_nav( [
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class . ' dropdown menu horizontal horizontal-menu mega-menu vertical',
			'theme_location' => $location,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'mobile_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|string
	 */
	function mobile_nav( string $location = 'mobile-nav', string $menu_class = 'mobile-menu', string $menu_id = 'mobile-menu' ) {
		return vertical_nav( [
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class . ' vertical menu',
			'theme_location' => $location,
			'echo'           => false,
		] );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'term_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 *
	 * @return bool|string
	 */
	function term_nav( string $location = 'policy-nav', string $menu_class = 'terms-menu' ) {
		return wp_nav_menu( [
			'container'      => false,
			'menu_class'     => $menu_class . ' menu horizontal horizontal-menu',
			'theme_location' => $location,
			'items_wrap'     => '<ul role="menubar" class="%2$s">%3$s</ul>',
			'depth'          => 1,
			'fallback_cb'    => false,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'productcat_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|string
	 */
	function productcat_nav( string $location = 'productcat-nav', string $menu_class = 'productcat-menu', string $menu_id = 'productcat-menu' ) {
		return vertical_nav( [
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class . ' vertical menu',
			'theme_location' => $location,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'social_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 *
	 * @return bool|string
	 */
	function social_nav( string $location = 'social-nav', string $menu_class = 'social-menu menu conn-lnk' ) {
		return wp_nav_menu( [
			'container'      => false,
			'theme_location' => $location,
			'menu_class'     => $menu_class,
			'depth'          => 1,
			'link_before'    => '<span class="text">',
			'link_after'     => '</span>',
			'fallback_cb'    => false,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'pagination_links' ) ) {
	/**
	 * @param bool $echo
	 *
	 * @return string|null
	 */
	function pagination_links( bool $echo = true ): ?string {
		global $wp_query;
		if ( $wp_query->max_num_pages > 1 ) {

			// This needs to be an unlikely integer
			$big = 999999999;

			// For more options and info view the docs for paginate_links()
			// http://codex.wordpress.org/Function_Reference/paginate_links
			$paginate_links = paginate_links(
				apply_filters(
					'wp_pagination_args',
					[
						'base'      => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $wp_query->max_num_pages,
						'end_size'  => 1,
						'mid_size'  => 1,
						'prev_next' => true,
						'prev_text' => '<i data-glyph=""></i>',
						'next_text' => '<i data-glyph=""></i>',
						'type'      => 'list',
					]
				)
			);

			$paginate_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination">', $paginate_links );
			$paginate_links = str_replace( '<li><span class="page-numbers dots">&hellip;</span></li>', '<li class="ellipsis"></li>', $paginate_links );
			$paginate_links = str_replace( '<li><span aria-current="page" class="page-numbers current">', '<li class="current"><span aria-current="page" class="show-for-sr">You\'re on page </span>', $paginate_links );
			$paginate_links = str_replace( '</span></li>', '</li>', $paginate_links );
			$paginate_links = preg_replace( '/\s*page-numbers\s*/', '', $paginate_links );
			$paginate_links = preg_replace( '/\s*class=""/', '', $paginate_links );

			// Display the pagination if more than one page is found.
			if ( $paginate_links ) {
				$paginate_links = '<nav aria-label="Pagination">' . $paginate_links . '</nav>';
				if ( $echo ) {
					echo $paginate_links;
				} else {
					return $paginate_links;
				}
			}
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'site_title_or_logo' ) ) {
	/**
	 * @param bool $echo
	 * @param string $home_tag
	 *
	 * @return string|void
	 */
	function site_title_or_logo( bool $echo = true, $home_tag = 'div' ) {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$logo = get_custom_logo();
			$html = '<' . $home_tag . ' class="logo">' . $logo . '</' . $home_tag . '>';
		} else {
			$tag  = is_home() ? $home_tag : 'div';
			$html = '<' . esc_attr( $tag ) . ' class="site-title"><a title href="' . Url::home() . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) . '>';
			if ( '' !== get_bloginfo( 'description' ) ) {
				$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
			}
		}

		$logo_heading = get_theme_mod_ssl( 'logo_heading_setting' );
		if ( $logo_heading && ( is_home() || is_front_page() ) ) {
			$html .= '<h1 class="hidden-text">' . $logo_heading . '</h1>';
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'site_logo' ) ) {
	/**
	 * @param string $theme
	 * @param string $class
	 * @param bool $url
	 *
	 * @return string
	 */
	function site_logo( string $theme = 'default', string $class = '', bool $url = true ): string {
		$html           = '';
		$custom_logo_id = null;

		if ( 'default' !== $theme && $theme_logo = get_theme_mod_ssl( $theme . '_logo' ) ) {
			$custom_logo_id = attachment_url_to_postid( $theme_logo );
		} else if ( 'default' == $theme && has_custom_logo() ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
		}

		// We have a logo. Logo is go.
		if ( $custom_logo_id ) {
			$custom_logo_attr = [
				'class'   => $theme . '-logo',
				'loading' => 'lazy',
			];

			/**
			 * If the logo alt attribute is empty, get the site title and explicitly pass it
			 * to the attributes used by wp_get_attachment_image().
			 */
			$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$image_alt = get_bloginfo( 'name', 'display' );
			}

			$custom_logo_attr['alt'] = $image_alt;

			/**
			 * If the alt attribute is not empty, there's no need to explicitly pass it
			 * because wp_get_attachment_image() already adds the alt attribute.
			 */
			$logo = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );

			if ( $url ) {
				if ( $class ) {
					$html = '<div class="' . $class . '"><a class="logo-overlay" title="' . $image_alt . '" href="' . Url::home() . '">' . $logo . '</a></div>';
				} else {
					$html = '<a class="logo-overlay" title="' . $image_alt . '" href="' . Url::home() . '">' . $logo . '</a>';
				}
			} else {
				if ( $class ) {
					$html = '<div class="' . $class . '"><span class="logo-overlay">' . $logo . '</span></div>';
				} else {
					$html = '<span class="logo-overlay">' . $logo . '</span>';
				}
			}
		}

		return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'loop_excerpt' ) ) {
	/**
	 * @param null $post
	 * @param string $class
	 *
	 * @return string|null
	 */
	function loop_excerpt( $post = null, string $class = 'excerpt' ): ?string {
		$excerpt = get_the_excerpt( $post );
		if ( ! Str::stripSpace( $excerpt ) ) {
			return null;
		}

		$excerpt = strip_tags( $excerpt );
		if ( ! $class ) {
			return $excerpt;
		}

		return "<p class=\"$class\">{$excerpt}</p>";
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'post_excerpt' ) ) {
	/**
	 * @param             $post
	 * @param string|null $class
	 * @param bool $glyph_icon
	 *
	 * @return string|null
	 */
	function post_excerpt( $post = null, $class = 'excerpt', bool $glyph_icon = false ): ?string {
		$post = get_post( $post );
		if ( ! Str::stripSpace( $post->post_excerpt ) ) {
			return null;
		}

		$open  = '';
		$close = '';
		$glyph = '';
		if ( true === $glyph_icon ) {
			$glyph = ' data-glyph=""';
		}
		if ( $class ) {
			$open  = '<div class="' . $class . '"' . $glyph . '>';
			$close = '</div>';
		}

		return $open . '<div>' . $post->post_excerpt . '</div>' . $close;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'term_excerpt' ) ) {
	/**
	 * @param int $term
	 * @param string|null $class
	 *
	 * @return string|null
	 */
	function term_excerpt( $term = 0, $class = 'excerpt' ): ?string {
		$description = term_description( $term );
		if ( ! Str::stripSpace( $description ) ) {
			return null;
		}

		if ( ! $class ) {
			return $description;
		}

		return "<div class=\"$class\">$description</div>";
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'primary_term' ) ) {
	/**
	 * @param             $post
	 * @param string|null $taxonomy
	 *
	 * @return array|bool|int|mixed|object|WP_Error|WP_Term|null
	 */
	function primary_term( $post, ?string $taxonomy = '' ) {
		$post = get_post( $post );
		$ID   = $post->ID ?? '';

		if ( ! $taxonomy ) {
			$post_type = get_post_type( $post );
			$taxonomy  = $post_type . '_cat';

			if ( 'post' == $post_type ) {
				$taxonomy = 'category';
			}

//            if ('product' == $post_type) {
//                $taxonomy = 'product_cat';
//            } elseif ('banner' == $post_type) {
//                $taxonomy = 'banner_cat';
//            } elseif ('service' == $post_type) {
//                $taxonomy = 'service_cat';
//            }
		}

		if ( $ID ) {
			// Rank Math SEO
			// https://vi.wordpress.org/plugins/seo-by-rank-math/
			$primary_term_id = get_post_meta( $ID, 'rank_math_primary_' . $taxonomy, true );
			if ( $primary_term_id ) {
				$term = get_term( $primary_term_id, $taxonomy );
				if ( $term && ! is_wp_error( $term ) ) {
					return $term;
				}
			}
		}

		// Yoast SEO
		// https://vi.wordpress.org/plugins/wordpress-seo/
		if ( class_exists( '\WPSEO_Primary_Term' ) ) {
			//$ID   = $post->ID ?? '';
			if ( $ID ) {

				// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
				$wpseo                 = new \WPSEO_Primary_Term( $taxonomy, $ID );
				$wpseo_primary_term_id = $wpseo->get_primary_term();
				$term                  = get_term( $wpseo_primary_term_id, $taxonomy );
				if ( $term && ! is_wp_error( $term ) ) {
					return $term;
				}
			}
		}

		// Default, first category
		$post_terms = get_the_terms( $post, $taxonomy );
		if ( is_array( $post_terms ) ) {
			return $post_terms[0];
		}

		return false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_primary_term' ) ) {
	/**
	 * @param        $post
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string $wrapper_close
	 *
	 * @return string|null
	 */
	function get_primary_term( $post = null, $taxonomy = '', string $wrapper_open = '<div class="terms">', string $wrapper_close = '</div>' ): ?string {
		$term = primary_term( $post, $taxonomy );
		if ( ! $term ) {
			return null;
		}

		$link = '<a href="' . esc_url( get_term_link( $term, $taxonomy ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}

		return $link;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'post_author' ) ) {

	/**
	 * @param $post_id
	 *
	 * @param string $wrapper_open
	 * @param string $wrapper_close
	 *
	 * @return string
	 */
	function post_author( $post_id, string $wrapper_open = '<div class="author" data-glyph="">', string $wrapper_close = '</div>' ): string {
		$link      = '';
		$author_id = get_post_field( 'post_author', $post_id );
		if ( $author_id ) {
			$display_name = get_the_author_meta( 'display_name', $author_id );
			$link         = '<a href="' . esc_url( get_author_posts_url( $author_id ) ) . '" title="' . esc_attr( $display_name ) . '">' . $display_name . '</a>';
		}

		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}

		return $link;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'post_terms' ) ) {
	/**
	 * @param        $id
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string $wrapper_close
	 *
	 * @return string|null
	 */
	function post_terms( $id, $taxonomy = 'category', string $wrapper_open = '<div class="terms">', string $wrapper_close = '</div>' ) {
		if ( ! $taxonomy ) {
			$taxonomy = 'category';
		}

		$link       = '';
		$post_terms = get_the_terms( $id, $taxonomy );
		if ( empty( $post_terms ) ) {
			return false;
		}

		foreach ( $post_terms as $term ) {
			if ( $term->slug ) {
				$link .= '<a href="' . esc_url( get_term_link( $term ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
			}
		}

		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}

		return $link;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_hashtags' ) ) {
	/**
	 * @param string $taxonomy
	 * @param int $id
	 * @param string $sep
	 *
	 * @return void
	 */
	function the_hashtags( $taxonomy = 'post_tag', int $id = 0, string $sep = '' ) {
		if ( ! $taxonomy ) {
			$taxonomy = 'post_tag';
		}

		// Get Tags for posts.
		$hashtag_list = get_the_term_list( $id, $taxonomy, '', $sep );

		// We don't want to output .entry-footer if it will be empty, so make sure its not.
		if ( $hashtag_list ) {
			echo '<div class="hashtags">';
			printf(
			/* translators: 1: SVG icon. 2: posted in label, only visible to screen readers. 3: list of tags. */
				'<div class="hashtag-links links">%1$s<span class="screen-reader-text">%2$s</span>%3$s</div>',
				'<i data-glyph="#"></i>',
				__( 'Tags', 'hd' ),
				$hashtag_list
			); // WPCS: XSS OK.

			echo '</div>';
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'post_image_src' ) ) {
	/**
	 * @param        $post_id
	 * @param string $size
	 *
	 * @return string|null
	 */
	function post_image_src( $post_id, string $size = 'thumbnail' ): ?string {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		if ( $thumbnail ) {
			return $thumbnail[0];
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'attachment_image_src' ) ) {
	/**
	 * @param        $attachment_id
	 * @param string $size
	 *
	 * @return string|null
	 */
	function attachment_image_src( $attachment_id, string $size = 'thumbnail' ): ?string {
		$image = wp_get_attachment_image_src( $attachment_id, $size );
		if ( $image ) {
			//[$src, $width, $height] = $image;
			return $image[0];
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_lang' ) ) {
	/**
	 * Get lang code
	 * @return string
	 */
	function get_lang(): string {
		return strtolower( substr( get_locale(), 0, 2 ) );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_f_locale' ) ) {
	/**
	 * @return string
	 */
	function get_f_locale(): string {
		$arr     = locale_array();
		$arr_key = array_keys( $arr );
		$locale  = get_locale();
		if ( ! in_array( $locale, $arr_key ) ) {
			return $locale;
		}

		return $arr[ $locale ];
	}
}


// -------------------------------------------------------------

if ( ! function_exists( 'locale_array' ) ) {
	/**
	 * @return array
	 */
	function locale_array(): array {
		return [
			'af' => 'af_ZA',
			'ar' => 'ar_AR',
			'az' => 'az_AZ',
			'ca' => 'ca_ES',
			'cy' => 'cy_GB',
			'el' => 'el_GR',
			'eo' => 'eo_EO',
			'et' => 'et_EE',
			'eu' => 'eu_ES',
			'fi' => 'fi_FI',
			'gu' => 'gu_IN',
			'hr' => 'hr_HR',
			'hy' => 'hy_AM',
			'ja' => 'ja_JP',
			'kk' => 'kk_KZ',
			'km' => 'km_KH',
			'lv' => 'lv_LV',
			'mn' => 'mn_MN',
			'mr' => 'mr_IN',
			'ps' => 'ps_AF',
			'sq' => 'sq_AL',
			'te' => 'te_IN',
			'th' => 'th_TH',
			'tl' => 'tl_PH',
			'uk' => 'uk_UA',
			'ur' => 'ur_PK',
			'vi' => 'vi_VN',
		];
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'placeholder_src' ) ) {
	/**
	 * placeholder_src function
	 *
	 * @param boolean $img_wrap
	 * @param boolean $thumb
	 *
	 * @return string
	 */
	function placeholder_src( bool $img_wrap = true, bool $thumb = true ): string {
		$src = W_THEME_URL . '/assets/img/placeholder.png';
		if ( $thumb ) {
			$src = W_THEME_URL . '/assets/img/placeholder-320x320.png';
		}
		if ( $img_wrap ) {
			$src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder\">";
		}

		return $src;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'acf_term_thumb' ) ) {
	/**
	 * @param        $term_id
	 * @param null $acf_field_name
	 * @param string $size
	 * @param false $img_wrap
	 *
	 * @return string|null
	 */
	function acf_term_thumb( $term_id, $acf_field_name = null, string $size = "thumbnail", bool $img_wrap = false ): ?string {
		if ( is_numeric( $term_id ) ) {
			$term_id = get_term( $term_id );
		}

		if ( function_exists( 'get_field' ) && $attach_id = get_field( $acf_field_name, $term_id ) ) {
			$img_src = attachment_image_src( $attach_id, $size );
			if ( $img_wrap ) {
				$img_src = wp_get_attachment_image( $attach_id, $size );
			}

			return $img_src;
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'humanize_time' ) ) {
	/**
	 * @param null $post
	 * @param null $from
	 * @param null $to
	 *
	 * @return mixed|void
	 */
	function humanize_time( $post = null, $from = null, $to = null, $format = false ) {
		$_ago = __( 'ago', 'hd' );

		if ( empty( $to ) ) {
			$to = current_time( 'U' );
		}
		if ( empty( $from ) ) {
			$from = get_the_time( 'U', $post );
		}

		if ( $format ) {
			$diff = (int) abs( $to - $from );

			$since = human_time_diff( $from, $to );
			$since = $since . ' ' . $_ago;

			return apply_filters( 'humanize_time', $since, $diff, $from, $to );
		}

		return date( get_option( 'date_format' ), $from );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_page_title_theme' ) ) {
	/**
	 * @param string $css_class
	 *
	 * @return void
	 */
	function the_page_title_theme( string $css_class = '' ) {
		get_template_part( 'template-parts/parts/page-title', null, [ 'css_class' => $css_class ] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_breadcrumbs' ) ) {
	/**
	 * Breadcrumbs
	 * return void
	 */
	function the_breadcrumbs() {
		global $post, $wp_query;

		$before = '<li class="current">';
		$after  = '</li>';

		if ( ! is_front_page() ) {

			echo '<ul id="breadcrumbs" class="breadcrumbs" aria-label="Breadcrumbs">';
			echo '<li><a class="home" href="' . Url::home() . '">' . __( 'Trang chủ', 'hd' ) . '</a></li>';

			//...
			if ( class_exists( '\WooCommerce' ) && @is_shop() ) {
				$shop_page_title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
				echo $before . $shop_page_title . $after;
			} elseif ( $wp_query->is_posts_page ) {
				$posts_page_title = get_the_title( get_option( 'page_for_posts', true ) );
				echo $before . $posts_page_title . $after;
			} elseif ( $wp_query->is_post_type_archive ) {
				$posts_page_title = post_type_archive_title( '', false );
				echo $before . $posts_page_title . $after;
			} /** page, attachment */
			elseif ( is_page() || is_attachment() ) {

				// parent page
				if ( $post->post_parent ) {
					$parent_id   = $post->post_parent;
					$breadcrumbs = [];

					while ( $parent_id ) {
						$page          = get_post( $parent_id );
						$breadcrumbs[] = '<li><a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a></li>';
						$parent_id     = $page->post_parent;
					}

					$breadcrumbs = array_reverse( $breadcrumbs );
					foreach ( $breadcrumbs as $crumb ) {
						echo $crumb;
					}
				}

				echo $before . get_the_title() . $after;
			} /** single */
			elseif ( is_single() && ! is_attachment() ) {

				if ( ! in_array( get_post_type(), [ 'post', 'product', 'service', 'project', 'video' ] ) ) {
					$post_type = get_post_type_object( get_post_type() );
					$slug      = $post_type->rewrite;
					if ( ! is_bool( $slug ) ) {
						echo '<li><a href="' . Url::home() . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
					}
				} else {
					$term = primary_term( $post );
					if ( $term ) {
						if ( $cat_code = get_term_parents_list( $term->term_id, $term->taxonomy, [ 'separator' => '' ] ) ) {
							$cat_code = str_replace( '<a', '<li><a', $cat_code );
							echo str_replace( '</a>', '</a></li>', $cat_code );
						}
					}
				}

				echo $before . get_the_title() . $after;
			} /** search page */
			elseif ( is_search() ) {
				echo $before;
				printf( __( 'Search Results for: %s', 'hd' ), get_search_query() );
				echo $after;
			} /** tag */
			elseif ( is_tag() ) {
				echo $before;
				printf( __( 'Tag Archives: %s', 'hd' ), single_tag_title( '', false ) );
				echo $after;
			} /** author */
			elseif ( is_author() ) {
				global $author;

				$userdata = get_userdata( $author );
				echo $before;
				echo $userdata->display_name;
				echo $after;
			} /** day, month, year */
			elseif ( is_day() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a></li>';
				echo $before . get_the_time( 'd' ) . $after;
			} elseif ( is_month() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo $before . get_the_time( 'F' ) . $after;
			} elseif ( is_year() ) {
				echo $before . get_the_time( 'Y' ) . $after;
			} /** category, tax */
			elseif ( is_category() || is_tax() ) {

				$cat_obj = $wp_query->get_queried_object();
				$thisCat = get_term( $cat_obj->term_id );

				if ( isset( $thisCat->parent ) && 0 != $thisCat->parent ) {
					$parentCat = get_term( $thisCat->parent );
					if ( $cat_code = get_term_parents_list( $parentCat->term_id, $parentCat->taxonomy, [ 'separator' => '' ] ) ) {
						$cat_code = str_replace( '<a', '<li><a', $cat_code );
						echo str_replace( '</a>', '</a></li>', $cat_code );
					}
				}

				echo $before . single_cat_title( '', false ) . $after;
			} /** 404 */
			elseif ( is_404() ) {
				echo $before;
				__( 'Not Found', 'hd' );
				echo $after;
			}

			//...
			if ( get_query_var( 'paged' ) ) {
				echo '<li class="paged">';
				echo ' (';
				echo __( 'page', 'hd' ) . ' ' . get_query_var( 'paged' );
				echo ')';
				echo $after;
			}

			echo '</ul>';
		}

		// reset
		wp_reset_query();
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_comment_html' ) ) {
	/**
	 * @param mixed $id The ID, to load a single record;
	 */
	function the_comment_html( $id = null ) {

		if ( get_post_type() === 'product' ) {
			global $product;
			$id = $product->get_id();
		}

		if ( is_null( $id ) ) {
			$id = get_post()->ID;
		}

		/*
		 * If the current post is protected by a password and
		 * the visitor has not yet entered the password we will
		 * return early without loading the comments.
		*/
		if ( post_password_required( $id ) ) {
			return;
		}

		$wrapper_open  = '<section id="comments-section" class="section comments-section comments-wrapper">';
		$wrapper_close = '</section>';

		//...
		$facebook_comment = false;
		$zalo_comment     = false;

		if ( class_exists( '\ACF' ) ) {
			$facebook_comment = get_field( 'facebook_comment', $id );
			$zalo_comment     = get_field( 'zalo_comment', $id );
		}

		if ( comments_open() || true === $facebook_comment || true === $zalo_comment ) {
			echo $wrapper_open;
			if ( comments_open() ) {
				//if ( ( class_exists( '\WooCommerce' ) && 'product' != $post_type ) || ! class_exists( '\WooCommerce' ) ) {
				comments_template();
				//}
			}
			if ( true === $facebook_comment ) {
				get_template_part( 'template-parts/comment/facebook' );
			}
			if ( true === $zalo_comment ) {
				get_template_part( 'template-parts/comment/zalo' );
			}

			echo $wrapper_close;
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'comment_enable' ) ) {
	/**
	 * @return bool
	 */
	function comment_enable() {
		return ( comments_open() || (bool) get_comments_number() ) && ! post_password_required();
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'menu_fallback' ) ) {
	/**
	 * A fallback when no navigation is selected by default.
	 *
	 * @return void
	 */
	function menu_fallback( $container = 'grid-container' ) {
		echo '<div class="menu-fallback">';
		if ( $container ) {
			echo '<div class="' . $container . '">';
		}

		/* translators: %1$s: link to menus, %2$s: link to customize. */
		printf(
			__( 'Please assign a menu to the primary menu location under %1$s or %2$s the design.', 'hd' ),
			/* translators: %s: menu url */
			sprintf(
				__( '<a class="_blank" href="%s">Menus</a>', 'hd' ),
				get_admin_url( get_current_blog_id(), 'nav-menus.php' )
			),
			/* translators: %s: customize url */
			sprintf(
				__( '<a class="_blank" href="%s">Customize</a>', 'hd' ),
				get_admin_url( get_current_blog_id(), 'customize.php' )
			)
		);
		if ( $container ) {
			echo '</div>';
		}
		echo '</div>';
	}
}
