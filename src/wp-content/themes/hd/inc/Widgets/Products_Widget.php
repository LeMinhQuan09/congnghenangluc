<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class Products_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Your site&#8217;s filter products by category.', 'hd' );
		$this->widget_name        = __( 'W - Products', 'hd' );
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
		ob_start();

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $this->get_instance_title( $instance ), $instance, $this->id_base );

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}

		// class
		$_class = $this->id;
		if ( $ACF->css_class ) {
			$_class = $_class . ' ' . $ACF->css_class;
		}

		$html_desc = $ACF->html_desc ?? '';

		//...
		$r = query_by_terms( $ACF->product_cat, 'product_cat', 'product', $ACF->include_children, $ACF->number );
		if ( ! $r ) {
			return;
		}

		?>
        <section class="section filter-products <?= $_class ?>">
			<?php if ( $title || $html_desc ) : ?>
                <div class="grid-container width-extra title-container">
					<?php if ( $title ) : ?>
                        <h2 class="heading-title"><?php echo $title; ?></h2>
					<?php endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
                        <div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>
                </div>
			<?php endif; ?>
            <div id="<?php echo $this->id; ?>" class="product type-product product-type-variable"
                 aria-label="<?php echo esc_attr( $title ); ?>">
				<?php if ( $ACF->wrapper ) {
					echo '<div class="grid-container width-extra">';
				} ?>
                <div class="grid-products grid-x">
					<?php
					$i = 0;

					// Load slides loop
					while ( $r->have_posts() && $i < $ACF->number ) : $r->the_post();

						//...
						if ( 0 === $i && $ACF->banner ) {
							echo '<div class="cell banner-cell">';
							echo '<figure>';
							if ( $ACF->url_banner ) {
								echo '<a class="after-overlay _blank" href="' . $ACF->url_banner . '" title="' . esc_attr( $title ) . '">';
							}
							echo wp_get_attachment_image( $ACF->banner, 'medium' );
							if ( $ACF->url_banner ) {
								echo '</a>';
							}
							echo '</figure>';
							echo '</div>';
						}

						global $product;

						if ( empty( $product ) || false === wc_get_loop_product_visibility( $product->get_id() ) || ! $product->is_visible() ) {
							continue;
						}
						echo '<div class="cell cell-' . $i . '">';
						wc_get_template_part( 'content', 'product' );
						echo '</div>';
						++ $i;
					endwhile;
					wp_reset_postdata();

					?>
                </div>
				<?php if ( $ACF->url ) : ?>
                    <a href="<?= esc_url( $ACF->url ) ?>" class="viewmore viewmore-button"
                       title="<?php echo esc_attr( $ACF->button_text ) ?>"
                       data-glyph-after=""><?php echo $ACF->button_text; ?></a>
				<?php endif; ?>
				<?php if ( $ACF->wrapper ) {
					echo '</div>';
				} ?>
            </div>
        </section>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.
	}
}
