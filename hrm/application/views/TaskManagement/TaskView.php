<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Task"; } 
        td:nth-of-type(3):before { content: "Desc"; } 
        td:nth-of-type(4):before { content: "Actions"; } 
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
                            <h3 class="box-title">Task Creation</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('TaskManagementCntrl/AddTask', 'Add Task', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Task Code</th>
                                        <th>Description</th>
                                        <th>Billable</th>  
                                        <th>Is Travel Task</th>
                                        <th>Parent Project</th>
                                        <th>Parent Task</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
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
        var base_url = '<?php echo base_url(); ?>';
        $('#datatable').DataTable({
            "autoWidth": true,
            "processing": true,
            "serverSide": true,
            "ajax": base_url + "index.php/TaskManagementCntrl/getTaskByPagination"
        });
    });
</script>