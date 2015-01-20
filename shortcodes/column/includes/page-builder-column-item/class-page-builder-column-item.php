<?php if (!defined('FW')) die('Forbidden');

class Page_Builder_Column_Item extends Page_Builder_Item
{
	private $restricted_types = array('column');

	public function get_type()
	{
		return 'column';
	}

	private function get_shortcode_options()
	{
		$shortcode_instance = fw()->extensions->get('shortcodes')->get_shortcode('column');
		return $shortcode_instance->get_options();
	}

	private function get_shortcode_config()
	{
		$shortcode_instance = fw_ext('shortcodes')->get_shortcode('column');
		return $shortcode_instance->get_config('page_builder');
	}

	public function enqueue_static()
	{
		$column_shortcode = fw()->extensions->get('shortcodes')->get_shortcode('column');
		wp_enqueue_style(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$column_shortcode->locate_URI('/includes/page-builder-column-item/static/css/styles.css'),
			array(),
			fw()->theme->manifest->get_version()
		);
		wp_enqueue_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$column_shortcode->locate_URI('/includes/page-builder-column-item/static/js/scripts.js'),
			array('fw-events', 'underscore'),
			fw()->theme->manifest->get_version(),
			true
		);
		wp_localize_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			str_replace('-', '_', $this->get_builder_type()) . '_item_type_' . $this->get_type() . '_data',
			$this->get_item_data()
		);
	}

	private function get_item_data()
	{
		$data = array(
			'restrictedTypes' => $this->restricted_types,
		);

		$options = $this->get_shortcode_options();
		if ($options) {
			fw()->backend->enqueue_options_static($options);
			$data['options'] = $this->transform_options($options);
		}

		$config = $this->get_shortcode_config();
		if (isset($config['popup_size'])) {
			$data['popup_size'] = $config['popup_size'];
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
			$transformed_options[] = array($id => $option);
		}
		return $transformed_options;
	}

	protected function get_thumbnails_data()
	{
		$column_shortcode  = fw_ext('shortcodes')->get_shortcode('column');
		$builder_widths    = fw_ext_builder_get_item_width($this->get_builder_type());

		$column_thumbnails = array();
		foreach ($builder_widths as $key => $value) {
			$column_thumbnails[$key] = array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => $value['title'],
				'description' => __("Add a {$value['title']} column" ,'fw'),
				'image'       => $column_shortcode->locate_URI("/includes/page-builder-column-item/static/img/{$key}.png"),
				'data'        => array(
					'width'   => $key
				)
			);
		}
		return apply_filters('fw_shortcode_column_thumbnails_data', $column_thumbnails);
	}

	public function get_value_from_attributes($attributes)
	{
		$attributes['type'] = $this->get_type();
		if (!isset($attributes['width'])) {
			// TODO: figure out a smarter way to set the default width
			$attributes['width'] = '1_1';
		}

		/*
		 * when saving the modal, the options values go into the
		 * 'atts' key, if it is not present it could be
		 * because of two things:
		 * 1. The shortcode does not have options
		 * 2. The user did not open or save the modal (which will be more likely the case)
		 */
		if (!isset($attributes['atts'])) {
			$options = $this->get_shortcode_options();
			if (!empty($options)) {
				$attributes['atts'] = fw_get_options_values_from_input($options, array());
			}
		}

		return $attributes;
	}

	public function get_shortcode_data($atts = array())
	{
		$return_atts = array(
			'width' => $atts['width'] ? $atts['width'] : '1_1'
		);
		if (isset($atts['atts'])) {
			$return_atts = array_merge($return_atts, $atts['atts']);
		}
		return array(
			'tag'  => $this->get_type(),
			'atts' => $return_atts
		);
	}
}
FW_Option_Type_Builder::register_item_type('Page_Builder_Column_Item');
