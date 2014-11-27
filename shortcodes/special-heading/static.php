<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-special-heading',
	$shortcodes_extension->get_declared_URI('/shortcodes/special-heading/static/css/styles.css')
);
