<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class TabsProducts_Carousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter tabs products carousel by category.', 'hd' );
		$this->widget_name        = __( 'W - Tabs Products Carousels', 'hd' );
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

		// This filter is documented in wp-includes/widgets/class-wp-widget-pages.php
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

		$tab_banners     = $ACF->tab_banners ?? '';
		$products_number = $ACF->products_number ?? 0;
		$products_tabs   = $ACF->products_cat_list ?? [];
		$button_text     = $ACF->button_text ?? '';
		$wrapper         = $ACF->wrapper ?? false;

		// banner cat
		$slides_query = false;
		if ( $tab_banners ) {
			$term = get_term_by( 'id', $tab_banners, 'banner_cat' );
			if ( $term ) {
				$slides_query = query_by_term( $term, 'banner', false, - 1, [ 'menu_order' => 'DESC' ] );
			}
		}

		?>
        <section class="section filter-tabs-products <?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>

			<?php if ( $slides_query ) : ?>
                <div class="swiper-section products-banners-outer section images_carousel is-banner full-banner">
					<?php
					$swiper_class = ' fraction';
					$_data        = [
						'navigation' => true,
						'pagination' => 'fraction',
						'desktop'    => 1,
						'tablet'     => 1,
						'mobile'     => 1,
						'delay'      => 8000,
						'speed'      => 400,
						'loop'       => true,
						'autoplay'   => true,
					];

					$_data = json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );

					?>
                    <div class="w-swiper swiper">
                        <div class="swiper-wrapper<?= $swiper_class ?>" data-options='<?= $_data; ?>'>
							<?php
							// Load slides loop.
							while ( $slides_query->have_posts() ) : $slides_query->the_post();
								$post = get_post();

								$ACF_banner = $this->acfFields( $post->ID );
								if ( isset( $ACF_banner->gallery ) && is_array( $ACF_banner->gallery ) ) :
									foreach ( $ACF_banner->gallery as $gal ) :
										$attachment_meta = wp_get_attachment( $gal );
										if ( Str::stripSpace( $attachment_meta->description ) && filter_var( $attachment_meta->description, FILTER_VALIDATE_URL ) ) {
											$_href = $attachment_meta->description;
										} else {
											$_href = false;
										}
										?>
                                        <div class="swiper-slide">
                                            <figure>
												<?php
												if ( $_href ) {
													echo '<a class="after-overlay" href="' . $_href . '" title>';
												}
												echo wp_get_attachment_image( $gal, 'widescreen' );
												if ( $_href ) {
													echo '</a>';
												}
												?>
                                            </figure>
                                            <div class="swiper-lazy-preloader"></div>
                                        </div>
									<?php endforeach;

                                elseif ( has_post_thumbnail() ) :

									$_class = '';
									$glyph  = '';

									$video_url = $ACF_banner->url ?? '';
									if ( $video_url ) :
										$_class = ' has-video';
										$glyph  = ' data-glyph=""';
									endif;

									$banner_url       = $ACF_banner->banner_url ?? '';
									$responsive_image = $ACF_banner->responsive_image ?? '';

									?>
                                    <div class="swiper-slide">
                                        <article class="item<?= $_class ?>">
                                            <div class="overlay"<?= $glyph ?>>
                                                <picture>
													<?php if ( $responsive_image ) : ?>
                                                        <source media="(max-width: 639.98px)"
                                                                srcset="<?= attachment_image_src( $responsive_image, 'medium' ) ?>">
													<?php else : ?>
                                                        <source media="(max-width: 1199.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'post-thumbnail' ) ?>">
                                                        <source media="(max-width: 1023.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'large' ) ?>">
                                                        <source media="(max-width: 639.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'medium' ) ?>">
													<?php endif; ?>
                                                    <img loading="lazy"
                                                         src="<?php echo post_image_src( $post->ID, 'widescreen' ) ?>"
                                                         alt="">
                                                </picture>
												<?php if ( $banner_url ) : ?>
                                                    <a class="link-overlay" href="<?= $banner_url ?>" title></a>
												<?php endif; ?>
												<?php if ( $video_url ) : ?>
                                                    <a class="link-overlay fcy-video" href="<?= $video_url ?>"
                                                       title></a>
												<?php endif; ?>
                                            </div>
                                        </article>
                                    </div>
								<?php
								endif;
							endwhile;
							wp_reset_postdata();
							unset( $ACF_banner );
							?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
                <h2 class="heading-title"><?php echo $title; ?></h2>
			<?php endif; ?>

			<?php if ( $products_tabs ) :
				$rand = 'tabs_' . $this->id;
				?>
                <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                    <div id="<?php echo 'tabs_' . $this->id; ?>" class="w-filter-tabs filter-tabs clearfix">
                        <div class="tabs-nav">
                            <ul>
								<?php foreach ( $products_tabs as $i => $cat_id ) :
									$term = get_term( $cat_id, 'product_cat' );
									?>
                                    <li>
                                        <a href="<?php echo '#' . $rand . $i . $cat_id ?>"
                                           aria-label="<?php echo esc_attr( $term->name ) ?>">
                                            <span><?php echo $term->name; ?></span>
                                        </a>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="tabs-content">
							<?php
							foreach ( $products_tabs as $i => $cat_id ) :
								$term = get_term( $cat_id, 'product_cat' );
								if ( $term ) :
									$count = (int) $term->count;
									$args = [
										'child_of' => $cat_id,
									];
									$tax_terms_child = get_terms( 'product_cat', $args );
									foreach ( $tax_terms_child as $tax_term_child ) {
										$count += (int) $tax_term_child->count;
									}

									$r = query_by_terms( $cat_id, 'product_cat', 'product', true, $products_number );
									?>
                                    <div id="<?php echo $rand . $i . $cat_id ?>"
                                         class="tabs-panel section carousels-section">
										<?php if ( $r ) : ?>

                                            <div class="swiper-section carousel-products grid-products">
												<?php
												$swiper_class = ' smallgap';
												$_data        = [
													'smallgap'   => 20,
													'navigation' => true,
													'desktop'    => 5,
													'tablet'     => 4,
													'mobile'     => 2,
													'delay'      => 8000,
													'speed'      => 400,
													'loop'       => true,
													'autoplay'   => true,
													'observer'   => true,
												];

												$_data = json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );
												?>
                                                <div class="w-swiper swiper">
                                                    <div class="swiper-wrapper<?= $swiper_class ?>"
                                                         data-options='<?= $_data; ?>'>
														<?php
														$j = 0;

														// Load slides loop
														while ( $r->have_posts() && $j < $products_number ) : $r->the_post();
															global $product;
															if ( empty( $product ) || false === wc_get_loop_product_visibility( $product->get_id() ) || ! $product->is_visible() ) {
																continue;
															}
															echo '<div class="swiper-slide">';
															wc_get_template_part( 'content', 'product' );
															echo '</div>';
															++ $j;
														endwhile;
														wp_reset_postdata();

														?>
                                                    </div>
                                                </div>
                                            </div>
										<?php endif; ?>

										<?php
										$view_more_title = $button_text ? $button_text . ' ' . $term->name . ' <span>(' . $count . ')</span>' : '';
										if ( $view_more_title && $r ) :
											?>
                                            <a href="<?= get_term_link( $term, 'product_cat' ) ?>"
                                               class="viewmore button"
                                               title="<?php echo esc_attr( $term->name ) ?>"><?php echo $view_more_title; ?></a>
										<?php endif; ?>

                                    </div>
								<?php endif; endforeach; ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

			<?php if ( $wrapper ) {
				echo '</div>';
			} ?>
        </section>
		<?php
	}
}
