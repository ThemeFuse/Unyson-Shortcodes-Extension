<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-team-member',
	$shortcodes_extension->get_declared_URI('/shortcodes/team-member/static/css/styles.css')
);
