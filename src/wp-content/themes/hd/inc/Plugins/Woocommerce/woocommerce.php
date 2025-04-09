<?php

use Webhd\Helpers\Str;

if (!defined('ABSPATH')) {
	exit;
}

// ----------------------------------------

if ( ! function_exists( 'woocommerce_product_specifications_tab' ) ) {

    /**
     * @return void
     */
    function woocommerce_product_specifications_tab()
    {
        wc_get_template( 'single-product/tabs/specifications.php' );
    }
}

// ----------------------------------------

if ( ! function_exists( 'woocommerce_product_policy_tab' ) ) {

    /**
     * @return void
     */
    function woocommerce_product_policy_tab() {
        wc_get_template( 'single-product/tabs/policy.php' );
    }
}

// ----------------------------------------

if ( ! function_exists( 'woocommerce_product_certificate_tab' ) ) {

    /**
     * @return void
     */
    function woocommerce_product_certificate_tab() {
        wc_get_template( 'single-product/tabs/certificate.php' );
    }
}

// ----------------------------------------

if ( ! function_exists( 'woocommerce_product_document_tab' ) ) {

	/**
	 * @return void
	 */
	function woocommerce_product_document_tab() {
		wc_get_template( 'single-product/tabs/document.php' );
	}
}

// ----------------------------------------

/**
 * Add default product tabs to product pages.
 *
 * @param array $tabs Array of tabs.
 * @return array
 */
function woocommerce_default_product_tabs( $tabs = array() ) {
    global $product, $post;

    // Description tab - shows product content.
    if ( $post->post_content ) {
        $tabs['description'] = array(
            'title'    => __( 'Tổng quan', 'hd' ),
            'priority' => 10,
            'callback' => 'woocommerce_product_description_tab',
        );
    }

    // Additional information tab - shows attributes.
    if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
        $tabs['additional_information'] = array(
            'title'    => __( 'Additional information', 'woocommerce' ),
            'priority' => 20,
            'callback' => 'woocommerce_product_additional_information_tab',
        );
    }

    // certificate
    if ( $product && function_exists('get_field')) {
        $certificate = get_field('certificate', $product->get_id());
        if (Str::stripSpace($certificate)) {
            $tabs['certificate'] = [
                'title'    => __( 'Chứng chỉ', 'hd' ),
                'priority' => 30,
                'callback' => 'woocommerce_product_certificate_tab',
            ];
        }
    }

    // policy
    if ( $product && function_exists('get_field')) {
        $policy = get_field('policy', $product->get_id());
        if (Str::stripSpace($policy)) {
            $tabs['policy'] = [
                'title'    => __( 'Chính sách', 'hd' ),
                'priority' => 31,
                'callback' => 'woocommerce_product_policy_tab',
            ];
        }
    }

	// document
	if ( $product && function_exists('get_field')) {
		$document = get_field('document', $product->get_id());
		if (Str::stripSpace($document)) {
			$tabs['document'] = [
				'title'    => __( 'Tài liệu', 'hd' ),
				'priority' => 32,
				'callback' => 'woocommerce_product_document_tab',
			];
		}
	}

    // Specifications
    if ( $product && function_exists('get_field')) {
        $specifications = get_field('specifications', $product->get_id());
        if (Str::stripSpace($specifications)) {
            $tabs['specifications'] = [
                'title'    => __( 'Thông số kỹ thuật', 'hd' ),
                'priority' => 32,
                'callback' => 'woocommerce_product_specifications_tab',
            ];
        }
    }

    // Reviews tab - shows comments.
    $facebook_comment = false;
    $zalo_comment = false;
    if ( class_exists( '\ACF' ) ) {
        $facebook_comment = get_field('facebook_comment', $product->get_id());
        $zalo_comment = get_field('zalo_comment', $product->get_id());
    }

    if ( comments_open() || true === $facebook_comment || true === $zalo_comment ) {
    //if ( comments_open() ) {
        $tabs['reviews'] = array(
            /* translators: %s: reviews count */
            //'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
            'title'    => __( 'Đánh giá', 'hd' ),
            'priority' => 40,
            //'callback' => 'comments_template',
            'callback' => 'the_comment_html',
        );
    }

    return $tabs;
}

