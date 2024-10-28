<?php
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Project Code"; } 
        td:nth-of-type(3):before { content: "Name"; } 
        td:nth-of-type(4):before { content: "Description"; } 
        td:nth-of-type(5):before { content: "Type"; } 
        td:nth-of-type(6):before { content: "From Date"; } 
        td:nth-of-type(7):before { content: "To Date"; } 
        td:nth-of-type(8):before { content: "Status"; } 
        td:nth-of-type(9):before { content: "Action"; } 
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
                            <h3 class="box-title">Projects</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('project/addNewProject', 'Add New', array(
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
                                        <th>Project Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Billable</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($projectDetails as $project) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['project_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['description'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['type_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($project['from_date'] != '0000-00-00')
                                                echo date('d/m/Y',  strtotime($project['from_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($project['to_date'] != '0000-00-00')
                                                echo date('d/m/Y',  strtotime($project['to_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['status_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $project['billable'] == 1 ? 'Yes' : 'No';
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('project/editProject/' . $project['id'], '<i class="fa fa-fw fa-edit"></i>');
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
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $('#roleTable').DataTable();
</script>

