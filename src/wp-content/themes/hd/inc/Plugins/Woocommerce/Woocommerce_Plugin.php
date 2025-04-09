<?php

namespace Webhd\Plugins\Woocommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// If plugin - 'Woocommerce' not exist then return.
if ( ! class_exists( '\WooCommerce' ) ) {
	return;
}

require __DIR__ . '/woocommerce.php';

if ( ! class_exists( 'Woocommerce_Plugin' ) ) {
	class Woocommerce_Plugin {
		public function __construct() {

			add_action( 'after_setup_theme', [ &$this, 'woocommerce_setup' ], 31 );
			add_action( 'woocommerce_share', [ &$this, 'woocommerce_share' ], 10 );
			add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 99 );

			add_action('woocommerce_before_shop_loop', function () {
                echo '<div class="woocommerce-sorting">';
            }, 7);

			add_action('woocommerce_before_shop_loop', function () {
                echo '</div>';
            }, 31);

			add_action('woocommerce_email', [&$this, 'woocommerce_email_hooks'], 100, 1);

			// Show only lowest prices in WooCommerce variable products
			add_filter( 'woocommerce_variable_sale_price_html', [ &$this, 'variation_price_format' ], 10, 2 );
			add_filter( 'woocommerce_variable_price_html', [ &$this, 'variation_price_format' ], 10, 2 );

			add_filter( 'body_class', [ &$this, 'woocommerce_body_class' ] );

            // woocommerce use priority 20
            add_filter( 'woocommerce_post_class', [ &$this, 'woocommerce_post_class' ], 21, 2 );
            add_filter( 'product_cat_class', [ &$this, 'product_cat_class' ], 21, 2 );

            // https://stackoverflow.com/questions/57321805/remove-header-from-the-woocommerce-administrator-panel
            add_action( 'admin_head', function () {
                //remove_action( 'in_admin_header', [ 'Automattic\WooCommerce\Internal\Admin\Loader', 'embed_page_header' ] );
                //remove_action( 'in_admin_header', [ 'Automattic\WooCommerce\Admin\Loader', 'embed_page_header' ] );

                echo '<style>#wpadminbar ~ #wpbody { margin-top: 0 !important; }.woocommerce-layout__header { display: none !important; }</style>';
            } );

            // Remove the default WooCommerce 3 JSON/LD structured data format
            add_action( 'init', function() {
                remove_action( 'wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10 );
                remove_action( 'woocommerce_email_order_details', [ WC()->structured_data, 'output_email_structured_data' ], 30 );
            }, 10 );

            // -------------------------------------------------------------

            // Display featured products first and Out of stock products at last in shop page
            add_filter('posts_clauses', function ($posts_clauses, \WP_Query $query) {
                global $wpdb;

                if ( $query->is_main_query() && ( is_product_tag() || is_product_taxonomy() || @is_shop() ) ) {
                    $featured_ids = wc_get_featured_product_ids();
                    $posts_clauses['join'] .= " LEFT JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '') ";
                    $posts_clauses['orderby'] = " if($wpdb->posts.ID in (0".(implode(",", $featured_ids))."), 0, 1), istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
                }

                return $posts_clauses;

            }, 10, 2);

            // -------------------------------------------------------------

            add_filter('woocommerce_product_single_add_to_cart_text', function () {
                return __('Thêm vào giỏ hàng', 'hd');
            });

            add_filter('woocommerce_product_add_to_cart_text', function () {
                return __('Thêm vào giỏ', 'hd');
            });

            // ---------------------------------------------------------------

            // Update WooCommerce Flexslider options
            add_filter( 'woocommerce_single_product_carousel_options', function ($options) {

                //$options['rtl'] = is_rtl();
                $options['animation'] = 'slide';
                $options['smoothHeight'] = true;
                $options['directionNav'] = false;
                $options['controlNav'] = 'thumbnails';
                $options['slideshow'] = true;
                //$options['animationSpeed'] = 500;
                //$options['animationLoop'] = false; // Breaks photoswipe pagination if true.
                //$options['allowOneSlide'] = false;

                return $options;
            } );

            // ---------------------------------------------------------------

			add_action( 'wp', [ &$this, 'visited_product_cookie' ] );

            //...
            add_action( 'woocommerce_product_video', [ &$this, 'acf_product_video'], 21 );
            add_action( 'woocommerce_product_thumbnails', [ &$this, 'acf_product_video'], 99 );

			/** Thông tin bổ sung bên trên mô tả trang chi tiết sản phẩm */
			add_action( 'woocommerce_single_product_summary', [&$this, 'acf_woocommerce_single_product_summary'], 31 );
			add_action( 'woocommerce_single_product_summary', [&$this, 'custom_contact_buttons'], 39 );

			//add_action( 'woocommerce_after_shop_loop_item', [ &$this, 'variations_loop_data'], 6 );
		}

        /** ---------------------------------------- */
        /** ---------------------------------------- */

		public function variations_loop_data() {
			global $product;

			$product = new \WC_Product_Variable( $product->get_id() );
			$variations = $product->get_available_variations();

			$data = [];
			if ( $variations ) {
				foreach ( $variations as $i => $item ) {
                    $_variation_id = $item['variation_id'] ?? $i;

					$data[$_variation_id]['variation_image_id'] = $item['variation_image_id'] ?? '';
					$data[$_variation_id]['price_html'] = $item['price_html'] ?? '';
					$data[$_variation_id]['attributes'] = $item['attributes'] ?? '';
				}
			}

			$data = json_encode( $data, JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE );

			?>
			<span class="_variations_loop" data-vars='<?= $data;?>'></span>
		<?php
		}

        /** ---------------------------------------- */

		public function custom_contact_buttons() {
			wc_get_template( 'single-product/custom-contact-buttons.php' );
		}

		public function acf_woocommerce_single_product_summary() {
			wc_get_template( 'single-product/acf-short-description.php' );
		}

        /**
         * @return void
         */
        public function enqueue_scripts() {

            wp_enqueue_style( "woocommerce-style", get_template_directory_uri() . '/assets/css/woocommerce.css', [ "layout-style" ], W_THEME_VERSION );

            $gutenberg_widgets_off = get_theme_mod_ssl( 'gutenberg_use_widgets_block_editor_setting' );
            $gutenberg_off         = get_theme_mod_ssl( 'use_block_editor_for_post_type_setting' );
            if ( $gutenberg_widgets_off && $gutenberg_off ) {

                // Remove WooCommerce block CSS
                wp_deregister_style( 'wc-blocks-vendors-style' );
                wp_deregister_style( 'wc-block-style' );
            }
        }

		/** ---------------------------------------- */
		/** ---------------------------------------- */

        /**
         * @return void
         */
        public function acf_product_video() {
            global $product;

            $video_link = get_product_video( $product->get_id(), 'video_link');
            //$video_poster_id = \get_field( 'video_poster', $product->get_id() ) ?? '';

            // $video_poster = attachment_image_src( $video_poster_id );
            // if ( ! $video_poster ) {
	        //     $video_poster = youtube_image( $video_link, [ 'default' ]);
            // }

            if ($video_link) {
                echo '<div class="product-vid">';
                echo '<a href="' . $video_link . '" data-glyph-after="" class="_blank fcy-video res ar-1-1"></a>';
                echo '</div>';
            }
        }

        /** ---------------------------------------- */
		/** ---------------------------------------- */

        /**
         * @return void
         */
        public function visited_product_cookie()
        {
            if (!is_singular('product')) {
                return;
            }

            global $post;

            if (empty($_COOKIE['woocommerce_recently_viewed'])) {
                $viewed_products = [];
            } else {
                $viewed_products = wp_parse_id_list((array) explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed'])));
            }

            $keys = array_flip($viewed_products);

            if (isset($keys[$post->ID])) {
                unset($viewed_products[$keys[$post->ID]]);
            }

            $viewed_products[] = $post->ID;

            if (count($viewed_products) > 22) {
                array_shift($viewed_products);
            }

            wc_setcookie('woocommerce_recently_viewed', implode('|', $viewed_products));
        }

        /** ---------------------------------------- */
        /** ---------------------------------------- */

		/**
		 * @param $mailer
		 */
		public function woocommerce_email_hooks($mailer)
		{
			add_action('woocommerce_order_status_pending_to_on-hold_notification', array(
				$mailer->emails['WC_Email_Customer_On_Hold_Order'],
				'trigger'
			));
		}

		/** ---------------------------------------- */
		/** ---------------------------------------- */

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return string
		 */
		public function variation_price_format( $price, $product ) {

			// Main Price
			$prices = [ $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) ];
			$price  = $prices[0] !== $prices[1] ? sprintf( __( '<span class="from">From:</span>%1$s', 'hd' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			// Sale Price
			$prices = [
				$product->get_variation_regular_price( 'min', true ),
				$product->get_variation_regular_price( 'max', true )
			];

			sort( $prices );
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( '<span class="from">From:</span>%1$s', 'hd' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			if ( $price !== $saleprice ) {
				$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}

			return $price;
		}

		/** ---------------------------------------- */
		/** ---------------------------------------- */

		/**
		 * Add 'woocommerce-active' class to the body tag
		 *
		 * @param array $classes css classes applied to the body tag.
		 *
		 * @return array $classes modified to include 'woocommerce-active' class
		 */
		public function woocommerce_body_class( $classes ) {
			$classes[] = 'wc-active';

			return $classes;
		}


		/** ---------------------------------------- */
		/** ---------------------------------------- */

        public function woocommerce_post_class( $classes, $product ) {
            if ( 'product' == get_post_type() ) {

                // remove product_cat- classes
                foreach ( $classes as $class ) {
                    if ( str_contains( $class, 'product_cat-' ) ) {
                        $classes = array_diff( $classes, [ $class ] );
                    }
                }
            }

            return $classes;
        }

        /** ---------------------------------------- */
		/** ---------------------------------------- */

        public function product_cat_class( $classes, $product ) {
            return $classes;
        }

        /** ---------------------------------------- */
		/** ---------------------------------------- */

		/**
		 * woocommerce_share action
		 * @return void
		 */
		public function woocommerce_share() {
			get_template_part( 'template-parts/parts/sharing' );
		}

		/** ---------------------------------------- */
		/** ---------------------------------------- */

		/**
		 * Woocommerce setup
		 *
		 * @return void
		 */
		public function woocommerce_setup() {

            add_theme_support('woocommerce');

			// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
			// Add support for WC features.
			//add_theme_support( 'wc-product-gallery-zoom' );
			//add_theme_support( 'wc-product-gallery-lightbox' );
			//add_theme_support( 'wc-product-gallery-slider' );

			// Remove default WooCommerce wrappers.
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

			remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

            // below add to cart
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
            add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 8 );

			/** */

			// Declare WooCommerce support.
			add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function ( $size ) {
				return [
					'width'  => 180,
					'height' => 180,
					'crop'   => 0,
				];
			} );

			// Trim zeros in price decimals
			add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

			/**
			 * @param array $args
			 *
			 * @return array
			 */
			add_filter( 'woocommerce_product_tag_cloud_widget_args', function ( array $args ) {
				$args['smallest'] = '11';
				$args['largest']  = '19';
				$args['unit']     = 'px';
				$args['number']   = 12;

				return $args;
			} );

			/**
			 * @param array $args
			 * @return array
			 */
			add_filter('woocommerce_output_related_products_args', function (array $args) {
				$args['columns'] = 4;
				$args['posts_per_page'] = 12;

				return $args;
			}, 20, 1);

			/**
			 * @param $orders
			 *
			 * @return array
			 */
			add_filter('woocommerce_catalog_orderby', function ($orders) {
				$orders = array(
                    'date' => __('Mới nhất', 'hd'),
                    'popularity' => __('Phổ biến', 'hd'),
					'menu_order' => __('Thứ tự sắp xếp', 'hd'),
					'rating' => __('Đánh giá trung bình', 'hd'),
					'price' => __('Giá thấp đến cao', 'hd'),
					'price-desc' => __('Giá cao đến thấp', 'hd'),
				);

				return $orders;
			}, 100, 1);

			/**
			 * @param $defaults
			 *
			 * @return array
			 */
			add_filter( 'woocommerce_breadcrumb_defaults', function ($defaults) {
				$defaults = [
					'delimiter'   => '',
					'wrap_before' => '<ol id="crumbs" class="breadcrumbs" aria-label="breadcrumbs">',
					'wrap_after'  => '</ol>',
					'before'      => '<li><span property="itemListElement" typeof="ListItem">',
					'after'       => '</span></li>',
					'home'        => _x( 'Home', 'breadcrumb', 'hd' ),
				];

				return $defaults;
			}, 11, 1 );
		}
	}
}