// ----------------------------------------

/**
 * Show an archive description on taxonomy archives.
 */
function woocommerce_taxonomy_archive_description() {
    // Don't display the description on search results page.
    if ( is_search() ) {
        return;
    }

    if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
        $term = get_queried_object();
        if ( $term ) {

            $seo_desc = null;
            if (function_exists('get_fields')) {
                $ACF = get_fields($term);
                isset($ACF['seo_desc']) && $seo_desc = $ACF['seo_desc'];
            }

            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

            /**
             * Filters the archive's raw description on taxonomy archives.
             *
             * @since 6.7.0
             *
             * @param string  $term_description Raw description text.
             * @param WP_Term $term             Term object for this taxonomy archive.
             */
            $term_description = apply_filters( 'woocommerce_taxonomy_archive_description_raw', $term->description, $term );

            //if ( $thumbnail_id  || $seo_desc || $term_description) {
            if ( $seo_desc || $term_description) {
                echo '<div class="woo-description product-desc-inner"><div class="grid-container width-extra">';
                //if ($thumbnail_id) :
                    //echo '<div class="term-thumb">' . wp_get_attachment_image($thumbnail_id, 'post-thumbnail') . '</div>';
                //endif;
                if ($term_description) :
                    echo '<div class="term-description">' . wc_format_content( wp_kses_post( $term_description ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                endif;
                if ($seo_desc) :
                    $seo = get_post($seo_desc);
                    echo '<div class="seo-description">' . get_the_content(null, false, $seo) . '</div>';
                endif;
                echo '</div></div>';
            }
        }
    }
}

// ----------------------------------------

/**
 * Show a shop page description on product archives.
 */
function woocommerce_product_archive_description() {
    // Don't display the description on search results page.
    if ( is_search() ) {
        return;
    }

    if ( is_post_type_archive( 'product' ) && in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true ) ) {
        $shop_page = get_post( wc_get_page_id( 'shop' ) );
        if ( $shop_page ) {

            $allowed_html = wp_kses_allowed_html( 'post' );

            // This is needed for the search product block to work.
            $allowed_html = array_merge(
                $allowed_html,
                array(
                    'form'   => array(
                        'action'         => true,
                        'accept'         => true,
                        'accept-charset' => true,
                        'enctype'        => true,
                        'method'         => true,
                        'name'           => true,
                        'target'         => true,
                    ),

                    'input'  => array(
                        'type'        => true,
                        'id'          => true,
                        'class'       => true,
                        'placeholder' => true,
                        'name'        => true,
                        'value'       => true,
                    ),

                    'button' => array(
                        'type'  => true,
                        'class' => true,
                        'label' => true,
                    ),

                    'svg'    => array(
                        'hidden'    => true,
                        'role'      => true,
                        'focusable' => true,
                        'xmlns'     => true,
                        'width'     => true,
                        'height'    => true,
                        'viewbox'   => true,
                    ),
                    'path'   => array(
                        'd' => true,
                    ),
                )
            );

            $post_thumbnail = get_the_post_thumbnail($shop_page, 'post-thumbnail');
            $description = wc_format_content( wp_kses( $shop_page->post_content, $allowed_html ) );
            //if ( $description  || $post_thumbnail) {
            if ( $description) {
                echo '<div class="woo-description product-desc-inner"><div class="grid-container width-extra">';
                //if ($post_thumbnail) :
                    //echo '<div class="page-thumb">' . $post_thumbnail . '</div>';
                //endif;
                if ($description) :
                echo '<div class="page-description">' . $description . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                endif;
                echo '</div></div>';
            }
        }
    }
}

// ----------------------------------------

if (!function_exists('woo_cart_available')) {
	/**
	 * Validates whether the Woo Cart instance is available in the request
	 *
	 * @return bool
	 */
	function woo_cart_available()
	{
		$woo = WC();
		return $woo instanceof \WooCommerce && $woo->cart instanceof \WC_Cart;
	}
}

// ----------------------------------------

