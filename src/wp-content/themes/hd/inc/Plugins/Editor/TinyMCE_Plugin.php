<?php

namespace Webhd\Plugins\Editor;

\defined( 'ABSPATH' ) || die;

/**
 * TinyMCE Plugin
 *
 * @author WEBHD
 */
class TinyMCE_Plugin {
	public function __construct() {

		add_filter( 'mce_buttons', [ &$this, 'tinymce_add_table_button' ] );
		add_filter( 'mce_buttons_2', [ &$this, 'tinymce_add_table_button_2' ] );

		add_filter( 'mce_external_plugins', [ &$this, 'tinymce_add_table_plugin' ] );
	}

	/**
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_button_2( $buttons ) {

		$buttons[] = 'media';
		$buttons[] = 'table';

		return $buttons;
	}

	/**
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_button( $buttons ) {

		$buttons[] = 'underline';
		$buttons[] = 'alignjustify';
		$buttons[] = 'unlink';

		$buttons[] = 'codesample';
		$buttons[] = 'toc';

		//array_push( $buttons, 'separator', 'fullscreen' );

		return $buttons;
	}

	/**
	 * @param $plugins
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_plugin( $plugins ) {
		$plugins['table']      = W_THEME_INC_URL . '/Plugins/Editor/tinymce/table/plugin.min.js';
		$plugins['codesample'] = W_THEME_INC_URL . '/Plugins/Editor/tinymce/codesample/plugin.min.js';
		$plugins['toc']        = W_THEME_INC_URL . '/Plugins/Editor/tinymce/toc/plugin.min.js';
		//$plugins['fullscreen'] = W_THEME_INC_URL . '/Plugins/Editor/tinymce/fullscreen/plugin.min.js';

		return $plugins;
	}
}