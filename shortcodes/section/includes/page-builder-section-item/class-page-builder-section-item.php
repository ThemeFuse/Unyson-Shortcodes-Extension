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
			$shortcode_instance->get_item_data()
		);
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
