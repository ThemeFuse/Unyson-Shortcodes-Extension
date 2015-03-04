<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var  array $option
 * @var  array $data
 * @var  int | string $i - row id
 * @var  int | string $j - column id
 */

?>

<?php foreach ( $option['columns_options'] as $id => $options ) : ?>
	<?php $data_cols = array(
		'value'       => isset( $data['value']['cols'][ $j ][ $id ] ) ? $data['value']['cols'][ $j ][ $id ] : '',
		'id_prefix'   => $option['attr']['id'] . '-cols-' . $j . '-',
		'name_prefix' => $option['attr']['name'] . '[cols][' . $j . ']'
	);
	?>

	<?php echo fw()->backend->option_type( $options['type'] )->render( $id, $options, $data_cols ); ?>

<?php endforeach; ?>

<a href="#" class="fw-table-add-column button button-large">
	<span class="text"><?php echo __( 'Add Column', 'fw' ) ?></span>
	<span class="plus">+</span>
</a>
