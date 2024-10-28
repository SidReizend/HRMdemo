<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "From Date"; } 
        td:nth-of-type(3):before { content: "To Date"; } 
        td:nth-of-type(4):before { content: "No:date"; } 
        td:nth-of-type(5):before { content: "Request"; } 
        td:nth-of-type(6):before { content: "Approval"; } 
        td:nth-of-type(7):before { content: "Details"; } 
        td:nth-of-type(8):before { content: "Actions"; } 
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
                            <h3 class="box-title">Leave Applications</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('myLeaves/addNewLeaves', 'Apply Leave', array(
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
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>No. of Leaves</th>
                                        <th>Request</th>
                                        <th>Approval</th>
                                        <th>Details</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($myLeavesDetails as $myLeaves) {
                                        $tooltip_text = "";
                                        if($myLeaves['considered']==0){ $tooltip_text = " Planned Leave. ";}
                                        $tooltip_text .= $myLeaves['note'];

                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $tooltip_text; ?>"
                                            <?php if($myLeaves['considered']==0){ echo 'class="bg-gray"';} ?>>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($myLeaves['from_date'] != '0000-00-00')
                                                echo date('d/m/Y', strtotime($myLeaves['from_date']));
                                                ?>
                                            </td>
                                            </td>
                                            <td>
                                                <?php
                                                if($myLeaves['to_date'] != '0000-00-00')
                                                echo date('d/m/Y', strtotime($myLeaves['to_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $leaveCount = $this->leaves->leaveCountEachForLeaveId($myLeaves['id']);
                                                echo $leaveCount;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $myLeaves['request'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($myLeaves['is_approved'] == 0) {
                                                    echo 'Pending';
                                                } else if ($myLeaves['is_approved'] == 1) {
                                                    echo 'Approved';
                                                } else if ($myLeaves['is_approved'] == 2) {
                                                    echo 'Declined';
                                                } else if ($myLeaves['is_approved'] == 3) {
                                                    echo 'Cancelled';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($myLeaves['to_date'] != '0000-00-00')
                                                echo anchor('#', 'View Details',
                                                    array('class' => 'view_details',
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#uploadModal',
                                                        'data-tid' => $myLeaves['id']
                                                    ));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($myLeaves['is_approved'] == 0) {
                                                    echo anchor('MyLeaves/cancelMyLeave/myleaveId/' . $myLeaves['id'], '<i class="fa fa-fw fa-trash-o"></i>');
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
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="donateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="donateModalLabel">Leave Details</h4>
            </div>
            .
            <div class="modal-body">
                <div class="box box-widget">
                    <div class='box-body' id="preview">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- /.modal -->
<?php
include_once APPPATH . 'views/footer.php';
?>


<script>
    $('#myOdTable').DataTable();
    $("body").on("click", ".view_details", function (e) {
        e.preventDefault();
        var id = $(this).data('tid');
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/LeaveApproval/view_details",
            type: "get", //send it through get method
            data: {
                tid: id
            },
            success: function (response) {
                $("#preview").html(response);
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });

    });
</script>