if (!function_exists('woo_cart_link_fragment')) {

	add_filter( 'woocommerce_add_to_cart_fragments', 'woo_cart_link_fragment', 11, 1 );

	/**
	 * Cart Fragments
	 * Ensure cart contents update when products are added to the cart via AJAX
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array            Fragments to refresh via AJAX
	 */
	function woo_cart_link_fragment($fragments)
	{
		global $woocommerce;

		ob_start();
		woo_cart_link();
		$fragments['a.header-cart-contents'] = ob_get_clean();

		ob_start();
		woo_handheld_footer_bar_cart_link();
		$fragments['a.footer-cart-contents'] = ob_get_clean();

		return $fragments;
	}
}

// ----------------------------------------

if (!function_exists('woo_handheld_footer_bar_cart_link')) {
	/**
	 * The cart callback function for the handheld footer bar
	 *
	 * @since 2.0.0
	 */
	function woo_handheld_footer_bar_cart_link()
	{
		if (!woo_cart_available()) {
			return;
		}
		?>
		<a class="footer-cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_attr_e('View your shopping cart', 'hd'); ?>">
			<span class="count"><?php echo wp_kses_data(WC()->cart->get_cart_contents_count()); ?></span>
		</a>
		<?php
	}
}

// ----------------------------------------

if (!function_exists('woo_cart_link')) {

	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 * @return void
	 * @since  1.0.0
	 */
	function woo_cart_link()
	{
		if (!woo_cart_available()) {
			return;
		}
		?>
		<a class="header-cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php echo esc_attr__( 'View your shopping cart', 'hd' ); ?>">
			<?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?>
			<div class="icon" data-glyph-after=""></div>
			<span class="count notranslate"><?php echo wp_kses_data(sprintf('%d', WC()->cart->get_cart_contents_count())); ?></span>
            <span class="txt"><?php echo __( 'Giỏ hàng', 'hd')?></span>
		</a>
		<?php
	}
}

// -------------------------------------------------------------

if (!function_exists('woo_header_cart')) {
	/**
	 * Display Header Cart
	 *
	 * @return void
	 * @uses  woocommerce_activated() check if WooCommerce is activated
	 * @since  1.0.0
	 */
	function woo_header_cart()
	{
		if (class_exists( '\WooCommerce' )) {
			if (is_cart()) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
			?>
			<ul id="site-header-cart" class="site-header-cart menu">
				<li class="<?php echo esc_attr($class); ?>">
					<?php woo_cart_link(); ?>
				</li>
				<li class="widget-menu-item">
					<?php the_widget('WC_Widget_Cart', 'title='); ?>
				</li>
			</ul>
			<?php
		}
	}
}

// -------------------------------------------------------------

if (!function_exists('get_product_video')) {
    /**
     * @param $product_id
     * @param string $acf_field
     * @return false|mixed
     */
    function get_product_video($product_id, string $acf_field = 'video_link')
    {
        if ( class_exists('\ACF') && $product_id ) {
            $vid_url = get_field($acf_field, $product_id);
            if ($vid_url && filter_var($vid_url, FILTER_VALIDATE_URL)) {
                return $vid_url;
            }
        }

        return false;
    }
}

// -------------------------------------------------------------

if (!function_exists('hd_get_gallery_image_html')) {
    /**
     * @param      $attachment_id
     * @param bool $main_image
     * @param bool $cover
     * @param bool $lightbox
     * @return string
     */
    function hd_get_gallery_image_html($attachment_id, bool $main_image = false, bool $cover = false, bool $lightbox = false ): string {

        $gallery_thumbnail = wc_get_image_size( 'large' );
        $thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
        $image_size        = apply_filters( 'woocommerce_gallery_image_size', $main_image ? 'woocommerce_single' : $thumbnail_size );
        $full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
        $thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
        $full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
        $alt_text = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
        $image             = wp_get_attachment_image(
            $attachment_id,
            $image_size,
            false,
            apply_filters(
                'woocommerce_gallery_image_html_attachment_image_params',
                array(
                    'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                    'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
                    'data-src'                => esc_url( $full_src[0] ),
                    'data-large_image'        => esc_url( $full_src[0] ),
                    'data-large_image_width'  => esc_attr( $full_src[1] ),
                    'data-large_image_height' => esc_attr( $full_src[2] ),
                    'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
                ),
                $attachment_id,
                $image_size,
                $main_image
            )
        );

        $ratio = get_theme_mod_ssl('product_menu_setting');
        $ratio_class = $ratio;
        if ($ratio == 'default' || empty($ratio)) {
            $ratio_class = '1-1';
        }

        $auto = $cover ? '' : ' auto';

        if ($lightbox) {
            $popup_image = '<span data-rel="lightbox" class="image-popup" data-src="'. esc_url($full_src[0]) .'" data-glyph=""></span>';
            return '<div data-thumb="' . esc_url($thumbnail_src[0]) . '" data-thumb-alt="' . esc_attr($alt_text) . '" class="wpg__image cover"><a class="res' . $auto . ' ar-' . $ratio_class . '" href="' . esc_url($full_src[0]) . '">' . $image . '</a>' . $popup_image . '</div>';
        }

        return '<div data-thumb="' . esc_url($thumbnail_src[0]) . '" data-thumb-alt="' . esc_attr($alt_text) . '" class="woocommerce-product-gallery__image wpg__thumb cover"><a class="res' . $auto . ' ar-' . $ratio_class . '" href="' . esc_url($full_src[0]) . '">' . $image . '</a></div>';
    }
}

