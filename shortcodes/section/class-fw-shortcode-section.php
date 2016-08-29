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
	}

	public function _action_register_builder_item_types() {
		if (fw_ext('page-builder')) {
			require $this->get_declared_path('/includes/page-builder-section-item/class-page-builder-section-item.php');
		}
	}
}
