<?php if (!defined('FW')) die('Forbidden');

$class = fw_ext_builder_get_item_width('page-builder', $atts['width'] . '/frontend_class');
?>
<div class="<?php echo $class; ?>">
	<?php echo do_shortcode($content); ?>
</div>
