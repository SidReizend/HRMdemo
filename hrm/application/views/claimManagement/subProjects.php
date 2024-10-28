<?php
echo form_open();
echo form_label('Sub Projects', 'subProject', array(
    'class' => 'col-sm-2 control-label',
));
?>
<div class="col-sm-10">

    <?php
    echo form_dropdown('subProject', $subProjectDropdown, '', array(
        'class' => 'form-control select2 selectOptions',
        'id' => 'subProjects',
    ));
    echo form_close();
    ?>