// -------------------------------------------------------------

if (!function_exists('woo_get_gallery_image_html')) {
	/**
	 * @param      $attachment_id
	 * @param bool $main_image
	 *
	 * @return string
	 */
	function woo_get_gallery_image_html($attachment_id, $main_image = false)
	{
		$flexslider = (bool)apply_filters(
			'woocommerce_single_product_flexslider_enabled',
			get_theme_support('wc-product-gallery-slider')
		);
		$gallery_thumbnail = wc_get_image_size('gallery_thumbnail');
		$thumbnail_size = apply_filters('woocommerce_gallery_thumbnail_size', array(
			$gallery_thumbnail['width'],
			$gallery_thumbnail['height']
		));

		$image_size = apply_filters(
			'woocommerce_gallery_image_size',
			$flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size
		);
		$full_size = apply_filters(
			'woocommerce_gallery_full_size',
			apply_filters('woocommerce_product_thumbnails_large_size', 'full')
		);
		$thumbnail_src = wp_get_attachment_image_src($attachment_id, $thumbnail_size);
		$full_src = wp_get_attachment_image_src($attachment_id, $full_size);
		$alt_text = trim(wp_strip_all_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
		$image = wp_get_attachment_image(
			$attachment_id,
			$image_size,
			false,
			apply_filters(
				'woocommerce_gallery_image_html_attachment_image_params',
				array(
					'title' => _wp_specialchars(
						get_post_field('post_title', $attachment_id),
						ENT_QUOTES,
						'UTF-8',
						true
					),
					'data-caption' => _wp_specialchars(
						get_post_field('post_excerpt', $attachment_id),
						ENT_QUOTES,
						'UTF-8',
						true
					),
					'data-src' => esc_url($full_src[0]),
					'data-large_image' => esc_url($full_src[0]),
					'data-large_image_width' => esc_attr($full_src[1]),
					'data-large_image_height' => esc_attr($full_src[2]),
					'class' => esc_attr($main_image ? 'wp-post-image' : ''),
				),
				$attachment_id,
				$image_size,
				$main_image
			)
		);
		$ratio = get_theme_mod_ssl('product_menu_setting');
		$ratio_class = $ratio;
		if ($ratio == 'default' || empty($ratio)) {
			$ratio_class = '1-1';
		}

		return '<div data-thumb="' . esc_url($thumbnail_src[0]) . '" data-thumb-alt="' . esc_attr($alt_text) . '" class="woocommerce-product-gallery__image"><a class="res auto ar-' . $ratio_class . '" href="' . esc_url($full_src[0]) . '">' . $image . '</a></div>';
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'sale_flash_percent' ) ) {
    /**
     * @param $product
     * @return float
     */
    function sale_flash_percent($product)
    {
        global $product;
        $percent_off = '';

        if ($product->is_on_sale()) {

            if ($product->is_type('variable')) {
                $percent_off = ceil(100 - ($product->get_variation_sale_price() / $product->get_variation_regular_price('min')) * 100);
            } elseif ($product->get_regular_price() && !$product->is_type('grouped')) {
                $percent_off = ceil(100 - ($product->get_sale_price() / $product->get_regular_price()) * 100);
            }
        }

        return $percent_off;
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'woocommerce_template_loop_quick_view' ) ) {

    /**
     * Get the quick view template for the loop.
     *
     * @param array $args Arguments.
     */
    function woocommerce_template_loop_quick_view( $args = [] ) {
        global $product;

        if ( $product ) {
            $defaults = array(
                'class'      => implode(
                    ' ',
                    array_filter(
                        array(
                            'button',
                            'product_type_' . $product->get_type()
                        )
                    )
                ),
                'attributes' => array(
                    'data-product_id'  => $product->get_id(),
                    'aria-label'       => __( 'Quick View', 'hd' ),
                    'rel'              => 'nofollow',
                ),
            );

            $args = apply_filters( 'woocommerce_loop_quick_view_args', wp_parse_args( $args, $defaults ), $product );

            if ( isset( $args['attributes']['aria-label'] ) ) {
                $args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
            }

            wc_get_template( 'loop/quick-view.php', $args );
        }
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'woocommerce_template_single_meta_sku' ) ) {

    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta_sku', 9 );

    /**
     * Output the product meta.
     */
    function woocommerce_template_single_meta_sku() {
        wc_get_template( 'single-product/meta_sku.php' );
    }
}

// -------------------------------------------------------------

if ( ! function_exists( 'woocommerce_output_recently_viewed_products' ) ) {

    // add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_recently_viewed_products', 25 );
    // add_action( 'woocommerce_after_single_product_relation', 'woocommerce_output_recently_viewed_products', 25 );
    // add_action( 'woocommerce_after_shop', 'woocommerce_output_recently_viewed_products', 19 );

    /**
     * Output the product sale flash.
     */
    function woocommerce_output_recently_viewed_products() {
        wc_get_template( 'single-product/recently_viewed.php' );
    }
}

// -------------------------------------------------------------

/**
 * Insert the opening anchor tag for products in the loop.
 */
function woocommerce_template_loop_product_link_open() {
	global $product;

	$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
	echo '<a href="' . esc_url( $link ) . '" class="product_link" aria-label="' . get_the_title() . '">';
}

/**
 * Show the product title in the product loop. By default this is an H2.
 */
function woocommerce_template_loop_product_title() {

	global $product;

//	$ACF           = \get_fields( $product->get_id() ) ?? '';
//	$label_txt     = $ACF['label_txt'] ?? '';
//	$label_color   = $ACF['label_color'] ?? '';
//	$label_bgcolor = $ACF['label_bgcolor'] ?? '';

	$_hot_label = '';
//	if ( $label_txt ) {
//		$_css = '';
//		if ( $label_bgcolor ) {
//			$_css .= 'background-color:' . $label_bgcolor . ';';
//		}
//		if ( $label_color ) {
//			$_css .= 'color:' . $label_color . ';';
//		}
//		if ( $_css ) {
//			$_css = ' style="' . $_css . '"';
//		}
//
//		$_hot_label = '<sup class="hot-label" ' . $_css . '>' . $label_txt . '</sup>';
//	}

	$ACF = \get_fields( $product->get_id() ) ?? '';

    $label_txt = $ACF['label_txt'] ?? '';
    $label_img = $ACF['label_img'] ?? '';
    $label_color = $ACF['label_color'] ?? '';
    $label_bgcolor1 = $ACF['label_bgcolor1'] ?? '';
    $label_bgcolor2 = $ACF['label_bgcolor2'] ?? '';

    //...
    $result_labels = '';
    $result_labels_css = '';
    if ( $label_txt ) {

        if ( $label_color ) $result_labels_css .= ' color:' . $label_color . ';';
        if ( $label_bgcolor1 && $label_bgcolor2 ) {
	        $result_labels_css .= ' background:linear-gradient(to right, ' . $label_bgcolor1 . ', ' . $label_bgcolor2 . ');';
        } elseif ( $label_bgcolor1 ) {
	        $result_labels_css .= ' background-color:' . $label_bgcolor1 . ';' ;
        } elseif ( $label_bgcolor2 ) {
	        $result_labels_css .= ' background-color:' . $label_bgcolor2 . ';' ;
        }

        if ( $result_labels_css ) {
            $result_labels_css = ' style="' . $result_labels_css . '"';
        }

        if ( $label_img ) {
            $result_labels .= wp_get_attachment_image( $label_img, 'thumbnail' );
        }

	    $result_labels .= '<span>' . $label_txt . '</span>';
        echo '<div class="result-labels"' . $result_labels_css . '>' . $result_labels . '</div>';
    }

    //...
    $acf_products_attrs = $ACF['acf_products_attrs'] ?? '';
    if ( $acf_products_attrs ) {

	    $_labels = '';
        foreach ( $acf_products_attrs as $attr_id ) {
            $attr = get_term( $attr_id, 'product_tag' );

            if ( $attr->name ) {
	            $_labels .= '<label>' . $attr->name . '</label>';
            }
        }

        if ( $_labels ) echo '<div class="labels">' . $_labels . '</div>';
    }

	echo '<h3 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h3>';
}

/**
 * Get the product thumbnail for the loop.
 */
function woocommerce_template_loop_product_thumbnail() {

	global $product;

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$thumbnail = woocommerce_get_product_thumbnail( 'medium' );

	$ratio = get_theme_mod_ssl( 'product_menu_setting' );
	$ratio_class = $ratio;
	if ($ratio == 'default' || empty($ratio)) {
        $ratio_class = '1-1';
	}

    $_class = "after-overlay res scale ar-" . $ratio_class;
    $second_thumb = \get_field( 'second_thumbs', $product->get_id() );
    if ( $second_thumb ) {
	    $_class = "after-overlay has-second-thumb res auto ar-" . $ratio_class;
    }

    ?>
    <div class="cover">
        <span class="<?= $_class ?>">
            <?php echo $thumbnail; ?>
            <?php echo wp_get_attachment_image( $second_thumb, 'medium' ); ?>
        </span>
    </div>
    <?php
}

// Xoa "Add to Cart" mac dinh
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 12);
// Mo div boc Price và "Add to Cart"
add_action('woocommerce_after_shop_loop_item_title', 'custom_wrap_price_and_add_to_cart', 9);
function custom_wrap_price_and_add_to_cart() {
    echo '<div class="group-price_cart">';
}
// Dong div boc Price và "Add to Cart"
add_action('woocommerce_after_shop_loop_item_title', 'custom_close_wrap_price_and_add_to_cart', 15);
function custom_close_wrap_price_and_add_to_cart() {
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'custom_model', 6);
function custom_model(){
    $model = get_field('model');
    $model_curr = get_field('name_model');
    $name_model_current = get_field('name_model_current');
    $queried_id = get_queried_object()->ID;
    if($model_curr || $name_model_current){
        echo '<div class="custom-model">';
        echo '<span class="curr">'. $model_curr .'</span>';
        echo '<div class="model-wrapper">';
        echo '<div>Model:';
        echo '<span class="selected-model">'.' '. $model_curr .'<span class="text">'.' '.$name_model_current.'</span></span>';
        echo '</div>';
        echo '</div>';
        echo '<div class="model-variants">';
        if($name_model_current){ ?>
        <a href="<?php echo get_the_permalink($queried_id); ?>" class="item active"><?php echo $name_model_current; ?></a><?php } ?>
        <?php if(get_field('model')){
        foreach($model as $val){ ?>
            <a href="<?php echo get_the_permalink($val['sl_model']->ID); ?>" class="item"><?php echo $val['name']; ?></a>
            <?php //print_r($val['sl_model']); ?>
        <?php }
        }
        echo '</div>';
        echo '</div>';
    }
}
// 
add_action('woocommerce_after_single_product_summary', 'after_posts', 12);
function after_posts(){
    if ( is_active_sidebar( 'w-after-post-sidebar' ) ) {
        echo '<div class="after-posts after-products">';
        dynamic_sidebar( 'w-after-post-sidebar' );
        echo '</div>';
    }
}
// Add button "Tiep tuc mua hang" khi ajax gio hang
add_action( 'woocommerce_widget_shopping_cart_buttons', 'custom_button_minicart', 29 );
function custom_button_minicart(){
    echo '<a href="javascript:void(0)" class="button continue">Tiếp tục</a>';
}
// Function chuyen khong nhap gia chuyen thanh Lien He
function wc_custom_get_price_html( $price, $product ) {
    if ( ! $product->get_price() ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
            $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
        } else {
            $price = '<span class="amount price-contact">' . __( 'Liên hệ', 'woocommerce' ) . '</span>';
        }
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'wc_custom_get_price_html', 10, 2 );

