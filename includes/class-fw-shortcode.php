<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode {
	private $tag;
	private $path;
	private $uri;
	private $rewrite_paths;
	private $rewrite_uris;
	private $options;
	private $config;

	final public function __construct( $args ) {
		$this->tag           = $args['tag'];
		$this->path          = $args['path'];
		$this->uri           = $args['uri'];
		$this->rewrite_paths = $args['rewrite_paths'];
		$this->rewrite_uris  = $args['rewrite_uris'];

		$this->_init();
	}

	protected function _init() {
	}

	/**
	 * Gets the shortcodes' tag (id)
	 * @return string
	 */
	final public function get_tag() {
		return $this->tag;
	}

	/**
	 * Gets the path at which the shortcode is located
	 *
	 * @param string $rel_path A string to append to the path like '/views/view.php'
	 *
	 * @return string
	 */
	final public function get_declared_path( $rel_path = '' ) {
		return $this->path . $rel_path;
	}

	/**
	 * Gets the uri at which the shortcode is located
	 *
	 * @param string $rel_path A string to append to the uri like '/views/view.php'
	 *
	 * @return string
	 */
	final public function get_declared_URI( $rel_path = '' ) {
		return $this->uri . $rel_path;
	}

	/**
	 * Searches the path first in child_theme (if activated), parent_theme and framework
	 *
	 * This allows to find overridden files like the view and static files
	 *
	 * @param $rel_path string A string to append to the path like '/views/view.php'
	 *
	 * @return string|bool The path if it was found or false otherwise
	 */
	final public function locate_path( $rel_path = '' ) {
		foreach ( array_merge( $this->rewrite_paths, array( $this->path ) ) as $path ) {
			$actual_path = $path . $rel_path;
			if ( file_exists( $actual_path ) ) {
				return $actual_path;
			}
		}

		return false;
	}

	/**
	 * @param string $append
	 *
	 * @return string
	 * @since 1.3.5
	 */
	public function get_uri( $append = '' ) {
		return $this->uri . $append;
	}

	/**
	 * Searches the uri first in child_theme (if activated), parent_theme and framework
	 *
	 * This allows to find uris to overridden files like the view and static files
	 *
	 * @param $rel_path string A string to append to the path like '/views/view.php'
	 *
	 * @return string|bool The path if it was found or false otherwise
	 */
	final public function locate_URI( $rel_path = '' ) {
		$paths = array_merge( $this->rewrite_paths, array( $this->path ) );
		$uris  = array_merge( $this->rewrite_uris, array( $this->uri ) );
		foreach ( $paths as $key => $path ) {
			$actual_path = $path . $rel_path;
			if ( file_exists( $actual_path ) ) {
				return $uris[ $key ] . $rel_path;
			}
		}

		return false;
	}

	public function get_config( $key = null ) {
		if ( ! $this->config ) {
			$config_path = $this->locate_path( '/config.php' );
			if ( $config_path ) {
				$vars         = fw_get_variables_from_file( $config_path, array( 'cfg' => null ) );
				$this->config = $vars['cfg'];
			}
		}

		if ( ! is_array( $this->config ) ) {
			return null;
		} else {
			return $key === null ? $this->config : fw_akg( $key, $this->config );
		}
	}

	public function get_options() {
		if ( ! $this->options ) {
			$options_path = $this->locate_path( '/options.php' );
			if ( $options_path ) {
				$vars          = fw_get_variables_from_file( $options_path, array( 'options' => null ) );
				$this->options = $vars['options'];
			}
		}

		return apply_filters( 'fw_shortcode_get_options', $this->options, $this->tag );
	}

	/**
	 * @param string $key
	 * @param null|mixed|FW_Callback $default
	 *
	 * @return mixed|null
	 */
	public function get_option( $key, $default = null ) {
		return fw_akg( $key, $this->get_options(), $default, '/' );
	}

	/**
	 * Used as an public alias method of enqueue_static
	 */
	public function _enqueue_static() {
		$this->enqueue_static();
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param string $tag deprecated
	 *
	 * @return string
	 */
	final public function render( $atts, $content = null, $tag = '' ) {
		if ( empty( $atts ) ) {
			$atts = array();
		} else {
			if ( version_compare( fw_ext( 'shortcodes' )->manifest->get_version(), '1.3.0', '>=' ) ) {
				/**
				 * @var WP_Post $post
				 */
				global $post;

				$atts = fw_ext_shortcodes_decode_attr( $atts,
					$this->tag,
					/**
					 * This was required, but sometimes the shortcode is rendered manually from code and
					 * very often is used PageBuilder encoder which doesn't require/need post id
					 */
					$post ? $post->ID : 0
				);

				if ( is_wp_error( $atts ) ) {
					return '<p>Shortcode attributes decode error: ' . $atts->get_error_message() . '</p>';
				}
			} else {
				/**
				 * @deprecated Since Shortcodes 1.3.0
				 */
				$atts = apply_filters( 'fw_shortcode_atts', $atts, $content, $this->tag );
			}
		}

		return $this->_render( $atts, $content );
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @param string $tag deprecated
	 *
	 * @return string
	 */
	protected function _render( $atts, $content = null, $tag = '' ) {
		$view_file = $this->locate_path( '/views/view.php' );
		if ( ! $view_file ) {
			trigger_error(
				sprintf( __( 'No default view (views/view.php) found for shortcode: %s', 'fw' ), $this->tag ),
				E_USER_ERROR
			);
		}

		$this->enqueue_static();

		$view_extra = apply_filters( 'fw_shortcode_render_view',
			array(
				'before' => '',
				'after'  => '',
			),
			$atts,
			$this->tag );

		return
			$view_extra['before'] .
			fw_render_view( $view_file,
				array(
					'atts'    => apply_filters( 'fw_shortcode_render_view:atts', $atts, $this->tag ),
					'content' => $content,
					'tag'     => $this->tag
				) ) .
			$view_extra['after'];
	}

	protected function enqueue_static() {
		$static_file = $this->locate_path( '/static.php' );
		if ( $static_file ) {
			fw_include_file_isolated( $static_file );
		}
	}
}
