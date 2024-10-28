<?php
$form_validator2 = 1;
$datepicker = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Edit</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Talent', 'talent', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Project', 'project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('project', $projectDropdown, $selectedProject, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                        'id' => 'project',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('project'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group" id="subProjectDiv" >
                                <?php
                                if (!empty($subProjects)) {
                                    ?>
                                    <?php
                                    echo form_label('Sub Projects', 'subProject', array(
                                        'class' => 'col-sm-2 control-label',
                                    ));
                                    ?>
                                    <div class="col-sm-10">

                                        <?php
                                        echo form_dropdown('subProject', $subProjectDropdown, $selectedSubProject, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'id' => 'subProjects',
                                        ));
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Amount', 'amount', array(
                                    'class' => 'col-sm-2 control-label',
                                    'required' => 'required',
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $amountText = array(
                                        'name' => 'amount',
                                        'id' => 'amount',
                                        'class' => 'form-control numeric',
                                        'value' => $amount,
                                        'required' => 'required',
                                    );

                                    echo form_input($amountText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('amount'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Date of approval', 'dateOfApproval', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $dateOfApprovalText = array(
                                        'name' => 'dateOfApproval',
                                        'id' => 'dateOfApproval',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $dateOfApproval,
                                        'required' => 'required',
                                    );

                                    echo form_input($dateOfApprovalText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('dateOfApproval'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Approved By', 'approvedBy', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('approvedBy', $talentsDropdown, $selectedApprovedBy, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('approvedBy'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Status', 'status', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('status', $statusDropdown, $selectedStatus, array(
                                        'class' => 'form-control',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('status'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Note', 'note', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $noteText = array(
                                        'name' => 'note',
                                        'id' => 'note',
                                        'class' => 'form-control',
                                        'value' => $note,
                                        'rows' => '2',
                                    );

                                    echo form_textarea($noteText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('note'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                    <a href="javascript:window.history.go(-1);">
                                        <?php
                                        $submitButton = array(
                                            'type' => 'button',
                                            'content' => 'Cancel',
                                            'id' => 'cancel',
                                            'class' => 'btn btn-small btn-primary'
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </a>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                        <?php
                        echo form_close();
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $(".form-horizontal").validate();
    });
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy"
    }).on('change', function () {
        $(this).valid();   // triggers the validation test
    });
    $("body").on("keyup", ".numeric", function (event) {
        if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        }
    });
    $("body").on("change", "#project", function (event) {
        var project = $(this).val();
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if (project != '') {
            $.ajax({
                url: base_url + "index.php/ClaimTrackingForms/subProjectMapping",
                type: "get", //send it through get method
                data: {
                    project: project,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                },
                success: function (response) {
                    $('#subProjectDiv').html(response);
                    $(".select2").select2();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $('[name="' + csrf_name + '"]').val(cct);
            });
        } else {
            $('#subProjectDiv').html('');
        }
    });
</script>

