<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Page_Carousel_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter page carousel.', 'hd' );
		$this->widget_name        = __( 'W - Page Carousel', 'hd' );
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

		$wrapper    = $ACF->wrapper ?? false;
		$pages_list = $ACF->pages_list ?? [];
		$html_desc  = $ACF->html_desc ?? '';

		?>
        <section class="section filter-page<?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>

			<?php if ( $title || $html_desc ) : ?>
                <div class="title-container">
					<?php if ( $title ) : ?>
                        <h2 class="heading-title"><?php echo $title; ?></h2>
					<?php endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                        <div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>
                </div>
			<?php endif; ?>

			<?php if ( $pages_list ) : ?>
                <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                    <div class="swiper-section pages-list">
						<?php
						$swiper_class = ' dynamic';
						$_data        = [];

						if ( $ACF->pagination ) {
							$_data["pagination"] = 'dynamic';
						}
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
						if ( $ACF->gap ) {
							$_data["gap"] = true;
						}

						if ( ! $ACF->number_desktop || ! $ACF->number_tablet || ! $ACF->number_mobile ) {
							$_data["autoview"] = true;
						} else {
							$_data["desktop"] = $ACF->number_desktop;
							$_data["tablet"]  = $ACF->number_tablet;
							$_data["mobile"]  = $ACF->number_mobile;
						}

						$_data = json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE );

						?>
                        <div class="w-swiper swiper<?= $swiper_class ?>">
                            <div class="swiper-wrapper" data-options='<?= $_data; ?>'>
								<?php
								foreach ( $pages_list as $page_id ) :
									$page = get_post( $page_id );

									$ACF_page = $this->acfFields( $page->ID );

									$title        = $page->post_title;
									$sub_title    = $ACF_page->sub_title ?? '';
									$second_title = $ACF_page->second_title ?? '';

									$link           = get_permalink( $page );
									$post_thumbnail = get_the_post_thumbnail( $page, 'large' );
									?>
                                    <div class="swiper-slide">
                                        <article class="item">
                                            <a href="<?= $link; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
												<?php if ( $post_thumbnail ) : ?>
                                                    <div class="cover">
                                                        <span class="after-overlay scale"><?php echo $post_thumbnail; ?></span>
                                                    </div>
												<?php endif; ?>
												<?php if ( $title || $sub_title || $second_title ) :
													if ( ! $second_title ) {
														$second_title = $title;
													}
													?>
                                                    <div class="cover-content">
                                                        <div class="content-inner">
															<?php if ( $sub_title ) : ?>
                                                                <span class="sub-title"><?= $sub_title ?></span>
															<?php endif; ?>
															<?php if ( $second_title ) : ?>
                                                                <p class="second-title"><?= $second_title ?></p>
															<?php endif; ?>
                                                        </div>
                                                    </div>
												<?php endif; ?>
                                            </a>
                                        </article>
                                    </div>
								<?php endforeach; ?>
                            </div>
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
