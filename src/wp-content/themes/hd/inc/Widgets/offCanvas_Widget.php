<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;

class offCanvas_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display offCanvas Button', 'hd' );
		$this->widget_name        = __( 'W - offCanvas Button', 'hd' );
		$this->settings           = [
			'hide_if_desktop' => [
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Hide if desktop devices', 'hd' ),
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
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		$hide_if_desktop = empty( $instance['hide_if_desktop'] ) ? 0 : 1;
		$class           = '';
		if ( $hide_if_desktop ) {
			$class = ' hide-for-large';
		}
		$name_btn = $ACF->name_btn;
		ob_start();

		?>
        <div class="off-canvas-content<?= $class ?>" data-off-canvas-content>
			<span class="menu-txt"><?php echo $name_btn; ?></span>
            <button class="menu-lines" type="button" data-open="offCanvasMenu" aria-label="button">
                <span class="hamburger"></span>
                <span class="line line-1"></span>
                <span class="line line-2"></span>
                <span class="line line-3"></span>
            </button>
        </div>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
