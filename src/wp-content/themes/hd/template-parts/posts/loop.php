<?php

\defined( '\WPINC' ) || die;

/**
 * Template part for displaying posts with excerpts
 *
 * @package WordPress
 * @since   1.0.0
 */

$post_name   = get_the_title();
$ratio       = get_theme_mod_ssl('news_menu_setting');
$ratio_class = $ratio;
if ('default' == $ratio or !$ratio) {
	$ratio_class = '3-2';
}

global $post;

$thumbnail_size = $args['thumbnail_size'] ?? 'medium';
$ratio_class = $args['ratio'] ?? $ratio_class;
$auto = isset( $args['auto_image'] ) && $args['auto_image'] ? ' auto': '';
$scale = isset($args['no_scale']) && $args['no_scale'] ? '' : ' scale';
$post_thumbnail = get_the_post_thumbnail($post, $thumbnail_size);

$hide_thumb = false;
$hide_time = false;
$hide_term = false;
$hide_desc = false;
$hide_view_detail = false;

if (isset($args['hide_thumb']) && true === $args['hide_thumb'] ) $hide_thumb = true;
if (isset($args['hide_time']) && true === $args['hide_time'] ) $hide_time = true;
if (isset($args['hide_term']) && true === $args['hide_term'] ) $hide_term = true;
if (isset($args['hide_desc']) && true === $args['hide_desc'] ) $hide_desc = true;
if (isset($args['hide_view_detail']) && true === $args['hide_view_detail'] ) $hide_view_detail = true;

$acf_fields = get_fields( $post->ID );
$logo_post = $acf_fields['logo_post'] ?? '';
$external_link = $acf_fields['external_link'] ?? '';

$link = $external_link ?: get_permalink();
$blank_class = $external_link ? 'd-block _blank' : 'd-block';

?>
<article class="item cell">
    <?php if (!$hide_thumb && $post_thumbnail) : ?>
	<a class="<?=$blank_class?>" href="<?= $link; ?>" aria-label="<?php echo esc_attr($post_name); ?>" tabindex="0">
		<div class="cover">
			<span class="after-overlay<?=$auto?> res<?=$scale?> ar-<?= $ratio_class ?>"><?php echo $post_thumbnail; ?></span>
		</div>
        <?php if ($logo_post) : ?>
        <span class="logo-post logo-overlay"><?php echo wp_get_attachment_image($logo_post, 'thumbnail'); ?></span>
		<?php endif;?>
	</a>
    <?php endif; ?>
	<div class="cover-content">
        <?php if (!$hide_time || !$hide_term) : ?>
        <div class="meta">
            <?php if (!$hide_time) echo '<div class="time">' . humanize_time() . '</div>'; ?>
            <?php //if (!$hide_term) echo get_primary_term($post); ?>
        </div>
        <?php endif; ?>
		<p class="h6"><a class="<?=$blank_class?>" href="<?= get_permalink(); ?>" title="<?php echo esc_attr($post_name); ?>"><?= $post_name; ?></a></p>
        <?php if (!$hide_desc) echo loop_excerpt($post); ?>
        <?php if (!$hide_view_detail) : ?>
		<a class="viewmore viewmore-button" href="<?= get_permalink(); ?>" title="<?php echo esc_attr($post_name); ?>" data-glyph-after="">
			<span><?php echo __('Xem tin', 'hd'); ?></span>
		</a>
        <?php endif; ?>
	</div>
</article>
