<?php
$datatables = 1;
$select2 = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */
        td:nth-of-type(1):before {
            content: "Sl No.";
        }
        td:nth-of-type(2):before {
            content: "Project Id";
        }
        td:nth-of-type(3):before {
            content: "Project Code";
        }
        td:nth-of-type(4):before {
            content: "Task Id";
        }
        td:nth-of-type(5):before {
            content: "Task Code";
        }
        td:nth-of-type(6):before {
            content: "Actions";
        }
    }
</style>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('Project/mapNewType', array(
                'class' => 'form-horizontal',
                'id' => 'eventForm'
            ));
            ?>

            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Project Type Mapping</h3>
                            <?php
                            echo anchor('project/listTypes', 'Projects Type', array(
                                'class' => 'btn btn-small btn-primary pull-right'
                            ));
                            ?>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <?php
                                echo form_label('Project Code', 'projectCode', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10"> 
                                    <?php
                                    echo form_dropdown('projectCode', $projects, '', array(
                                        'class' => 'form-control select2',
                                        'id' => 'projectCode'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('projectCode'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Type', 'type', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">   <?php
                                    echo form_dropdown('assigned_types', $types, '', array(
                                        'class' => 'form-control select2',
                                        'id' => 'assigned_types'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('assigned_types'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'button',
                                        'class' => 'btn btn-small btn-primary',
                                        'content' => 'Assign Type',
                                        'id' => 'butsave'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                                <!-- /.col -->
                            </div>
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Project</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
<script type="text/javascript">

// Ajax post
    $(document).ready(function () {
        $(".select2").select2();
        var base_url = '<?php echo base_url(); ?>';
        $('#datatable').DataTable({
            "autoWidth": true,
            "processing": true,
            "serverSide": true,
            "ajax": base_url + "index.php/Project/typeTable"

        });
    });
    $("#butsave").click(function ()
    {
        var projectcode = $('#projectCode').val();
        var assigned_types = $('#assigned_types').val();

        if (projectcode != "" && assigned_types != "")
        {
            jQuery.ajax({
                type: "POST",
                url: "<?php echo base_url('/index.php/Project/mapNewType'); ?>",
                dataType: 'html',
                data: {projectCode: projectcode, assigned_types: assigned_types},
                success: function (res)
                {
                    if (res == 1)
                    {
                        alertify.success('Task maped to the project successfully');

                    } else if (res == 2) {
                        alertify.alert('Mapping already exist');
                    } else
                    {
                        alertify.alert('Data not saved');
                    }
                    $('#datatable').DataTable().draw();
                    $('#projectCode').val('').trigger('change');
                    $('#assigned_types').val('').trigger('change');
                },
                error: function ()
                {
                    alertify.error('data not saved');
                }
            });
        } else
        {
            alertify.alert("Please fill all fields");
        }

    });
</script>