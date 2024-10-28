<?php
$datatables = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
    </div>
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Skills View</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        echo form_open('', array(
                            'class' => 'form-horizontal',
                            'id' => 'skillsView',
                        ));
                        ?>
                        <div class="box-body">
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="">Skills</label>
                                    <?php
                                    echo form_dropdown('skills', $skills, '', array(
                                        'class' => 'form-control select2 skillSelectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'skills',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <label class="">Select Year</label>
                                    <br>
                                    <span id="ex18-label-2a " class="hidden">Example low value</span>
                                    <span id="ex18-label-2b " class="hidden">Example high value</span>
                                    <input id="ex18b" class="skillSelectOptions" type="text"/>
                                </div>
                            </div>
                        </div>
                        <?php
                        echo form_close();
                        ?>
                        <table id="table_responsive" class="table table-bordered table-hover tableView">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>

    </style>
</div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();

        // With JQuery
        $("#ex18b").slider({
            min: 1,
            max: 25,
            value: [1, 25],
            labelledby: ['ex18-label-2a', 'ex18-label-2b']
        });

    });
    $("body").on("click", ".approveBtn", function (e) {
        $('.approveSubmit').attr('data-id', $(this).data('id'));
        $('#skillName').val($(this).data('text'));
        $('.skillNameErrMsg').html('');
    });

    $("body").on("change", ".skillSelectOptions", function (e) {
        var yearOfExperience = $('#ex18b').val();
        var skills = $('#skills').val();
        var base_url = '<?php echo base_url() ?>';
        $.ajax({
            url: base_url + "index.php/MySkill/skillsSearch",
            type: "post", //send it through post method
            dataType: 'json',
            data: {
                skills: skills,
                yearOfExperience: yearOfExperience
            },
            success: function (response) {
                $('.tableView').html(response.userTable);

            },
            error: function (xhr) {
                //Do Something to handle error
                alertify.alert("some error found");
            }
        });
    });


</script>

