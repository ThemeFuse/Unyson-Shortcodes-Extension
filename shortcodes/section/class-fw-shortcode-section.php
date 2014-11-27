<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Section extends FW_Shortcode
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
		require $this->get_declared_path('/includes/page-builder-section-item/class-page-builder-section-item.php');
	}

	protected function handle_shortcode($atts, $content, $tag)
	{

		wp_enqueue_style(
			'shortcode-video',
			$this->get_uri() . '/static/css/jquery.fs.wallpaper.css',
			array(),
			fw()->theme->manifest->get_version()
		);

		wp_enqueue_style(
			'fullwidth-section',
			$this->get_uri() . '/static/css/styles.css',
			array(),
			fw()->theme->manifest->get_version()
		);

		wp_enqueue_script(
			'shortcode-video',
			$this->get_uri() . '/static/js/jquery.fs.wallpaper.js',
			array('jquery'),
			fw()->theme->manifest->get_version(),
			true
		);

		return fw_render_view($this->get_path() . '/views/view.php', array(
			'atts' => $atts,
			'content' => $content,
			'tag' => $tag
		));
	}
}
