<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Reviews_Carousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter reviews carousel.', 'hd' );
		$this->widget_name        = __( 'W - Reviews Carousels', 'hd' );
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

		$sub_title = $ACF->sub_title ?? '';
		$html_desc = $ACF->html_desc ?? '';
		$number    = $ACF->number ?? 0;
		$wrapper    = $ACF->wrapper ?? true;

		// banner cat
		$slides_query = false;
		if ( $ACF->banner_cat ) {
			$term = get_term_by( 'id', $ACF->banner_cat, 'banner_cat' );
			if ( $term ) {
				$slides_query = query_by_term( $term, 'banner', false, $number, [ 'menu_order' => 'DESC' ] );
			}
		}

		?>
        <section class="section carousels-section reviews-carousels<?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>
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
                </div>
			<?php
			endif;
			if ( $slides_query ) :

				?>
                <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                    <div class="swiper-section reviews-outer">
						<?php
						$swiper_class = ' dynamic';
						$_data        = [
							'pagination' => 'dynamic',
							'gap'        => true,
						];

						if ( $ACF->navigation ) {
							$_data["navigation"] = true;
						}
						if ( $ACF->autoplay ) {
							$_data["autoplay"] = true;
						}
						if ( $ACF->delay ) {
							$_data["delay"] = $ACF->delay;
						}
						if ( $ACF->speed ) {
							$_data["speed"] = $ACF->speed;
						}
						if ( $ACF->loop ) {
							$_data["loop"] = true;
						}

						$_data["desktop"] = 2;
						$_data["tablet"]  = 2;
						$_data["mobile"]  = 1;

						$_data = json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );

						?>
                        <div class="w-swiper swiper<?= $swiper_class ?>">
                            <div class="swiper-wrapper" data-options='<?= $_data; ?>'>
								<?php
								// Load slides loop.
								while ( $slides_query->have_posts() ) : $slides_query->the_post();
									$post       = get_post();
									$ACF_banner = $this->acfFields( $post->ID );

									if ( has_post_thumbnail() ) :
										?>
                                        <div class="swiper-slide">
											<?php
											$_class = '';
											$glyph  = '';

											$responsive_image = $ACF_banner->responsive_image ?? '';

											$sub_title    = $ACF_banner->sub_title ?? '';
											$html_title   = $ACF_banner->html_title ?? '';
											$html_desc    = $ACF_banner->html_desc ?? '';
											$video_url    = $ACF_banner->url ?? '';
											$button_url   = $ACF_banner->button_url ?? '';
											$button_glyph = $ACF_banner->button_glyph ?? '';
											$button_text  = $ACF_banner->button_text ?? '';

											$rating        = $ACF_banner->rating ?? false;
											$rating_number = $ACF_banner->rating_number ?? 1;
											$rating_name   = $ACF_banner->rating_name ?? '';

											if ( $video_url ) :
												$_class = ' has-video';
												$glyph  = ' data-glyph=""';
											endif;

											?>
                                            <article class="item<?= $_class ?>">
                                                <div class="item-inner">
                                                    <div class="hide overlay"<?= $glyph ?>>
                                                        <picture>
															<?php if ( $responsive_image ) : ?>
                                                                <source media="(max-width: 639.98px)" srcset="<?= attachment_image_src( $responsive_image, 'medium' ) ?>">
															<?php else : ?>
                                                                <source media="(max-width: 639.98px)" srcset="<?= post_image_src( $post->ID, 'medium' ) ?>">
                                                                <source media="(max-width: 1023.98px)" srcset="<?= post_image_src( $post->ID, 'large' ) ?>">
															<?php endif; ?>
                                                            <img src="<?php echo post_image_src( $post->ID, 'widescreen' ) ?>" alt="">
                                                        </picture>
														<?php if ( $video_url ) : ?>
                                                            <a class="link-overlay _blank fcy-video" href="<?= $video_url ?>" title></a>
														<?php endif; ?>
                                                    </div>
													<?php if ( $html_title || $html_desc || $sub_title ) : ?>
                                                    <div class="content-wrap">
                                                        <div class="content-inner">
                                                            <div class="inner">
                                                                <div class="icon-group">
                                                                    <?php echo get_the_post_thumbnail( $post->ID, 'medium' ); ?>
										                            <?php if ( $html_title || $sub_title ) : ?>
                                                                    <div>
                                                                        <?php if ( Str::stripSpace( $sub_title ) ) : ?>
                                                                            <p class="sub-title"><?= $sub_title ?></p>
                                                                        <?php endif; ?>
                                                                        <?php if ( Str::stripSpace( $html_title ) ) : ?>
                                                                            <div class="html-title"><?= $html_title ?></div>
                                                                        <?php endif; ?>
                                                                    </div>
										                            <?php endif; ?>
                                                                </div>

                                                                <?php if ( Str::stripSpace( $html_desc ) ) : ?>
                                                                <div class="html-desc"><?= $html_desc ?></div>
                                                                <?php endif; ?>

                                                                <?php if ( $rating ) :
                                                                    $percent = 100 * $rating_number / 5;
                                                                ?>
                                                                <div class="star-rating2">
                                                                    <div class="rating-inner">
                                                                        <i data-glyph=""></i>
                                                                        <i data-glyph=""></i>
                                                                        <i data-glyph=""></i>
                                                                        <i data-glyph=""></i>
                                                                        <i data-glyph=""></i>
                                                                        <div style="width: <?= $percent ?>%">
                                                                            <i data-glyph=""></i>
                                                                            <i data-glyph=""></i>
                                                                            <i data-glyph=""></i>
                                                                            <i data-glyph=""></i>
                                                                            <i data-glyph=""></i>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ( $rating_name ) : ?>
                                                                    <p class="rating-name">(<?= $rating_name ?>)</p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?php endif; ?>

                                                                <?php if ( $button_url ?? '' ) : ?>
                                                                <div class="btn-link"><a class="viewmore viewmore-button" href="<?= $button_url ?>" aria-label="<?php esc_attr_e( $button_text, 'hd' ); ?>"<?php if ( $button_glyph ) {echo ' data-glyph-after="' . $button_glyph . '"';} ?>><span><?php echo __( $button_text, 'hd' ) ?></span></a></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
													<?php endif; ?>
                                                </div>
                                            </article>
                                        </div>
									<?php
									endif;
								endwhile;
								wp_reset_postdata();
								?>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif;
			if ( $wrapper ) {
				echo '</div>';
			}

			?>
        </section>
		<?php
	}
}
