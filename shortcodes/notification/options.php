<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'message' => array(
		'label' => __( 'Message', 'fw' ),
		'desc'  => __( 'Notification message', 'fw' ),
		'type'  => 'text',
		'value' => __( 'Message!', 'fw' ),
	),
	'type'   => array(
		'label'   => __( 'Type', 'fw' ),
		'desc'    => __( 'Notification type', 'fw' ),
		'type'    => 'select',
		'choices' => array(
			'success' => __( 'Congratulations', 'fw' ),
			'info'    => __( 'Information', 'fw' ),
			'warning' => __( 'Alert', 'fw' ),
			'danger'  => __( 'Error', 'fw' ),
		)
	),
);