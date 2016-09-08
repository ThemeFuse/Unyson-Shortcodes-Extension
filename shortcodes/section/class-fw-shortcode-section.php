<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Section extends FW_Shortcode
{
	/**
	 * @internal
	 */
	public function _init()
	{
		add_action(
			'fw_option_type_builder:page-builder:register_items',
			array($this, '_action_register_builder_item_types')
		);

		add_filter( 'fw_ext:shortcodes:collect_shortcodes_data', array(
			$this, 'add_section_data_to_filter'
		) );
	}

	public function add_section_data_to_filter( $structure )
	{
		$data['section'] = $this->get_item_data();
		return array_merge( $structure, $data );
	}

	private function get_shortcode_options()
	{
		$shortcode_instance = fw_ext('shortcodes')->get_shortcode('section');
		return $shortcode_instance->get_options();
	}

	private function get_shortcode_config()
	{
		$shortcode_instance = fw_ext('shortcodes')->get_shortcode('section');
		$config = $shortcode_instance->get_config('page_builder');
		return array_merge(
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('Section', 'fw'),
				'description' => __('Creates a section', 'fw'),
				'title_template' => null,
			),
			is_array($config) ? $config : array()
		);
	}

	private function get_item_data()
	{
		$data = array();

		$options = $this->get_shortcode_options();
		if ($options) {
			fw()->backend->enqueue_options_static($options);
			$data['options'] = $this->transform_options($options);
		}

		$config = $this->get_shortcode_config();

		if (isset($config['popup_size'])) {
			$data['popup_size'] = $config['popup_size'];
		}

		if (isset($config['popup_header_elements'])) {
			$data['header_elements'] = $config['popup_header_elements'];
		}

		$data['title'] = $config['title'];
		$data['title_template'] = $config['title_template'];

		$data['l10n'] = array(
			'edit'      => __( 'Edit', 'fw' ),
			'duplicate' => __( 'Duplicate', 'fw' ),
			'remove'    => __( 'Remove', 'fw' ),
			'collapse'	=> __( 'Collapse', 'fw' ),
		);

		$data['tag'] = 'section';
		if ($options) {
			$data['default_values'] = fw_get_options_values_from_input(
				$options, array()
			);
		}

		return $data;
	}

	/*
	 * Puts each option into a separate array
	 * to keep it's order inside the modal dialog
	 */
	private function transform_options($options)
	{
		$transformed_options = array();
		foreach ($options as $id => $option) {
			if (is_int($id)) {
				/**
				 * this happens when in options array are loaded external options using fw()->theme->get_options()
				 * and the array looks like this
				 * array(
				 *    'hello' => array('type' => 'text'), // this has string key
				 *    array('hi' => array('type' => 'text')) // this has int key
				 * )
				 */
				$transformed_options[] = $option;
			} else {
				$transformed_options[] = array($id => $option);
			}
		}
		return $transformed_options;
	}

	public function _action_register_builder_item_types() {
		if (fw_ext('page-builder')) {
			require $this->get_declared_path('/includes/page-builder-section-item/class-page-builder-section-item.php');
		}
	}
}
