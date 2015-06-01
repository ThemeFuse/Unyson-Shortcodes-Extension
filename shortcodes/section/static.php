<?php if (!defined('FW')) die('Forbidden');

wp_enqueue_style('fw-ext-builder-frontend-grid');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-section-background-video',
	$shortcodes_extension->get_uri('/shortcodes/section/static/css/jquery.fs.wallpaper.css')
);
wp_enqueue_style(
	'fw-shortcode-section',
	$shortcodes_extension->get_uri('/shortcodes/section/static/css/styles.css')
);

wp_enqueue_script(
	'fw-shortcode-section-background-video',
	$shortcodes_extension->get_uri('/shortcodes/section/static/js/jquery.fs.wallpaper.min.js'),
	array('jquery'),
	false,
	true
);
wp_enqueue_script(
	'fw-shortcode-section',
	$shortcodes_extension->get_uri('/shortcodes/section/static/js/scripts.js'),
	array('fw-shortcode-section-background-video'),
	false,
	true
);
