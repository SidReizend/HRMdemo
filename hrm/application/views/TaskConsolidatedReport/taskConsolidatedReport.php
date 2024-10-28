<?php
$datepicker = 1;
$datatables = 1;
$alertify = 1;
$select2 = 1;
$chartjs = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
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
                            <h3 class="box-title">Task Consolidated Report</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <input type="text" id="taskCode" class="form-control"/>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <button class="btn btn-primary" id="fetchData">Fetch</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12" id="show_data_task">

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
        $('#fetchData').on('click', function (e) {
            var taskCode = $('#taskCode').val();
            jQuery.ajax({
                type: "get",
                url: base_url + "index.php/TimePredictionCntrl/taskConsolidatedAjax",
                data: {
                    taskCode: taskCode
                },
                success: function (response) {
                    $('#show_data_task').html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });
    });

</script>
