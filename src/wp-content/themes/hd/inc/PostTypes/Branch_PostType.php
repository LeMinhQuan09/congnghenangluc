<?php

namespace Webhd\PostTypes;

\defined('\WPINC') || die;

if (!class_exists('Branch_PostType')) {
    class Branch_PostType
    {
        public function __construct()
        {
            add_action('init', [&$this, 'branch_post_type'], 10);
        }

        /***********************************************/

        // Register Custom Post Type
        public function branch_post_type()
        {

            $labels = [
                'name'                  => __('Branch', 'hd'),
                'singular_name'         => __('Branch', 'hd'),
                'menu_name'             => __('Branch', 'hd'),
                'name_admin_bar'        => __('Branch', 'hd'),
                'archives'              => __('Item Archives', 'hd'),
                'attributes'            => __('Item Attributes', 'hd'),
                'parent_item_colon'     => __('Parent Item:', 'hd'),
                'all_items'             => __('All Branch', 'hd'),
                'add_new_item'          => __('Add New Item', 'hd'),
                'add_new'               => __('Add New', 'hd'),
                'new_item'              => __('New Item', 'hd'),
                'edit_item'             => __('Edit Item', 'hd'),
                'update_item'           => __('Update Item', 'hd'),
                'view_item'             => __('View Item', 'hd'),
                'view_items'            => __('View Items', 'hd'),
                'search_items'          => __('Search Items', 'hd'),
                'not_found'             => __('Not found', 'hd'),
                'not_found_in_trash'    => __('Not found in Trash', 'hd'),
                'featured_image'        => __('Featured Image', 'hd'),
                'set_featured_image'    => __('Set featured image', 'hd'),
                'remove_featured_image' => __('Remove featured image', 'hd'),
                'use_featured_image'    => __('Use as featured image', 'hd'),
                'insert_into_item'      => __('Insert Item', 'hd'),
                'uploaded_to_this_item' => __('Uploaded to this item', 'hd'),
                'items_list'            => __('Items list', 'hd'),
                'items_list_navigation' => __('Items list navigation', 'hd'),
                'filter_items_list'     => __('Filter items list', 'hd'),
            ];
            $args = [
                'label'               => __('Branch', 'hd'),
                'description'         => __('Post Type Description', 'hd'),
                'labels'              => $labels,
                'supports'            => [ 'title', 'editor', 'thumbnail', 'page-attributes', 'comments', 'excerpt', 'author' ],
                'taxonomies'          => [],
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 13,
                'menu_icon'           => 'dashicons-admin-generic',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'show_in_rest'        => true,
            ];

            register_post_type('branch', $args);
        }
    }
}
