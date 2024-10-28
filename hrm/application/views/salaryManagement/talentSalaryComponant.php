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
                            <h3 class="box-title">Salary Setup</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <?php
                                echo form_label('Talent', 'talent', array(
                                    'class' => 'col-sm-2 control-label',
                                ));
                                ?>
                                <div class="col-sm-10">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, "", array(
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                        'id' => 'talent'
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('talent'); ?> </span>
                                </div>
                            </div>
                            <div id="ajaxTalentSalaryComponants" >
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
        $(".select2").select2();
        $(".form-horizontal").validate();
    });
    $("body").on("change", "#talent", function (event) {
        var talent = $(this).val();
        var base_url = '<?php echo base_url() ?>';
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        if (talent != '') {
            $.ajax({
                url: base_url + "index.php/payRoll/ajaxTalantSalaryComponants",
                type: "get", //send it through get method
                data: {
                    talent: talent,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                },
                success: function (response) {
                    $('#ajaxTalentSalaryComponants').html(response);
                },
                error: function (xhr) {
                    //Do Something to handle error
                    alertify.alert("some error found");
                }
            }).complete(function () {
                var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                $('[name="' + csrf_name + '"]').val(cct);
            });
        } else {
            $('#ajaxTalentSalaryComponants').html('');
        }
    });
    $("body").on("change", ".numeric", function (e) {
        var value = $(this).val();
        var talent = $("#talent").val();
        var id = $(this).attr('id');
        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/payRoll/ajaxUpdateCompanant",
            type: "get", //send it through get method
            data: {
                talent: talent,
                id: id,
                value: value
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
</script>

