<?php
$datatables = 1;
$select2 = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent"; } 
        td:nth-of-type(3):before { content: "Reason of OD"; } 
        td:nth-of-type(4):before { content: "OD Type"; } 
        td:nth-of-type(5):before { content: "From Date"; } 
        td:nth-of-type(6):before { content: "To Date"; } 
        td:nth-of-type(7):before { content: "Status"; } 
        td:nth-of-type(8):before { content: "Actions"; } 
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
                            <h3 class="box-title">OD Approvals</h3>
                        </div>
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2    ">
                                        <?php
                                        echo form_open('', array(
                                            'class' => 'form-horizontal',
                                            'id' => 'showOd',
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
                                        <th>Reason of OD</th>
                                        <th>OD Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($odDetails as $Od) {
                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $Od['decline_note']; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $Od['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $Od['reason'];
                                                ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php
                                                echo $Od['od_type_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $Od['from_date'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $Od['to_date'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($Od['is_approved'] == 0) {
                                                    echo 'Pending';
                                                } else if ($Od['is_approved'] == 1) {
                                                    echo 'Approved';
                                                } else if ($Od['is_approved'] == 2) {
                                                    echo 'Declined';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $talentDetails = $this->talent->getTalentDetailsByTalentId($Od['talent']);
                                                $date = DateTime::createFromFormat('d/m/Y', $Od['from_date']);
                                                $dateOfAttendence = $date->format('Y-m-d');
                                                $croneJobStatus = 0;
                                                /* Checks the monthly process is done */
                                                if (strtotime($dateOfAttendence) <= strtotime($lastCronJobDate)) {
                                                    $croneJobStatus = 1;
                                                }
                                                /* if talent is not resigned and monthly process is not done then povides option to edit */
                                                if ((0 == $talentDetails->is_resigned && 0 == $croneJobStatus)) {
                                                    $text = 'edit';
                                                } else {
                                                    $text = 'view';
                                                }
                                                echo anchor('OdWaitingForApproval/editOdWaitingForApproval/odId/' . $Od['id'] . $redirect, $text, array(
                                                    'class' => 'btn btn-small btn-primary'
                                                ));
                                                echo '&nbsp';
                                                if ($Od['is_approved'] == 0) {
                                                    echo anchor('OdWaitingForApproval/approveOd/odId/' . $Od['id'] . $redirect, 'Approve', array(
                                                        'class' => 'btn btn-small btn-warning approve_events'
                                                    ));
                                                    echo '&nbsp';
                                                    echo anchor('OdWaitingForApproval/declainOd/odId/' . $Od['id'] . $redirect, 'Decline', array(
                                                        'class' => 'btn btn-small btn-danger cancel_events',
                                                        'id' => $Od['id']
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
    $(document).ready(function () {
        $(".select2").select2();
        $('#myOdTable').DataTable();
        $("body").on("click", ".cancel_events", function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            var id = $(this).attr('id');
            var base_url = '<?php echo base_url() ?>';
            alertify.prompt("Please add a note for the reason", function (e) {
                var alertify_text = $(".alertify-text").val();
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/odWaitingForApproval/savenote",
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
            if ($('#resigned').prop("checked") == true) {
                $('#resigned').val('1');
            } else {
                $('#resigned').val('0');
            }
            $("#showOd").submit();
        });
    });
</script>