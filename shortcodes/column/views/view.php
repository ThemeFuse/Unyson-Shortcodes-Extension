<?php if (!defined('FW')) die('Forbidden'); ?>

<?php

	$id_to_class = array(
		'1_6' => 'fw-col-sm-2',
		'1_4' => 'fw-col-sm-3',
		'1_3' => 'fw-col-sm-4',
		'1_2' => 'fw-col-sm-6',
		'2_3' => 'fw-col-sm-8',
		'3_4' => 'fw-col-sm-9',
		'1_1' => 'fw-col-sm-12'
	);

?>

<div class="<?php echo $id_to_class[$atts['width']]; ?>">
	<?php echo do_shortcode($content); ?>
</div>
