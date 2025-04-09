<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Url;

class Search_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the search box', 'hd' );
		$this->widget_name        = __( 'W - Search', 'hd' );
		$this->settings           = [
			'title'     => [
				'type'  => 'text',
				'std'   => __( 'Tìm kiếm', 'hd' ),
				'label' => __( 'Title', 'hd' ),
			],
			'css_class' => [
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Css Class', 'hd' ),
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

		$title     = $this->get_instance_title( $instance );
		$css_class = isset( $instance['css_class'] ) ? trim( strip_tags( $instance['css_class'] ) ) : '';

		$_unique_id        = esc_attr( uniqid( 'search-form-' ) );
		$title_for         = __( 'Search for', 'hd' );
		// $placeholder_title = esc_attr( __( 'Tìm kiếm', 'hd' ) );

		?>
        <div class="inside-search <?php echo $css_class; ?>">
            <form role="search" action="<?php echo Url::home(); ?>" class="frm-search" method="get"
                  accept-charset="UTF-8" data-abide novalidate>
                <label for="<?php echo $_unique_id; ?>" class="screen-reader-text"><?php echo $title_for; ?></label>
                <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search" autocomplete="off"
                       name="s" value="<?php echo get_search_query(); ?>"
                       placeholder="<?php echo $title; ?>">
                <button type="submit" data-glyph="">
                    <span><?php echo $title; ?></span>
                </button>
				<?php if ( class_exists( '\WooCommerce' ) ) : ?>
                    <input type="hidden" name="post_type" value="product">
				<?php endif; ?>
            </form>
			<div id="ajaxSearchResults" class="smart-search-wrapper"></div>
        </div>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
