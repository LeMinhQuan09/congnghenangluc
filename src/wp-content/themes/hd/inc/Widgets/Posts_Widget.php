<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;

class Posts_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter posts by category.', 'hd' );
		$this->widget_name        = __( 'W - Posts', 'hd' );
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
			$_class = $ACF->css_class;
		}
		$html_desc = $ACF->html_desc ?? '';
		//...
		$r = query_by_terms( $ACF->cat, 'category', 'post', $ACF->include_children, $ACF->number );
		if ( ! $r ) {
			return;
		} ?>
        <section class="section filter-posts <?= $_class ?>">
			<?php if ( $title || $html_desc ) : ?>
				<?php if ( $ACF->wrapper ) {
					echo '<div class="grid-container width-extra title-container">';
				} ?>
				<?php if ( $title ) : ?>
                    <h2 class="heading-title"><?php echo $title; ?></h2>
				<?php endif; ?>
				<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                    <div class="html-desc"><?php echo $html_desc; ?></div>
				<?php endif; ?>
				<?php if ( $ACF->url ?? '' ) : ?>
				<a href="<?= esc_url( $ACF->url ) ?>" class="viewmore viewmore-button"
					title="<?php echo esc_attr( $ACF->button_text ) ?>"
					data-glyph-after=""><?php echo $ACF->button_text; ?></a>
				<?php endif; ?>
				<?php if ( $ACF->wrapper ) {
					echo '</div>';
				} ?>
			<?php endif; ?>

            <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php if ( $ACF->wrapper ) {
					echo '<div class="grid-container width-extra posts-container">';
				} ?>
                <div class="grid-posts posts-list">
					<?php
					$i = 0;
					while ( $r->have_posts() && $i < $ACF->number ) :
						$r->the_post();
						if ( 0 === $i ) {
							echo '<div class="first">';
						} elseif ( 1 === $i ) {
							echo '<div class="second">';
						}
						get_template_part( 'template-parts/posts/loop', null, Cast::toArray( $ACF ) );
						if ( 0 === $i ) {
							echo '</div>';
						}
						++ $i;
					endwhile;
					if ( 1 < $i ) {
						echo '</div>';
					}
					wp_reset_postdata(); ?>
                </div>
				<?php if ( $ACF->wrapper ) {
					echo '</div>';
				} ?>
            </div>
        </section>
		<?php
		//$content = ob_get_clean();
		//echo $content; // WPCS: XSS ok.
	}
}