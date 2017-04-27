<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$id = uniqid( 'testimonials-' );
?>
<script>
	jQuery(document).ready(function ($) {
		$('#<?php echo esc_attr($id) ?>').carouFredSel({
			swipe: {
				onTouch: true
			},
			next : "#<?php echo esc_attr($id) ?>-next",
			prev : "#<?php echo esc_attr($id) ?>-prev",
			pagination : "#<?php echo esc_attr($id) ?>-controls",
			responsive: true,
			infinite: false,
			items: 1,
			auto: false,
			scroll: {
				items : 1,
				fx: "crossfade",
				easing: "linear",
				duration: 300
			}
		});
	});
</script>
<div class="fw-testimonials fw-testimonials-2">
	<?php if (!empty($atts['title'])): ?>
		<h3 class="fw-testimonials-title"><?php echo $atts['title']; ?></h3>
	<?php endif; ?>

	<div class="fw-testimonials-list" id="<?php echo esc_attr($id); ?>">
		<?php foreach ( fw_akg( 'testimonials', $atts, array() ) as $testimonial ): ?>
			<div class="fw-testimonials-item clearfix">
				<div class="fw-testimonials-text">
					<p><?php echo $testimonial['content']; ?></p>
				</div>
				<div class="fw-testimonials-meta">
					<div class="fw-testimonials-avatar">
						<?php
						$author_image_url = !empty($testimonial['author_avatar']['url'])
											? $testimonial['author_avatar']['url']
											: fw_get_framework_directory_uri('/static/img/no-image.png');
						?>
						<img src="<?php echo esc_attr($author_image_url); ?>" alt="<?php echo esc_attr($testimonial['author_name']); ?>"/>
					</div>
					<div class="fw-testimonials-author">
						<span class="author-name"><?php echo $testimonial['author_name']; ?></span>
						<em><?php echo $testimonial['author_job']; ?></em>
						<span class="fw-testimonials-url">
							<a href="<?php echo esc_attr($testimonial['site_url']); ?>"><?php echo $testimonial['site_name']; ?></a>
						</span>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="fw-testimonials-arrows">
		<a class="prev" id="<?php echo esc_attr($id); ?>-prev" href="#"><i class="fa"></i></a>
		<a class="next" id="<?php echo esc_attr($id); ?>-next" href="#"><i class="fa"></i></a>
	</div>

	<div class="fw-testimonials-pagination" id="<?php echo esc_attr($id); ?>-controls"></div>
</div>