<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  array $option
 * @var  array $internal_options
 */

?>

<div class="fw-table-cell-content"><?php echo $cell_value['textarea'] ?></div>

<div class="fw-table-cell-price">

	<?php foreach($internal_options['pricing-options'] as $key => $options): ?>

		<?php $data_options = array(
			'id_prefix'   => $option['attr']['id'] . '-',
			'name_prefix' => $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . '][pricing]',
			'value'       => isset($cell_value['pricing'][$key]) ? $cell_value['pricing'][$key] : '',
		);

		?>

		<?php $div_attr = $options['attr']; ?>
		<?php $div_attr['class'] .= ' fw-cell-option-wrapper ' ?>

		<div <?php echo fw_attr_to_html($div_attr) ?>>
			<?php echo fw()->backend->option_type($options['type'])->render($key, $options, $data_options ); ?>
		</div>

	<?php endforeach; ?>
</div>

<?php
$popup_data = array(
	'id_prefix'   => $option['attr']['id'] . '-',
	'name_prefix' => $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . ']',
	'value'       => isset($cell_value['button']) ? json_encode($cell_value['button']) : ''
);

//set popup-button title
$internal_options['button-options']['button'] = __( 'Edit', 'fw' );
if ( empty( $cell_value['button'] ) ) {
	$internal_options['button-options']['button'] = __( 'Add', 'fw' );
}

?>

<div class="fw-table-cell-button"><?php echo fw()->backend->option_type( 'popup' )->render( 'button', $internal_options['button-options'], $popup_data ) ?></div>

<?php
$switch_data = array(
	'id_prefix'   => $option['attr']['id'] . '-',
	'name_prefix' => $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . ']',
	'value'       => isset($cell_value['switch']) ? $cell_value['switch'] : ''
);

?>

<div class="fw-table-cell-switch"><?php echo fw()->backend->option_type( 'switch' )->render( 'switch-' . $i . '-' . $j, $internal_options['switch-options'], $switch_data ) ?></div>

<div class="fw-table-cell-default">
	<?php echo '<textarea rows="5" id="' . $option['attr']['id'] . '-textarea-' . $i . '-' . $j . '" name="' . $option['attr']['name'] . '[content]' . '[' . $i . '][' . $j . '][textarea]" value="' . $cell_value['textarea'] . '">' . $cell_value['textarea'] . '</textarea>' ?>
</div>