<?php if (!defined('FW')) die('Forbidden');

class Page_Builder_Row_Item extends Page_Builder_Item
{
	public function get_type()
	{
		return 'row';
	}

	public function enqueue_static()
	{
	}

	protected function get_thumbnails_data()
	{
		return array();
	}

	public function get_value_from_attributes($attributes)
	{
		$attributes['type'] = $this->get_type();
		return $attributes;
	}

	public function get_shortcode_data($atts = array())
	{
		return array(
			'tag'  => $this->get_type()
		);
	}
}
FW_Option_Type_Builder::register_item_type('Page_Builder_Row_Item');

