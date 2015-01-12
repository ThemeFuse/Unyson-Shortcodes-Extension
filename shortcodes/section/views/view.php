<?php if (!defined('FW')) die('Forbidden');

$bg_color = '';
if (!empty($atts['background_color'])) {
	$bg_color = 'background-color:' . $atts['background_color'] . ';';
}

$bg_image = '';
if (!empty($atts['background_image']['data']['icon'])) {
	$bg_image = 'background-image:url(' . $atts['background_image']['data']['icon'] . ');';
}

$bg_video_data_attr    = '';
$section_extra_classes = '';
if (!empty($atts['video'])) {
	$bg_video_data_attr     = 'data-wallpaper-options=' . json_encode(array('source' => array('video' => $atts['video'])));
	$section_extra_classes .= ' background-video';
}

$section_style   = ($bg_color || $bg_image) ? 'style="' . $bg_color  .  $bg_image . '"' : '';
$container_class = (isset($atts['is_fullwidth']) && $atts['is_fullwidth']) ? 'fw-container-fluid' : 'fw-container';
?>
<section class="fw-main-row <?php echo $section_extra_classes ?>" <?php echo $section_style;  ?> <?php echo $bg_video_data_attr; ?>>
	<div class="<?php echo $container_class; ?>">
		<?php echo do_shortcode($content); ?>
	</div>
</section>