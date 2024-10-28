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
        td:nth-of-type(3):before { content: "From Date"; } 
        td:nth-of-type(4):before { content: "To Date"; } 
        td:nth-of-type(5):before { content: "No:date"; } 
        td:nth-of-type(6):before { content: "Request"; } 
        td:nth-of-type(7):before { content: "Approval"; } 
        td:nth-of-type(8):before { content: "Details"; } 
        td:nth-of-type(9):before { content: "Actions"; } 
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
                            <h3 class="box-title">Leave Approvals</h3>
                        </div>
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2    ">
                                        <?php
                                        echo form_open('', array(
                                            'class' => 'form-horizontal',
                                            'id' => 'showLeave',
                                        ));
                                        echo form_dropdown('year', $yearArray, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                    </div>

                                    <div class="col-sm-2">
                                        <?php
                                        echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="material-switch pull-right">
                                        Sign On <input name="resigned" value="1" id="resigned" class="someSwitchOption001 selectOptions"
                                        <?php
                                        if ($isResigned == 1) {
                                            echo 'checked';
                                        }
                                        ?>
                                                       type="checkbox" />
                                        <label for="resigned" class="label-success"></label> Resigned
                                    </div>
                                </div>
                            </div>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>No. of Leaves</th>
                                        <th>Request</th>
                                        <th>Approval</th>
                                        <th>Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    $halfDayMechanism = $this->defaultValues->getValue(34);
                                    if ($halfDayMechanism == 1) {
                                        foreach ($leaveDueToAbsent as $leaveAbsent) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo ++$serialNumber;
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $leaveAbsent['first_name'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo date('d/m/Y', strtotime($leaveAbsent['attendance_date']));
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo date('d/m/Y', strtotime($leaveAbsent['attendance_date']));
                                                    ?>
                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    <?php
                                                    echo "System raised due to daily shortage";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($leaveAbsent['absent_raise_status'] == 1) {
                                                        echo 'Present';
                                                    } else if ($leaveAbsent['absent_raise_status'] == 2) {
                                                        echo 'Leave';
                                                    }
                                                    ?>
                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    <?php
                                                    echo anchor('#', 'Manage', array(
                                                        'class' => 'manage',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#manageModal',
                                                        'data-attendance' => $leaveAbsent['id'],
                                                        'data-date' => $leaveAbsent['attendance_date'],
                                                        'data-talent' => $leaveAbsent['talent_id']
                                                    ));
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    foreach ($myLeavesDetails as $myLeaves) {
                                        $tooltip_text = "";
                                        if ($myLeaves['considered'] == 0) {
                                            $tooltip_text = " Planned Leave. ";
                                        }
                                        $tooltip_text .= $myLeaves['note'];
                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $tooltip_text; ?>"
                                        <?php
                                        if ($myLeaves['considered'] == 0) {
                                            echo 'class="bg-gray"';
                                        }
                                        ?>>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myLeaves['first_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($myLeaves['from_date']));
                                                ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($myLeaves['to_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $leaveCount = $this->leaves->leaveCountEachForLeaveId($myLeaves['id']);
                                                echo $leaveCount;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myLeaves['request'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($myLeaves['is_approved'] == 0) {
                                                    echo 'Pending';
                                                } else if ($myLeaves['is_approved'] == 1) {
                                                    echo 'Approved';
                                                } else if ($myLeaves['is_approved'] == 2) {
                                                    echo 'Declined';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('#', 'View Details', array('class' => 'view_details',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#uploadModal',
                                                    'data-tid' => $myLeaves['id']
                                                ));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($myLeaves['is_approved'] == 0) {
                                                    echo anchor('LeaveApproval/acceptleave/myleaveId/' . $myLeaves['id'] . $redirect, 'Approve', array('class' => 'btn btn-success'));
                                                    echo '&nbsp;';
                                                    echo anchor('LeaveApproval/cancelLeave/myleaveId/' . $myLeaves['id'] . $redirect, 'Decline', array('class' => 'btn btn-danger cancel_events',
                                                        'id' => $myLeaves['id']));
                                                }
                                                ?>
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
        </section>
    </div>
</div>
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="donateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="donateModalLabel">Leave Details</h4>
            </div>
            .
            <div class="modal-body">
                <div class="box box-widget">
                    <div class='box-body' id="preview">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- /.modal -->
<div class="modal fade" id="manageModal" tabindex="-1" role="dialog" aria-labelledby="donateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="donateModalLabel">Manage Attendance</h4>
            </div>
            .
            <div class="modal-body">
                <div class="box box-widget">
                    <div class='box-body' id="attendanceMapping">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- /.modal -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $(".select2").select2();

        $('#myOdTable').DataTable();
        $("body").on("click", ".cancel_events", function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            var id = $(this).attr('id');
            base_url = '<?php echo base_url(); ?>';
            alertify.prompt("Please add A Note For the Reason", function (e) {
                var alertify_text = $(".alertify-text").val();
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/leaveApproval/savenote",
                        type: "get", //send it through get method
                        data: {
                            alertify_text: alertify_text,
                            id: id
                        },
                        success: function (response) {
                            if (response == 1) {
                                alertify.success("Your Note Entered");
                                location.href = link;
                            } else {
                                alertify.alert("some error in Enterig the Note");
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
        $("body").on("click", ".view_details", function (e) {
            e.preventDefault();
            var id = $(this).data('tid');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/LeaveApproval/view_details",
                type: "get", //send it through get method
                data: {
                    tid: id
                },
                success: function (response) {
                    $("#preview").html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });

        });

        //for showing a pop for map the leave shortage

        $("body").on("click", ".manage", function (e) {
            e.preventDefault();
            var attendance = $(this).data('attendance');
            var date = $(this).data('date');
            var talent = $(this).data('talent');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/LeaveApproval/manageAttendance",
                type: "get", //send it through get method
                data: {
                    attendance: attendance,
                    date: date,
                    talent: talent,
                    redirect: '<?php echo $redirect; ?>'
                },
                success: function (response) {
                    $("#attendanceMapping").html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });

        });
        $("body").on("change", ".selectOptions", function (e) {
            $("#showLeave").submit();
        });
    });
</script>