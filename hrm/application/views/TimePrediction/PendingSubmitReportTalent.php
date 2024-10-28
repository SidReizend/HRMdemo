<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Date"; } 
        td:nth-of-type(5):before { content: "Action"; } 
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
                            <h3 class="box-title">My Pending Sheets</h3>
                        </div>
                        <div class="box-body">

                            <?php
                            //echo validation_errors();
                            echo form_open('TimePredictionCntrl/HomeDataApproval', array(
                                'class' => 'form-horizontal',
                                'id' => 'eventForm'
                            ));
                            ?>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <?php
                                        $selectedMonth = date('m');
                                        $selectedYear = date('Y');
                                        $yearDropDown = $years;
                                        echo form_dropdown('year', $yearDropDown, $selectedYear, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'year',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>
                                    <div class="col-sm-6    ">
                                        <?php
                                        $monthDropDown = $months;
                                        echo form_dropdown('month', $monthDropDown, $selectedMonth, array(
                                            'class' => 'form-control select2 selectOptions talentChoose',
                                            'data-validation' => 'required',
                                            'id' => 'month',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
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
                            <div class="overlay" id="overlay1" style="display:none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <div class="col-sm-12" id="change_data">
                                <table id="table_responsive" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sl No.</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $serialNumber = 0;
                                        foreach ($PendingSubmitReport as $data) {
                                            ?>
                                            <tr>
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
                                                    echo "<a href='AddNewPrediction/date/" . $data['date'] . "' class='btn btn-info' >Add</a>";
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
            </div>
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $("body").on("change", ".talentChoose", function (e) {
            month = $("#month").val();
            year = $("#year").val();
            if ((month != "") && (year != "")) {

                $("#overlay1").show();
                base_url = '<?php echo base_url(); ?>';
                $.ajax({
                    url: base_url + "index.php/TimePredictionCntrl/PendingSubmitReportTalentAjax",
                    type: "get", //send it through get method
                    data: {
                        month: month,
                        year: year
                    },
                    success: function (response) {
                        $("#change_data").html(response);
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
        });
    });
</script>
