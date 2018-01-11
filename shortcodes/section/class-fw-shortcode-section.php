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
			$this, '_filter_add_section_data'
		) );
	}

	/**
	 * @internal
	 */
	public function _filter_add_section_data( $structure )
	{
		$data['section'] = $this->get_item_data();
		return array_merge( $structure, $data );
	}

	public function get_shortcode_config()
	{
		$config = $this->get_config('page_builder');

		$icon = $this->locate_path( "/static/img/page_builder.svg" );

		if (!$icon) {
			$icon = $this->locate_URI( "/static/img/page_builder.png" );
		} else {
			$icon = file_get_contents($icon);
		}

		return array_merge(
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('Section', 'fw'),
				'description' => __('Creates a section', 'fw'),
				'title_template' => null,
				'icon' => $icon
			),
			(is_array($config) ? $config : array())
		);
	}

	/**
	 * Adds data about section to be pushed further to the frontend.
	 *
	 * @since 1.3.21
	 */
	public function get_item_data()
	{
		$data = array();
		$options = $this->get_options();

		if ($options) {
			fw()->backend->enqueue_options_static($options);
			$data['options'] = $this->transform_options($options);

			$data['default_values'] = fw_get_options_values_from_input(
				$options, array()
			);
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
