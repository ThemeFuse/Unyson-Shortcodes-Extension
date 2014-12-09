<?php if (!defined('FW')) die('Forbidden');

class FW_Option_Type_Textarea_Cell extends FW_Option_Type
{
	public function get_type()
	{
		return 'textarea-cell';
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static($id, $option, $data) {
		$table_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'table' );
		$static_uri = $table_shortcode->get_declared_uri() . '/includes/fw-option-type-textarea-cell/static';

		wp_enqueue_style(
			'fw-option-' . $this->get_type() . '-css',
			$static_uri . '/css/styles.css',
			array(),
			fw()->theme->manifest->get_version()
		);
		wp_enqueue_script(
			'fw-option-' . $this->get_type() . '-js-scripts',
			$static_uri . '/js/scripts.js',
			array( 'jquery' ),
			fw()->theme->manifest->get_version(),
			true
		);

	}

	/**
	 * @internal
	 */
	protected function _render($id, $option, $data)
	{
		$option['value'] = (string)$data['value'];

		unset($option['attr']['value']); // be sure to remove value from attributes

		$option['attr'] = array_merge(array('rows' => '6'), $option['attr']);

		$table_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'table' );
		$view_path = $table_shortcode->get_declared_path() . '/includes/fw-option-type-textarea-cell/views/view.php';
		return fw_render_view( $view_path, compact('id', 'option', 'data') );
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input($option, $input_value)
	{
		return (string)(is_null($input_value) ? $option['value'] : $input_value);
	}

	/**
	 * @internal
	 */
	protected function _get_defaults()
	{
		return array(
			'value' => ''
		);
	}
}
FW_Option_Type::register('FW_Option_Type_Textarea_Cell');