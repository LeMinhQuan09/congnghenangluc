<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;

class Iframe_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the iframe section', 'hd' );
		$this->widget_name        = __( 'W - Iframe', 'hd' );
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
		$_class = '';
		if ( $ACF->css_class ?? '' ) {
			$_class = $ACF->css_class;
		}

		$iframe     = $ACF->html_content ?? '';
		$full_width = $ACF->full_width ?? false;
		$ratio      = $ACF->ratio ?? 'none';

	?>
	<section class="section iframe-section <?= $_class ?>">
		<?php if ( ! $full_width ) echo '<div class="grid-container width-extra">'; ?>
		<?php if ( $title ) : ?>
        <div class="title-container">
            <h2 class="heading-title"><?php echo $title; ?></h2>
        </div>
		<?php endif; ?>
        <div id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
            <div class="embed-inner">
                <span class="res ar-<?=$ratio?>"><?=$iframe?></span>
            </div>
        </div>
		<?php if ( ! $full_width ) echo '</div>'; ?>
	</section>
	<?php
	}
}
