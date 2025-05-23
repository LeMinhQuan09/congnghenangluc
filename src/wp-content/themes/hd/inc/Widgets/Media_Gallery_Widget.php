<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Cast;
use Webhd\Helpers\Str;
use WP_Widget_Media;

class Media_Gallery_Widget extends \WP_Widget_Media_Gallery
{
    /**
     * @param $args
     * @param $instance
     *
     * @return void
     */
    public function widget($args, $instance)
    {

        // ACF attributes
        $ACF = $this->acfFields('widget_' . $args['widget_id']);

        $wrapper = $ACF->wrapper ?? false;
        $html_desc = $ACF->html_desc ?? '';
        $css_class = $ACF->css_class ?? '';
        $heading_tag = $ACF->heading_tag ?? 'h2';
        $heading_class = $ACF->heading_class ?? 'heading-title';

        $instance = wp_parse_args($instance, wp_list_pluck($this->get_instance_schema(), 'default'));

        // Short-circuit if no media is selected.
        if (!$this->has_content($instance) && !Str::stripSpace($html_desc)) {
            return;
        }

        $before_widget = '<div class="section widget_media_gallery">';
        if ($css_class) {
            $before_widget = '<div class="section widget_media_gallery ' . $css_class . '">';
        }

        echo $before_widget;
        if ($wrapper) echo '<div class="grid-container width-extra">';

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = $instance['title'] ?? '';

        if ($title) {
            echo '<' . $heading_tag . ' class="' . $heading_class . '">' . $title . '</' . $heading_tag . '>';
        }

        if (Str::stripSpace($html_desc)) {
            echo '<div class="html-desc">' . $html_desc . '</div>';
        }

        /**
         * Filters the media widget instance prior to rendering the media.
         *
         * @param array $instance Instance data.
         * @param array $args Widget args.
         * @param WP_Widget_Media $widget Widget object.
         * @since 4.8.0
         *
         */
        $instance = apply_filters("widget_{$this->id_base}_instance", $instance, $args, $this);

        $this->acf_render_media($instance, $ACF);

        if ($wrapper) echo '</div>';
        echo $args['after_widget'];
    }

    /**
     * @param $instance
     * @param $ACF
     *
     * @return void
     */
    public function acf_render_media($instance, $ACF): void
    {
        $instance = array_merge(wp_list_pluck($this->get_instance_schema(), 'default'), $instance);

        $shortcode_atts = array_merge(
            $instance,
            [
                'link' => $instance['link_type'],
            ]
        );

        // @codeCoverageIgnoreStart
        if ($instance['orderby_random']) {
            $shortcode_atts['orderby'] = 'rand';
        }

        // @codeCoverageIgnoreEnd
        echo $this->acf_gallery_shortcode($shortcode_atts, $ACF);
    }

