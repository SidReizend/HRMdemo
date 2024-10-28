<?php
$form_validator = 1;
//echo validation_errors();
echo form_open('', array(
    'class' => 'form-horizontal'
));
?>
<?php
$reportingManagers_dropdown = $reportingManagers;
echo form_dropdown('reportingManager', $reportingManagers_dropdown, '', array(
    'class' => 'form-control select2'
));
?>
<span class="text-danger"> <?php echo form_error('reportingManager'); ?> </span>


<?php
echo form_close();
?>