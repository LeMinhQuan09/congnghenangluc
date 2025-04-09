<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;

class Faq_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Frequently Asked Questions.', 'hd' );
		$this->widget_name        = __( 'W - FAQ', 'hd' );
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
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$wrapper   = $ACF->wrapper ?? false;
		$sub_title = $ACF->sub_title ?? '';

		$faq_list   = $ACF->faq_list ?? [];
		$half_start = (int) ceil( count( $faq_list ) / 2 );

		?>
        <section class="section filter-faq<?= $_class ?>">
			<?php if ( $wrapper ) {
				echo '<div class="grid-container width-extra">';
			} ?>

			<?php if ( $title ) : ?>
                <div class="title-container">
                    <h2 class="heading-title"><?php echo $title; ?></h2>
					<?php if ( $sub_title ) {
						echo '<p class="sub-title">' . $sub_title . '</p>';
					} ?>
                </div>
			<?php endif; ?>

            <div class="accordion-outer" id="<?php echo $this->id; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
                <ul class="accordion accordion-1" data-accordion data-multi-expand="true" data-allow-all-closed="true">
					<?php
					for ( $i = 0; $i < $half_start; $i ++ ) :
						$faq = $faq_list[ $i ];

						$_active_class = '';
						if ( $i == 0 ) {
							$_active_class = ' is-active';
						}

						$re_title   = $faq['re_title'] ?? '';
						$re_content = $faq['re_content'] ?? '';
						$re_url     = $faq['re_url'] ?? '';
						$re_extlink = $faq['re_extlink'] ? ' target="_blank"' : '';

						if ( $re_title ) :
							?>
                            <li data-num="<?= ( $i + 1 ) ?>" class="accordion-item<?= $_active_class ?>"
                                data-accordion-item>
                                <a class="accordion-title" href="#"
                                   aria-label="<?php echo esc_attr( $re_title ); ?>"><span><?= $re_title ?></span></a>
								<?php if ( $re_content ) : ?>
                                    <div class="accordion-content" data-tab-content>
                                        <div class="desc"><?= $re_content ?></div>
										<?php if ( $re_url ) : ?>
                                            <span class="btn-outer">
                                <a<?= $re_extlink ?> class="btn-link" href="<?= $re_url ?>"
                                                     title="<?php echo esc_attr__( 'Chi tiết', 'hd' ); ?>"><?php echo __( 'Chi tiết', 'hd' ); ?></a>
                            </span>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>
                            </li>
						<?php
						endif;
					endfor;
					?>
                </ul>
                <ul class="accordion accordion-2" data-accordion data-multi-expand="true" data-allow-all-closed="true">
					<?php
					for ( ; $i < count( $faq_list ); $i ++ ) :
						$faq = $faq_list[ $i ];

						$_active_class = '';
						if ( $i == $half_start ) {
							$_active_class = ' is-active';
						}

						$re_title   = $faq['re_title'] ?? '';
						$re_content = $faq['re_content'] ?? '';
						$re_url     = $faq['re_url'] ?? '';
						$re_extlink = $faq['re_extlink'] ? ' target="_blank"' : '';

						if ( $re_title ) :

							?>
                            <li data-num="<?= ( $i + 1 ) ?>" class="accordion-item<?= $_active_class ?>"
                                data-accordion-item>
                                <a class="accordion-title" href="#"
                                   aria-label="<?php echo esc_attr( $re_title ); ?>"><span><?= $re_title ?></span></a>
								<?php if ( $re_content ) : ?>
                                    <div class="accordion-content" data-tab-content>
                                        <div class="desc"><?= $re_content ?></div>
										<?php if ( $re_url ) : ?>
                                            <span class="btn-outer">
                                    <a<?= $re_extlink ?> class="btn-link" href="<?= $re_url ?>"
                                                         title="<?php echo esc_attr__( 'Chi tiết', 'hd' ); ?>"><?php echo __( 'Chi tiết', 'hd' ); ?></a>
                                </span>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>
                            </li>
						<?php endif; endfor; ?>
                </ul>
            </div>

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
