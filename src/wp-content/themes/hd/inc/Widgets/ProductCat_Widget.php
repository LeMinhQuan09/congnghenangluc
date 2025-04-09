<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;
use Webhd\Helpers\Url;

class ProductCat_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display products categories + Custom Fields', 'hd' );
		$this->widget_name        = __( 'W - Products Cat', 'hd' );
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
		$select_title = $ACF->select_title ?? '';
		$html_desc     = $ACF->html_desc ?? '';
		$title_heading = $ACF->title_heading ?? 'h3';
		$view_count    = $ACF->view_count ?? false;
		$hide_thumbs   = $ACF->hide_thumbs ?? false;

		?>
        <div class="section filter-productcat <?= $_class ?>">
			<?php if ( $title || Str::stripSpace( $html_desc ) ) : ?>
				<div class="grid-container width-extra title-container">
					<?php if ( $title ) :
						if($select_title) {
							echo '<' . $select_title . ' class="heading-title"'?>>
							<?php echo $title; ?>
							<?php echo '</' . $select_title . '>'; 
							} else { ?>
							<h2 class="heading-title"><?php echo $title; ?></h2>
						<?php } ?>
					<?php endif; ?>
					<?php if ( Str::stripSpace( $html_desc ) ) : ?>
						<div class="html-desc"><?php echo $html_desc; ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php
				// loop
				$product_cat = $ACF->product_cat ?? '';
				if ( $product_cat ) : ?>
				<div class="wrapper">
					<div class="wrapper-inner">
						<div class="grid-x grid-productcat list-productcat">
							<?php
							foreach ( $product_cat as $key => $term_id ) :
								$term = get_term( $term_id );
								if ( $term ) :
									$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

									$_view_number = (int) $term->count;
									?>
									<div class="cell cell-<?= $key ?>">
											<a <?php wc_product_cat_class( 'd-block item' ); ?>
													href="<?php echo get_term_link( $term_id, 'product_cat' ); ?>"
													title="<?php echo esc_attr( $term->name ); ?>">
												<?php if ( ! $hide_thumbs ) : ?>
													<div class="cover">
														<span class="after-overlay res auto scale ar-4-3"><?php echo wp_get_attachment_image( $thumbnail_id, 'full' ); ?></span>
													</div>
												<?php endif; ?>
												<div class="cover-content">
													<?php
													if ( ! $view_count ) :
														echo '<' . $title_heading . ' class="h6">' . $term->name . '</' . $title_heading . '>';
													else :
														echo '<' . $title_heading . ' class="h6">' . $term->name . '<span class="count">(' . $_view_number . ')</span>' . '</' . $title_heading . '>';
													endif;
													?>
												</div>
											</a>
									</div>
								<?php
								endif;
							endforeach; ?>
						</div>
						<div class="grid-x grid-productcat list-productcat">
							<?php
							foreach ( $product_cat as $key => $term_id ) :
								$term = get_term( $term_id );
								if ( $term ) :
									$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );

									$_view_number = (int) $term->count;
									?>
									<div class="cell cell-<?= $key ?>">
											<a <?php wc_product_cat_class( 'd-block item' ); ?>
													href="<?php echo get_term_link( $term_id, 'product_cat' ); ?>"
													title="<?php echo esc_attr( $term->name ); ?>">
												<?php if ( ! $hide_thumbs ) : ?>
													<div class="cover">
														<span class="after-overlay res auto scale ar-4-3"><?php echo wp_get_attachment_image( $thumbnail_id, 'full' ); ?></span>
													</div>
												<?php endif; ?>
												<div class="cover-content">
													<?php
													if ( ! $view_count ) :
														echo '<' . $title_heading . ' class="h6">' . $term->name . '</' . $title_heading . '>';
													else :
														echo '<' . $title_heading . ' class="h6">' . $term->name . '<span class="count">(' . $_view_number . ')</span>' . '</' . $title_heading . '>';
													endif;
													?>
												</div>
											</a>
									</div>
								<?php
								endif;
							endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
        </div>
		<?php
		$content = ob_get_clean();
		echo $content; // WPCS: XSS ok.
	}
}