// An o input email trong phan binh luan san pham
function hide_email_field_on_comment(){
    if( !is_admin() && is_product() ){
        ?>
        <style type="text/css">
            .comment-form-email{
                display: none;
            }
        </style>
        <?php
    }
}
add_action( 'wp_head', 'hide_email_field_on_comment', 10, 1 );
// Xoa phan hien thi email khi da binh luan
function remove_dummy_email( $commentdata ){
    unset( $commentdata['comment_author_email'] );
    return $commentdata;
}
add_filter( 'preprocess_comment', 'remove_dummy_email', 10, 1 );
// 
// Edit form review comment in single product
add_filter( 'comment_form_defaults', 'render_tel_fields_for_anonymous_users', 99, 1 );
add_action( 'comment_form_top', 'render_tel_fields_for_authorized_users' );
function get_tel_fields_html() {
	ob_start(); ?>
    <p class="comment-form-tel">
        <label for="tel">Số điện thoại*</label>
        <input id="tel" name="tel" type="tel" size="30" required pattern="[0-9]{10}">
    </p>
	<?php
	return ob_get_clean();
}
function render_tel_fields_for_authorized_users() {
	if ( ! is_product() || ! is_user_logged_in() ) {
		return;
	}
	echo get_tel_fields_html();
}
function render_tel_fields_for_anonymous_users( $defaults ) {
	if ( ! is_product() || is_user_logged_in() ) {
		return;
	}
	$defaults['comment_notes_before'] .= get_tel_fields_html();
	return $defaults;
}
// Save
add_action( 'comment_post', 'save_review_tel', 10, 3 );
function save_review_tel( $comment_id, $approved, $commentdata ) {
	$tel = isset( $_POST['tel'] ) ? $_POST['tel'] : '';
	update_comment_meta( $comment_id, 'tel', esc_html( $tel ) );
}

