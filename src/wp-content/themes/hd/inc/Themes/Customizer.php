<?php

namespace Webhd\Themes;

/**
 * Customize Class
 * @author   WEBHD
 */

\defined( '\WPINC' ) || die;

use WP_Customize_Color_Control;
use WP_Customize_Image_Control;
use WP_Customize_Manager;

final class Customizer {
	public function __construct() {

		// Setup the Theme Customizer settings and controls.
		add_action( 'customize_register', [ &$this, 'register' ], 30 );
	}

	/**
	 * Register customizer options.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function register( $wp_customize ) {

		// logo mobile
		$wp_customize->add_setting( 'alt_logo' );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'alt_logo',
				[
					'label'    => __( 'Alternative Logo', 'hd' ),
					'section'  => 'title_tagline',
					'settings' => 'alt_logo',
					'priority' => 8,
				]
			)
		);

        // add control
        $wp_customize->add_setting( 'logo_heading_setting', [
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh'
        ] );

        $wp_customize->add_control(
            'logo_heading_control',
            [
                'label'       => __( 'Home SEO title', 'hd' ),
                'section'     => 'title_tagline',
                'settings'    => 'logo_heading_setting',
                'type'        => 'text',
                'priority' => 9,
            ]
        );

        // add control
        $wp_customize->add_setting( 'logo_title_setting', [
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh'
        ] );

        $wp_customize->add_control(
            'logo_title_control',
            [
                'label'       => __( 'Tiêu đề logo', 'hd' ),
                'section'     => 'title_tagline',
                'settings'    => 'logo_title_setting',
                'type'        => 'text',
                'priority' => 9,
            ]
        );

        //...
        // add control
        $wp_customize->add_setting( 'logo_slogan_setting', [
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh'
        ] );

        $wp_customize->add_control(
            'logo_slogan_control',
            [
                'label'       => __( 'Slogan', 'hd' ),
                'section'     => 'title_tagline',
                'settings'    => 'logo_slogan_setting',
                'type'        => 'text',
                'priority' => 9,
            ]
        );

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create custom panels
		$wp_customize->add_panel(
			'addon_menu_panel',
			[
				'priority'       => 140,
				'theme_supports' => '',
				'title'          => __( 'HD', 'hd' ),
				'description'    => __( 'Controls the add-on menu', 'hd' ),
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create offcanvas section
		$wp_customize->add_section(
			'offcanvas_menu_section',
			[
				'title'    => __( 'offCanvas menu', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1000,
			]
		);

		// add offcanvas control
		$wp_customize->add_setting(
			'offcanvas_menu_setting',
			[
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'offcanvas_menu_control',
			[
				'label'    => __( 'offCanvas position', 'hd' ),
				'type'     => 'radio',
				'section'  => 'offcanvas_menu_section',
				'settings' => 'offcanvas_menu_setting',
				'choices'  => [
					'left'    => __( 'Left', 'hd' ),
					'right'   => __( 'Right', 'hd' ),
					'top'     => __( 'Top', 'hd' ),
					'bottom'  => __( 'Bottom', 'hd' ),
					'default' => __( 'Default (Right)', 'hd' ),
				],
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create news section
		$wp_customize->add_section(
			'news_menu_section',
			[
				'title'    => __( 'News images', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1001,
			]
		);

		// add news control
		$wp_customize->add_setting(
			'news_menu_setting',
			[
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'news_menu_control',
			[
				'label'    => __( 'Image ratio', 'hd' ),
				'type'     => 'radio',
				'section'  => 'news_menu_section',
				'settings' => 'news_menu_setting',
				'choices'  => [
					'1-1'     => __( '1:1', 'hd' ),
					'3-2'     => __( '3:2', 'hd' ),
					'4-3'     => __( '4:3', 'hd' ),
					'16-9'    => __( '16:9', 'hd' ),
					'default' => __( 'Ratio default (16:9)', 'hd' ),
				],
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create video section
		$wp_customize->add_section(
			'video_menu_section',
			[
				'title'    => __( 'Video images', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1001,
			]
		);

		// add news control
		$wp_customize->add_setting(
			'video_menu_setting',
			[
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'video_menu_control',
			[
				'label'    => __( 'Image ratio', 'hd' ),
				'type'     => 'radio',
				'section'  => 'video_menu_section',
				'settings' => 'video_menu_setting',
				'choices'  => [
					'1-1'     => __( '1:1', 'hd' ),
					'3-2'     => __( '3:2', 'hd' ),
					'4-3'     => __( '4:3', 'hd' ),
					'16-9'    => __( '16:9', 'hd' ),
					'default' => __( 'Ratio default (16:9)', 'hd' ),
				],
			]
		);

        // -------------------------------------------------------------
        // -------------------------------------------------------------

        if ( class_exists( '\WooCommerce' ) ) {

            // Create product section
            $wp_customize->add_section(
                'product_menu_section',
                [
                    'title' => __('Product images', 'hd'),
                    'panel' => 'addon_menu_panel',
                    'priority' => 1002,
                ]
            );

            // add product control
            $wp_customize->add_setting(
                'product_menu_setting',
                [
                    'default' => 'default',
                    'sanitize_callback' => 'sanitize_text_field',
                    'transport' => 'refresh',
                ]
            );
            $wp_customize->add_control(
                'product_menu_control',
                [
                    'label' => __('Image ratio', 'hd'),
                    'type' => 'radio',
                    'section' => 'product_menu_section',
                    'settings' => 'product_menu_setting',
                    'choices' => [
                        '1-1' => __('1:1', 'hd'),
                        '3-2' => __('3:2', 'hd'),
                        '4-3' => __('4:3', 'hd'),
                        '16-9' => __('16:9', 'hd'),
                        'default' => __('Ratio default (16:9)', 'hd'),
                    ],
                ]
            );
        }

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create custom field for social settings layout
		$wp_customize->add_section(
			'socials_menu_section',
			[
				'title'    => __( 'Social', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1005,
			]
		);

		// Add options for facebook appid
		$wp_customize->add_setting( 'fb_menu_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'fb_menu_control',
			[
				'label'       => __( 'Facebook AppID', 'hd' ),
				'section'     => 'socials_menu_section',
				'settings'    => 'fb_menu_setting',
				'type'        => 'text',
				'description' => __( "You can do this at <a href='https://developers.facebook.com/apps/'>developers.facebook.com/apps</a>", 'hd' ),
			]
		);

		// Add options for facebook page_id
		$wp_customize->add_setting( 'fbpage_menu_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'fbpage_menu_control',
			[
				'label'       => __( 'Facebook PageID', 'hd' ),
				'section'     => 'socials_menu_section',
				'settings'    => 'fbpage_menu_setting',
				'type'        => 'text',
				'description' => __( "How do I find my Facebook Page ID? <a href='https://www.facebook.com/help/1503421039731588'>facebook.com/help/1503421039731588</a>", 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'fb_chat_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'fb_chat_control',
			[
				'type'     => 'checkbox',
				'settings' => 'fb_chat_setting',
				'section'  => 'socials_menu_section',
				'label'    => __( 'Facebook Live Chat', 'hd' ),
				//'description' => __( 'Thêm facebook messenger live chat', 'hd' ),
			]
		);

		// Zalo Appid
		$wp_customize->add_setting( 'zalo_menu_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'zalo_menu_control',
			[
				'label'       => __( 'Zalo AppID', 'hd' ),
				'section'     => 'socials_menu_section',
				'settings'    => 'zalo_menu_setting',
				'type'        => 'text',
				'description' => __( "You can do this at <a href='https://developers.zalo.me/docs/'>developers.zalo.me/docs/</a>", 'hd' ),
			]
		);

		// Zalo oaid
		$wp_customize->add_setting( 'zalo_oa_menu_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'zalo_oa_menu_control',
			[
				'label'       => __( 'Zalo OAID', 'hd' ),
				'section'     => 'socials_menu_section',
				'settings'    => 'zalo_oa_menu_setting',
				'type'        => 'text',
				'description' => __( "You can do this at <a href='https://oa.zalo.me/manage/oa?option=create'>oa.zalo.me/manage/oa?option=create</a>", 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'zalo_chat_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
				'transport'         => 'refresh',
			]
		);
		$wp_customize->add_control(
			'zalo_chat_control',
			[
				'type'     => 'checkbox',
				'settings' => 'zalo_chat_setting',
				'section'  => 'socials_menu_section',
				'label'    => __( 'Zalo Live Chat', 'hd' ),
				//'description' => __( 'Thêm zalo live chat', 'hd' ),
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create hotline section
		$wp_customize->add_section(
			'hotline_menu_section',
			[
				'title'    => __( 'Hotline', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1006,
			]
		);

		// add control
		$wp_customize->add_setting( 'hotline_setting', [
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh'
		] );

		$wp_customize->add_control(
			'hotline_control',
			[
				'label'       => __( 'Hotline', 'hd' ),
				'section'     => 'hotline_menu_section',
				'settings'    => 'hotline_setting',
				'description' => __( 'Hotline number, support easier interaction on the phone', 'hd' ),
				'type'        => 'text',
			]
		);

		// add control
		$wp_customize->add_setting( 'hotline_zalo_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
		$wp_customize->add_control(
			'hotline_zalo_control',
			[
				'label'       => __( 'Zalo Hotline', 'hd' ),
				'section'     => 'hotline_menu_section',
				'settings'    => 'hotline_zalo_setting',
				'type'        => 'text',
				'description' => __( 'Zalo Hotline number, support easier interaction on the zalo', 'hd' ),
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create breadcrumbs section
		$wp_customize->add_section(
			'breadcrumb_section',
			[
				'title'    => __( 'Breadcrumbs', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1007,
			]
		);

		// add control
		$wp_customize->add_setting( 'breadcrumb_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'breadcrumb_bg_control',
				[
					'label'    => __( 'Breadcrumb background', 'hd' ),
					'section'  => 'breadcrumb_section',
					'settings' => 'breadcrumb_bg_setting',
					'priority' => 9,
				]
			)
		);

        // -------------------------------------------------------------
        // -------------------------------------------------------------

        // Create header section
        $wp_customize->add_section(
            'header_section',
            [
                'title'    => __( 'Header', 'hd' ),
                'panel'    => 'addon_menu_panel',
                'priority' => 1008,
            ]
        );

        // add control
        $wp_customize->add_setting(
            'header_bgcolor_setting',
            [
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'capability'        => 'edit_theme_options',
            ]
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize,
                'header_bgcolor_control',
                [
                    'label'    => __( 'Header background color', 'hd' ),
                    'section'  => 'header_section',
                    'settings' => 'header_bgcolor_setting',
                    'priority' => 9,
                ]
            )
        );

		// add control
		$wp_customize->add_setting( 'header_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'header_bg_control',
				[
					'label'    => __( 'Header background', 'hd' ),
					'section'  => 'header_section',
					'settings' => 'header_bg_setting',
				]
			)
		);

		// Add control
		$wp_customize->add_setting(
			'top_header_setting',
			[
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field'
			]
		);
		$wp_customize->add_control(
			'top_header_control',
			[
				'label'       => __( 'Top-Header columns', 'hd' ),
				'section'     => 'header_section',
				'settings'    => 'top_header_setting',
				'type'        => 'number',
				'description' => __( 'Top Header columns number', 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'top_header_container_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
			]
		);
		$wp_customize->add_control(
			'top_header_container_control',
			[
				'type'     => 'checkbox',
				'settings' => 'top_header_container_setting',
				'section'  => 'header_section',
				'label'    => __( 'Top Header Container', 'hd' ),
			]
		);

		// Add control
		$wp_customize->add_setting(
			'header_setting',
			[
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			]
		);
		$wp_customize->add_control(
			'header_control',
			[
				'label'       => __( 'Header columns', 'hd' ),
				'section'     => 'header_section',
				'settings'    => 'header_setting',
				'type'        => 'number',
				'description' => __( 'Header columns number', 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'header_container_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
			]
		);
		$wp_customize->add_control(
			'header_container_control',
			[
				'type'     => 'checkbox',
				'settings' => 'header_container_setting',
				'section'  => 'header_section',
				'label'    => __( 'Header Container', 'hd' ),
			]
		);

		// Add control
		$wp_customize->add_setting(
			'bottom_header_setting',
			[
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field'
			]
		);
		$wp_customize->add_control(
			'bottom_header_control',
			[
				'label'       => __( 'Bottom Header columns', 'hd' ),
				'section'     => 'header_section',
				'settings'    => 'bottom_header_setting',
				'type'        => 'number',
				'description' => __( 'Bottom Header columns number', 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'bottom_header_container_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
			]
		);
		$wp_customize->add_control(
			'bottom_header_container_control',
			[
				'type'     => 'checkbox',
				'settings' => 'bottom_header_container_setting',
				'section'  => 'header_section',
				'label'    => __( 'Bottom Header Container', 'hd' ),
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Create footer section
		$wp_customize->add_section(
			'footer_section',
			[
				'title'    => __( 'Footer', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1008,
			]
		);

		// add control Footer background
		$wp_customize->add_setting( 'footer_bg_setting', [ 'transport' => 'refresh' ] );
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'footer_bg_control',
				[
					'label'    => __( 'Footer background', 'hd' ),
					'section'  => 'footer_section',
					'settings' => 'footer_bg_setting',
					'priority' => 9,
				]
			)
		);

		// add control
		$wp_customize->add_setting(
			'footer_color_setting',
			[
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
				'capability'        => 'edit_theme_options',
			]
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize,
				'footer_color_control',
				[
					'label'    => __( 'Footer color', 'hd' ),
					'section'  => 'footer_section',
					'settings' => 'footer_color_setting',
				]
			)
		);

        // add control
        $wp_customize->add_setting(
            'footer_bgcolor_setting',
            [
                'default'           => '',
                'sanitize_callback' => 'sanitize_hex_color',
                'capability'        => 'edit_theme_options',
            ]
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize,
                'footer_bgcolor_control',
                [
                    'label'    => __( 'Footer background Color', 'hd' ),
                    'section'  => 'footer_section',
                    'settings' => 'footer_bgcolor_setting',
                ]
            )
        );

        // add control
        $wp_customize->add_setting( 'footer_row_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
        $wp_customize->add_control(
            'footer_row_control',
            [
                'label'       => __( 'Footer rows number', 'hd' ),
                'section'     => 'footer_section',
                'settings'    => 'footer_row_setting',
                'type'        => 'number',
                'description' => __( 'Footer rows number', 'hd' ),
            ]
        );

        // add control
        $wp_customize->add_setting( 'footer_col_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
        $wp_customize->add_control(
            'footer_col_control',
            [
                'label'       => __( 'Footer columns number', 'hd' ),
                'section'     => 'footer_section',
                'settings'    => 'footer_col_setting',
                'type'        => 'number',
                'description' => __( 'Footer columns number', 'hd' ),
            ]
        );

		// add control
		$wp_customize->add_setting(
			'footer_container_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
			]
		);
		$wp_customize->add_control(
			'footer_container_control',
			[
				'type'     => 'checkbox',
				'settings' => 'footer_container_setting',
				'section'  => 'footer_section',
				'label'    => __( 'Footer Container', 'hd' ),
			]
		);

		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// Block Editor + Gutenberg + Widget
		$wp_customize->add_section(
			'block_editor_section',
			[
				'title'    => __( 'Block Editor', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1010,
			]
		);

		// add control
		$wp_customize->add_setting(
			'use_widgets_block_editor_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
				'transport'         => 'refresh',
			]
		);

		$wp_customize->add_control(
			'use_widgets_block_editor_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'use_widgets_block_editor_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Disable block widgets', 'hd' ),
				'description' => __( 'Disables the block editor from managing widgets', 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'gutenberg_use_widgets_block_editor_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
				'transport'         => 'refresh',
			]
		);

		$wp_customize->add_control(
			'gutenberg_use_widgets_block_editor_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'gutenberg_use_widgets_block_editor_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Disable Gutenberg widgets', 'hd' ),
				'description' => __( 'Disables the block editor from managing widgets in the Gutenberg plugin', 'hd' ),
			]
		);

		// add control
		$wp_customize->add_setting(
			'use_block_editor_for_post_type_setting',
			[
				'default'           => false,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_checkbox',
				'transport'         => 'refresh',
			]
		);

		$wp_customize->add_control(
			'use_block_editor_for_post_type_control',
			[
				'type'        => 'checkbox',
				'settings'    => 'use_block_editor_for_post_type_setting',
				'section'     => 'block_editor_section',
				'label'       => __( 'Use Classic Editor', 'hd' ),
				'description' => __( 'Use Classic Editor - Disable Gutenberg Editor', 'hd' ),
			]
		);

        // -------------------------------------------------------------
        // -------------------------------------------------------------

		// Other
		$wp_customize->add_section(
			'other_section',
			[
				'title'    => __( 'Other', 'hd' ),
				'panel'    => 'addon_menu_panel',
				'priority' => 1011,
			]
		);

        /** Button contact */
        $wp_customize->add_setting( 'btn_contact_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
        $wp_customize->add_control(
            'btn_contact_control',
            [
                'label'       => __( 'Title of contact button', 'hd' ),
                'section'     => 'other_section',
                'settings'    => 'btn_contact_setting',
                'type'        => 'text',
                'description' => __( 'Tiêu đề nút liên hệ', 'hd' ),
            ]
        );

        /** */
        $wp_customize->add_setting( 'btn_contact_link_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
        $wp_customize->add_control(
            'btn_contact_link_control',
            [
                'section'     => 'other_section',
                'settings'    => 'btn_contact_link_setting',
                'type'        => 'text',
                'description' => __( 'Link liên hệ', 'hd' ),
            ]
        );

        /** */
        $wp_customize->add_setting(
            'btn_contact_window_setting',
            [
                'default'           => false,
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport'         => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'btn_contact_window_control',
            [
                'type'     => 'checkbox',
                'settings' => 'btn_contact_window_setting',
                'section'  => 'other_section',
                'label'    => __( 'Mở cửa sổ mới', 'hd' ),
            ]
        );

        /** */
        $wp_customize->add_setting(
            'btn_contact_popup_setting',
            [
                'default'           => false,
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_checkbox',
                'transport'         => 'refresh',
            ]
        );
        $wp_customize->add_control(
            'btn_contact_popup_control',
            [
                'type'     => 'checkbox',
                'settings' => 'btn_contact_popup_setting',
                'section'  => 'other_section',
                'label'    => __( 'Popup', 'hd' ),
            ]
        );

        /** */
        $wp_customize->add_setting('btn_contact_shortcode_setting', ['sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh']);
        $wp_customize->add_control(
            'btn_contact_shortcode_control',
            [
                //'label'       => __( 'Shortcode forms', 'hd' ),
                'section'     => 'other_section',
                'settings'    => 'btn_contact_shortcode_setting',
                'description' => __( 'Shortcode forms', 'hd' ),
                'type'        => 'textarea',
            ]
        );

		// add control
		$wp_customize->add_setting(
			'GPKD_setting',
			[
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh'
			]
		);

		$wp_customize->add_control(
			'GPKD_control',
			[
				'label'       => __( 'GPKD', 'hd' ),
				'section'     => 'other_section',
				'settings'    => 'GPKD_setting',
				'description' => __( 'Thông tin Giấy phép kinh doanh (nếu có)', 'hd' ),
				'type'        => 'textarea',
			]
		);

		// add control
		// meta theme-color
		$wp_customize->add_setting(
			'theme_color_setting',
			[
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
				'capability'        => 'edit_theme_options',
			]
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control( $wp_customize,
				'theme_color_control',
				[
					'label'    => __( 'Theme Color', 'hd' ),
					'section'  => 'other_section',
					'settings' => 'theme_color_setting',
				]
			)
		);

        // Hide menu
        $wp_customize->add_setting('remove_menu_setting', ['sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh']);
        $wp_customize->add_control(
            'remove_menu_control',
            [
                'label'       => __( 'Remove Menu', 'hd' ),
                'section'     => 'other_section',
                'settings'    => 'remove_menu_setting',
                'description' => __( 'The menu list will be hidden', 'hd' ),
                'type'        => 'textarea',
            ]
        );
	}
}
