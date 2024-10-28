<?php
$select2 = 1;
$form_validator = 1;
$datepicker = 1;
$alertify = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Date of Attendance"; } 
        td:nth-of-type(3):before { content: "Uploaded By"; } 
        td:nth-of-type(4):before { content: "Uploaded On"; } 
        td:nth-of-type(5):before { content: "view"; }
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal',
                'id' => 'attendanceUpload'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Talent Details Modification</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Date of Attendence', 'dateOfAttendence', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-10">  <?php
                                            $dobText = array(
                                                'name' => 'dateOfAttendence',
                                                'id' => 'dateOfAttendence',
                                                'placeholder' => 'dd/mm/YYYY',
                                                'class' => 'form-control datepicker dateOfAttendence',
                                                'value' => '',
                                                'data-validation' => 'required',
                                                'readonly' => 'readonly',
                                            );

                                            echo form_input($dobText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('dob'); ?> </span>
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
                                        'id' => 'save',
                                        'content' => 'Save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                        <table id="table_responsive" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Date of Attendance</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded On</th>
                                    <th>view</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                
                                foreach ($attendanceSheets as $attendanceSheetsSpecific) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('d/m/Y',  strtotime($attendanceSheetsSpecific['attendance_date']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $attendanceSheetsSpecific['username'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('d/m/Y H:i:s',  strtotime($attendanceSheetsSpecific['uploaded_on']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo anchor('attendenceSheetUpload/uploadedDetailView/attendanceId/' . $attendanceSheetsSpecific['id'], '<i class="fa fa-fw fa-edit"></i>');
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
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
//Date picker
    $(document).ready(function () {
        $('#attendanceTable').DataTable();
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) {
            dateValidation();
        });
        $.validate({
            lang: 'en'
        });
        function dateValidation() {
            var dateOfAttendence = $("#dateOfAttendence").val();
            var base_url = '<?php echo base_url() ?>';
            //console.log();
            var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $.ajax({
                    url: base_url + "index.php/attendenceSheetUpload/dateContentExistCheck",
                    type: "post", //send it through get method
                    data: {dateOfAttendence: dateOfAttendence, '<?php echo $this->security->get_csrf_token_name(); ?>': cct},
                    success: function (response) {
                        if (response == 1) {
                            alertify.alert("Already data uploaded for the date");
                        }
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.error("some error found");
                    }
                }).complete(function(){
                    var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                    var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                    $('[name="'+ csrf_name +'"]').val(cct);
                });
            
        }
    });
</script>

