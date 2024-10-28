<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent"; } 
        td:nth-of-type(3):before { content: "Date"; } 
        td:nth-of-type(4):before { content: "Projects"; } 
        td:nth-of-type(5):before { content: "Time"; } 
        td:nth-of-type(6):before { content: "Status"; } 
        td:nth-of-type(7):before { content: "Actions"; } 
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Approved Timesheets</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl/HomeDataApproval', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <?php
                                        echo form_dropdown('talent', $talents, $talent, array(
                                            'class' => 'form-control select2 talentChoose',
                                            'required' => 'required',
                                            'id' => 'talent_id',
                                            'name' => 'talent'
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                        $selectedMonth = date('m');
                                        $selectedYear = date('Y');
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                </div>
                            </div> 
                            <br/>
                            <br/>
                            <br/>
                            <?php
                            //echo $this->db->last_query();
                            echo form_close();
                            ?>
                            <div class="col-sm-12" id="change_data">
                                <table id="table_responsive" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Talent</th>
                                            <th>Date</th>
                                            <th>Projects</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serialNumber = 0;
                                        foreach ($HomeGetTotalData as $data) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo ++$serialNumber;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $data['name'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo date('d/m/Y', strtotime($data['date']));
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $ProjectsOfDate = $this->TimePredictionModel->ProjectsOfDate($data['date'], $data['talent'], $data['is_approved']);
                                                    foreach ($ProjectsOfDate as $project) {
                                                        echo '<button class="btn btn-info">' . $project['project_code'] . '</button>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $TimeOfDate = $this->TimePredictionModel->TimeOfDate($data['date'], $data['talent'], $data['is_approved']);
                                                    $time_array = array();
                                                    foreach ($TimeOfDate as $time) {
                                                        $time_array[] = $time['time'];
                                                    }
                                                    echo $this->TimePredictionModel->sum_time($time_array);
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="#" id="view_details" 
                                                       data-talent="<?php echo $data['talent']; ?>" data-approv="<?php echo $data['is_approved']; ?>"
                                                       data-date="<?php echo $data['date']; ?>" >View Details
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>                                 
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal" id="modelshowbody">
    <div class="modal-dialog" style="width:75%;">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">  
                    <table id="table_responsive" class="table table-bordered table-hover show_details"  >

                    </table>
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
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $("body").on("change", ".talentChoose", function (e) {
            var year = $('#year').val();
            var month = $('#month').val();
            var talent_id = $("#talent_id").val();
            if (talent_id != '') {
                base_url = '<?php echo base_url(); ?>';
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/HomeDataApprovedAjax",
                    type: "get", //send it through get method
                    data: {
                        talent: talent_id,
                        month: month,
                        year: year
                    },
                    success: function (response) {
                        $("#change_data").html(response);
                        $('#table_responsive').DataTable();
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                });
            }

        });
        $("body").on("click", "#view_details", function (e) {
            e.preventDefault();
            var talent = $(this).data('talent');
            var date = $(this).data('date');
            var approv = $(this).data('approv');
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ViewDetailsApprove",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    date: date,
                    approv: approv
                },
                success: function (response) {
                    $(".show_details").html(response);
                    $("#modelshowbody").modal('show');
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });

        });
        $("body").on("click", ".click_declain", function (e) {
            e.preventDefault();
            var talent = $(this).data('talent');
            var date = $(this).data('date');
            var id = $(this).data('id');
            base_url = '<?php echo base_url(); ?>';
            alertify.prompt("Please add A Note For the Reason", function (e) {
                var alertify_text = $(".alertify-text").val();
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/TimePredictionCntrl/DeclineApprovedTime",
                        type: "get", //send it through get method
                        data: {
                            reason: alertify_text,
                            date: date,
                            talent: talent
                        },
                        success: function (response) {
                            if (response == 1) {
                                alertify.success("Declined Succesfully");
                                $("#action_process" + id).html('<button class="btn btn-danger">Declined</button>');
                            } else {
                                alertify.alert("some error Found");
                            }
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    });

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
        $("body").on("click", ".click_approve", function (e) {
            e.preventDefault();
            var talent = $(this).data('talent');
            var date = $(this).data('date');
            var id = $(this).data('id');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ApproveTime",
                type: "get", //send it through get method
                data: {
                    date: date,
                    talent: talent
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("Approved Succesfully");
                        $("#action_process" + id).html('<button class="btn btn-success">Approved</button>');
                    } else {
                        alertify.alert("some error Found");
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });
    });
</script>