    /**
     * Builds the Gallery shortcode output.
     *
     * @param $attr
     * @param $ACF
     *
     * @return mixed|string
     */
    protected function acf_gallery_shortcode($attr, $ACF)
    {

        $_masonry = $ACF->masonry_layout ?? false;
        $_masonry = $_masonry ? ' data-masonry' : '';

        $post = get_post();

        static $instance = 0;
        $instance++;

        if (!empty($attr['ids'])) {
            // 'ids' is explicitly ordered, unless you specify otherwise.
            if (empty($attr['orderby'])) {
                $attr['orderby'] = 'post__in';
            }
            $attr['include'] = $attr['ids'];
        }

        $output = apply_filters('acf_post_gallery', '', $attr, $instance);

        if (!empty($output)) {
            return $output;
        }

        $html5 = current_theme_supports('html5', 'gallery');
        $atts = shortcode_atts(
            [
                'order' => 'ASC',
                'orderby' => 'menu_order ID',
                'id' => $post ? $post->ID : 0,
                'itemtag' => $html5 ? 'figure' : 'dl',
                'icontag' => $html5 ? 'div' : 'dt',
                'captiontag' => $html5 ? 'figcaption' : 'dd',
                'columns' => 3,
                'size' => 'thumbnail',
                'include' => '',
                'exclude' => '',
                'link' => '',
            ],
            $attr,
            'gallery'
        );

        $id = (int)$atts['id'];

        if (!empty($atts['include'])) {
            $_attachments = get_posts(
                [
                    'include' => $atts['include'],
                    'post_status' => 'inherit',
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $atts['order'],
                    'orderby' => $atts['orderby'],
                ]
            );

            $attachments = [];
            foreach ($_attachments as $key => $val) {
                $attachments[$val->ID] = $_attachments[$key];
            }
        } elseif (!empty($atts['exclude'])) {
            $attachments = get_children(
                [
                    'post_parent' => $id,
                    'exclude' => $atts['exclude'],
                    'post_status' => 'inherit',
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $atts['order'],
                    'orderby' => $atts['orderby'],
                ]
            );
        } else {
            $attachments = get_children(
                [
                    'post_parent' => $id,
                    'post_status' => 'inherit',
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image',
                    'order' => $atts['order'],
                    'orderby' => $atts['orderby'],
                ]
            );
        }

        if (empty($attachments)) {
            return '';
        }

        if (is_feed()) {
            $output = "\n";
            foreach ($attachments as $att_id => $attachment) {
                if (!empty($atts['link'])) {
                    if ('none' === $atts['link']) {
                        $output .= wp_get_attachment_image($att_id, $atts['size'], false, $attr);
                    } else {
                        $output .= wp_get_attachment_link($att_id, $atts['size'], false);
                    }
                } else {
                    $output .= wp_get_attachment_link($att_id, $atts['size'], true);
                }
                $output .= "\n";
            }
            return $output;
        }

        $itemtag = tag_escape($atts['itemtag']);
        $captiontag = tag_escape($atts['captiontag']);
        $icontag = tag_escape($atts['icontag']);
        $valid_tags = wp_kses_allowed_html('post');
        if (!isset($valid_tags[$itemtag])) {
            $itemtag = 'dl';
        }
        if (!isset($valid_tags[$captiontag])) {
            $captiontag = 'dd';
        }
        if (!isset($valid_tags[$icontag])) {
            $icontag = 'dt';
        }

        $columns = (int)$atts['columns'];
        $itemwidth = $columns > 0 ? floor(100 / $columns) : 100;
        $float = is_rtl() ? 'right' : 'left';

        $selector = "gallery-{$instance}";

        $gallery_style = '';

        if (apply_filters('acf_use_default_gallery_style', !$html5)) {
            $type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';

            $gallery_style = "
		<style{$type_attr}>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>\n\t\t";
        }

        $size_class = sanitize_html_class(is_array($atts['size']) ? implode('x', $atts['size']) : $atts['size']);
        $gallery_div = "<div" . $_masonry . " id='$selector' class='gallery gallery-id-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

        /**
         * Filters the default gallery shortcode CSS styles.
         *
         * @param string $gallery_style Default CSS styles and opening HTML div container
         *                              for the gallery shortcode output.
         * @since 2.5.0
         *
         */
        $output = apply_filters('gallery_style', $gallery_style . $gallery_div);

        $i = 0;

        foreach ($attachments as $id => $attachment) {

            $attr = (trim($attachment->post_excerpt)) ? ['aria-describedby' => "$selector-$id"] : '';

            if (!empty($atts['link']) && 'file' === $atts['link']) {
                $image_output = wp_get_attachment_link($id, $atts['size'], false, false, false, $attr);
            } elseif (!empty($atts['link']) && 'none' === $atts['link']) {
                $image_output = wp_get_attachment_image($id, $atts['size'], false, $attr);
            } else {
                $image_output = wp_get_attachment_link($id, $atts['size'], true, false, false, $attr);
            }

            $image_meta = wp_get_attachment_metadata($id);

            $orientation = '';

            if (isset($image_meta['height'], $image_meta['width'])) {
                $orientation = ($image_meta['height'] > $image_meta['width']) ? 'portrait' : 'landscape';
            }

            $output .= "<{$itemtag} class='gallery-item'>";
            $output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";

            if ($captiontag && trim($attachment->post_excerpt)) {
                $output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
            }

            $output .= "</{$itemtag}>";

            if (!$html5 && $columns > 0 && 0 === ++$i % $columns) {
                $output .= '<br style="clear: both" />';
            }
        }

        if (!$html5 && $columns > 0 && 0 !== $i % $columns) {
            $output .= "
			<br style='clear: both' />";
        }

        $output .= "
		</div>\n";

        return $output;
    }

    /**
     * @param $id
     * @return array|object
     */
    protected function acfFields($id)
    {
        if (!class_exists('\ACF')) {
            return [];
        }

        $_fields = \get_fields($id) ?? [];
        if ($_fields) {
            return Cast::toObject($_fields);
        }

        return [];
    }
}
