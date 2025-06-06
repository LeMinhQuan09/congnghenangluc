<?php

namespace Webhd\Plugins\ACF;

/**
 * ACF Plugins
 * @author   WEBHD
 */

\defined( '\WPINC' ) || die;

use Webhd\Helpers\Cast;

// If plugin - 'ACF' not exist then return.
if ( ! class_exists( '\ACF' ) ) {
	return;
}

if ( ! class_exists( 'ACF_Plugin' ) ) {
	class ACF_Plugin {
		public function __construct() {

			// Hide the ACF Admin UI
			//add_filter( 'acf/settings/show_admin', '__return_false' );

			//$this->_optionsPage();

			add_filter( 'acf/fields/wysiwyg/toolbars', [ &$this, 'wysiwyg_toolbars' ], 11, 1 );
			add_filter( 'wp_nav_menu_objects', [ &$this, 'wp_nav_menu_objects' ], 10, 2 );
		}

		// -------------------------------------------------------------

		/**
		 * @return void
		 */
		//private function _optionsPage() {}

        // -------------------------------------------------------------

        /**
         * @param $toolbars
         * @return mixed
         */
        public function wysiwyg_toolbars( $toolbars )
        {
            // Add a new toolbar called "Minimal" - this toolbar has only 1 row of buttons
            $toolbars['Minimal'] = [];
            $toolbars['Minimal'][1] = [
                'formatselect',
                'bold',
                'italic',
	            'bullist',
	            'numlist',
                'underline',
	            'alignjustify',
	            'alignleft',
	            'aligncenter',
	            'alignright',
                'link',
                'unlink',
                'forecolor',
                'blockquote'
            ];

            // remove the 'Basic' toolbar completely (if you want)
            //unset( $toolbars['Full' ] );
            //unset( $toolbars['Basic' ] );

            // return $toolbars - IMPORTANT!
            return $toolbars;
        }

        // -------------------------------------------------------------

        /**
         * @param $items
         * @param $args
         * @return mixed
         */
		public function wp_nav_menu_objects( $items, $args )
        {
			foreach ( $items as &$item ) {

				//$title = $item->title;
				$title = '<span>' . $item->title . '</span>';
				$fields = \get_fields( $item ) ?? '';

                $object_id = $item->object_id ?? '';
                $object = $item->object ?? '';
                //$type = $item->type ?? '';

				if ($fields) {
					$fields = Cast::toObject( $fields );

					if ( $fields->icon_glyph ?? false ) {
						$item->classes[] = 'glyph-menu';
						$title = '<span data-glyph="' . $fields->icon_glyph . '">' . $item->title . '</span>';
					}

					if ( $fields->icon_svg ?? false ) {
						$item->classes[] = 'icon-menu';
						$title = $fields->icon_svg . $title;
					}

					if ( $fields->icon_image ?? false ) {
						$item->classes[] = 'thumb-menu';
						$title = '<img width="48px" height="48px" alt="' . esc_attr( $item->title ) . '" src="' . attachment_image_src( $fields->icon_image ) . '" loading="lazy" />' . $title;
					}

                    //...
					if ( $fields->label_text ?? false ) {
						$_str = '';
						if ( $fields->label_color ?? false ) {
                            $_str .= 'color:' . $fields->label_color . ';';
                        }
						if ( $fields->label_background ?? false ) {
                            $_str .= 'background-color:' . $fields->label_background . ';';
                        }

						$_style = $_str ? ' style="' . $_str . '"' : '';
						$title .= '<sup' . $_style . '>' . $fields->label_text . '</sup>';
					}
				}

                /** */
                if ( 'product_cat' == $object && $object_id ) {
                    $term = get_term( $object_id );
                    if ($term) {
                        $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                        $image_src = wp_get_attachment_image_src( $thumbnail_id, 'medium' );

                        $attachment_meta = wp_get_attachment( $thumbnail_id );
                        $caption = '';
                        $description = '';
                        if ($attachment_meta->caption ?? '') {
                            $caption = $attachment_meta->caption;
                        }

                        if ($attachment_meta->description ?? '') {
                            $description = $attachment_meta->description;
                        }

                        // if ($image_src[0]) {
                        //     $title .= '<span class="menu-thumb" data-catid="' . $term->term_id . '" data-thumb="' . $image_src[0] . '" data-caption="' . esc_attr($caption) . '" data-description="' . esc_attr($description) . '"></span>';
                        // }
                    }
                }

                $item->title = $title;
			}

			return $items;
		}
	}
}