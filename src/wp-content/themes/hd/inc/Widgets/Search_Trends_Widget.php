<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Search_Trends_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the search tags', 'hd' );
		$this->widget_name        = __( 'W - Search Trends', 'hd' );
		$this->settings           = [
			'title' => [
				'type'  => 'text',
				'std'   => __( '', 'hd' ),
				'label' => __( 'Title', 'hd' ),
			]
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
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$wrapper   = $ACF->wrapper ?? false;
		$tags_list = $ACF->tags_list ?? null;

		?>
        <section class="section search-tags<?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>
            <h2 class="heading-title"><?php echo $title; ?></h2>
			<?php if ( $tags_list ) : ?>
                <div class="search-tags-outer">
                    <ul>
						<?php foreach ( $tags_list as $tag ) :
							$re_title = $tag['re_title'] ?? '';
							$re_url = $tag['re_url'] ?: '#';
							$re_extlink = $tag['re_extlink'] ?? false;
							if ( $re_title ) :
								$target = $re_extlink ? ' target="_blank"' : '';
								?>
                                <li><a<?= $target ?> href="<?= $re_url ?>" title="<?php echo esc_attr( $re_title ); ?>"><span><?php echo $re_title; ?></span></a>
                                </li>
							<?php endif; endforeach; ?>
                    </ul>
                </div>
			<?php endif; ?>
			<?php if ( $wrapper ) {
				echo '</div>';
			} ?>
        </section>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
