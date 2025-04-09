<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;

class Title_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display custom title', 'hd' );
		$this->widget_name        = __( 'W - Title', 'hd' );
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
		$title = $this->get_instance_title( $instance );

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}

		$heading_tag   = $ACF->heading_tag ?? 'h2';
		$heading_class = $ACF->heading_class ?? 'heading-title';

		if ( $title ) :
			echo '<' . $heading_tag . ' class="' . $heading_class . '">' . $title . '</' . $heading_tag . '>';
		endif;
	}
}