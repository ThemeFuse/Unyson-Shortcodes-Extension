<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$map_shortcode = fw_ext('shortcodes')->get_shortcode('map');
$options = array(
	'data_provider' => array(
		'type'  => 'multi-picker',
		'label' => false,
		'desc'  => false,
		'picker' => array(
			'population_method' => array(
				'label'   => __('Population Method', 'fw'),
				'desc'    => __( 'Select map population method (Ex: events, custom)', 'fw' ),
				'type'    => 'select',
				'choices' => $map_shortcode->_get_picker_dropdown_choices(),
			)
		),
		'choices' => $map_shortcode->_get_picker_choices(),
		'show_borders' => false,
		'hide_picker' => true
	),
	'gmap-key' => array_merge(
		array(
			'label' => __( 'Google Maps API Key', 'fw' ),
			'desc' => sprintf(
				__( 'Create an application in %sGoogle Console%s and add the Key here.', 'fw' ),
				'<a href="https://console.developers.google.com/flows/enableapi?apiid=places_backend,maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true">',
				'</a>'
			),
		),
		version_compare(fw()->manifest->get_version(), '2.5.7', '>=')
		? array(
			'type' => 'gmap-key',
		)
		: array(
			'type' => 'text',
			'fw-storage' => array(
				'type'      => 'wp-option',
				'wp_option' => 'fw-option-types:gmap-key',
			),
		)
	),
	'map_type' => array(
		'type'  => 'select',
		'label' => __('Map Type', 'fw'),
		'desc'  => __('Select map type', 'fw'),
		'choices' => array(
			'roadmap'   => __('Roadmap', 'fw'),
			'terrain' => __('Terrain', 'fw'),
			'satellite' => __('Satellite', 'fw'),
			'hybrid'    => __('Hybrid', 'fw')
		)
	),
	'map_height' => array(
		'label' => __('Map Height', 'fw'),
		'desc'  => __('Set map height (Ex: 300)', 'fw'),
		'type'  => 'text'
	),
	'disable_scrolling' => array(
		'type'  => 'switch',
		'value' => false,
		'label' => __('Disable zoom on scroll', 'fw'),
		'desc'  => __('Prevent the map from zooming when scrolling until clicking on the map', 'fw'),
		'left-choice' => array(
			'value' => false,
			'label' => __('Yes', 'fw'),
		),
		'right-choice' => array(
			'value' => true,
			'label' => __('No', 'fw'),
		),
	),
);