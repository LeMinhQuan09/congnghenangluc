<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class LinkBox_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display link box + Custom Fields', 'hd' );
		$this->widget_name        = __( 'W - LinkBox', 'hd' );
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

		//...
		$attr_glyph = '';
		$glyph      = $ACF->glyph ?? '';
		if ( Str::stripSpace( $glyph ) ) {
			$attr_glyph = ' data-glyph="' . $glyph . '"';
		}

		$wrapper_open  = '<div class="linkbox-inner"' . $attr_glyph . '>';
		$wrapper_close = '</div>';

		if ( Str::stripSpace( $ACF->url ) ) {
			$_a_class = 'linkbox-inner';
			if ( $ACF->_blank ) {
				$_a_class .= ' _blank';
			}

			$xfn = '';
			if ( $ACF->xfn ?? '' ) {
				$xfn = ' rel="' . $ACF->xfn . '"';
			}

			$wrapper_open  = '<a' . $xfn . ' class="' . $_a_class . '  ' . $ACF->css_class_a . '" href="' . $ACF->url . '" title="' . $title . '"' . $attr_glyph . '>';
			$wrapper_close = '</a>';
		}

		?>
        <div class="linkbox <?php echo $_class; ?>">
			<?php
			echo $wrapper_open;

			// has image thumb
			$thumb_image = $ACF->thumb_image ?? '';
			if ( $thumb_image ) {
				echo wp_get_attachment_image( $thumb_image, 'thumbnail' );
			}

			if ( $glyph || $thumb_image ) {
				echo '<div>';
			}

			if ( $title ) {
				echo '<span class="txt">' . $title . '</span>';
			}
			if ( Str::stripSpace( $ACF->html_desc ) ) {
				echo '<div class="desc">' . $ACF->html_desc . '</div>';
			}

			if ( $glyph || $thumb_image ) {
				echo '</div>';
			}
			echo $wrapper_close;

			?>
        </div>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
