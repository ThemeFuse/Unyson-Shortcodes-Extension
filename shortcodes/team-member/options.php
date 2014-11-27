<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'image' => array(
		'label' => __( 'Team Member Image', 'fw' ),
		'desc'  => __( 'Either upload a new, or choose an existing image from your media library', 'fw' ),
		'type'  => 'upload'
	),
	'name'  => array(
		'label' => __( 'Team Member Name', 'fw' ),
		'desc'  => __( 'Name of the person', 'fw' ),
		'type'  => 'text',
		'value' => ''
	),
	'job'   => array(
		'label' => __( 'Team Member Job Title', 'fw' ),
		'desc'  => __( 'Job title of the person.', 'fw' ),
		'type'  => 'text',
		'value' => ''
	),
	'desc'  => array(
		'label' => __( 'Team Member Description', 'fw' ),
		'desc'  => __( 'Enter a few words that describe the person', 'fw' ),
		'type'  => 'textarea',
		'value' => ''
	)
);