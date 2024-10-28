<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "OD Type"; } 
        td:nth-of-type(3):before { content: "Action"; } 
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
                            <h3 class="box-title">OD Types</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('odTypes/addNewOdType', 'Add New', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>OD Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($odTypeDetails as $odType) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $odType['od_type_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('odTypes/editOdType/odTypeId/' . $odType['id'], '<i class="fa fa-fw fa-edit"></i>');
                                                echo '&nbsp&nbsp&nbsp&nbsp';
                                                echo anchor('odTypes/deleteOdType/odTypeId/' . $odType['id'], '<i class="fa fa-fw fa-times"></i>', array( 'class' => 'cancel_events'));
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
        $('#odTypeTable').DataTable();
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
</script>