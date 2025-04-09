<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Cast;
use WP_Widget_Media;

class Media_Image_Widget extends \WP_Widget_Media_Image {

	/**
	 * @param $args
	 * @param $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );

		$wrapper   = $ACF->wrapper ?? false;
		$css_class = $ACF->css_class ? ' ' . $ACF->css_class : '';

		//
		$instance = wp_parse_args( $instance, wp_list_pluck( $this->get_instance_schema(), 'default' ) );

		// Short-circuit if no media is selected.
		if ( ! $this->has_content( $instance ) ) {
			return;
		}

		$before_widget = '<div class="section widget_media_image' . $css_class . '">';
		echo $before_widget;

		if ( $wrapper ) {
			echo '<div class="grid-container width-extra">';
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = $instance['title'] ?? '';

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		/**
		 * Filters the media widget instance prior to rendering the media.
		 *
		 * @param array $instance Instance data.
		 * @param array $args Widget args.
		 * @param WP_Widget_Media $widget Widget object.
		 *
		 * @since 4.8.0
		 *
		 */
		$instance = apply_filters( "widget_{$this->id_base}_instance", $instance, $args, $this );

		$this->acf_render_media( $instance, $ACF );

		if ( $wrapper ) {
			echo '</div>';
		}
		echo $args['after_widget'];
	}

	/**
	 * @param $instance
	 * @param $ACF
	 *
	 * @return void
	 */
	public function acf_render_media( $instance, $ACF ): void {

		$_blank      = $ACF->_blank ?? false;
		$_link_blank = $instance['link_target_blank'];

		if ( $_blank ) {
			$_link_blank = true;
		}

		$instance = array_merge( wp_list_pluck( $this->get_instance_schema(), 'default' ), $instance );
		$instance = wp_parse_args(
			$instance,
			[
				'size' => 'thumbnail',
			]
		);

		$attachment = null;

		if ( $this->is_attachment_with_mime_type( $instance['attachment_id'], $this->widget_options['mime_type'] ) ) {
			$attachment = get_post( $instance['attachment_id'] );
		}

		if ( $attachment ) {
			$caption = '';
			if ( ! isset( $instance['caption'] ) ) {
				$caption = $attachment->post_excerpt;
			} elseif ( trim( $instance['caption'] ) ) {
				$caption = $instance['caption'];
			}

			$image_attributes = [
				'class' => sprintf( 'image wp-image-%d %s', $attachment->ID, $instance['image_classes'] ),
				'style' => 'max-width: 100%; height: auto;',
			];
			if ( ! empty( $instance['image_title'] ) ) {
				$image_attributes['title'] = $instance['image_title'];
			}

			if ( $instance['alt'] ) {
				$image_attributes['alt'] = $instance['alt'];
			}

			$size = $instance['size'];

			if ( 'custom' === $size || ! in_array( $size, array_merge( get_intermediate_image_sizes(), [ 'full' ] ), true ) ) {
				$size  = [ $instance['width'], $instance['height'] ];
				$width = $instance['width'];
			} else {
				$caption_size = _wp_get_image_size_from_meta( $instance['size'], wp_get_attachment_metadata( $attachment->ID ) );
				$width        = empty( $caption_size[0] ) ? 0 : $caption_size[0];
			}

			$image_attributes['class'] .= sprintf( ' attachment-%1$s size-%1$s', is_array( $size ) ? implode( 'x', $size ) : $size );

			$image = wp_get_attachment_image( $attachment->ID, $size, false, $image_attributes );

		} else {
			if ( empty( $instance['url'] ) ) {
				return;
			}

			$instance['size'] = 'custom';
			$caption          = $instance['caption'];
			$width            = $instance['width'];
			$classes          = 'image ' . $instance['image_classes'];
			if ( 0 === $instance['width'] ) {
				$instance['width'] = '';
			}
			if ( 0 === $instance['height'] ) {
				$instance['height'] = '';
			}

			$image = sprintf(
				'<img class="%1$s" src="%2$s" alt="%3$s" width="%4$s" height="%5$s" loading="lazy" />',
				esc_attr( $classes ),
				esc_url( $instance['url'] ),
				esc_attr( $instance['alt'] ),
				esc_attr( $instance['width'] ),
				esc_attr( $instance['height'] )
			);
		} // End if().

		$acf_att    = \get_fields( $attachment->ID ) ?? false;
		$src_mobile = $acf_att['src_mobile'] ?? '';

		$picture = '';
		if ( $src_mobile ) {
			$picture = '<picture>';
			$picture .= '<source media="(max-width: 639.98px)" srcset="' . attachment_image_src( $src_mobile, 'medium' ) . '">';
			$picture .= $image;
			$picture .= '</picture>';
		}

		if ( $picture ) {
			$image = $picture;
		}

		$url = '';
		if ( 'file' === $instance['link_type'] ) {
			$url = $attachment ? wp_get_attachment_url( $attachment->ID ) : $instance['url'];
		} elseif ( $attachment && 'post' === $instance['link_type'] ) {
			$url = get_attachment_link( $attachment->ID );
		} elseif ( 'custom' === $instance['link_type'] && ! empty( $instance['link_url'] ) ) {
			$url = $instance['link_url'];
		}

		if ( $url ) {

			$_a_title = '';
			if ( $instance['alt'] ) {
				$_a_title = 'aria-label="' . esc_attr( $instance['alt'] ) . '"';
			}

			$link = sprintf( '<a href="%1$s" %2$s', esc_url( $url ), $_a_title );
			if ( ! empty( $instance['link_classes'] ) ) {
				$link .= sprintf( ' class="link-overlay %s"', esc_attr( $instance['link_classes'] ) );
			} else {
				$link .= ' class="link-overlay"';
			}

			if ( ! empty( $instance['link_rel'] ) ) {
				$link .= sprintf( ' rel="%s"', esc_attr( $instance['link_rel'] ) );
			}
			if ( ! empty( $_link_blank ) ) {
				$link .= ' target="_blank"';
			}
			$link .= '>';
			//$link .= $image;
			$link .= '</a>';
			$link = wp_targeted_link_rel( $link );
		}
		if(empty($link)){
			echo '<div class="overlay">' . $image .'</div>';
		} else {
			echo '<div class="overlay">' . $image . $link . '</div>';
		}
		//echo '<div class="overlay">' . $image . $link . '</div>';
	}

	/**
	 * @param $id
	 *
	 * @return array|object
	 */
	protected function acfFields( $id ) {
		if ( ! class_exists( '\ACF' ) ) {
			return [];
		}

		$_fields = \get_fields( $id ) ?? [];
		if ( $_fields ) {
			return Cast::toObject( $_fields );
		}

		return [];
	}
}
