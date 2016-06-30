<?php if (!defined('FW')) die('Forbidden');

if (!is_admin()) {
	wp_register_style(
		'fw-font-awesome',
		fw_get_framework_directory_uri('/static/libs/font-awesome/css/font-awesome.min.css'),
		array(),
		fw()->manifest->get_version()
	);
}

wp_register_script(
	'fw-ext-shortcodes-editor-integration',
	fw_ext('shortcodes')->get_uri('/static/js/json-coder.js'),
	array(),
	fw_ext('shortcodes')->manifest->get('version'),
	true
);

