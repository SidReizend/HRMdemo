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
                'class' => 'form-horizontal',
                'id' => 'showCertificates',
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Joining Docket</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <?php
                                            $talentsDropdown = $talentsUnderReportManager;
                                            echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                                'class' => 'form-control select2',
                                                'id' => 'talent'
                                            ));
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                        </div>
                                        <div class="col-sm-8">
                                        </div>
                                        <div class="material-switch col-sm-2">
                                            <br/>
                                            Sign On <input name="resigned" value="1" id="resigned" class="someSwitchOption001"
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
                                            echo anchor(base_url().'index.php/docUrl/docUrlGenerator/'.$myCertificate['filename'], $myCertificate['filename'], 'target="_blank"');
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
        $("body").on("change", "#resigned", function (e) {
            if ($(this).prop("checked") == true) {
                location.href = "resignedTalents";
            } else {
                location.href = "signOnTalents";
            }
        });
    });
    $("body").on("change", "#talent", function (e) {
        $("#showCertificates").submit();
    });
    $.validate({
        lang: 'en'
    });
</script>

