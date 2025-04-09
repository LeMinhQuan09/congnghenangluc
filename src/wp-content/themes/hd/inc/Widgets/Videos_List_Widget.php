<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;

class Videos_List_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the video-list section', 'hd' );
		$this->widget_name        = __( 'W - Video List', 'hd' );
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
	 * Creating widget front-end
	 *
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
		$_class_section = '';
		if ( $ACF->css_class ) {
			$_class_section = $_class_section . ' ' . $ACF->css_class;
		}

		$html_desc     = $ACF->html_desc ?? '';
		$term          = get_term( $ACF->cat, 'video_cat' );
		$number_videos = $ACF->number_videos ?? 5;

		$r = query_by_terms( $ACF->cat, 'video_cat', 'video', true, - 1 );

		$total_number = $r->post_count;
		if ( ! $r ) {
			return;
		}

		?>
        <section class="section video-list-section video-list-js-section<?= $_class_section ?>">
            <div class="grid-container width-extra">
                <div class="inner-container">
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
                    <div class="video-list-js-inner">
                        <div class="grid-videos">
							<?php
							$i = 0;

							// Load slides loop.
							while ( $r->have_posts() && $i < $number_videos ) :
								$r->the_post();

								if ( 0 === $i ) : echo '<div class="first">';
                                elseif ( 1 === $i ) :
									echo '<div class="second">';
									echo '<span class="heading-outer">';
									echo '<span class="cat_title">' . $term->name . '</span>';
									echo '<span class="numbers">' . $total_number . ' videos</span>';
									echo '</span>';
									echo '<div class="second-inner">';
									//echo '<div class="second"><span class="heading">' . $total_number . '</span><div class="second-inner">';
								endif;

								get_template_part( 'template-parts/videos/loop_gallery', null, Cast::toArray( $ACF ) );

								if ( 0 === $i ) {
									echo '</div>';
								}
								++ $i;

							endwhile;
							if ( 1 < $i ) {
								echo '</div></div>';
							}
							wp_reset_postdata();
							?>
                        </div>
						<?php if ( $ACF->url ?? '' ) : ?>
                            <a href="<?= esc_url( $ACF->url ) ?>" class="viewmore viewmore-button"
                               title="<?php echo esc_attr( $ACF->button_text ) ?>"
                               data-glyph-after=""><?php echo $ACF->button_text; ?></a>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
		<?php
	}
}
