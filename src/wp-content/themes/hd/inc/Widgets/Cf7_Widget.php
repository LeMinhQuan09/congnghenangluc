<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Cf7_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Contact Form 7 + Custom Fields', 'hd' );
		$this->widget_name        = __( 'W - CF7 Form', 'hd' );
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

		$heading_tag   = $ACF->heading_tag ?? 'h2';
		$heading_class = $ACF->heading_class ?? 'heading-title';

		$wrapper          = $ACF->wrapper ?? false;
		$background_image = $ACF->background_image ?? '';
		$second_img       = $ACF->second_img ?? '';
		$html_title       = $ACF->html_title ?? '';
		$html_desc        = $ACF->html_desc ?? '';
		$cf7_form         = $ACF->form ?? '';

		$css_id = $ACF->css_id ?: $this->id;

		?>
        <div class="section cf7-section<?= $_class ?>" id="<?php echo $css_id; ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			}
			echo '<div class="cf7-inner">';
			?>

			<?php if ( $background_image ) : ?>
                <span class="bg"><?php echo wp_get_attachment_image( $background_image, 'widescreen' ); ?></span>
			<?php endif; ?>

            <div class="grid-inner">
				<?php if ( $title || Str::stripSpace( $html_title ) || Str::stripSpace( $html_desc ) ) : ?>
                    <div class="title-section title-container">
						<?php
						if ( $title ) :
							echo '<' . $heading_tag . ' class="' . $heading_class . '">' . $title . '</' . $heading_tag . '>';
						endif;

						if ( Str::stripSpace( $html_title ) ) :
							echo '<div class="html-title">';
							echo $html_title;
							echo '</div>';
						endif;

						if ( Str::stripSpace( $html_desc ) ) :
							echo '<div class="html-desc">';
							echo $html_desc;
							echo '</div>';
						endif;
						?>
                    </div>
				<?php endif; ?>

                <div class="form-section">

					<?php if ( $second_img ) : ?>
                        <span class="second-bg"><?php echo wp_get_attachment_image( $second_img, 'large' ); ?></span>
					<?php endif; ?>

					<?php
					if ( $cf7_form ) :
						$form = get_post( $cf7_form );
						echo do_shortcode( '[contact-form-7 id="' . $form->ID . '" title="' . esc_attr( $form->post_title ) . '"]' );
					endif;

					?>
                </div>
            </div>

			<?php if ( $wrapper ) {
				echo '</div>';
			}
			echo '</div>' ?>
        </div>
		<?php
	}
}