//add_filter( 'comment_text', 'add_tel_to_review_text', 10, 1 );
// function add_tel_to_review_text( $text ) {
// 	if ( is_admin() || ! is_product() ) {
// 		return $text;
// 	}
// 	$tel = get_comment_meta( get_comment_ID(), 'tel', true );
// 	$tel_html = '<div class="pcf-row">' . esc_html( $tel ) . '</div>';
// 	$updated_text = $tel_html . $text;
// 	return $updated_text;
// }
add_action( 'add_meta_boxes_comment', 'extend_comment_add_meta_box', 10, 1 );
function extend_comment_add_meta_box( $comment ) {
	$post_id = $comment->comment_post_ID;
	$product = wc_get_product( $post_id );
	if ( $product === null || $product === false ) {
		return;
	}
    add_meta_box( 'pcf_fields', 'Tel', 'render_pcf_fields_metabox', 'comment', 'normal', 'high' );
}

function render_pcf_fields_metabox ( $comment ) {
    $tel = get_comment_meta( $comment->comment_ID, 'tel', true );
    wp_nonce_field( 'pcf_metabox_update', 'pcf_metabox_nonce', false ); ?>
    <p>
        <label for="phone">Số điện thoại:</label>
        <input type="tel" name="tel" id="tel" value="<?php echo esc_attr( $tel ); ?>" class="widefat" />
    </p>
    <?php
}

add_action( 'edit_comment', 'save_pcf_changes', 10, 1 );
function save_pcf_changes( $comment_id ) {
	if ( ! isset( $_POST['pcf_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['pcf_metabox_nonce'], 'pcf_metabox_update') ) {
		wp_die( 'You can not do this action' );
	}
	if ( isset( $_POST['tel'] ) ) {
		$tel = $_POST['tel'];
		update_comment_meta( $comment_id, 'tel', esc_html( $tel ) );
	}
}

function add_form_after_review() {
    echo '<div class="form_quote">';
    echo do_shortcode('[contact-form-7 id="a72a328" title="Form Đăng ký báo giá"]');
    echo '</div>';
}
add_action('comment_form_after', 'add_form_after_review');
