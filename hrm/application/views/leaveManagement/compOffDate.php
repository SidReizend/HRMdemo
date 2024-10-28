<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Date"; } 
        td:nth-of-type(3):before { content: "Used/Not used"; } 
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
                            <h3 class="box-title">Comp-off</h3>
                        </div>
                        <div class="box-body">

                            <div class="form-group">
                                <div class="col-sm-2">
                                    <?php
                                    echo form_open('', array(
                                        'class' => 'form-horizontal',
                                        'id' => 'showLeave',
                                    ));
                                    echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                        'class' => 'form-control select2 selectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'talent',
                                    ));
                                    echo form_close();
                                    ?>
                                </div>
                            </div>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Date</th>
                                        <th>Used/Not Used</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($creditDetailsComp as $comp) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $currentCompOffDate = date('d/m/Y', strtotime($comp['date_of_compoff']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($comp['used'] == 1) {
                                                    echo 'Used';
                                                } else {
                                                    echo 'Not used';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($comp['used'] != 1) {
                                                    echo anchor('#', 'Edit', array(
                                                        'class' => 'manage',
                                                        'data-toggle' => 'modal',
                                                        'data-id' => $comp['id'],
                                                        'data-compdate' => $currentCompOffDate,
                                                        'data-target' => '#manageModal',
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
<div class="modal fade" id="manageModal" tabindex="-1" role="dialog" aria-labelledby="donateModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="donateModalLabel">Manage Comp Off Date</h4>
            </div>
            .
            <div class="modal-body">
                <div class="box box-widget">
                    <div class='box-body' id="attendanceMapping">
                        <?php
                        echo form_open('talentLeaveLeft/compOffDate', array(
                            'class' => 'form-horizontal',
                            'id' => 'compForm',
                        ));
                        ?>
                        <div class="col-sm-12">
                            <?php
                            echo form_label('Current Date', 'currentDate', array(
                                'class' => 'col-sm-4 control-label'
                            ));
                            echo form_label('', 'currentDateValue', array(
                                'class' => 'col-sm-2 control-label',
                                'id' => 'currentDateValue'
                            ));
                            ?>
                        </div>
                        <br/>
                        <br/>
                        <div class="col-sm-12">
                            <?php
                            echo form_label('New Date', '', array(
                                'class' => 'col-sm-4 control-label'
                            ));
                            ?>
                            <div class="col-sm-4">  <?php
                                $fromDateText = array(
                                    'name' => 'newDate',
                                    'id' => 'newDate',
                                    'placeholder' => 'dd/mm/YYYY',
                                    'class' => 'form-control datepicker daysLoad',
                                    'value' => ''
                                );
                                echo form_input($fromDateText);
                                ?>
                            </div>
                            <input type="hidden" name="compOffId" id="compOffId" value="">
                        </div>
                        <br/>
                        <br/>
                        <br/>
                        <div class="col-sm-12">
                            <div class="col-sm-4 control-label">
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <?php
                                $submitButton = array(
                                    'type' => 'submit',
                                    'content' => 'Submit',
                                    'class' => 'btn btn-small btn-primary'
                                );
                                echo form_button($submitButton)
                                ?>
                            </div>
                            <!-- /.col -->
                        </div>
                        <?php
                        form_close();
                        ?>
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
    $(document).ready(function () {
        $('.table').DataTable();
        $(".select2").select2();
        //Date picker
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        })
        $("body").on("change", ".selectOptions", function (e) {
            $("#showLeave").submit();
        });
        $("body").on("click", ".manage", function (e) {
            var compOffDate = $(this).data('compdate');
            var id = $(this).data('id');
            $("#currentDateValue").html(compOffDate);
            $("#newDate").val(compOffDate);
            $("#compOffId").val(id);

        });
    });
</script>