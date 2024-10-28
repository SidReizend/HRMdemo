<?php
$datatables = 1;
$select2 = 1;
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
                            <h3 class="box-title">All Talent Monthly Report</h3>
                        </div>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="box-body">
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-1">
                                        <label for="Year" class="control-label">Year</label>           
                                    </div>
                                    <div class="col-sm-4"> 
                                        <input type="text" readonly name="year" value="<?php echo $year; ?>" id="year" class="form-control" 
                                               data-validation="required">
                                        <span class="text-danger">  </span>
                                    </div>
                                    <div class="col-sm-1">
                                    </div>
                                    <div class="col-sm-1">
                                        <label for="Month" class="col-sm-2 control-label">Month</label>                     
                                        <span class="text-danger">  </span>
                                    </div>
                                    <div class="col-sm-4"> 
                                        <input type="text" readonly name="month" value="<?php echo $month; ?>" id="month" class="form-control" 
                                               data-validation="required">
                                        <span class="text-danger">  </span>
                                    </div>
                                    <div class="col-xs-4">
                                    </div>
                                    <div class="col-xs-4">
                                        <br/>
                                        <button type="button" class="btn btn-small btn-primary" <?php echo $disabled; ?> id="process">Process</button>
                                    </div>
                                    <br/>
                                </div>
                                <div class="box-body" id="talentDiv">
                                </div>
                            </div>
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
    $("body").on("click", "#process", function (e) {
        $('#process').prop("disabled", true);
        var year = $("#year").val();
        var month = $("#month").val();
        fetchTalents(year, month);
    });

    function fetchTalents(year, month) {
        $("#overlay1").show();
        $.ajax({
            url: base_url + "index.php/allTalentsMonthlyReport/triggerProcess",
            type: "post", //send it through get method
            dataType: 'json',
            data: {
                year: year,
                month: month,
            },
            success: function (response) {
                if (response.status === 'success') {
                    $("#talentDiv").html(response.view);
                    var selectTalent = $(".talentPending").attr('data-id');
                    triggerSalaryProcess(selectTalent, year, month);
                }
                if (response.status === 'done') {
                    $("#overlay1").hide();
                    $("#talentDiv").html("");
                    alertify.success(response.msg);
                }
                if (response.status === 'error') {
                    $("#overlay1").hide();
                    alertify.error(response.msg);
                }
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        })
    }

    function triggerSalaryProcess(talentid, year, month) {
        $.ajax({
            url: base_url + "index.php/allTalentsMonthlyReport/runProcess",
            type: "post", //send it through get method
            dataType: 'json',
            data: {
                talent: talentid,
                year: year,
                month: month
            },
            success: function (response) {
                if (response.status === 'success') {
                    fetchTalents(year, month);
                }
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    }
</script>
