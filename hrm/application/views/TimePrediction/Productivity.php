<?php
$alertify = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No"; } 
        .notitle>td:nth-of-type(2):before { content: "Date"; } 
        .notitle>td:nth-of-type(3):before { content: "Productivity"; } 
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
                            <h3 class="box-title">Cost Matrix</h3>
                        </div>
                        <div class="box-body">    
                            <div class="form-group">
                                <div class="col-sm-4"></div>
                                <?php
                                echo form_label('Date', 'fromDate', array(
                                    'class' => 'col-sm-1 control-label'
                                ));
                                ?>
                                <div class="col-sm-2">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker',
                                        'value' => date('d/m/Y'),
                                        'data-validation' => 'required',
                                        'readonly' => 'readonly',
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('fromDate'); ?> </span>
                                </div>
                            </div>
                            <br/>
                            <br/>
                            <div id="productivityDiv">
                                <?php
                                foreach ($talents as $talent) {
                                    $prdtty = $this->TimePredictionModel->Productivity($talent['id'], date('Y-m-d'));
                                    ?>
                                    <div class="form-group">
                                        <?php
                                        echo form_label($talent['name'], 'TaskCode', array(
                                            'class' => 'col-sm-2 control-label talentName',
                                            'data-talent' => $talent['id'],
                                            'data-toggle' => "modal",
                                            'data-target' => "#productivityModal",
                                            'data-name' => $talent['name'],
                                            'style' => 'cursor:pointer'
                                        ));
                                        ?>
                                        <div class="col-sm-4">  <?php
                                            $fromDateText = array(
                                                'name' => 'taskcode',
                                                'id' => 'taskcode',
                                                'class' => 'form-control txtpdt',
                                                'required' => 'required',
                                                'value' => $prdtty,
                                                'type' => 'text',
                                                'data-talent' => $talent['id']
                                            );

                                            echo form_input($fromDateText);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('taskcode'); ?> </span>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div id="productivityModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="talentNameHeading">

                </h4>
                <br/>
                <div class="form-group" id="userProductivityDiv">
                </div>
            </div>
            <br/>
        </div>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        showProductivity();
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) {
            showProductivity();
        });
        function showProductivity() {
            var date = $('#fromDate').val();
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/showProductivity",
                type: "get", //send it through get method
                data: {
                    date: date
                },
                success: function (response) {
                    $('#productivityDiv').html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        }
        $("body").on("change", ".txtpdt", function (e) {
            var date = $('#fromDate').val();
            var productivity = $(this).val();
            var talent = $(this).data('talent');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/ProductivityUpdate",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    date: date,
                    productivity: productivity
                },
                success: function (response) {
                    if (response == 1) {
                        alertify.success("updated")
                    } else {
                        alertify.error("Error");
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });

        });
        $("body").on("click", ".talentName", function (e) {
            var talent = $(this).data('talent');
            var name = $(this).data('name');
            $('#talentNameHeading').html(name);
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/getProductivityLog",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                },
                success: function (response) {
                    $('#userProductivityDiv').html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });



    });
</script>