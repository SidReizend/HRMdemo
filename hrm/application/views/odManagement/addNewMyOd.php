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
            echo form_open('MyOds/addNewMyOd', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">New OD Application</h3>
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
                                        'value' => set_value('reason'),
                                        'data-validation' => 'required',
                                    );

                                    echo form_input($reasonText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('reason'); ?> </span>
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
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('fromDate'),
                                        'data-validation' => 'required',
                                        'readonly' => 'readonly',
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
                                        'class' => 'form-control datepicker',
                                        'value' => set_value('toDate'),
                                        'data-validation' => 'required',
                                        'readonly' => 'readonly',
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('toDate'); ?> </span>
                                </div>
                            </div>
                            <div id="daysLoad">

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
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) {
            dataLoading();
        });
        $(".select2").select2();
        function dataLoading() {
            fromDate = $("#fromDate").val();
            toDate = $("#toDate").val();
            if (fromDate != '' && toDate != '') {
                base_url = '<?php base_url() ?>';
                //console.log();
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "showDatesBetweenDates",
                    type: "post", //send it through get method
                    data: {fromDate: fromDate, toDate: toDate, '<?php echo $this->security->get_csrf_token_name(); ?>': cct},
                    success: function (response) {
                        if (response == 1) {
                            alertify.alert("Invalid to date, to date should be with in month end processing of current month");
                            $("#daysLoad").html('');
                        } else if (response == 2) {
                            alertify.alert("Invalid to date add from date");
                            $("#daysLoad").html('');
                        } else if (response == 3) {
                            alertify.alert("Sorry monthly process are done, can't apply for the from date");
                            $("#daysLoad").html('');
                        } else {
                            $("#daysLoad").html(response);
                        }

                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alert("some error found");
                    }
                });
            } else {
                $("#daysLoad").html('');
            }
        }
    });
    $.validate({
        lang: 'en'
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

</script>

