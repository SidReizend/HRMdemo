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
        td:nth-of-type(4):before { content: "Actual Hours spend"; } 
        td:nth-of-type(5):before { content: "OD hours"; } 
        td:nth-of-type(6):before { content: "Productive hours"; } 
        td:nth-of-type(7):before { content: "Day Status"; } 
        td:nth-of-type(8):before { content: "Approval Status"; } 
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
                            <h3 class="box-title">Comp-off Approvals</h3>
                        </div>
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2    ">
                                        <?php
                                        echo form_open('', array(
                                            'class' => 'form-horizontal',
                                            'id' => 'showComp',
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
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Talent</th>
                                        <th>Date</th>
                                        <th>Actual Hours spend</th>
                                        <th>OD hours</th>
                                        <th>Productive hours</th>
                                        <th>Day Status</th>
                                        <th>Approval Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($compOffDetails as $compOff) {
                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $compOff['note']; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $compOff['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($compOff['attendance_date']));
                                                ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php
                                                echo $compOff['actual_hours_spend'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $compOff['od_hours'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $compOff['productive_hrs'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($compOff['is_holiday'] == 1) {
                                                    echo 'Holiday';
                                                } else if ($compOff['is_leave'] == 1) {
                                                    echo 'Leave';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($compOff['holiday_hr_status'] == 1) {
                                                    echo 'CompOff';
                                                } else if ($compOff['holiday_hr_status'] == 0) {
                                                    echo 'Waiting';
                                                } else if ($compOff['holiday_hr_status'] == 2) {
                                                    echo 'Approved';
                                                } else if ($compOff['holiday_hr_status'] == 3) {
                                                    echo 'Declined';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (($compOff['productive_hrs'] >= $this->defaultValues->getValue(14)) && ($compOff['holiday_hr_status'] == 0)) {
                                                    echo anchor('compOffManagement/compOffApprove/attendanceId/' . $compOff['id'] . $redirect, 'Comp-Off', array(
                                                        'class' => 'btn btn-small btn-warning approve_events'
                                                    ));
                                                    echo '&nbsp';
                                                    echo anchor('compOffManagement/compOffDecline/attendanceId/' . $compOff['id'] . $redirect, 'Decline', array(
                                                        'class' => 'btn btn-small btn-danger cancel_events',
                                                        'id' => $compOff['id']
                                                    ));
                                                } else if (($compOff['productive_hrs'] < $this->defaultValues->getValue(14)) && ($compOff['holiday_hr_status'] == 0)) {
                                                    echo anchor('compOffManagement/extraApprove/attendanceId/' . $compOff['id'] . $redirect, 'Approve', array(
                                                        'class' => 'btn btn-small btn-primary approve_events'
                                                    ));
                                                    echo '&nbsp';
                                                    echo anchor('compOffManagement/compOffDecline/attendanceId/' . $compOff['id'] . $redirect, 'Decline', array(
                                                        'class' => 'btn btn-small btn-danger cancel_events',
                                                        'id' => $compOff['id']
                                                    ));
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
<?php
include_once APPPATH . 'views/footer.php';
?>


<script>
    $('#myCompOffTable').DataTable();
    $("body").on("click", ".cancel_events", function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        var id = $(this).attr('id');
        base_url = '<?php echo base_url() ?>';
        alertify.prompt("Please add A Note For the Reason", function (e) {
            var alertify_text = $(".alertify-text").val();
            if (e) {
                $.ajax({
                    url: base_url + "index.php/compOffManagement/savenote",
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
                            alertify.alert("some error in Entering the Note");
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
    $("body").on("click", ".approve_events", function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        alertify.confirm("Are you sure to approve", function (e) {
            if (e) {
                alertify.success("You clicked okay");
                location.href = link;
            } else {
                alertify.error("You've clicked Cancel");
            }
        });
    });
    $("body").on("change", ".selectOptions", function (e) {
        $("#showComp").submit();
    });
    $(".select2").select2();
</script>