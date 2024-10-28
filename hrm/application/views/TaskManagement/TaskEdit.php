<?php
$form_validator2 = 1;
$alertify = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('', array(
                'class' => 'form-horizontal',
                'id' => 'eventForm'
            ));
            ?>
            <?php
            foreach ($TaskDetails as $task) {
                $taskcode = $task['task_code'];
                $taskdesc = $task['task_desc'];
                $billable = $task['billable'];
                $isTravel = $task['is_travel'];
                $parentTask = $task['parent_task'];
                $parentProject = $task['parent_project'];
                $status = $task['status'];
                $invoice_status = $task['invoice_status'];
                $advance_invoice_number = $task['advance_invoice_number'];
                $invoice_number = $task['invoice_number'];
                $invoice_date = date("d/m/Y", strtotime($task['invoice_date']));
                if ($task['invoice_date'] == null) {
                    $invoice_date = '';
                }
                $advance_invoice_date = date("d/m/Y", strtotime($task['advance_invoice_date']));
                if ($task['advance_invoice_date'] == null) {
                    $advance_invoice_date = '';
                }
                $deploy_date = date("d/m/Y", strtotime($task['deploy_date']));
                if ($task['deploy_date'] == null) {
                    $deploy_date = '';
                }
                $is_recurring = $task['is_recurring'];
                $is_crossbillable = $task['is_crossbillable'];
                if ($parentProject) {
                    $project_details = $this->projects->getProjectById($parentProject);
                    $assigned_tasks = explode(",", $project_details->assigned_tasks);
                    $tasks = $this->TimePredictionModel->getProjectTask($assigned_tasks);
                    $taskDropdown = array("" => "[SELECT Task]");
                    foreach ($tasks as $task) {
                        $taskDropdown[$task['id']] = $task['task_code'] . "-" . $task['task_desc'];
                    }
                }
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Task Creation</h3>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <?php
                                echo form_label('Task Code', 'TaskCode', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'taskcode',
                                        'id' => 'taskcode',
                                        'placeholder' => 'dev',
                                        'class' => 'form-control',
                                        'required' => 'required',
                                        'value' => $taskcode,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('taskcode'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Task Description', 'desc', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'taskdesc',
                                        'id' => 'taskdesc',
                                        'class' => 'form-control',
                                        'required' => 'required',
                                        'value' => $taskdesc,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('taskdesc'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Billable', 'billable', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('billable', $billables, $billable, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('billable'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Is Travel Task', 'isTravel', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('is_travel', $isTravelArray, $isTravel, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('is_travel'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Parent project', 'parent_project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('parent_project', $projectDropdown, $parentProject, array(
                                        'class' => 'form-control select2 ',
                                        'id' => 'parent_project',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('parent_project'); ?> </span>
                                </div>
                            </div>
                            <!-- Select task -->
                            <div class="form-group">
                                <?php
                                echo form_label('Parent Task', 'parent_task', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10" id="parentTaskDiv">
                                    <?php
                                    echo form_dropdown('parent_task', $taskDropdown, $parentTask, array(
                                        'class' => 'form-control select2',
                                        'id' => 'parent_task',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('task_code'); ?> </span>
                                </div>
                            </div>
                            <!-- choose status -->
                            <div class="form-group">
                                <?php
                                echo form_label('Status', 'status', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('status', $statusArray, $status, array(
                                        'class' => 'form-control select2',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('status'); ?> </span>
                                </div>
                            </div>
                            <!-- choose invoice status -->
                            <div class="form-group">
                                <?php
                                echo form_label('Invoice Status', 'InvoiceStatus', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('invoice_status', $invoicestatus, $invoice_status, array(
                                        'class' => 'form-control select2',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('invoice_status'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Advance Invoice Number', 'AdvanceInvoiceNumber', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'advanceinvoicenumber',
                                        'id' => 'advanceinvoicenumber',
                                        'placeholder' => 'Advance Invoice Number',
                                        'class' => 'form-control',
                                        'value' => $advance_invoice_number,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('advanceinvoicenumber'); ?> </span>
                                </div>
                            </div>
                            <!-- Invoice datepicker -->
                            <div class="form-group">
                                <?php
                                echo form_label('Advance Invoice Date', 'advanceinvoicedate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'advanceinvoicedate',
                                        'id' => 'advanceinvoicedate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $advance_invoice_date,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('advanceinvoicedate'); ?> </span>
                                </div>
                            </div>
                            <!-- Invoice No -->
                            <div class="form-group">
                                <?php
                                echo form_label('Invoice Number', 'InvoiceNumber', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'invoicenumber',
                                        'id' => 'invoicenumber',
                                        'class' => 'form-control',
                                        'value' => $invoice_number,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('invoicenumber'); ?> </span>
                                </div>
                            </div>
                            <!-- Invoice datepicker -->
                            <div class="form-group">
                                <?php
                                echo form_label('Invoice Date', 'invoicedate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'invoicedate',
                                        'id' => 'invoicedate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $invoice_date,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('invoicedate'); ?> </span>
                                </div>
                            </div>
                            <!-- is recurring billable-->
                            <div class="form-group">
                                <?php
                                echo form_label('Is Recurring Billable', 'isrecurring', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('isrecurring', $isRecurring, $is_recurring, array(
                                        'class' => 'form-control select2',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('isrecurring'); ?> </span>
                                </div>
                            </div>
                            <!-- is cross billable-->
                            <div class="form-group">
                                <?php
                                echo form_label('Is Cross Billable', 'iscrossbillable', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('iscrossbillable', $isRecurring, $is_crossbillable, array(
                                        'class' => 'form-control select2',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('isrecurring'); ?> </span>
                                </div>
                            </div>
                            <!-- deployment date -->
                            <div class="form-group">
                                <?php
                                echo form_label('Deployed date', 'deploydate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'deployDate',
                                        'id' => 'deployDate',
                                        'class' => 'form-control datepicker',
                                        'value' => $deploy_date,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('deploy_date'); ?> </span>
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
        $("#eventForm").validate();
    });
    $(".select2").select2();
    $("body").on("change", "#parent_project", function (e) {
        e.preventDefault();
        var project_id = $(this).val();
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/TimePredictionCntrl/getTaskByProjectById",
            type: "get", //send it through get method
            data: {
                project_id: project_id,
                task_id: 'parent_task'
            },
            success: function (response) {
                $("#parentTaskDiv").html(response);
                $("#parent_task").select2({
                    placeholder: "Task",
                    dropdownAutoWidth: true,
                    width: '100%'
                });

            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }

        })
    });
//Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
</script>

