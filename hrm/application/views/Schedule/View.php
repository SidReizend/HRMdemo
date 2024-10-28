<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Schedule"; } 
        td:nth-of-type(2):before { content: "From"; } 
        td:nth-of-type(3):before { content: "To"; } 
        td:nth-of-type(4):before { content: "Comments"; } 
        td:nth-of-type(5):before { content: "Actions"; } 
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
                            <h3 class="box-title">Schedule</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('Schedule/Add', 'Add', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Schedule</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Comments</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($schedules as $project) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo $project['schedule'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['from'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['to'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['comments'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('Schedule/Edit/id/' . $project['id'], 'Edit', array('class' => 'btn btn-default'));
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
        
    });
</script>