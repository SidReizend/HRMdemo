<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Reason of OD"; } 
        td:nth-of-type(3):before { content: "OD Type"; } 
        td:nth-of-type(4):before { content: "From Date"; } 
        td:nth-of-type(5):before { content: "To Date"; } 
        td:nth-of-type(6):before { content: "Status"; } 
        td:nth-of-type(7):before { content: "Actions"; }
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
                            <h3 class="box-title">OD Applications</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('myOds/addNewMyOd', 'Apply OD', array(
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
                                    foreach ($myOdDetails as $myOd) {
                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $myOd['decline_note']; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myOd['reason'];
                                                ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myOd['od_type_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myOd['from_date'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myOd['to_date'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($myOd['is_approved'] == 0) {
                                                    echo 'Pending';
                                                } else if ($myOd['is_approved'] == 1) {
                                                    echo 'Approved';
                                                } else if ($myOd['is_approved'] == 2) {
                                                    echo 'Declined';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('MyOds/editMyOd/MyOdId/' . $myOd['id'], 'Edit',array(
                                                    'class' => 'btn btn-small btn-primary'
                                                ));
                                                if ($myOd['is_approved'] == 0) {
                                                    echo '&nbsp';
                                                    echo anchor('MyOds/declainOd/' . $myOd['id'] , 'Cancel', array(
                                                        'class' => 'btn btn-small btn-danger cancel_events'
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
    $('#myOdTable').DataTable();
</script>