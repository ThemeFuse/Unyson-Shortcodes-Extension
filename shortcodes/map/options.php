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
		'show_borders' => true,
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
	)
);