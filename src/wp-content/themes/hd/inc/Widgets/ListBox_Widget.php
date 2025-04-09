<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;

class ListBox_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display linkbox list + Custom Fields', 'hd' );
		$this->widget_name        = __( 'W - LinkBox List', 'hd' );
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
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = $this->get_instance_title( $instance );

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}

		// class
		$_class = '';
		if ( Str::stripSpace( $ACF->css_class ) ) {
			$_class = $ACF->css_class;
		}

		$heading_tag   = $ACF->heading_tag ?? 'h2';
		$heading_class = $ACF->heading_class ?? 'heading-title';

		$counter   = $ACF->counter ?? false;
		$html_desc = $ACF->html_desc;

		$background_img = $ACF->background_img ?? '';
		$banner_link    = $ACF->banner_link ?? '';
		$video_link     = $ACF->video_link ?? false;

		$wrapper = $ACF->wrapper ?? false;

		$_video_class = ' _blank';
		if ( $video_link ) {
			$_video_class = ' fcy-video';
		}

		?>
        <section class="section listbox-section <?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>
			<?php if ( $title || $html_desc ) : ?>
                <div class="title-section title-container">
					<?php if ( $title ) :
						echo '<' . $heading_tag . ' class="' . $heading_class . '">' . $title . '</' . $heading_tag . '>';
					endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                        <div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>
                </div>
			<?php endif; ?>
            <div class="listbox-outer">
				<?php if ( Str::stripSpace( $background_img ) ) : ?>
                    <div class="background-col col">
						<?php if ( $banner_link ) {
							echo '<a class="after-overlay' . $_video_class . '" href="' . $banner_link . '" title>';
						} ?>
                        <span class="bg"><?php echo wp_get_attachment_image( $background_img, 'large' ); ?></span>
						<?php if ( $banner_link ) {
							echo '</a>';
						} ?>
                    </div>
				<?php endif; ?>
				<?php
				if ( $ACF->list_box ?? [] ) :
					echo "<ul class=\"col listbox {$this->id}\">";
					foreach ( $ACF->list_box as $i => $item ) {
						$item = Cast::toObject( $item );

						//$num = null;
						if ( $i < 10 ) {
							$num = '0' . ( $i + 1 );
						} else {
							$num = $i + 1;
						}

						echo '<li>';

						//...
						$glyph = '';
						if ( Str::stripSpace( $item->glyph ) ) {
							$glyph = ' data-glyph="' . $item->glyph . '"';
						}

						$wrapper_open  = '<div data-num="' . $num . '" class="linkbox-inner"' . $glyph . '>';
						$wrapper_close = '</div>';
						if ( Str::stripSpace( $item->url ) ) {
							$_a_class = 'linkbox-inner';
							if ( $item->_blank ) {
								$_a_class .= ' _blank';
							}
							$wrapper_open  = '<a data-num="' . $num . '" class="' . $_a_class . '" href="' . $item->url . '" title="' . $item->title . '"' . $glyph . '>';
							$wrapper_close = '</a>';
						}

						//...
						echo $wrapper_open;

						// has image thumb
						if ( $item->image ?? '' ) {
							echo '<span class="overlay">';
							echo wp_get_attachment_image( $item->image, 'medium' );
							echo '</span>';
						}

						if ( $item->image2 ?? '' ) {
							echo '<span class="overlay bg-second">';
							echo wp_get_attachment_image( $item->image2, 'thumbnail' );
							echo '</span>';
						}

						echo '<div>';

						$re_title = $item->title ? '<span class="title">' . $item->title . '</span>' : '';
						if ( $counter && is_numeric( $item->counter_number ) ) {

							$counter_text = $item->counter_text ? '<span class="counter-txt">' . $item->counter_text . '</span>' : '';
							echo '<div class="title-outer">' . $re_title . '<span class="counter-number" data-counter="' . $item->counter_number . '"></span>' . $counter_text . '</div>';
						} else {
							echo $re_title;
						}

						if ( Str::stripSpace( $item->desc ) ) {
							echo '<div class="desc">' . $item->desc . '</div>';
						}
						echo '</div>';

						echo $wrapper_close;

						echo '</li>';
					}
					echo "</ul>";
				endif;
				echo '</div>';

				$url         = $ACF->url ?? '';
				$button_text = $ACF->button_text ?? '';

				if ( $url ) :
					?>
                    <a href="<?= esc_url( $url ) ?>" class="viewmore viewmore-button"
                       title="<?php echo esc_attr( $button_text ) ?>"
                       data-glyph-after=""><?php echo $button_text; ?></a>
				<?php
				endif;

				if ( $wrapper ) {
					echo '</div>';
				}

				?>
        </section>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
