<?php
$select2 = 1;
$form_validator = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>

<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent"; } 
        td:nth-of-type(3):before { content: "Year"; } 
        td:nth-of-type(4):before { content: "Month"; } 
        td:nth-of-type(5):before { content: "Note"; } 
        td:nth-of-type(6):before { content: "Uploaded By"; } 
        td:nth-of-type(7):before { content: "Uploaded On"; } 
        td:nth-of-type(9):before { content: "File"; } 
        td:nth-of-type(10):before { content: "Action"; } 
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal',
                'id' => 'addSlip'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Salary Slip</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Talent', 'talent', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                                'class' => 'form-control select2',
                                                'id' => 'talent',
                                                'data-validation' => 'required',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                        </div>
                                        <?php
                                        echo form_label('Month', 'month', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('month', $monthDropdown, $selectedMonth, array(
                                                'class' => 'form-control select2',
                                                'style' => 'width: 100%',
                                                'id' => 'month',
                                                'data-validation' => 'required',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('month'); ?> </span>
                                        </div>
                                        <?php
                                        echo form_label('Year', 'year', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-2">
                                            <?php
                                            echo form_dropdown('year', $yearDropdown, $selectedYear, array(
                                                'class' => 'form-control select2',
                                                'id' => 'year',
                                                'data-validation' => 'required',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('year'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Note', 'note', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-10">  <?php
                                            $noteText = array(
                                                'name' => 'note',
                                                'id' => 'note',
                                                'class' => 'form-control',
                                                'value' => set_value('note'),
                                            );

                                            echo form_input($noteText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('note'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Upload File', 'uploadFile', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $upload = Array(
                                                "name" => "uploadFile",
                                                "id" => "uploadFile",
                                                'data-validation' => 'required',
                                            );
                                            echo form_upload($upload);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('uploadFile'); ?> </span>
                                        </div>
                                    </div>
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
                                        'content' => 'Save',
                                        'id' => 'save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
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
<!-- /.container -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $("#table_responsive").dataTable();
        $("body").on("click", ".cancel_events", function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            alertify.confirm("Are you sure", function (e) {
                if (e) {
                    alertify.success("You've clicked OK");
                    location.href = link;

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
        $("#save").on('click', function (e) {
            var talent = $('#talent').val();
            var month = $('#month').val();
            var year = $('#year').val();
            if (talent != '' && month != '' && year != '') {
                e.preventDefault();
                base_url = "<?php echo base_url() ?>";
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/salarySlip/checkAlreadyExist",
                    type: "get", //send it through get method
                    data: {
                        talent: talent,
                        month: month,
                        year: year,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                    },
                    success: function (response) {
                        if (response == true) {
                            $('#addSlip').submit();
                        } else {
                            alertify.alert(response);
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
            }
        });
    });
    $.validate({
        lang: 'en'
    });
</script>

