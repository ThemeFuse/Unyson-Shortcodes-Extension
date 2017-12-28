<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class Page_Builder_Column_Item extends Page_Builder_Item {

	public function get_type() {
		return 'column';
	}

	private function get_shortcode_options() {
		$shortcode_instance = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'column' );

		return $shortcode_instance->get_options();
	}

	private function get_shortcode_config() {
		$shortcode_instance = fw_ext( 'shortcodes' )->get_shortcode( 'column' );

		return $shortcode_instance->get_config( 'page_builder' );
	}

	public function enqueue_static() {
		/**
		 * @var FW_Shortcode $column_shortcode
		 */
		$column_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'column' );

		wp_enqueue_style(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$column_shortcode->get_uri( '/includes/page-builder-column-item/static/css/styles.css' ),
			array(),
			fw()->theme->manifest->get_version()
		);

		wp_enqueue_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$column_shortcode->get_uri( '/includes/page-builder-column-item/static/js/scripts.js' ),
			array( 'fw-events', 'underscore' ),
			fw()->theme->manifest->get_version(),
			true
		);

		wp_localize_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			str_replace( '-', '_', $this->get_builder_type() ) . '_item_type_' . $this->get_type() . '_data',
			$column_shortcode->get_item_data()
		);
	}

	protected function get_thumbnails_data() {
		/**
		 * @var FW_Shortcode $column_shortcode
		 */
		$column_shortcode = fw_ext( 'shortcodes' )->get_shortcode( 'column' );
		$builder_widths   = fw_ext_builder_get_item_width( $this->get_builder_type() );

		$column_thumbnails = array();
		foreach ( $builder_widths as $key => $value ) {
			$column_thumbnails[ $key ] = array(
				'tab'         => __( 'Layout Elements', 'fw' ),
				'title'       => apply_filters( 'fw_ext_shortcodes_column_title', $value['title'], $key ),
				'description' => apply_filters( 'fw_ext_shortcodes_column_description',
					sprintf( __( 'Add a %s column', 'fw' ), $value['title'] ), $key ),
				'icon'        => ($icon = $column_shortcode->locate_URI( "/thumbnails/{$key}.png" ))
					? $icon : 'dashicons dashicons-align-none',
				'data'        => array(
					'width' => $key
				)
			);
		}

		return apply_filters( 'fw_shortcode_column_thumbnails_data', $column_thumbnails );
	}

	public function get_value_from_attributes( $attributes ) {
		$attributes['type'] = $this->get_type();
		$original_attributes = $attributes;

		if ( ! isset( $attributes['width'] ) ) {
			$attributes['width'] = fw_ext_builder_get_item_width( $this->get_builder_type() );
			end( $attributes['width'] ); // move to the last width (usually it's the biggest)
			$attributes['width'] = key( $attributes['width'] );
		}

		$options = $this->get_shortcode_options();

		if ( ! empty( $options ) ) {
			if ( empty( $attributes['atts'] ) ) {
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
				$options = fw_extract_only_options( $options );

				foreach ( $attributes['atts'] as $option_id => $option_value ) {
					if ( isset( $options[ $option_id ] ) ) {
						$options[ $option_id ]['value'] = $option_value;
					}
				}

				$attributes['atts'] = fw_get_options_values_from_input(
					$options, array()
				);
			}
		}

		return apply_filters(
			'fw:ext:shortcodes:column:value-from-attributes',
			$attributes,
			$original_attributes
		);
	}

	public function get_shortcode_data( $atts = array() ) {
		$default_width = fw_ext_builder_get_item_width( $this->get_builder_type() );
		end( $default_width ); // move to the last width (usually it's the biggest)
		$default_width = key( $default_width );

		$return_atts = array(
			'width' => $atts['width'] ? $atts['width'] : $default_width
		);
		if ( isset( $atts['atts'] ) ) {
			$return_atts = array_merge( $return_atts, $atts['atts'] );
		}

		return array(
			'tag'  => $this->get_type(),
			'atts' => $return_atts
		);
	}
}

FW_Option_Type_Builder::register_item_type( 'Page_Builder_Column_Item' );
