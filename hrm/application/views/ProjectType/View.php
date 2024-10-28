<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Project Type"; } 
        td:nth-of-type(2):before { content: "Schedule"; } 
        td:nth-of-type(3):before { content: "Targeted Value"; } 
        td:nth-of-type(4):before { content: "Date of target accomplishment"; } 
        td:nth-of-type(5):before { content: "Valid Till"; } 
        td:nth-of-type(6):before { content: "Actions"; } 
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
                            <h3 class="box-title">Project Type</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('ProjectType/Add', 'Add', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Project Type</th>
                                        <th>Schedule</th>
                                        <th>Targeted Value</th>
                                        <th>Date of target accomplishment</th>
                                        <th>Valid Till</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($projectTypes as $project) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $project['project_type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['schedule'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['targeted_value']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($project['target_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($project['valid_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('ProjectType/Edit/id/' . $project['id'], 'Edit', array('class' => 'btn btn-default'));
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
    });
</script>