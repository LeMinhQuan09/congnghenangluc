<?php

namespace Webhd\Widgets;

use Webhd\Helpers\Abstract_Widget;
use Webhd\Helpers\Str;

class ListImage_Widget extends Abstract_Widget {
	public function __construct() {
		$this->widget_description = __( 'List Image', 'hd' );
		$this->widget_name        = __( 'W - List Image', 'hd' );

		parent::__construct();
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */

		// ACF attributes
		$ACF = $this->acfFields( 'widget_' . $args['widget_id'] );
		if ( is_null( $ACF ) ) {
			wp_die( __( 'Required: "Advanced Custom Fields" plugin', 'hd' ) );
		}
		// class
		$class =  $ACF->css_class;
		$title = $ACF->title_list ?? '';
        $list_image = $ACF->list_image;
		?>
        <div class="section listimage <?php if($class){ echo $class; } ?>">
            <div class="grid-container width-extra">
				<?php if($title){
					echo '<p class="heading-title">' . $title . '</p>';
				} ?>
                <?php if($list_image){
					if($class == 'gallery-sidebar'){ echo '<div class="wrapper">';
					echo '<div class="wrapper-inner">'; } ?>
                    <div class="grid-x">
                        <?php foreach($list_image as $val){ ?>
                            <div class="cell">
                                <a href="<?php if($val['link']){ echo $val['link']; } else {
                                    echo 'javascript:void(0)';
                                } ?>" class="relative">
                                    <img src="<?php echo esc_url($val['image']['url']); ?>" alt="image">
                                </a> 
                            </div>
                        <?php } ?>
                    </div>
					<?php if($class == 'gallery-sidebar'){ ?>
						<div class="grid-x">
                        <?php foreach($list_image as $val){ ?>
                            <div class="cell">
                                <a href="<?php if($val['link']){ echo $val['link']; } else {
                                    echo 'javascript:void(0)';
                                } ?>" class="relative">
                                    <img src="<?php echo esc_url($val['image']['url']); ?>" alt="image">
                                </a> 
                            </div>
                        <?php } ?>
                    </div>
					<?php } ?>
					<?php 
					if($class == 'gallery-sidebar'){ echo '</div>';
						echo '</div>'; } ?>
                <?php } ?>
            </div>
        </div>
		<?php
	}
}
