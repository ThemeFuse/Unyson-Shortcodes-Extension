<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  string $id
 * @var  array $option
 * @var  array $data
 * @var  array $internal_options
 */
?>

<?php $div_attr = $option['attr'];
unset($div_attr['name'], $div_attr['value'], $div_attr['rows']);
?>

<div <?php echo fw_attr_to_html($div_attr) ?>>
	<div class="fw-textarea-tab content">
		<?php echo htmlspecialchars($option['value'], ENT_COMPAT, 'UTF-8') ?>
	</div>
	<div class="fw-textarea-tab control closed">
		<textarea <?php echo fw_attr_to_html($option['attr']) ?> ><?php echo htmlspecialchars($option['value'], ENT_COMPAT, 'UTF-8') ?></textarea>
	</div>
</div>