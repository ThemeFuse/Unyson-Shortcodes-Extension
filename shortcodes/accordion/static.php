<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_script(
	'fw-shortcode-accordion',
	$shortcodes_extension->get_declared_URI('/shortcodes/accordion/static/js/scripts.js'),
	array('jquery-ui-accordion'),
	false,
	true
);
wp_enqueue_style(
	'fw-shortcode-accordion',
	$shortcodes_extension->get_declared_URI('/shortcodes/accordion/static/css/styles.css')
);

