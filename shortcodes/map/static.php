<?php if (!defined('FW')) die('Forbidden');

$shortcodes_extension = fw_ext('shortcodes');

{
	$query_params = array(
		'v'         => '3.30',
		'language'  => substr( get_locale(), 0, 2 ),
		'libraries' => 'places',
	);

	/**
	 * Check if Map option type has the `api_key` method, as user may have a older Unyson version.
	 * TODO: Remove in next versions and provide a better solution
	 */
	if (method_exists('FW_Option_Type_Map', 'api_key')) {
		$query_params['key'] = FW_Option_Type_Map::api_key();
	}

	wp_enqueue_script(
		'google-maps-api-v3',
		'https://maps.googleapis.com/maps/api/js?'. http_build_query($query_params),
		array(),
		$query_params['v'],
		true
	);
}

wp_enqueue_style(
	'fw-shortcode-map',
	$shortcodes_extension->get_uri('/shortcodes/map/static/css/styles.css')
);

wp_enqueue_script(
	'fw-shortcode-map-script',
	$shortcodes_extension->get_uri('/shortcodes/map/static/js/scripts.js'),
	array('jquery', 'underscore', 'google-maps-api-v3'),
	fw()->manifest->get_version(),
	true
);
