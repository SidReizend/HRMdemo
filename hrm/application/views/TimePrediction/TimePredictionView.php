<?php
$datepicker = 1;
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Date"; } 
        td:nth-of-type(3):before { content: "Projects"; } 
        td:nth-of-type(4):before { content: "Time"; } 
        td:nth-of-type(5):before { content: "Status"; } 
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
                            <h3 class="box-title">Time Management</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('TimePredictionCntrl/AddNewPrediction', 'Add New Time', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <?php
                                    if (isset($post_fromdate)) {
                                        $fromdate = $post_fromdate;
                                        $todate = $post_todate;
                                    } else {
                                        $todate = date('t/m/Y');
                                        $fromdate = date('01/m/Y');
                                    }
                                    echo form_label('From Date', 'Date', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3">  <?php
                                        $fromDateText = array(
                                            'name' => 'fromdate',
                                            'id' => 'fromdate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => $fromdate,
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <?php
                                    echo form_label('To Date', 'Date', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3">  <?php
                                        $fromDateText = array(
                                            'name' => 'todate',
                                            'id' => 'todate',
                                            'placeholder' => 'dd/mm/YYYY',
                                            'class' => 'form-control datepicker daysLoad',
                                            'value' => $todate,
                                        );

                                        echo form_input($fromDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">
                                        <?php
                                        $submitButton = array(
                                            'type' => 'submit',
                                            'content' => 'Search',
                                            'class' => 'btn btn-small btn-primary'
                                        );
                                        echo form_button($submitButton)
                                        ?>
                                    </div>

                                </div>
                            </div> 
                            <br/>
                            <br/>
                            <br/>
                            <?php
                            //echo $this->db->last_query();
                            echo form_close();
                            ?>

                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Date</th>
                                        <th>Projects</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($HomeGetTotalData as $data) {
                                        ?>
                                        <tr data-toggle="tooltip" title="" data-original-title="<?php echo $data['reason']; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($data['date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $ProjectsOfDate = $this->TimePredictionModel->ProjectsOfDate($data['date'], $data['talent'], $data['is_approved']);
                                                foreach ($ProjectsOfDate as $project) {
                                                    echo '<button class="btn btn-info">' . $project['project_code'] . '</button>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $TimeOfDate = $this->TimePredictionModel->TimeOfDate($data['date'], $data['talent'], $data['is_approved']);
                                                $time_array = array();
                                                foreach ($TimeOfDate as $time) {
                                                    $time_array[] = $time['time'];
                                                }
                                                echo $this->TimePredictionModel->sum_time($time_array);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($data['is_approved'] == 0) {
                                                    $status = "Created";
                                                    $class = "warning";
                                                    $button = "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Edit</a>";
                                                }
                                                if ($data['is_approved'] == 2) {
                                                    $status = "Cancelled";
                                                    $class = "danger";
                                                    $button = "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Edit</a>";
                                                } else if ($data['is_approved'] == 3) {
                                                    $status = "Declined";
                                                    $class = "danger";
                                                    $button = "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Edit</a>";
                                                }
                                                if ($data['send_approval'] == 1) {
                                                    $status = "Approval Send";
                                                    $class = "default";
                                                    $button = "<a href='TimePredictionCntrl/CancelApproval/id/" . $data['id'] . "' class='btn btn-danger' >Cancel</a>";
                                                }
                                                if ($data['is_approved'] == 1) {
                                                    $status = "Approved";
                                                    $class = "success";
                                                    $button = "<a href='TimePredictionCntrl/AddNewPrediction/date/" . $data['date'] . "' class='btn btn-success' >View</a>";
                                                }
                                                echo '<button class="btn btn-' . $class . '">' . $status . '</button>';
                                                ?> 
                                            </td>
                                            <td>
                                                <?php echo $button; ?>
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

        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        });

        $('#myOdTable').DataTable();
    });
</script>