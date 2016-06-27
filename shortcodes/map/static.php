<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');
wp_enqueue_style(
	'fw-shortcode-map',
	$shortcodes_extension->get_declared_URI('/shortcodes/map/static/css/styles.css')
);

$language = substr( get_locale(), 0, 2 );

//Check if Map option type has the `api_key` method, as user may have a older Unyson version.
//TODO: Remove in next versions and provide a better solution
$key = method_exists('FW_Option_Type_Map', 'api_key') ? '&key=' . FW_Option_Type_Map::api_key() : '';

wp_enqueue_script(
	'google-maps-api-v3',
	'https://maps.googleapis.com/maps/api/js?v=3.23' . $key . '&libraries=places&language=' . $language,
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