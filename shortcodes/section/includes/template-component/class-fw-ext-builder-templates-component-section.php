<?php if (!defined('FW')) die('Forbidden');

class FW_Ext_Builder_Templates_Component_Section extends FW_Ext_Builder_Templates_Component
{
	public function get_type()
	{
		return 'section';
	}

	public function get_title()
	{
		return __('Sections', 'fw');
	}

	public function _render($data)
	{
		if ($data['builder_type'] !== 'page-builder') {
			return;
		}

		if (version_compare(fw_ext('builder')->manifest->get_version(), '1.1.14', '<')) {
			// some important changes were added in Builder v1.1.14
			return;
		}

		$templates = $this->get_templates($data['builder_type']);

		{
			$this->fake_created_value = 0;

			$templates = array_map( // make this to keep elements order after applying uasort()
				array($this, 'array_map_add_fake_created_key'),
				$templates
			);
		}

		uasort($templates, array($this, 'sort_templates'));

		$html = '';

		foreach ($templates as $template_id => $template) {
			if (isset($template['type']) && $template['type'] === 'predefined') {
				$delete_btn = '';
			} else {
				$delete_btn = '<a href="#" onclick="return false;" data-delete-template="'. fw_htmlspecialchars($template_id) .'"'
				              . ' class="template-delete dashicons fw-x"></a>';
			}

			$html .=
				'<li>'
					. $delete_btn
					. '<a href="#" onclick="return false;" data-load-template="'. fw_htmlspecialchars($template_id) .'"'
						. ' class="template-title">'
						. fw_htmlspecialchars($template['title'])
					. '</a>'
				. '</li>';
		}

		if (empty($html)) {
			$html = '<div class="fw-text-muted">'. __('No Templates Saved', 'fw') .'</div>';
		} else {
			$html =
				'<p class="fw-text-muted load-template-title">'. __('Load Template', 'fw') .':</p>'
				. '<ul class="std">'. $html .'</ul>';
		}

		return $html;
	}

	public function _enqueue($data)
	{
		if ($data['builder_type'] !== 'page-builder') {
			return;
		}

		if (version_compare(fw_ext('builder')->manifest->get_version(), '1.1.14', '<')) {
			// some important changes were added in Builder v1.1.14
			return;
		}

		$uri = fw_ext('shortcodes')->get_uri('/shortcodes/'. $this->get_type() .'/includes/template-component');
		$version = fw_ext('shortcodes')->manifest->get_version();

		wp_enqueue_style(
			'fw-option-builder-templates-'. $this->get_type(),
			$uri .'/styles.css',
			array('fw-option-builder-templates'),
			$version
		);

		wp_enqueue_script(
			'fw-option-builder-templates-'. $this->get_type(),
			$uri .'/scripts.js',
			array('fw-option-builder-templates'),
			$version,
			true
		);

		wp_localize_script(
			'fw-option-builder-templates-'. $this->get_type(),
			'_fw_option_type_builder_templates_'. $this->get_type(),
			array(
				'l10n' => array(
					'template_name' => __('Template Name', 'fw'),
					'save_template' => __('Save Section', 'fw'),
					'save_template_tooltip' => __('Save as Template', 'fw'),
				),
			)
		);
	}

	public function _init()
	{
		add_action('wp_ajax_fw_builder_templates_'. $this->get_type() .'_load',   array($this, '_action_ajax_load_template'));
		add_action('wp_ajax_fw_builder_templates_'. $this->get_type() .'_save',   array($this, '_action_ajax_save_template'));
		add_action('wp_ajax_fw_builder_templates_'. $this->get_type() .'_delete', array($this, '_action_ajax_delete_template'));
	}

	private function get_templates($builder_type)
	{
		return $this->get_db_templates($builder_type) + $this->get_predefined_templates($builder_type);
	}

	/**
	 * @internal
	 */
	public function _action_ajax_load_template()
	{
		if (!current_user_can('edit_posts')) {
			wp_send_json_error();
		}

		$builder_type = (string)FW_Request::POST('builder_type');

		if (!$this->builder_type_is_valid($builder_type)) {
			wp_send_json_error();
		}

		$templates = $this->get_templates($builder_type);

		$template_id = (string)FW_Request::POST('template_id');

		if (!isset($templates[$template_id])) {
			wp_send_json_error();
		}

		wp_send_json_success(array(
			'json' => $templates[$template_id]['json']
		));
	}

