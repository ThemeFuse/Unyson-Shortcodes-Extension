<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Row extends FW_Shortcode
{
	/**
	 * @internal
	 */
	public function _init()
	{
		if (is_admin()) {
			$this->load_item_type();
		}
	}

	private function load_item_type()
	{
		require $this->get_declared_path('/includes/page-builder-row-item/class-page-builder-row-item.php');
	}
}
