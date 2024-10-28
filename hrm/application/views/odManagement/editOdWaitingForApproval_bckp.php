<?php
$form_validator = 1;
$select2 = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    .tooltip-inner {
        white-space:pre-wrap;
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            echo form_open('', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <?php
            //var_dump($myOdDetailsById);
            foreach ($myOdDetailsById as $myOdSpecific) {
                $selected_odType = $myOdSpecific['od_type'];
                $isApproved = $myOdSpecific['is_approved'];
                $reason = $myOdSpecific['reason'];
                $fromDate = $myOdSpecific['from_date'];
                $toDate = $myOdSpecific['to_date'];
            }
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">OD Approval</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('OD Type', 'odType', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    $odTypes_dropdown = $odTypes;
                                    echo form_dropdown('odType', $odTypes_dropdown, $selected_odType, array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('odType'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Reason Of OD', 'reason', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $reasonText = array(
                                        'name' => 'reason',
                                        'id' => 'reason',
                                        'class' => 'form-control',
                                        'placeholder' => 'reason',
                                        'value' => $reason,
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($reasonText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
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
                                        'readonly' => 'readonly',
                                        'class' => 'form-control',
                                        'placeholder' => 'reason',
                                        'value' => date('d/m/Y', strtotime($fromDate)),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
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
                                        'readonly' => 'readonly',
                                        'class' => 'form-control',
                                        'placeholder' => 'reason',
                                        'value' => date('d/m/Y', strtotime($toDate)),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
                                </div>
                            </div>
                            <?php
                            foreach ($myOdSpecificByMyOdId as $eachDays) {
                                $dateCountStandard = date('Y-m-d', strtotime($eachDays['date_of_od']));
                                $strToTimeDate = strtotime($dateCountStandard);
                                $day = strtolower(date("l", $strToTimeDate));
                                $color = 'green';
                                $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateCountStandard);
                                if (in_array($dateCountStandard, $talentOds)) {
                                    $color = 'red';
                                } else if (in_array($dateCountStandard, $leaves)) {
                                    $color = 'black';
                                } else if (in_array($dateCountStandard, $holidays)) {
                                    $color = 'blue';
                                } else if (in_array($day, $weekends) && ($checkDateInStrictlyWorking == 0)) {
                                    $color = 'blue';
                                }
                                ?>
                                <div class="col-md-4 od_details"
                                     id="<?php echo $eachDays['date_of_od']; ?>" 
                                     style="color: <?php echo $color; ?>"
                                     data-original-title=""
                                     data-toggle="tooltip" >
                                         <?php
                                         echo form_label(date('d/m/Y', strtotime($eachDays['date_of_od'])), 'dateOfOd', array(
                                             'class' => 'col-sm-2 control-label'
                                         ));
                                         ?>
                                    <div class="col-sm-12">  <?php
                                        $hoursText = array(
                                            'name' => 'hour[' . $eachDays['date_of_od'] . ']',
                                            'id' => $eachDays['date_of_od'],
                                            'class' => 'form-control',
                                            'style' => 'border-color: ' . $color,
                                            'type' => 'number',
                                            'max' => '23',
                                            'value' => date("H", strtotime($eachDays['hours_of_od']))
                                        );

                                        echo form_input($hoursText) . 'hr';
                                        ?>
                                        <br/>
                                        <br/>
                                    </div>
                                    <div class="col-sm-12">  <?php
                                        $minText = array(
                                            'name' => 'min[' . $eachDays['date_of_od'] . ']',
                                            'id' => $eachDays['date_of_od'],
                                            'class' => 'form-control',
                                            'type' => 'number',
                                            'max' => '59',
                                            'style' => 'border-color: ' . $color,
                                            'value' => date("i", strtotime($eachDays['hours_of_od']))
                                        );

                                        echo form_input($minText) . 'min';
                                        ?>
                                        <br/>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col-xs-11">
                                <center>
                                    <div class="form-group" >
                                        <div class="col-sm-2 control-label">
                                        </div>
                                        <!-- /.col -->

                                        <?php
                                        echo form_hidden('myOdId', $myOdId);
                                        echo form_hidden('redirect', $redirect);
                                        $talentDetails = $this->talent->getTalentDetailsByTalentId($talent);
                                        $croneJobStatus = 0;
                                        /* Checks the monthly process is done */
                                        if (strtotime($fromDate) <= strtotime($lastCronJobDate)) {
                                            $croneJobStatus = 1;
                                        }
                                        /* if talent is not resigned and monthly process is not done then povides option to edit */
                                        if ((0 == $talentDetails->is_resigned && 0 == $croneJobStatus)) {
                                            $submitButton = array(
                                                'type' => 'submit',
                                                'content' => 'Modify',
                                                'class' => 'btn btn-small btn-primary'
                                            );
                                            echo form_button($submitButton);
                                        }
                                        ?>
                                        &nbsp;
                                        <?php
                                        if ($isApproved == 0) {
                                            ?>
                                            <?php
                                            echo anchor('OdWaitingForApproval/approveOd/odId/' . $myOdId, 'Approve', array(
                                                'class' => 'btn btn-small btn-warning approve_events',
                                                'id' => $myOdId
                                            ));
                                            ?>
                                            &nbsp;
                                            <?php
                                            echo anchor('OdWaitingForApproval/declainOd/odId/' . $myOdId, 'Decline', array(
                                                'class' => 'btn btn-small btn-danger cancel_events',
                                                'id' => $myOdId
                                            ));
                                            ?>

                                            <?php
                                        }
                                        ?>
                                        &nbsp;

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
                                    <?php
                                    $odOwnerText = array(
                                        'odOwner' => $odOwner,
                                    );

                                    echo form_hidden($odOwnerText);
                                    ?>
                            </div>
                            </center>
                        </div>
                        <span style="color: red">
                            * Already applied OD for the date
                        </span>
                        &nbsp;&nbsp;
                        <span style="color: green">
                            * Working Day
                        </span>
                        &nbsp;&nbsp;
                        <span style="color: black">
                            * Leave 
                        </span>
                        &nbsp;&nbsp;
                        <span style="color: blue">
                            * Holiday 
                        </span>
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

    $.validate({
        lang: 'en'
    });
//Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
    });
    $("body").on("click", ".cancel_events", function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        var id = $(this).attr('id');
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        alertify.prompt("Please add a note for the reason", function (e) {
            var alertify_text = $(".alertify-text").val();
            if (e) {
                $.ajax({
                    url: base_url + "index.php/odWaitingForApproval/savenote",
                    type: "get", //send it through get method
                    data: {
                        alertify_text: alertify_text,
                        id: id,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == 1) {
                            alertify.success("Your Note Entered");
                            location.href = link;
                        } else {
                            alertify.alert("some error in enterig the note");
                        }
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                }).complete(function () {
                    var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                    var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                    $('[name="' + csrf_name + '"]').val(cct);
                });

            } else {
                alertify.error("You've clicked Cancel");
            }
        });
    });
    $("body").on("mouseover", ".od_details", function (e) {
        var od_owner = $('[name="odOwner"]').val();
        var date = $(this).attr('id');
        base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        $.ajax({
            url: base_url + "index.php/odWaitingForApproval/dateDetails",
            type: "get", //send it through get method
            data: {
                odOwner: od_owner,
                date: date,
                '<?php echo $this->security->get_csrf_token_name(); ?>': cct
            },
            success: function (response) {
                $('#' + date).attr('data-original-title', response);
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        }).complete(function () {
            var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
            $('[name="' + csrf_name + '"]').val(cct);
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
</script>

