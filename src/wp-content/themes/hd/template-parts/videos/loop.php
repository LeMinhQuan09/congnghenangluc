<?php

\defined( '\WPINC' ) || die;

global $post;

$post_name   = get_the_title();
$ratio       = get_theme_mod_ssl( 'video_menu_setting' );
$ratio_class = $ratio;
if ( 'default' == $ratio or ! $ratio ) {
	$ratio_class = '3-2';
}

$thumbnail_size = $args['thumbnail_size'] ?? 'medium';
$ratio_class    = $args['ratio'] ?? $ratio_class;
$scale          = isset( $args['no_scale'] ) ? '' : ' scale';

$acf_fields = get_fields( $post->ID );
$video_url  = $acf_fields['video_link'] ?? '#';

$post_thumbnail = get_the_post_thumbnail( $post, $thumbnail_size );
if ( ! $post_thumbnail && '#' != $video_url ) {
	$post_thumbnail = youtube_image( $video_url );
}

?>
<div class="item cell">
	<a class="d-block fcy-video" href="<?= $video_url; ?>" aria-label="<?php echo esc_attr($post_name); ?>" tabindex="0">
		<div class="cover">
			<span class="after-overlay res<?=$scale?> ar-<?= $ratio_class ?>"><?php echo $post_thumbnail; ?></span>
		</div>
	</a>
	<div class="cover-content">
		<div class="meta">
			<?php echo get_primary_term($post); ?>
		</div>
		<h6><a class="fcy-video" href="<?= $video_url; ?>" title="<?php echo esc_attr($post_name); ?>"><?= $post_name; ?></a></h6>
	</div>
</div>
