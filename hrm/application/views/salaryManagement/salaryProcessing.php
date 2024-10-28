<?php
$form_validator2 = 1;
$datepicker = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>

<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">

            <?php
            //echo validation_errors();
            echo form_open('', array(
                'class' => 'form-horizontal',
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Salary Processing</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Talent', 'talent', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-8">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, "", array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                        'id' => 'talent'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'button',
                                        'content' => 'Process',
                                        'id' => 'process',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                            </div>
                            <div id="ajaxTalentSalaryComponants" >
                            </div>
                            <div id="overlay" class="overlay ">

                                <i class="fa fa-spinner fa-spin fa-4x "></i>
                            </div>
                        </div>
                        <?php
                        echo form_close();
                        ?>
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
    //Initialize Select2 Elements
    $(document).ready(function () {
        $('body').find('.overlay').hide();
        $(".select2").select2();
        $(".form-horizontal").validate();
    });
    $("body").on("click", "#process", function (e) {
        var talent = $("#talent").val();
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/payRoll/ajaxProcessSalary",
            type: "get", //send it through get method
            data: {
                talent: talent,
            }, beforeSend: function () {
                $('body').find('.overlay').show();
                $('#overlay').show();
            },
            success: function (response) {
                $('#ajaxTalentSalaryComponants').html(response);
                $('body').find('.overlay').hide();
            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });

    });
    $("body").on("click", "#excel", function (e) {
        var talent = $("#talent").val();
        base_url = '<?php echo base_url(); ?>';
//        $.ajax({
//            url: base_url + "index.php/payRoll/ajaxExcel",
//            type: "get", //send it through get method
//            data: {
//                talent: talent,
//            },
//            success: function (response) {
//                $('#ajaxTalentSalaryComponants').html(response);
//            },
//            error: function (xhr) {
//                //Do Something to handle error
//                alertify.alert("some error found");
//            }
//        });
        window.open('ajaxExcel/' + talent);

    });
</script>

