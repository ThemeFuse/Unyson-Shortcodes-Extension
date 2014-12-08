<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  array $option
 * @var  array $internal_options
 */

?>
<div class="fw-table-cell-content"></div>

<?php

$popup_data = array(
	'id_prefix'   => $option['attr']['id'] . '-',
	'name_prefix' => $option['attr']['name'] . '[content][_template_key_row_][_template_key_col_]',
	'value'       => ''
);

$internal_options['button-option']['button'] = __( 'Add', 'fw' );
?>
<div class="fw-table-cell-button"><?php echo fw()->backend->option_type( 'popup' )->render( 'button', $internal_options['button-option'], $popup_data ) ?></div>

<?php
$switch_data = array(
	'id_prefix'   => $option['attr']['id'] . '-',
	'name_prefix' => $option['attr']['name'] . '[content]' . '[_template_key_row_][_template_key_col_]',
	'value'       => $internal_options['switch-option']['value']
);

?>

<div class="fw-table-cell-switch"><?php echo fw()->backend->option_type( 'switch' )->render( 'switch-_template_key_row_-_template_key_col_', $internal_options['switch-option'], $switch_data ) ?></div>

<?php echo '<textarea rows="5" id="' . $option['attr']['id'] . '-textarea-_template_key_row_-_template_key_col_" name="' . $option['attr']['name'] . '[content][_template_key_row_][_template_key_col_][textarea]" value=""></textarea>' ?>
