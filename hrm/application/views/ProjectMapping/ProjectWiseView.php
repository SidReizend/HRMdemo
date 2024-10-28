<?php
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('ProjectMappingCntrl', array(
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
                            <h3 class="box-title">Project Wise View</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            echo anchor('ProjectMappingCntrl/TalentWiseMapping', 'Talent Wise Mapping', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <?php
                            echo anchor('ProjectMappingCntrl/ProjectWiseMapping', 'Project Wise Mapping', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <?php
                            echo anchor('ProjectMappingCntrl/TalentWiseView', 'Talent Wise View', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>

                            <div class="col-sm-12">
                                <div class="form-group" >
                                    <?php
                                    echo form_label('Project', 'project', array(
                                        'class' => 'col-sm-2 control-label',
                                    ));
                                    ?>
                                    <div class="col-sm-10">
                                        <?php
                                        echo form_dropdown('project_id', $projects, '', array(
                                            'class' => 'form-control select2',
                                            'required' => 'required',
                                            'id' => 'project_id'
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <div class="box box-primary" >
                                    <div class="box-body no-padding" id="recievedata" style="overflow-x:auto;">
                                        <!-- THE CALENDAR -->
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="overlay" id="overlay1" style="display:none;">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                </div>
                                <!-- /. box -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal" id="modelshowbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="show_details">                                    
                </div>


            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>            
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php
echo form_close();
?>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {

        function changedata(project,dat="") {
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/ProjectMappingCntrl/ProjectWiseViewAjax",
                type: "get", //send it through get method
                data: {
                    project: project,
                    date:dat,
                    mapping:0
                },
                success: function (response) {
                    $("#recievedata").html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
            ;
        }

        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $("body").on("change", "#project_id", function (e) {
            var project = $("#project_id").val();
            if (project != "") {
                changedata(project);
            }else{
                $("#recievedata").html('');
            }

        });
        $("body").on("click", ".cngDate", function (e) {
            var project = $("#project_id").val();
            var dat=$(this).data('date');
            if (project != "") {
                changedata(project,dat);
            }else{
                $("#recievedata").html('');
            }

        });
        $("body").on("click", ".click_show", function (e) {
            var shw = $(this).data('shw');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/ProjectMappingCntrl/Showdetails",
                type: "get", //send it through get method
                data: {
                    shw: shw
                },
                success: function (response) {
                    $("#show_details").html(response);
                    $("#modelshowbody").modal('show');
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });
    });
</script>