<div class="col-md-12 col-sm-12" id="change_needed">
    <div class="col-md-12">
        <?php
        echo form_open('leaveApproval/manageAbsentAttendance'.$redirect, array(
            'class' => 'form-horizontal',
            'id' => 'eventForm'
        ));
        ?>
        <?php
        echo form_label('Actual Hour:', 'actualHour', array(
            'class' => 'col-sm-2 control-label'
        ));
        ?>
        <div class="col-sm-2">
            <?php
            echo form_label($actualHoursSpend, 'actualHour', array(
                'class' => 'col-sm-2 control-label'
            ));
            ?>
        </div>
        <?php
        echo form_label('Od Hour:', 'manageAttendance', array(
            'class' => 'col-sm-2 control-label'
        ));
        ?>
        <div class="col-sm-6">
        <?php
        echo form_label($odHours, 'manageAttendance', array(
            'class' => 'col-sm-2 control-label'
        ));
        ?>
        <br/>
        <br/>
        <br/>
        </div>
        <?php
        echo form_label('Manage Attendance', 'manageAttendance', array(
            'class' => 'col-sm-2 control-label'
        ));
        ?>
        <div class="col-sm-6">
            <?php
            echo form_dropdown('manageAbsent', $options, '1', array(
                'class' => 'form-control',
                'data-validation' => 'required',
                'id' => 'manageAbsent',
            ));
            ?>
        </div>
        <div class="col-xs-4">
            <?php
            $submitButton = array(
                'type' => 'submit',
                'content' => 'Submit',
                'class' => 'btn btn-small btn-primary'
            );
            echo form_button($submitButton)
            ?>
        </div>
        <input type="hidden" name="talent" value="<?php echo $talent; ?>">
        <input type="hidden" name="date" value="<?php echo $dateFormated; ?>">
        <input type="hidden" name="attendance" value="<?php echo $attendance; ?>">
        <?php
        echo form_close();
        ?>
    </div>
</div>