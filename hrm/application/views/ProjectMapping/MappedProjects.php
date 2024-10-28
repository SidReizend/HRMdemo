<?php
$full_calendar=1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            //echo validation_errors();
            echo form_open('ProjectMappingCntrl', array(
                'class' => 'form-horizontal',
                'id' => 'eventForm'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mapped Projects</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-md-12" >
                                <div class="box box-primary" >
                                    <div class="box-body no-padding" id="recievedata" style="overflow-x:auto;">
                                        <!-- THE CALENDAR -->
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="overlay" id="overlay1" style="display:none;">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                </div>
                                <!-- /. box -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<input type="hidden" id="talent_id" value="<?php echo $talent; ?>">
<div class="modal" id="modelshowbody">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <div class="box-body" id="show_details">                                    
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
echo form_close();
?>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $(document).ready(function () {
        var talent_id = $("#talent_id").val();
         changedata(talent_id);
        function changedata(talent, dat = "") {
            $("#overlay1").show();
            $.ajax({
                url: base_url + "index.php/ProjectMappingCntrl/TalentWiseViewAjax",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    date: dat,
                    mapping:0
                },
                success: function (response) {
                    $("#recievedata").html(response);
                },
                error: function (xhr) {
                    $("#recievedata").html('');
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                $("#overlay1").hide();
            });
            ;
        }
  
        $("body").on("click", ".cngDate", function (e) {
            var talent_id = $("#talent_id").val();
            var dat = $(this).data('date');
            if (talent_id != "") {
                changedata(talent_id, dat);
            } else {
                $("#recievedata").html('');
            }

        });
        $("body").on("click", ".click_show", function (e) {
            var shw = $(this).data('shw');
            base_url = '<?php echo base_url(); ?>';
            $.ajax({
                url: base_url + "index.php/ProjectMappingCntrl/Showdetails",
                type: "get", //send it through get method
                data: {
                    shw: shw
                },
                success: function (response) {
                    $("#show_details").html(response);
                    $("#modelshowbody").modal('show');
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            });
        });
    });
</script>