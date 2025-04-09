<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\File;

class Video_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'Display the video-play section', 'hd' );
		$this->widget_name        = __( 'W - Video', 'hd' );
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
		$_class_section = '';
		if ( $ACF->css_class ) {
			$_class_section = $_class_section . ' ' . $ACF->css_class;
		}

		$inline_url  = $ACF->inline_url ?? '';
		$youtube_url = $ACF->youtube_url ?? '';
		$autoplay    = $ACF->autoplay ?? false;
		$ratio       = $ACF->ratio ?? '21-9';

		if ( $inline_url || $youtube_url ) :

			$_class = $autoplay ? 'autoplay' : '';
			if ( ! $inline_url && $youtube_url ) {
				$_class .= ' video-js-youtube';
			}

			$_attr = $autoplay ? 'autoplay playsinline disablePictureInPicture muted loop' : 'controls disablePictureInPicture preload="none"';

			?>
            <section class="section video-section video-js-section<?= $_class_section ?>">
                <div class="video-js-inner">
					<?php if ( $inline_url ) :
						$ext = File::fileExtension( $inline_url );
						?>
                        <video class="video-js vjs-default-skin res ar-<?= $ratio ?> <?= $_class ?>" <?= $_attr ?>>
							<?php echo '<source src="' . $inline_url . '" type="video/' . $ext . '" />'; ?>
                        </video>
					<?php elseif ( $youtube_url ) : ?>
                        <video class="video-js video-js-ytb vjs-default-skin res ar-<?= $ratio ?> <?= $_class ?>" <?= $_attr ?>
                               width="640" height="264"
                               data-setup='{ "techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "<?= $youtube_url ?>"}], "youtube": { "customVars": { "wmode": "transparent" } } }'></video>
					<?php endif; ?>
                </div>
            </section>
		<?php endif;
	}
}
