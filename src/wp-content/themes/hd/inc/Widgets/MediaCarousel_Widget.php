<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class MediaCarousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the Media Carousels + Custom Fields', 'hd' );
		$this->widget_name        = __( 'W - Media Carousels', 'hd' );
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
	 * Outputs the content for the Images Carousel widget instance.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the Images Carousel widget instance.
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
			$_class = $ACF->css_class;
		}

		$number     = $ACF->number ?? 0;
		$html_desc  = $ACF->html_desc ?? '';
		$sub_title  = $ACF->sub_title ?? '';
		$html_title = $ACF->html_title ?? '';

		// banner cat
		$slides_query = false;
		if ( $ACF->banner_cat ) {
			$term = get_term_by( 'id', $ACF->banner_cat, 'banner_cat' );
			if ( $term ) {
				$slides_query = query_by_term( $term, 'banner', false, $number, [ 'menu_order' => 'DESC' ] );
			}
		}

		?>
        <section class="section images_carousel media_carousel <?= $_class ?>">
			<?php if ( $title || $html_desc ) : ?>
				<?php if ( ! $ACF->full_width ) {
					echo '<div class="grid-container width-extra">';
				} ?>
                <div class="title-container">
					<?php if ( $sub_title ) : ?>
                        <p class="sub-title"><?php echo $sub_title; ?></p>
					<?php endif; ?>
					<?php if ( $title ) : ?>
                        <h2 class="heading-title"><?php echo $title; ?></h2>
					<?php endif; ?>
					<?php if ( $html_title ) : ?>
                        <div class="html-title"><?php echo $html_title; ?></div>
					<?php endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                        <div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>
					<?php if ( $ACF->button_url ?? false ) : ?>
                        <div class="heading-link"><a href="<?= $ACF->button_url ?>"
                                                     aria-label="<?php esc_attr_e( $ACF->button_text, 'hd' ); ?>"<?php if ( $ACF->button_glyph ?? false ) {
								echo ' data-glyph-after="' . $ACF->button_glyph . '"';
							} ?>><?php echo __( $ACF->button_text, 'hd' ) ?></a></div>
					<?php endif; ?>
                </div>
				<?php if ( ! $ACF->full_width ) {
					echo '</div>';
				} ?>
			<?php endif; ?>
			<?php

			// loop
			if ( $slides_query ) :
				if ( ! $ACF->full_width ) {
					echo '<div class="grid-container width-extra">';
				}

				?>
                <div class="swiper-section<?php if ( $ACF->marquee ) {
					echo ' swiper-marquee';
				} ?>">
					<?php

					//...
					$swiper_class = '';
					$_data        = [];
					if ( isset( $ACF->gap ) && $ACF->gap ) {
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
					if ( $ACF->marquee ) {
						$_data["marquee"] = true;
					}
					if ( $ACF->fade ) {
						$_data["fade"] = true;
					}
					if ( $ACF->loop ) {
						$_data["loop"] = true;
					}
					if ( $ACF->centered ) {
						$_data["centered"] = true;
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
							// Load slides loop.
							while ( $slides_query->have_posts() ) : $slides_query->the_post();
								$post = get_post();

								$ACF_banner = $this->acfFields( $post->ID );
								if ( isset( $ACF_banner->gallery ) && is_array( $ACF_banner->gallery ) ) :
									foreach ( $ACF_banner->gallery as $gal_id => $gal ) :
										$attachment_meta = wp_get_attachment( $gal );
										if ( Str::stripSpace( $attachment_meta->description ) && ( filter_var( $attachment_meta->description, FILTER_VALIDATE_URL ) || preg_match( '/[\#]/', $attachment_meta->description ) ) ) {
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
												echo wp_get_attachment_image( $gal, 'large' );
												if ( $_href ) {
													echo '</a>';
												}
												?>
                                            </figure>
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

									$_bg_class = '';
									$bg_color  = $ACF_banner->bg_color ?? '';
									if ( $bg_color ) :
										$_bg_class = ' style="background-color: ' . $bg_color . '"';
									endif;

									$banner_url = $ACF_banner->banner_url ?? '';
									$second_img = $ACF_banner->second_img ?? '';
									?>
                                    <div class="swiper-slide"<?= $_bg_class ?>>
                                        <article class="item<?= $_class ?>">
                                            <div class="overlay"<?= $glyph ?>>
                                                <picture>
													<?php if ( $ACF_banner->responsive_image ) : ?>
                                                        <source media="(max-width: 639.98px)"
                                                                srcset="<?= attachment_image_src( $ACF_banner->responsive_image, 'large' ) ?>">
													<?php else : ?>
                                                        <source media="(max-width: 1199.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'post-thumbnail' ) ?>">
                                                        <source media="(max-width: 1023.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'large' ) ?>">
                                                        <source media="(max-width: 639.98px)"
                                                                srcset="<?= post_image_src( $post->ID, 'large' ) ?>">
													<?php endif; ?>
                                                    <img loading="lazy"
                                                         src="<?php echo post_image_src( $post->ID, 'widescreen' ) ?>"
                                                         alt="<?php echo $post->post_name; ?>">
                                                </picture>
												<?php if ( $banner_url ) : ?>
                                                    <a class="link-overlay" href="<?= $banner_url ?>" title></a>
												<?php endif; ?>
												<?php if ( $video_url ) : ?>
                                                    <a class="link-overlay fcy-video" href="<?= $video_url ?>"
                                                       title></a>
												<?php endif; ?>
												<?php if ( $second_img ) : ?>
                                                    <span class="icon-img">
                                        <?php echo wp_get_attachment_image( $second_img, 'large' ); ?>
                                    </span>
												<?php endif; ?>
                                            </div>
											<?php

											$html_title = $ACF_banner->html_title ?? '';
											$button_url = $ACF_banner->button_url ?? '';

											if ( Str::stripSpace( $html_title ) ) : ?>
                                                <div class="content-wrap">
                                                    <div class="content-inner">
                                                        <div class="inner">
															<?php if ( Str::stripSpace( $ACF_banner->sub_title ?? '' ) ) : ?>
                                                                <h6 class="sub-title"><?= $ACF_banner->sub_title ?></h6>
															<?php endif; ?>
                                                            <div class="html-title"><?= $html_title ?></div>
															<?php if ( Str::stripSpace( $ACF_banner->html_desc ?? '' ) ) : ?>
                                                                <div class="html-desc"><?= $ACF_banner->html_desc ?></div>
															<?php endif; ?>
															<?php if ( $button_url ) :
																$button_glyph = $ACF_banner->button_glyph ?? '';
																?>
                                                                <div class="btn-link"><a
                                                                            class="viewmore viewmore-button"
                                                                            href="<?= $button_url ?>"
                                                                            aria-label="<?php esc_attr_e( $ACF_banner->button_text, 'hd' ); ?>"<?php if ( $button_glyph ) {
																		echo ' data-glyph-after="' . $button_glyph . '"';
																	} ?>><span><?php echo __( $ACF_banner->button_text, 'hd' ) ?></span></a>
                                                                </div>
															<?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
											<?php endif; ?>


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
				<?php if ( ! $ACF->full_width ) {
				echo '</div>';
			} ?>
			<?php endif; ?>
        </section>
		<?php
		//$content = ob_get_clean();
		//echo $content; // WPCS: XSS ok.
	}
}
