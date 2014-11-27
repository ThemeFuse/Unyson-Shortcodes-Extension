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
		$column_shortcode = fw()->extensions->get('shortcodes')->get_shortcode('column');
		$img_uri          = $column_shortcode->locate_URI('/includes/page-builder-column-item/static/img/');
		return array(
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('1/6', 'fw'),
				'description' => __('Creates a 1/6 column' ,'fw'),
				'image'       => $img_uri . '1-6.png',
				'data'        => array(
					'width'   => '1_6'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('1/4', 'fw'),
				'description' => __('Creates a 1/4 column' ,'fw'),
				'image'       => $img_uri . '1-4.png',
				'data'        => array(
					'width'   => '1_4'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('1/3', 'fw'),
				'description' => __('Creates a 1/3 column' ,'fw'),
				'image'       => $img_uri . '1-3.png',
				'data'        => array(
					'width'   => '1_3'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('1/2', 'fw'),
				'description' => __('Creates a 1/2 column' ,'fw'),
				'image'       => $img_uri . '1-2.png',
				'data'        => array(
					'width'   => '1_2'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('2/3', 'fw'),
				'description' => __('Creates a 2/3 column' ,'fw'),
				'image'       => $img_uri . '2-3.png',
				'data'        => array(
					'width'   => '2_3'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('3/4', 'fw'),
				'description' => __('Creates a 3/4 column' ,'fw'),
				'image'       => $img_uri . '3-4.png',
				'data'        => array(
					'width'   => '3_4'
				)
			),
			array(
				'tab'         => __('Layout Elements', 'fw'),
				'title'       => __('1/1', 'fw'),
				'description' => __('Creates a 1/1 column' ,'fw'),
				'image'       => $img_uri . '1-1.png',
				'data'        => array(
					'width'   => '1_1'
				)
			)
		);
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
