<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-testimonials',
	$shortcodes_extension->get_declared_URI('/shortcodes/testimonials/static/css/styles.css'),
	array('font-awesome')
);
wp_enqueue_script(
	'fw-shortcode-testimonials-caroufredsel',
	$shortcodes_extension->get_declared_URI('/shortcodes/testimonials/static/js/jquery.carouFredSel-6.2.1-packed.js'),
	array('jquery'),
	false,
	true
);
