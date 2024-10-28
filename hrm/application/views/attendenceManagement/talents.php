<?php
echo form_open('', array(
    'class' => 'form-horizontal',
    'id' => 'showReport',
));
?>
    <?php
    echo form_dropdown('talent', $talents, $selectedTalent, array(
        'class' => 'form-control select2 selectOptions',
        'data-validation' => 'required',
        'id' => 'talent',
    ));
    ?>
    <span class="text-danger"> <?php echo form_error('role'); ?> </span>
<?php
echo form_close();
?>

