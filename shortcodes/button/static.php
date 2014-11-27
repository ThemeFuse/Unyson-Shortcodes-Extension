<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-button',
	$shortcodes_extension->get_declared_URI('/shortcodes/button/static/css/styles.css')
);

