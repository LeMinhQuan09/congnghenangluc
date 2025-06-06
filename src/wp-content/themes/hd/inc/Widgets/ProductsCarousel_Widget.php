<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class ProductsCarousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter products carousel by category.', 'hd' );
		$this->widget_name        = __( 'W - Products Carousels', 'hd' );
		$this->settings           = [
			'title' => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'hd' ),
			],
		];

		parent::__construct();
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}

		// class
		$_class = $this->id;
		if ( $ACF->css_class ) {
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$sub_title = $ACF->sub_title ?? '';
		$html_desc = $ACF->html_desc ?? '';
		$banner_bg = $ACF->banner_bg ?? '';

		//...
		$r = query_by_terms( $ACF->product_cat, 'product_cat', 'product', $ACF->include_children, $ACF->number );
		if ( ! $r ) {
			return;
		} ?>
        <section class="section carousels-section filter-products <?= $_class ?>">
	        <?php if ( ! $ACF->full_width ) {
		        echo '<div class="grid-container width-extra">';
	        } ?>
			<?php if ( $banner_bg ) : ?>
                <div class="promo-banner">
                    <span class="cover"><?php echo wp_get_attachment_image( $banner_bg, 'widescreen' ); ?></span>
                </div>
			<?php endif; ?>
			<?php if ( $title || $html_desc || $sub_title ) : ?>
                <div class="title-container">
	                <?php if ( $sub_title ) : ?>
                    <p class="sub-title"><?php echo $sub_title; ?></p>
	                <?php endif; ?>
					<?php if ( $title ) : ?>
                        <h2 class="heading-title"><?php echo $title; ?></h2>
					<?php endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                        <div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>

	                <?php if ( $ACF->url ) : ?>
                    <a href="<?= esc_url( $ACF->url ) ?>" class="viewmore viewmore-button"
                       title="<?php echo esc_attr( $ACF->button_text ) ?>"><?php echo $ACF->button_text; ?></a>
	                <?php endif; ?>
                </div>
			<?php endif; ?>
            <div class="products-container" id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <div class="swiper-section carousel-products grid-products">
					<?php
					$swiper_class = '';
					$_data        = [];
					if ( $ACF->gap ) {
						$_data["gap"] = true;
						$swiper_class .= ' gap';
					} elseif ( $ACF->smallgap ) {
						$_data["smallgap"] = $ACF->smallgap;
						$swiper_class      .= ' smallgap';
					}

					if ( $ACF->navigation ) {
						$_data["navigation"] = true;
					}
					if ( $ACF->pagination ) {
						$_data["pagination"] = "dynamic";
					}
					if ( $ACF->delay ) {
						$_data["delay"] = $ACF->delay;
					}
					if ( $ACF->speed ) {
						$_data["speed"] = $ACF->speed;
					}
					if ( $ACF->autoplay ) {
						$_data["autoplay"] = true;
					}
					if ( $ACF->loop ) {
						$_data["loop"] = true;
					}

					if ( ! $ACF->number_desktop || ! $ACF->number_tablet || ! $ACF->number_mobile ) {
						$_data["autoview"] = true;
						$swiper_class      .= ' autoview';
					} else {
						$_data["desktop"] = $ACF->number_desktop;
						$_data["tablet"]  = $ACF->number_tablet;
						$_data["mobile"]  = $ACF->number_mobile;
					}

					if ( $ACF->row > 1 ) {
						$_data["row"]  = $ACF->row;
						$_data["loop"] = false;
					}

					$_data = json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );
					?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $_data; ?>'>
							<?php
							$i = 0;

							// Load slides loop
							while ( $r->have_posts() && $i < $ACF->number ) : $r->the_post();
								global $product;
								if ( empty( $product ) || false === wc_get_loop_product_visibility( $product->get_id() ) || ! $product->is_visible() ) {
									continue;
								}
								echo '<div class="swiper-slide">';
								wc_get_template_part( 'content', 'product' );
								echo '</div>';
								++ $i;
							endwhile;
							wp_reset_postdata();

							?>
                        </div>
                    </div>
                </div>
            </div>
	        <?php if ( ! $ACF->full_width ) {
		        echo '</div>';
	        } ?>

        </section>
		<?php
		//$content = ob_get_clean();
		//echo $content; // WPCS: XSS ok.
	}
}
