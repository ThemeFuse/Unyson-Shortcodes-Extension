<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$calendar_shortcode = fw_ext('shortcodes')->get_shortcode('calendar');
$options = array(
	'data_provider' => array(
		'type'  => 'multi-picker',
		'label' => false,
		'desc'  => false,
		'picker' => array(
			'population_method' => array(
				'label'   => __('Population Method', 'fw'),
				'desc'    => __( 'Select calendar population method (Ex: events, custom)', 'fw' ),
				'type'    => 'short-select',
				'value'   => 'custom',
				'choices' => $calendar_shortcode->_get_picker_dropdown_choices(),
			)
		),
		'choices' => $calendar_shortcode->_get_picker_choices(),
		'show_borders' => false,
	),
	'template' => array(
		'label'   => __('Calendar Type', 'fw' ),
		'desc'    => __('Select calendar type', 'fw'),
		'type'    => 'short-select',
		'value'   => 'day',
		'choices' => array(
			'day'   => __('Daily', 'fw'),
			'week'  => __('Weekly', 'fw'),
			'month' => __('Monthly', 'fw')
		),
	),
	'first_week_day' => array(
		'label' => __('Start Week On', 'fw'),
		'desc'    => __( 'Select first day of week', 'fw' ),
		'type'    => 'short-select',
		'choices' => array(
			'1' => __('Monday', 'fw'),
			'2' => __('Sunday', 'fw')
		),
		'value' => 1
	),
);
