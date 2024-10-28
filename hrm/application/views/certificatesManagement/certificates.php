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
        td:nth-of-type(2):before { content: "Type"; } 
        td:nth-of-type(3):before { content: "Name of Document"; } 
        td:nth-of-type(4):before { content: "File"; }
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Joining Docket Upload</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Document Type', 'cerificateType', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-10">
                                            <?php
                                            $cerificateTypeDropdown = $cerificateTypes;
                                            echo form_dropdown('cerificateType', $cerificateTypeDropdown, $selectedCerificateType, array(
                                                'class' => 'form-control select2',
                                                'data-validation' => 'required',
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('cerificateType'); ?> </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Name of Document', 'nameOfCertificates', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-10">  <?php
                                            $nameOfCertificatesText = array(
                                                'name' => 'nameOfCertificates',
                                                'id' => 'nameOfCertificates',
                                                'class' => 'form-control datepicker',
                                                'value' => set_value('nameOfCertificates'),
                                                'data-validation' => 'required',
                                            );

                                            echo form_input($nameOfCertificatesText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('nameOfCertificates'); ?> </span>
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
                                    <th>Type</th>
                                    <th>Name of Document</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                foreach ($myCertificates as $myCertificate) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $myCertificate['type_name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $myCertificate['name_of_certificate'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo anchor(base_url().'index.php/docUrl/docUrlGenerator/'.$myCertificate['filename'], $myCertificate['filename'],'target="_blank"');
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $("#certificateTable").dataTable();
    });
    $.validate({
        lang: 'en'
    });
</script>

