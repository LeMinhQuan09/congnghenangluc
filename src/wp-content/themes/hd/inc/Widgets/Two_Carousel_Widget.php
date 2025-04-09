<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;

class Two_Carousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Two carousel widget.', 'hd' );
		$this->widget_name        = __( 'W - Two Carousel', 'hd' );
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
		$title = $this->get_instance_title( $instance );

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}

		// class
		$_class = '';
		if ( $ACF->css_class ) {
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$wrapper = $ACF->wrapper ?? false;

		// banner cat
		$slides_query = [];
		if ( $ACF->cat_banners ) {
			$term = get_term_by( 'id', $ACF->cat_banners, 'banner_cat' );
			if ( $term ) {
				$slides_query = query_by_term( $term, 'banner', false, - 1, [ 'menu_order' => 'DESC' ] );
			}
		}

		$_clone_slides_img = clone $slides_query;

		?>
        <section class="section carousels-section two-carousels<?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>

			<?php if ( $title ) : ?>
                <div class="title-container">
                    <h2 class="heading-title"><?php echo $title; ?></h2>
                </div>
			<?php endif; ?>

            <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <div class="carousel-outer main-outer">
                    <div class="swiper-section swiper-one">
                        <div class="swiper main-swiper">
                            <div class="swiper-wrapper">
								<?php
								// Load slides loop.
								while ( $slides_query->have_posts() ) : $slides_query->the_post();
									$post = get_post();

									$acf_post = \get_fields( $post->ID );

									$sub_title    = $acf_post['sub_title'] ?? '';
									$button_url   = $acf_post['button_url'] ?? '';
									$button_text  = $acf_post['button_text'] ?? '';
									$button_glyph = $acf_post['button_glyph'] ?? '';

									$html_title = $acf_post['html_title'] ?? '';
									$html_desc  = $acf_post['html_desc'] ?? '';

									?>
                                    <div class="swiper-slide">
                                        <div class="item-inner">
                                            <div class="item">
                                                <div class="left">
                                                    <p class="name">
                                                        <span><?= $sub_title ?></span><?= $post->post_title ?></p>
                                                    <div class="subtitle"><?= $html_title ?></div>
                                                    <div class="desc">
                                                        <div class="desc-inner"><?= $html_desc ?></div>
                                                    </div>
													<?php if ( $button_url ) : ?>
                                                        <div class="btn-link"><a class="bg-btn"
                                                                                 href="<?= $button_url ?>"
                                                                                 aria-label="<?php echo esc_attr( $button_text ); ?>"<?php if ( $button_glyph ) {
																echo ' data-glyph-after="' . $button_glyph . '"';
															} ?>><span><?php echo $button_text ?></span></a></div>
													<?php endif; ?>
                                                </div>
                                                <div class="right">
                                                    <div class="cover">
														<?php
														if ( has_post_thumbnail() ) {
															echo get_the_post_thumbnail( $post, 'thumbnail' );
														}
														?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php
								endwhile;
								wp_reset_postdata();
								?>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-section swiper-two">
                        <div thumbsSlider="" class="swiper thumbs-swiper">
                            <div class="swiper-wrapper">
								<?php

								// Load slides loop.
								while ( $_clone_slides_img->have_posts() ) : $_clone_slides_img->the_post();
									$post = get_post();

									$img = '';
									if ( has_post_thumbnail() ) {
										$img = get_the_post_thumbnail( $post, 'thumbnail' );
									}

									$_second_img = \get_field( 'second_img', $post->ID );
									if ( $_second_img ) {
										$img = wp_get_attachment_image( $_second_img, 'thumbnail' );
									}
									?>
                                    <div class="swiper-slide">
                                        <figure>
                                            <span class="after-overlay"><?php echo $img; ?></span>
                                        </figure>
                                    </div>
								<?php
								endwhile;
								wp_reset_postdata();
								?>
                            </div>
                        </div>
                        <div class="swiper-controls">
                            <div class="swiper-pagination two-pagination"></div>
                            <div class="swiper-button swiper-button-prev two-button-prev" data-glyph=""></div>
                            <div class="swiper-button swiper-button-next two-button-next" data-glyph=""></div>
                        </div>
                    </div>
                </div>
            </div>

			<?php if ( $wrapper ) {
				echo '</div>';
			} ?>
        </section>
		<?php
	}
}
