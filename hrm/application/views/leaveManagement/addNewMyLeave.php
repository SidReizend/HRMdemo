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
            echo form_open('myLeaves/addNewLeaves', array(
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
                            <h3 class="box-title">Leave Application</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-10">
                                    <?php
                                    // var_dump($getMyLeavesLeft);
                                    foreach ($getMyLeavesLeft as $getmyleave) {
                                        $showLeaveLeft = TRUE;
                                        $paternityEnabled = $this->defaultValues->getValue(32);
                                        $emergencyEnabled = $this->defaultValues->getValue(33);
                                        if ($emergencyEnabled == 0 && $getmyleave['id'] == 3) {
                                            $showLeaveLeft = false;
                                        }
                                        if ($paternityEnabled == 0 && ($getmyleave['id'] == 4 || $getmyleave['id'] == 5)) {
                                            $showLeaveLeft = FALSE;
                                        }
                                        if (TRUE === $showLeaveLeft) {
                                            $leaveLeft = $getmyleave['id'] == 2 ? ($getmyleave['leave_left']) : round($getmyleave['leave_left']);
                                            $halfDayMechanism = $this->defaultValues->getValue(34);
                                            if ($halfDayMechanism != 1) {
                                                $leaveLeft = round($leaveLeft);
                                            }
                                            ?>
                                            <div class="col-lg-2 col-xs-6">
                                                <!-- small box -->
                                                <div class="small-box <?php
                                                if ($getmyleave['leave_left'] == 0) {
                                                    echo 'bg-red';
                                                } else {
                                                    echo 'bg-aqua';
                                                }
                                                ?>">
                                                    <div class="inner">
                                                        <h3><?php echo $leaveLeft; ?></h3>
                                                        <p><?php echo $getmyleave['type']; ?></p>
                                                    </div>
                                                    <div class="icon">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <!--                                    <div class="col-lg-2 col-xs-6">
                                                                             small box 
                                                                            <div class="small-box <?php
//                                        if ($getMylopLeft == 0) {
//                                            echo 'bg-aqua';
//                                        } else {
//                                            echo 'bg-red';
//                                        }
                                    ?>">
                                                                                <div class="inner">
                                                                                    <h3><?php // echo $getMylopLeft;      ?></h3>
                                                                                    <p>LOP</p>
                                                                                </div>
                                                                                <div class="icon">
                                                                                </div>
                                                                            </div>
                                                                        </div> -->
                                    <div class="col-lg-2 col-xs-6">
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                echo form_label('From date', 'fromDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => set_value('fromDate'),
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('To date', 'toDate', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => set_value('toDate')
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('toDate'); ?> </span>
                                </div>
                            </div>
                            <div id="daysLoad">

                            </div>
                            <div class="form-group">
                                <?php
                                echo form_label('Request', 'request', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-10">  <?php
                                    $requestText = array(
                                        'name' => 'request',
                                        'id' => 'request',
                                        'class' => 'form-control',
                                        'rows' => '3',
                                        'value' => set_value('request'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_textarea($requestText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('request'); ?> </span>
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
                                        'content' => 'Apply',
                                        'class' => 'btn btn-small btn-primary save_all'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="col-sm-5"> 
                            </div> 
                            <div class="col-sm-5"> 
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".save_all").hide();
        $(".select2").select2();
        $.validate({
            lang: 'en'
        });
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        })
        $("body").on("changeDate", ".daysLoad", function (e) {
            fromDate = $("#fromDate").val();
            toDate = $("#toDate").val();
            if ((fromDate != "") && (toDate != "")) {
                $(".save_all").show();
                base_url = '<?php echo base_url(); ?>';
                //console.log();
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/MyLeaves/showDatesBetweenDates",
                    type: "post", //send it through get method
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == '<center><h2><b>Leave Application Controlled To This Financial Year Only</b></h2></center>') {
                            $(".save_all").hide();
                        }
                        $("#daysLoad").html(response);
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                });
            }
        });
        $("body").on("click", ".leave_type_apply", function (e) {
            e.preventDefault();
            fromDate = $("#fromDate").val();
            toDate = $("#toDate").val();
            if ((fromDate == "") || (toDate == "")) {
                alertify.alert("please Fill all the fields")
            } else {
                var left = +$("body .leave_type_apply").data("left");
                fromDate = $("#fromDate").val();
                toDate = $("#toDate").val();
                var btn = $(this);
                if (btn.hasClass("selected")) {
                    btn.removeClass("selected");
                } else {
                    btn.addClass("selected");
                }
                var leave_info_array = [];
                var selected_seats = $("body .selected");
                $.each(selected_seats, function (key, value) {
                    var left = $(value).data("left");
                    var type = $(value).data("type");
                    var leave_infos = {"left": left, "type": type};
                    leave_info_array.push(leave_infos);
                });
                base_url = '<?php echo base_url(); ?>';
                //console.log();
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/MyLeaves/showLeavedates",
                    type: "post", //send it through get method
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        "leave_info_array": leave_info_array,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        $("#change_needed").html(response);
                        var compoff_after = $("#compoff_after").val();
                        var el_after = $("#el_after").val();
                        var compoff_hid = $("#compoff_hid").val();
                        var el_hid = $("#el_hid").val();
                        var emergency_hid = $("#emergency_hid").val();
                        var emergency_after = $("#emergency_after").val();
                        var diff1 = compoff_hid - compoff_after;
                        var diff2 = el_hid - el_after;
                        var diff3 = emergency_hid - emergency_after;
                        if ((compoff_hid > 0) && (diff1 == compoff_hid)) {
                            if ((el_hid > 0) && (diff2 != el_hid)) {
                                $(".el").trigger("click");
                            }
                            if ((emergency_hid > 0) && (diff3 != emergency_hid)) {
                                $(".emergency").trigger("click");
                            }
                        }
                        if ((el_hid > 0) && (diff2 == el_hid)) {
                            if ((emergency_hid > 0) && (diff3 != emergency_hid)) {
                                $(".emergency").trigger("click");
                            }
                        }
                        if (diff1 == 0) {
                            if ($(".el").hasClass("disabled")) {
                                $(".el").addClass("leave_type_apply");
                                $(".el").removeClass("disabled");
                            }
                        } else {
                            if ($(".el").hasClass("leave_type_apply")) {
                                $(".el").addClass("disabled");
                                $(".el").removeClass("leave_type_apply");
                            }
                        }
                        if ((diff2 == 0) && (diff1 == 0)) {
                            if ($(".emergency").hasClass("disabled")) {
                                $(".emergency").addClass("leave_type_apply");
                                $(".emergency").removeClass("disabled");
                            }
                        } else {
                            if ($(".emergency").hasClass("leave_type_apply")) {
                                $(".emergency").addClass("disabled");
                                $(".emergency").removeClass("leave_type_apply");
                            }
                        }


                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                });
            }
        });
        $("body").on("click", ".save_all", function (e) {
            //e.preventDefault();
            //Check for the 'click detail button clicked or not'
            if ($("#current_quarter").length) {
                var compoff_hid = $("#compoff_hid").val();
                var el_hid = $("#el_hid").val();
                var total = $("#total").val();
                if (total > 0) {
                    //checks if it has compoff or el available
                    if ((compoff_hid + el_hid) > 0) {
                        if ($(".selected").length) {
                            var compoff_after = $("#compoff_after").val();
                            var el_after = $("#el_after").val();
                            var emergency_after = $("#emergency_after").val();
                            var lop_hid = $("#lop_hid").val();
                            var diff1 = compoff_hid - compoff_after;
                            var diff2 = round(el_hid) - round(el_after);
                            if (diff1 > 0) {
                                if (emergency_after > 0) {
                                    alertify.alert("Please Choose Emergency After Your Compoff");
                                    e.preventDefault();
                                }
                                if (el_after > 0) {
                                    alertify.alert("Please Choose EL After Your Compoff");
                                    e.preventDefault();
                                }

                            } else if (diff2 > 0) {
                                if (emergency_after > 0) {
                                    alertify.alert("Please Choose Emergency After Your EL");
                                    e.preventDefault();
                                }
                            }
                            if ((diff1 + diff2) > 0) {
                                if (lop_hid > 0) {
                                    if (diff1 > 0) {
                                        alertify.alert("Please Choose Your COMP-OFF Available");
                                        e.preventDefault();
                                    } else {
                                        alertify.alert("Please Choose Your EL Available");
                                        e.preventDefault();
                                    }
                                }
                            }
                        } else {
                            alertify.alert("Please Choose Your Leave Type");
                            e.preventDefault();
                        }
                    }
                } else {
                    alertify.alert("You don't select your Leave Type <br>OR<br> Your selection don't have any Working Days ");
                    e.preventDefault();
                }

            }
            if ($("#next_quarter").length) {
                var total_planned = $("#total_planned").val();
                if (total_planned == 0) {
                    alertify.alert("Your selection dont have any Working Days OR Weekends in Next Quarter");
                    e.preventDefault();
                }
            }
        });
    });

</script>

