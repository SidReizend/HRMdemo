<?php
$datatables = 1;
$datepicker = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Date"; } 
        td:nth-of-type(3):before { content: "Talent"; } 
        td:nth-of-type(4):before { content: "Type"; } 
        td:nth-of-type(5):before { content: "Reviews"; } 
        td:nth-of-type(6):before { content: "Recorded By"; } 
        td:nth-of-type(7):before { content: "Action"; } 
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
                            <h3 class="box-title">Interaction Track</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('interactionTrack/addNewInteraction', 'Add New', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/><br/>
                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'searchForm'
                            ));
                            ?>
                            <div class="form-group">                                
                                <div class="col-sm-2">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $fromDate,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                </div>
                                <div class="col-sm-2">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $toDate,
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('talent', $talents, $selectedtalent, array(
                                        'class' => 'form-control select2',
                                        'id' => 'talent',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('type', $types, $selectedtype, array(
                                        'class' => 'form-control select2',
                                        'id' => 'type',
                                    ));
                                    ?>
                                </div>

                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('manager', $managers, $selectedmanager, array(
                                        'class' => 'form-control select2',
                                        'id' => 'manager',
                                    ));
                                    ?>
                                </div>
                                
                                <div class="col-sm-2">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Search',
                                        'id' => 'submit',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                            </div>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Date</th>
                                        <th>Talent</th>
                                        <th>Type</th>
                                        <th>Reviews</th>
                                        <th>Recorded By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($interactionDetails as $interaction) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if($interaction['from_date'] != '0000-00-00')
                                                echo date('d/m/Y',  strtotime($interaction['from_date']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $interaction['talent'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $interaction['type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $interaction['description'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $interaction['reporting_manager'];
                                                ?>
                                            </td>                                            
                                            <td>
                                                <?php
                                                echo anchor('interactionTrack/editInteraction/' . $interaction['id'], '<i class="fa fa-fw fa-edit"></i>');
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
    $(document).ready(function () {
        $('#roleTable').DataTable();
        $(".select2").select2();
    });
    $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        }).on('changeDate', function (e) {
            formSubmittion();
        });
</script>

