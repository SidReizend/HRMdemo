<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Ongoing Marketing Efforts"; } 
        td:nth-of-type(2):before { content: "Stage"; } 
        td:nth-of-type(3):before { content: "Value"; } 
        td:nth-of-type(4):before { content: "Project Type"; } 
        td:nth-of-type(5):before { content: "Comments"; } 
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
                            <h3 class="box-title">Pipeline</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('Pipeline/Add', 'Add', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Ongoing Marketing Efforts</th>
                                        <th>Stage</th>
                                        <th>Proposed Value</th>
                                        <th>Approved Value</th>
                                        <th>Project Type</th>
                                        <th>Comments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($allProjects as $project) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $project['ongoing_projects'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['stage'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['value']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo number_format($project['approved_value']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['project_type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['comments'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('Pipeline/Edit/id/' . $project['id'], 'Edit', array('class' => 'btn btn-default'));
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