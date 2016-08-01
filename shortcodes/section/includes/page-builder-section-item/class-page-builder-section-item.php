<?php

class Page_Builder_Section_Item extends Page_Builder_Item
{
	public function get_type()
	{
		return 'section';
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

	/**
	 * Called when builder is rendered
	 */
	public function enqueue_static()
	{
		$shortcode_instance = fw_ext('shortcodes')->get_shortcode('section');
		wp_enqueue_style(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$shortcode_instance->locate_URI('/includes/page-builder-section-item/static/css/styles.css'),
			array(),
			fw()->theme->manifest->get_version()
		);
		wp_enqueue_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$shortcode_instance->locate_URI('/includes/page-builder-section-item/static/js/scripts.js'),
			array('fw-events', 'underscore'),
			fw()->theme->manifest->get_version(),
			true
		);
		wp_localize_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			str_replace('-', '_', $this->get_builder_type() . '_item_type_' . $this->get_type() . '_data'),
			$this->get_item_data()
		);
	}

	private function get_item_data()
	{
		$data    = array();

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

	protected function get_thumbnails_data()
	{
		return array($this->get_shortcode_config());
	}

	public function get_value_from_attributes($attributes)
	{
		$attributes['type'] = $this->get_type();

		$options = $this->get_shortcode_options();
		if (!empty($options)) {
			if (empty($attributes['atts'])) {
				/**
				 * The options popup was never opened and there are no attributes.
				 * Extract options default values.
				 */
				$attributes['atts'] = fw_get_options_values_from_input(
					$options, array()
				);
			} else {
				/**
				 * There are saved attributes.
				 * But we need to execute the _get_value_from_input() method for all options,
				 * because some of them may be (need to be) changed (auto-generated) https://github.com/ThemeFuse/Unyson/issues/275
				 * Add the values to $option['value']
				 */
				$options = fw_extract_only_options($options);

				foreach ($attributes['atts'] as $option_id => $option_value) {
					if (isset($options[$option_id])) {
						$options[$option_id]['value'] = $option_value;
					}
				}

				$attributes['atts'] = fw_get_options_values_from_input(
					$options, array()
				);
			}
		}

		return $attributes;
	}

	public function get_shortcode_data($atts = array())
	{
		$return = array(
			'tag'  => $this->get_type()
		);
		if (isset($atts['atts'])) {
			$return['atts'] = $atts['atts'];
		}
		return $return;
	}
}
FW_Option_Type_Builder::register_item_type('Page_Builder_Section_Item');
