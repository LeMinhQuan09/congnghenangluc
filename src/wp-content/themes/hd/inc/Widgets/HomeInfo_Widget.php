<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class HomeInfo_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Home Information Section.', 'hd' );
		$this->widget_name        = __( 'W - Home Information', 'hd' );
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
		if ( $ACF->css_class ) {
			$_class = $ACF->css_class;
		}

		$heading_tag   = $ACF->heading_tag ?? 'h2';
		$heading_class = $ACF->heading_class ?? 'heading-title';

		$sub_title    = $ACF->sub_title ?? '';
		$html_desc    = $ACF->html_desc ?? '';
		$html_content = $ACF->html_content ?? '';

		?>
        <section class="section home-info-section <?= $_class ?>">
            <div class="grid-container width-extra">
                <div class="grid-x">
                    <div class="cell title-container">
						<?php if ( $sub_title ) : ?>
                            <p class="h6 sub-title"><?php echo $sub_title; ?></p>
						<?php endif; ?>
						<?php if ( $title ) :
							echo '<' . $heading_tag . ' class="' . $heading_class . '">' . $title . '</' . $heading_tag . '>';
						endif; ?>
						<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                            <div class="html-desc"><?php echo $html_desc; ?></div>
						<?php endif; ?>
						<?php if ( Str::stripSpace( $html_content ) ) : ?>
                            <div class="html-content"><?php echo $html_content; ?></div>
						<?php endif; ?>
						<?php if ( $ACF->url ?? '' ) : ?>
                            <a rel="nofollow" href="<?= esc_url( $ACF->url ) ?>" class="viewmore viewmore-button"
                               title="<?php echo esc_attr( $ACF->button_text ) ?>"
                               data-glyph-after=""><?php echo $ACF->button_text; ?></a>
						<?php endif; ?>
                    </div>
					<?php
					if ( $ACF->thumbs ?? '' ) :
						$thumbs_link = $ACF->thumbs_link ?? '';
						$thumbs_video = $ACF->thumbs_video ?? false;
						$thumbs2 = $ACF->thumbs2 ?? '';

						$_class = ' _blank';
						$_video = '';
						if ( $thumbs_link && $thumbs_video ) {
							$_video = ' data-glyph=""';
							$_class = ' fcy-video';
						}


						$attachment_meta = wp_get_attachment( $ACF->thumbs );
						?>
                        <div class="cell thumbs-container">
							<?php
							if ( $thumbs_link ) {
								echo '<a rel="nofollow" class="after-overlay ' . $_class . '" href="' . $thumbs_link . '" title="' . $attachment_meta->title . '">';
							} else {
								echo '<span class="after-overlay">';
							}
							?>
                            <span class="bg"<?= $_video ?>><?php echo wp_get_attachment_image( $ACF->thumbs, 'large' ); ?></span>
							<?php
							if ( $thumbs_link ) {
								echo '</a>';
							} else {
								echo '</span>';
							}
							?>
							<?php if ( $thumbs2 ) : ?>
                                <span class="bg-inner"><span
                                            class="bg2"><?php echo wp_get_attachment_image( $thumbs2, 'medium' ); ?></span></span>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </section>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
