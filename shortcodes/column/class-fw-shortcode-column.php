<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Column extends FW_Shortcode
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
		require $this->get_declared_path('/includes/page-builder-column-item/class-page-builder-column-item.php');
	}
}
