<?php
$form_validator = 1;
$datepicker = 1;
$select2 = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('TaskManagementCntrl/AddTask', array(
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
                            <h3 class="box-title">Talent Wise Mapping</h3>
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
                            echo anchor('ProjectMappingCntrl/ProjectWiseView', 'Project Wise View', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>

                            <div class="form-group">
                                <?php
                                echo form_label('Talent', 'talent', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('talent', $talents, '', array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                        'id' => 'talent_id'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('type'); ?> </span>
                                </div>
                            </div> 
                            <div class="form-group">
                                <?php
                                $date = date('d/m/Y');
                                echo form_label('From date', 'fromDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-3">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $date,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                </div>
                                <?php
                                echo form_label('To date', 'toDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-3">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $date,
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('toDate'); ?> </span>
                                </div>
                                <div class="col-sm-2"> 
                                    <button class="btn btn-default date-to-date-add"
                                            style="float:right;" title="Add new" type="button">
                                        <i class="fa fa-align-center"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-12" id="change_needed" style="overflow-x:auto;">

                            </div>

                        </div>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal" id="modelbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Add Project For Mapping</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="form-group" style="padding-bottom:30px;">
                        <?php
                        echo form_label('Project', 'project', array(
                            'class' => 'col-sm-2 control-label',
                        ));
                        ?>
                        <div class="col-sm-10">
                            <?php
                            echo form_dropdown('talent', $projects, '', array(
                                'class' => 'form-control select2',
                                'required' => 'required',
                                'id' => 'project_id'
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div  class="col-sm-2">
                            Description
                        </div>
                        <div  class="col-sm-10">
                            <textarea class="form-control" id="textarea_prjt"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div  class="col-sm-2">
                            Importance
                        </div>
                        <div  class="col-sm-10 demo-table">
                            <input type="hidden" name="rating" id="rating" value="" />
                            <ul onMouseOut="resetRating();">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    ?>
                                    <li class='' onmouseover="highlightStar(this);" 
                                        onmouseout="removeHighlight();" onClick="addRating(this);">&#9733;</li>  
                                    <?php } ?>
                            </ul>

                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                <button class="btn btn-primary save_project" type="button">Add Project</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
                <button class="btn btn-primary save_project" type="button">Add Talent</button>
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

    function highlightStar(obj) {
        removeHighlight();
        $('.demo-table li').each(function (index) {
            $(this).addClass('highlight');
            if (index == $('.demo-table  li').index(obj)) {
                return false;
            }
        });
    }

    function removeHighlight() {
        $('.demo-table li').removeClass('selected');
        $('.demo-table li').removeClass('highlight');
    }

    function addRating(obj) {
        $('.demo-table li').each(function (index) {
            $(this).addClass('selected');
            $('#rating').val((index + 1));
            if (index == $('.demo-table li').index(obj)) {
                return false;
            }
        });
    }

    function resetRating() {
        if ($('#rating').val() != 0) {
            $('.demo-table li').each(function (index) {
                $(this).addClass('selected');
                if ((index + 1) == $('#rating').val()) {
                    return false;
                }
            });
        }
    }
</script>
<script>
    $(document).ready(function () {
        //$(".select2").select2();
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $.validate({
            lang: 'en'
        });
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        })


        $("body").on("change", "#talent_id", function (e) {
            change_table();
        });
        $("body").on("click", ".click_delete", function (e) {
            $("#overlay1").show();
            var dlt = $(this).data('dlt');
            base_url = '<?php echo base_url() ?>';
            $.ajax({
                url: base_url + "index.php/ProjectMappingCntrl/Talentwisedeleting",
                type: "get", //send it through get method
                dataType: 'json',
                data: {
                    dlt: dlt
                },
                success: function (response) {
                    if (response.status == 1) {
                        alertify.error(response.message);
                    } else {
                        alertify.success(response.message);
                        change_table();
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        });
        $("body").on("click", ".save_project", function (e) {
            e.preventDefault();
            $("#overlay1").show();

            var project_id = $("#project_id").val();
            var textarea_prjt = $("#textarea_prjt").val();
            var rating = $("#rating").val();
            base_url = '<?php echo base_url() ?>';
            if (project_id != "") {
                var save_all = $(this).data('save_all');
                var talent_id = $(this).data('talent_id');
                if (save_all == 0) {
                    var talent_date = $(this).data('talent_date');
                    $.ajax({
                        url: base_url + "index.php/ProjectMappingCntrl/Talentwisesaving",
                        type: "get", //send it through get method
                        dataType: 'json',
                        data: {
                            talent_id: talent_id,
                            talent_date: talent_date,
                            project_id: project_id,
                            textarea_prjt: textarea_prjt,
                            rating: rating
                        },
                        success: function (response) {
                            if (response.status == 1) {
                                alertify.error(response.message);
                            } else {
                                alertify.success(response.message);
                                $("#modelbody").modal('hide');
                                change_table();
                            }
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    }).complete(function () {
                        $("#overlay1").hide();
                    });
                } else {
                    var talent_from_date = $(this).data('talent_from_date');
                    var talent_to_date = $(this).data('talent_to_date');
                    $.ajax({
                        url: base_url + "index.php/ProjectMappingCntrl/Allsaving",
                        type: "get", //send it through get method
                        dataType: 'json',
                        data: {
                            talent_id: talent_id,
                            talent_from_date: talent_from_date,
                            talent_to_date: talent_to_date,
                            project_id: project_id,
                            textarea_prjt: textarea_prjt,
                            rating: rating
                        },
                        success: function (response) {
                            if (response.status == 1) {
                                alertify.error(response.message);
                            } else {
                                alertify.success(response.message);
                                $("#modelbody").modal('hide');
                                change_table();
                            }
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    }).complete(function () {
                        $("#overlay1").hide();
                    });
                }




            } else {
                alertify.error("Please select Project");
            }
        });
        $("body").on("click", ".initiate_adding", function (e) {
            var talent_id = $(this).data('talent_id');
            var talent_date = $(this).data('talent_date');
            $(".save_project").data('talent_id', talent_id);
            $(".save_project").data('talent_date', talent_date);
            $(".save_project").data('save_all', '0');
        });
        $("body").on("click", ".date-to-date-add", function (e) {
            var talent = $("#talent_id").val();
            if (talent == "") {
                alertify.alert("Please select Talent");
                e.preventDefault();
            }
            var fromDate = $("#fromDate").val();
            if (fromDate == "") {
                alertify.alert("Please select From Date");
                e.preventDefault();
            }
            var toDate = $("#toDate").val();
            if (toDate == "") {
                alertify.alert("Please select To Date");
                e.preventDefault();
            }
            if (talent != "") {
                $("#modelbody").modal('show');
                $(".save_project").data('talent_id', talent);
                $(".save_project").data('talent_from_date', fromDate);
                $(".save_project").data('talent_to_date', toDate);
                $(".save_project").data('save_all', '1');
            }

        });
        $("body").on("click", ".cngDate", function (e) {
            var dat = $(this).data('date');
            change_table(dat);
        });
        function change_table(dat = "") {
            var talent = $("#talent_id").val();
            base_url = '<?php echo base_url() ?>';
            if (talent != "") {
                $("#overlay1").show();
                $.ajax({
                    url: base_url + "index.php/ProjectMappingCntrl/TalentWiseViewAjax",
                    type: "get", //send it through get method
                    data: {
                        talent: talent,
                        date: dat,
                        mapping: 1
                    },
                    success: function (response) {
                        $("#change_needed").html(response);
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                }).complete(function () {
                    $("#overlay1").hide();
                });
            } else {
                $("#change_needed").html('');
            }
        }
    });
</script>

