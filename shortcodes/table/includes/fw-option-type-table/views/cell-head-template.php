<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
?>

<?php $data_cols = array(
	'value'       => isset($data['value']['cols'][ $j ][ 'class' ]) ? $data['value']['cols'][ $j ][ 'class' ] : '',
	'id_prefix'   => $option['attr']['id'] . '-cols-' . $j . '-',
	'name_prefix' => $option['attr']['name'] . '[cols][' . $j . ']'
);
?>

<?php echo fw()->backend->option_type( 'select' )->render( 'class', $option['columns_options'], $data_cols ); ?>



<a href="#" class="fw-table-add-column button button-large"><?php echo __( 'Add Column', 'fw' ) ?></a>
