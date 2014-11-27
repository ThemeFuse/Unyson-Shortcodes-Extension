<?php if (!defined('FW')) die( 'Forbidden' ); ?>
<?php $color_class = !empty($atts['color']) ? "fw-btn-{$atts['color']}" : ''; ?>
<a href="<?php echo $atts['link'] ?>" target="<?php echo $atts['target'] ?>" class="fw-btn fw-btn-1 <?php echo $color_class; ?>">
	<span><?php echo $atts['label']; ?></span>
</a>