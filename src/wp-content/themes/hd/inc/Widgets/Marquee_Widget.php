<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Marquee_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display marquee contents', 'hd' );
		$this->widget_name        = __( 'W - Marquee', 'hd' );
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
		if ( $ACF->css_class ) {
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$wrapper   = $ACF->wrapper ?? false;
		$marquee_list   = $ACF->marquee_list ?? [];

		?>
	<section class="section section-marquee<?= $_class ?>">
		<?php if ( $wrapper ) {
			echo '<div class="grid-container width-extra">';
		} ?>

		<?php if ( $title ) : ?>
            <div class="title-container">
                <h2 class="heading-title"><?php echo $title; ?></h2>
            </div>
		<?php endif; ?>

        <?php if ( $marquee_list ) : ?>
        <div class="filter-marquee" id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">

            <?php
            $_num = count( $marquee_list );
            $_loop = (int) ceil( 12 / $_num );

            while ( $_loop > 0 ) :

            ?>

            <ul class="marquee">
                <?php foreach ( $marquee_list as $marquee ) :
                    $re_title = $marquee['re_title'] ?? '';
                    $re_url = $marquee['re_url'] ?? '';
                    $re_target_blank = $marquee['re_target_blank'] ?? false;
	                $re_target_blank = $re_target_blank ? ' target="_blank"' : '';
                ?>
                <li>
                    <?php
                    if ( $re_url ) echo '<a class="marquee-link" href="' . $re_url . '"' . $re_target_blank . '>';
                    else echo '<span class="marquee-link">';

                    echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" height="23" viewBox="0 0 25 23" width="25"><path d="m12.503 23h-.006c-.0145-2.592-.0984-5.0809-.5856-6.3063-.8597-2.1625-1.5873-2.8741-3.76994-3.7259-1.49419-.5832-4.3766-.5832-8.14146-.5832 3.76486 0 6.64727 0 8.14146-.5831 2.18264-.8519 2.91024-1.5635 3.76994-3.72596.5886-1.4804.5886-4.34543.5886-8.07554 0 3.73011 0 6.59514.5886 8.07554.8597 2.16246 1.5873 2.87406 3.7699 3.72596 1.4942.5831 4.3766.5831 8.1415.5831-3.7649 0-6.6473 0-8.1415.5832-2.1826.8518-2.9102 1.5634-3.7699 3.7259-.4872 1.2254-.5711 3.7143-.5856 6.3063z" fill="#fff"></path></svg>';
                    echo $re_title;

                    if ( $re_url ) echo '</a>';
                    else echo '</span>';
                    ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php --$_loop; endwhile; ?>
        </div>
        <?php endif; ?>

		<?php if ( $wrapper ) {
			echo '</div>';
		} ?>
	</section>
		<?php
	}
}
