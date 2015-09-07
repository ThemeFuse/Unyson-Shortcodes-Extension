<?php if (!defined('FW')) die('Forbidden');

$class = fw_ext_builder_get_item_width('page-builder', $atts['width'] . '/frontend_class');
?>
<div class="<?php echo esc_attr($class); ?>">
	<?php echo do_shortcode($content); ?>
</div>
