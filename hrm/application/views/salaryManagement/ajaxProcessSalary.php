<?php
echo form_open();
?>

<div class="col-sm-2">
</div>
<div class="col-sm-2">
</div>
<div class="col-sm-2">
    <?php
    $submitButton = array(
        'type' => 'button',
        'content' => 'Excel',
        'id' => 'excel',
        'class' => 'btn btn-small btn-success'
    );
    echo form_button($submitButton)
    ?>
    <a target="_blank" href="<?php echo base_url(); ?>assets/img/sunil.pdf">
    <?php
    $submitButton = array(
        'type' => 'button',
        'content' => 'Salary Slip',
        'id' => 'pdf',
        'class' => 'btn btn-small btn-warning'
    );
    echo form_button($submitButton)
    ?>
    </a>
</div>
<?php
echo form_close();
?>

