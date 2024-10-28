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
        td:nth-of-type(6):before { content: "Uploaded On"; } 
        td:nth-of-type(7):before { content: "File"; }
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
                            <h3 class="box-title">My Salary Slips</h3>
                        </div>
                        <table id="table_responsive" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Talent</th>
                                    <th>Year</th>
                                    <th>Month</th>
                                    <th>Note</th>
                                    <th>Uploaded On</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                foreach ($salarySlips as $salarySlip) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['year'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['month'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $salarySlip['note'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('d/m/Y H:i:s',  strtotime($salarySlip['date_of_upload']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo anchor(base_url() . 'index.php/docUrl/docUrlGenerator/'.$salarySlip['file_name'], $salarySlip['file_name'],'target="_blank"');
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
    });
    $.validate({
        lang: 'en'
    });
</script>

