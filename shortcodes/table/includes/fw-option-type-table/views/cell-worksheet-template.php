<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  array $option
 * @var  string $current_row_name - e.g. default-row, pricing-row
 * @var  array $cell_value - values for current cell options
 * @var  string | int $i - row id
 * @var  string | int $j - column id
 * @var  int $desc_col
 */

?>

<?php foreach ( $option['row_options']['name']['choices'] as $row_name => $value ) : ?>

	<?php

	if ( $desc_col == $j ) {
		$current_row_name = 'default-row';
	}
	?>

	<div class="fw-table-cell-content <?php echo esc_attr($row_name) ?> <?php echo $current_row_name === $row_name ? 'fw-active-content' : '' ?>">

		<?php foreach ( $option['content_options'][ $row_name ] as $key => $options ) : ?>
			<?php

			$data_options = array(
				'id_prefix'   => $option['attr']['id'] . '-',
				'name_prefix' => $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . '][' . $row_name . ']',
				'value'       => isset( $cell_value[ $key ] ) ? $cell_value[ $key ] : '',
			);
			?>

			<?php if ( $row_name == 'button-row' ): ?>
				<?php $options['button'] = __( 'Edit', 'fw' );
				if ( empty( $cell_value['button'] ) ) {
					$options['button'] = __( 'Add', 'fw' );
				} ?>
			<?php endif; ?>

			<?php $div_attr = isset( $options['wrapper_attr'] ) ? $options['wrapper_attr'] : array(); ?>
			<?php $div_attr['class'] = isset( $div_attr['class'] ) ? $div_attr['class'] . ' fw-cell-option-wrapper ' : ' fw-cell-option-wrapper ' ?>

			<div <?php echo fw_attr_to_html( $div_attr ) ?>>
				<?php echo fw()->backend->option_type( $options['type'] )->render( $key . '-' . $i . '-' . $j, $options, $data_options ); ?>
			</div>

		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
