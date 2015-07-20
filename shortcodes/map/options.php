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
	'map_style' => array(
		'label' => __('Map Style', 'fw'),
		'desc'  => __('Change the map appearance by <a href="https://snazzymaps.com/" target="_blank">pasting in a config</a>', 'fw'),
		'help'  => __('Change the map appearance by pasting in a style array. Many styles can be found <a href="https://snazzymaps.com/" target="_blank">here</a>.<br>The config looks a little like this: [{ ... }]', 'fw'),
		'type'  => 'text'
	)
);