	/**
	 * @internal
	 */
	public function _action_ajax_save_template()
	{
		if (!current_user_can('edit_posts')) {
			wp_send_json_error();
		}

		$builder_type = (string)FW_Request::POST('builder_type');

		if (!$this->builder_type_is_valid($builder_type)) {
			wp_send_json_error();
		}

		$template = array(
			'title' => trim((string)FW_Request::POST('template_name')),
			'json' => trim((string)FW_Request::POST($this->get_type() .'_json')),
			'created' => time(),
		);

		if (
			empty($template['json'])
			||
			($decoded_json = json_decode($template['json'], true)) === null
			||
			!isset($decoded_json['type'])
			||
			$decoded_json['type'] !== $this->get_type()
		) {
			wp_send_json_error();
		}

		unset($decoded_json);

		if (empty($template['title'])) {
			$template['title'] = __('No Title', 'fw');
		}

		$template_id = md5($template['json']);

		update_option(
			$this->get_wp_option_prefix($builder_type) . $template_id,
			$template,
			false
		);

		/**
		 * Remove from old storage (to prevent array key merge with old value on get)
		 */
		{
			$old_templates = fw_get_db_extension_data('builder', 'templates:'. $this->get_type() .'/'. $builder_type, array());

			unset($old_templates[$template_id]);

			fw_set_db_extension_data('builder', 'templates:'. $this->get_type() .'/'. $builder_type, $old_templates);

			unset($old_templates);
		}

		wp_send_json_success();
	}

	/**
	 * @internal
	 */
	public function _action_ajax_delete_template()
	{
		if (!current_user_can('edit_posts')) {
			wp_send_json_error();
		}

		$builder_type = (string)FW_Request::POST('builder_type');

		if (!$this->builder_type_is_valid($builder_type)) {
			wp_send_json_error();
		}

		$template_id = (string)FW_Request::POST('template_id');

		delete_option($this->get_wp_option_prefix($builder_type) . $template_id);

		/**
		 * Remove from old storage (to prevent array key merge with old value on get)
		 */
		{
			$old_templates = fw_get_db_extension_data('builder', 'templates:'. $this->get_type() .'/'. $builder_type, array());

			unset($old_templates[$template_id]);

			fw_set_db_extension_data('builder', 'templates:'. $this->get_type() .'/'. $builder_type, $old_templates);

			unset($old_templates);
		}

		wp_send_json_success();
	}

	/**
	 * @param $builder_type
	 * @return mixed|null
	 *
	 * Note: Templates can be very big and saving them in a single wp option can throw mysql error on update query
	 */
	protected function get_db_templates($builder_type)
	{
		$templates = array();

		/**
		 * Note: 'prefix + name' max length should be 64
		 */
		$option_prefix = $this->get_wp_option_prefix($builder_type); // + md5 (length=32)

		/**
		 * @var WPDB $wpdb
		 */
		global $wpdb;

		foreach ((array)$wpdb->get_results($wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( $option_prefix ) .'%'
		), ARRAY_A) as $row) {
			$templates[
				// extract (suffix) md5 used as id
			preg_replace('/^'. preg_quote($option_prefix, '/') .'/', '', $row['option_name'])
			] = get_option($row['option_name']);
		}

		$templates +=
			/**
			 * Append old templates
			 * This can't be removed because a lot of installations already use this
			 */
			fw_get_db_extension_data('builder', 'templates:'. $this->get_type() .'/'. $builder_type, array());

		return $templates;
	}

	private $fake_created_value;

	private function array_map_add_fake_created_key($el)
	{
		if (!isset($el['created'])) {
			/**
			 * Before 1.1.14 templates were appended
			 * After 1.1.14 templates are prepended
			 * So reverse old templates to be in the same order as the new ones
			 */
			$el['created'] = (++$this->fake_created_value);
		}

		return $el;
	}

	private function sort_templates($a, $b)
	{
		$at = isset($a['created']) ? $a['created'] : 0;
		$bt = isset($b['created']) ? $b['created'] : 0;

		if ($at == $bt) {
			return 0;
		}

		return ($at > $bt) ? -1 : 1;
	}

	private function get_wp_option_prefix($builder_type)
	{
		return 'fw:bt:s:'. $builder_type .':';
	}
}
