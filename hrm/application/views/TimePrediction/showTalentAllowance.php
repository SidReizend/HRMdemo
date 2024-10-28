<?php
$select2 = 1;
$datepicker = 1;
$alertify = 1;
$datatables = 1;
include_once APPPATH . 'views/header.php';
?>

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
                            <h3 class="box-title">Talent Allowance</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl', array(
                                'class' => 'form-horizontal',
                                'id' => 'showTalentAllowance'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <?php
                                    echo form_label('Year', 'Year', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3">  <?php
                                        $toDateText = array(
                                            'name' => 'year',
                                            'id' => 'year',
                                            'autocomplete' => 'off',
                                            'placeholder' => 'YYYY',
                                            'class' => 'form-control datepicker'
                                        );

                                        echo form_input($toDateText);
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('year'); ?> </span>
                                    </div>
                                    <?php
                                    echo form_label('Month', 'month', array(
                                        'class' => 'col-sm-2 control-label'
                                    ));
                                    ?>
                                    <div class="col-sm-3">  <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, '', array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('month'); ?> </span>
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
                            <div id="showTalentAllowancedetails">
                                <table id="table_responsive" class="table table-bordered table-hover" >
                                    <thead style="text-align:center;">
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Talent</th>
                                            <th>Food Allowance</th>
                                            <th>Food And Travel Allowance</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                    </tbody>
                                </table>
                            </div>
                            <div class="overlay" id="overlay1" style="display:none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal" id="modelshowbody">
    <div class="modal-dialog" style="width:50% !important">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Allowance Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="show_allowance_details">                                    
                </div>


            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $(".select2").select2({
            placeholder: "Month",
            dropdownAutoWidth: false,
            width: '100%'
        });
        $('.datepicker').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        }).on('change', function () {

        });
        $("body").on("change", "#month", function (e) {
            e.preventDefault();
            showTalentAllowance();
        });
        $("body").on("change", "#year", function (e) {
            e.preventDefault();
            showTalentAllowance();
        });

        //get talent allowance details in selected year and month
        function showTalentAllowance() {
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            if (year === '' || month === "") {
                $("#overlay1").hide();
                return false;
            }
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/getTalentAllowance",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month
                },
                success: function (response) {
                    $("#showTalentAllowancedetails").html(response);
                     $('#table_responsive').DataTable();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                    $("#overlay1").hide();
                }

            });
            $("#overlay1").hide();
        }
        $("body").on("click", "#allowance_details", function (e) {
            e.preventDefault();
            $("#overlay1").show();
            var year = $("#year").val();
            var month = $("#month").val();
            var talent = $(this).data('talent');
            var talentname = $(this).data('talentname');


            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/TimePredictionCntrl/getAllowanceDetails",
                type: "get", //send it through get method
                data: {
                    year: year,
                    month: month,
                    talent: talent,
                    talentname:talentname

                },
                success: function (response) {
                    $("#show_allowance_details").html(response);
                    $("#modelshowbody").modal('show');
                    $('.popupAllowance').DataTable();
                    $("#overlay1").hide();
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }

            });
            $("#overlay1").hide();
        });

    });


</script>