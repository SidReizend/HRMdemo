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
        td:nth-of-type(3):before { content: "Name"; } 
        td:nth-of-type(4):before { content: "Action"; } 
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
                            <h3 class="box-title">Holidays</h3>
                        </div>
                        <div class="overlay" id="overlay1" style="display:none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <div class="box-body">
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($holidayDetails as $holiday) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($holiday['date_of_holiday']));
                                                ?>
                                            </td>
                                            <td class="talentWorking" data-id="<?php echo $holiday['id']; ?>">
                                                <?php
                                                echo $holiday['name_of_holiday'];
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
<div class="modal" id="modelshowbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Talent allowed to apply</h4>
            </div>
            <div class="modal-body">
                <div class="box-body"> 

                    <?php
//echo validation_errors();
                    echo form_open('', array(
                        'class' => 'form-horizontal'
                    ));
                    ?>
                    <div class="row">
                        <!-- left column -->
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Talents', 'talent', array(
                                    'class' => 'col-sm-2 control-label'
                                ));
                                ?>
                                <div class="col-sm-8">
                                    <?php
                                    $talents_dropdown = $talents;
                                    echo form_dropdown('talent', $talents_dropdown, "", array(
                                        'class' => 'form-control select2',
                                        'data-validation' => 'required',
                                        'id' => 'talent'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                                <button class="btn btn-primary saveTalent" type="button">Add Talent</button>
                                <input type="hidden" id="holidayId" value="">
                            </div>
                        </div>
                        <?php
                        echo form_close();
                        ?>
                    </div>
                </div>
            </div>
            <div class="box-body" id="respDiv"> 
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        $('#holidayTable').DataTable();
        $("body").on("click", ".cancel_events", function (e) {
            e.preventDefault();
            var link = $(this).attr('href');
            alertify.confirm("Are you sure", function (e) {
                if (e) {
                    alertify.success("You've clicked OK");
                    location.href = link;

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
        $("body").on("click", ".talentWorking", function (e) {
            var id = $(this).attr('data-id');
            $("#holidayId").val(id);
            fetchListTable(id);
        });

        function fetchListTable(id) {
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/holidays/talentAllowedToApply",
                type: "post", //send it through get method
                data: {
                    id: id,
                },
                success: function (response) {
                    $("#respDiv").html(response);
                    $("#modelshowbody").modal('show');
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        }

        $("body").on("click", ".saveTalent", function (e) {
            $("#overlay1").show();
            var holidayId = $("#holidayId").val();
            var talent = $("#talent").val();
            if (!talent) {
                alertify.error("Please select talent");
                return;
            }
            $.ajax({
                url: base_url + "index.php/holidays/addTalent",
                type: "post", //send it through get method
                dataType: 'json',
                data: {
                    holidayId: holidayId,
                    talent: talent
                },
                success: function (response) {
                    if (response.status === 'error') {
                        alertify.error(response.msg);
                    } else {
                        $("#talent").val('');
                        $("#talent").trigger('change');
                        alertify.success(response.msg);
                        fetchListTable(holidayId);
                    }
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
        });
    });
</script>

