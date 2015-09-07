<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
} ?>
<div class="fw-call-to-action">
	<div class="fw-action-content">
		<?php if (!empty($atts['title'])): ?>
		<h2><?php echo $atts['title']; ?></h2>
		<?php endif; ?>
		<p><?php echo $atts['message']; ?></p>
	</div>
	<div class="fw-action-btn">
		<a href="<?php echo esc_attr($atts['button_link']); ?>" class="fw-btn fw-btn-1" target="<?php echo esc_attr($atts['button_target']); ?>">
			<span><?php echo $atts['button_label']; ?></span>
		</a>
	</div>
</div>