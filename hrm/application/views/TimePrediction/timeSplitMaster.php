<?php
$datatables = 1;
$form_validator2 = 1;
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
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Time split master</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Project', 'parent_project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('parent_project', $projectDropdown, $selectedProject, array(
                                        'class' => 'form-control select2 ',
                                        'id' => 'parent_project',
                                        'required' => 'required',
                                    ));
                                    ?>
                                </div>
                            </div>
                            <!-- Select task -->
                            <div class="form-group">
                                <?php
                                echo form_label('Task', 'task_code', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10" id="prevTaskDiv">
                                    <?php
                                    echo form_dropdown('task_code', $taskDropdown, $selectedTask, array(
                                        'class' => 'form-control select2',
                                        'id' => 'task_code',
                                        'required' => 'required',
                                    ));
                                    ?>
                                </div>
                            </div>
                            <!-- Actual project -->
                            <div class="form-group">
                                <?php
                                echo form_label('To project', 'to_project', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('to_projects[]', $projectDropdown, $selected_prjects, array(
                                        'class' => 'form-control select2',
                                        'multiple' => true,
                                        'required' => 'required',
                                        'id' => 'to_project'
                                    ));
                                    ?>
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
                                        'class' => 'btn btn-small btn-primary',
                                        'id' => 'submit'
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
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Project</th>
                                        <th>Task</th>
                                        <th>To Project</th>
                                        <th>Created by</th>
                                        <th>Create Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($tabledata as $dataList) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $dataList['parent_project_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $dataList['task_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $projectsList = explode(",", $dataList['to_project']);
                                                foreach ($projectsList as $pro) {
                                                    if ($pro) {
                                                        $projectDetailsById = $this->Projects->getProjectById($pro);
                                                        echo $projectDetailsById->project_code . ', ';
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $dataList['created_by'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y H:i', strtotime($dataList['created_at']));
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        echo form_close();
                        ?>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
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
    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    $("body").on("change", "#parent_project", function (e) {
        e.preventDefault();
        var project_id = $(this).val();
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/TimePredictionCntrl/getTaskByProject",
            type: "get", //send it through get method
            data: {
                project_id: project_id
            },
            success: function (response) {
                $("#prevTaskDiv").html(response);
                $("#task_code").select2({
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $(".form-horizontal").validate();
        $("body").on("submit", function (event) {
            event.preventDefault();
            var base_url = '<?php echo base_url() ?>';
            var parent_project = $('#parent_project').val();
            var task_code = $('#task_code').val();
            var to_project = [];
            $('#to_project :selected').each(function (i, selected) {
                to_project[i] = $(selected).val();
            });
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/savetimeSplittingMaster",
                type: "post", //send it through post method
                data: {
                    parent_project: parent_project,
                    to_project: to_project,
                    task_code: task_code,
                },
                success: function (response) {
                    $(location).attr('href', base_url + "index.php/TimePredictionCntrl/timeSplittingMaster");

                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });
    });
    $("body").on("keyup", ".numeric", function (event) {
        if (this.value != this.value.replace(/[^0-9\.]/g, '')) {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        }
    });
    $('#table_responsive').DataTable();
</script>

