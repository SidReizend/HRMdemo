<?php
$datepicker = 1;
$datatables = 1;
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No"; } 
        .notitle>td:nth-of-type(7):before { content: "Month"; } 
        .notitle>td:nth-of-type(7):before { content: "Year"; } 
        .notitle>td:nth-of-type(5):before { content: "Total Time"; } 
    }
    tr.header
    {
        cursor:pointer;
    }
</style>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container-fluid">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Task Split Report</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <?php
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2    ">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-2">

                                        <button type="button" class="btn btn-primary" id="calculate">
                                            Calculate
                                        </button>
                                    </div>

                                </div>

                            </div> 
                            <br/>
                            <br/>
                            <br/>
                            <div class="col-sm-12" id="show_data">

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
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    var base_url = '<?php echo base_url(); ?>';
    $(document).ready(function () {
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });

        $("body").on("click", "#calculate", function (e) {
            change_table();

        });
        function change_table() {
            var month = $("#month").val();
            var year = $("#year").val();

            if ((month != "") && (year != "")) {
                $("#overlay1").show();
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/calculateSplitReport",
                    type: "get", //send it through get method
                    data: {
                        month: month,
                        year: year
                    },
                    success: function (response) {
                        $("#show_data").html(response);
                        $('#table_responsive').DataTable();
                    },
                    error: function (xhr) {
                        //Do Something to handle error
                        alertify.alert("some error found");
                    }
                }).complete(function () {
                    $("#overlay1").hide();
                });
            }
        }
    });
</script>