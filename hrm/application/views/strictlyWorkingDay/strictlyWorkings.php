<?php
$datepicker = 1;
$form_validator = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Day"; }
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open('StrictlyWorkingDay/addStrictWorking', array(
                'class' => 'form-horizontal'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Dates to Work</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php
                                        echo form_label('Date to Work', 'dateToWork', array(
                                            'class' => 'col-sm-2 control-label'
                                        ));
                                        ?>
                                        <div class="col-sm-10">  <?php
                                            $dateToWorkText = array(
                                                'name' => 'dateToWork',
                                                'id' => 'dateToWork',
                                                'class' => 'form-control datepicker',
                                                'value' => set_value('dateToWork'),
                                                'data-validation' => 'required',
                                            );

                                            echo form_input($dateToWorkText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('dateToWork'); ?> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2 control-label">
                                </div>
                                <!-- /.col -->
                                <div class="col-xs-10">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Save',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                                <!-- /.col -->
                            </div>
                        </div>
                        <table id="table_responsive" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Date to Work</th>
                                    <th>Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $serialNumber = 0;
                                foreach ($strictWorkingDays as $strictWorkingDay) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo ++$serialNumber;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('d/m/Y',  strtotime($strictWorkingDay['date_to_work']));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo date('l',  strtotime($strictWorkingDay['date_to_work']));
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        echo form_close();
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- /.container -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
    });
    $.validate({
        lang: 'en'
    });
</script>

