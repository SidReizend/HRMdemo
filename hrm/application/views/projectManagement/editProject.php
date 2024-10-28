<?php
$select2 = 1;
$datepicker = 1;
$form_validator2 = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
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
                            <h3 class="box-title">Project Edit</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Project Code', 'projectCode', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $projectCodeText = array(
                                        'name' => 'projectCode',
                                        'id' => 'projectCode',
                                        'class' => 'form-control',
                                        'value' => $projectDetailsById->project_code,
                                        'required' => 'required',
                                    );

                                    echo form_input($projectCodeText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('projectCode'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Project Name', 'name', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $nameText = array(
                                        'name' => 'name',
                                        'id' => 'name',
                                        'class' => 'form-control',
                                        'value' => $projectDetailsById->name,
                                        'required' => 'required',
                                    );

                                    echo form_input($nameText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('name'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Description', 'description', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10"> <?php
                                    $descriptionText = array(
                                        'name' => 'description',
                                        'id' => 'description',
                                        'class' => 'form-control',
                                        'value' => $projectDetailsById->description,
                                        'required' => 'required',
                                    );
                                    echo form_textarea($descriptionText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('description'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Project Head', 'type', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">  
                                    <?php 
                                    echo form_dropdown('project_head', $heads, $projectDetailsById->head, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('Project_head'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group hidden">
                                <?php
                                echo form_label('Type', 'type', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    if($projectDetailsById->type){
                                        $selectedTypeid = $projectDetailsById->type;
                                    }
                                    echo form_dropdown('type', $types, $selectedTypeid, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('type'); ?> </span>
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
                                    echo form_dropdown('status', $statuses, $projectDetailsById->status, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group" >
                                <?php
                                echo form_label('Allowance', 'allowance', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('allowance', $allowance, $projectDetailsById->allowance, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('allowance'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group" id="billableDiv" 
                            <?php
                            if ($projectDetailsById->sub_project == 1) {
                                echo 'style="display:none;"';
                            }
                            ?>
                                 >
                                     <?php
                                     echo form_label('Billable', 'billable', array(
                                         'class' => 'col-sm-2 control-label',
                                     ));
                                     ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('billable', $billables, $projectDetailsById->billable, array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('billable'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('From Date', 'fromDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'required' => 'required',
                                        'value' => $projectDetailsById->from_date != '0000-00-00' ? date('d/m/Y', strtotime($projectDetailsById->from_date)) : '',
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('To Date', 'toDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => $projectDetailsById->to_date != '0000-00-00' ? date('d/m/Y', strtotime($projectDetailsById->to_date)) : '',
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('toDate'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Estimated Hours', 'estimatedHours', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $estimatedHoursText = array(
                                        'name' => 'estimatedHours',
                                        'id' => 'estimatedHours',
                                        'class' => 'form-control',
                                        'value' => $projectDetailsById->estimated_hours,
                                    );

                                    echo form_input($estimatedHoursText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('estimatedHours'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Url', 'url', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $urlText = array(
                                        'name' => 'url',
                                        'id' => 'url',
                                        'class' => 'form-control',
                                        'value' => $projectDetailsById->url,
                                    );

                                    echo form_input($urlText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('url'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Stable talents', 'stable', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">   <?php
                                    $stable_talents = explode(",", $projectDetailsById->stable_talents);
                                    //var_dump($stable_talents);
                                    echo form_dropdown('stable_talents[]', $talents, $stable_talents, array(
                                        'class' => 'form-control select2',
                                        'multiple' => true,
                                        'id' => 'stable_talents'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('stable_talents'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Sub Projects', 'sub_projects', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">    
                                    <div class="material-switch pull-left">
                                        <input id="someSwitchOption001" name="sub_project" <?php
                                        if ($projectDetailsById->sub_project == 1) {
                                            echo "checked";
                                        }
                                        ?> class="someSwitchOption001"  type="checkbox" value="1"/>
                                        <label for="someSwitchOption001" class="label-success"></label>
                                    </div>

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
<!-- /.container -->
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
        format: 'dd/mm/yyyy'
    });
    $('body').on('click', '#someSwitchOption001', function (e) {
        if($(this).is(':checked')){
            $('#billableDiv').hide();
        } else {
            $('#billableDiv').show();
        }
    });
</script>
