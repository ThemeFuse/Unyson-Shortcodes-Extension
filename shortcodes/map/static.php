<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-map',
	$shortcodes_extension->get_declared_URI('/shortcodes/map/static/css/styles.css')
);

$language = substr( get_locale(), 0, 2 );
wp_enqueue_script(
	'google-maps-api-v3',
	'https://maps.googleapis.com/maps/api/js?v=3.15&sensor=false&libraries=places&language=' . $language,
	array(),
	'3.15',
	true
);
wp_enqueue_script(
	'fw-shortcode-map-script',
	$shortcodes_extension->get_declared_URI('/shortcodes/map/static/js/scripts.js'),
	array('jquery', 'underscore', 'google-maps-api-v3'),
	fw()->manifest->get_version(),
	true
);