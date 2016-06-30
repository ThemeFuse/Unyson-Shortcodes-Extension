<?php if (!defined('FW')) die('Forbidden');

if (!is_admin()) {
	wp_register_style(
		'fw-font-awesome',
		fw_get_framework_directory_uri('/static/libs/font-awesome/css/font-awesome.min.css'),
		array(),
		fw()->manifest->get_version()
	);
}

if (is_admin()) {
	wp_enqueue_script(
		'fw-ext-shortcodes-editor-integration',
		fw_ext('shortcodes')->locate_js_URI('json-coder'),
		array(),
		fw_ext('shortcodes')->manifest->get('version'),
		true
	);
}
