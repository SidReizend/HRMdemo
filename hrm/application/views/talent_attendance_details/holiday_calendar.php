<?php
$full_calendar = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
$yearDropDown = $years;
?>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Calendar of Talents </h3>

                    <div class="material-switch">
                        <br/>
                        Sign On <input name="resigned" value="1" id="resigned" class="someSwitchOption001"
                        <?php
                        if ($isResigned == 1) {
                            echo 'checked';
                        }
                        ?>
                                       type="checkbox" />
                        <label for="resigned" class="label-success"></label> Resigned
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- general form elements -->

                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'showReport',
                            ));
                            ?>
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-2">
                                        <?php
                                        echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                            'class' => 'form-control select2 selectOptions',
                                            'data-validation' => 'required',
                                            'id' => 'talent',
                                        ));
                                        ?>
                                        <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                    </div>

                                    <div class="col-sm-2 control-label" style="font-weight: bold;" id="talents">
                                        <?php
                                        //if talent is resigned then shows resigned date
                                        if ($selectedTalent) {
                                            $talentDetails = $this->talent->getTalentDetailsByTalentId($selectedTalent);
                                            if (1 == $talentDetails->is_resigned) {
                                                echo 'Resigned on : ' . date('d/m/Y', strtotime($talentDetails->date_of_resignation));
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    echo form_close();
                                    ?>

                                    <?php
                                    $details_count = count($details);
                                    if (0 < $details_count) {
                                        ?>
                                        <div class="col-md-7 col-sm-5">
                                            <div class="box box-primary">
                                                <div class="box-body no-padding">
                                                    <!-- THE CALENDAR -->
                                                    <div id="calendar"></div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                            <!-- /. box -->
                                        </div>
                                        <!-- /.col -->
                                        <?php
                                    }
                                    ?>
                                </div>
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
        $(".select2").select2();
        $("body").on("change", ".selectOptions", function (e) {
            var year = $("#year").val();

            if (year != '') {
                $("#showReport").submit();

            }
        });
        $("body").on("change", "#resigned", function (e) {
            if ($(this).prop("checked") == true) {
                location.href = "resignedTalentsDetails";
            } else {
                location.href = "signOnTalentsDetails";
            }
        });

        // page is now ready, initialize the calendar...
<?php
if (0 < $details_count) {
    ?>

    <?php for ($i = 1; $i <= 12; $i++) { ?>

        <?php
    }
}
?>

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'prev,next',
            },
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: <?php echo $events; ?>,
        });

    });
</script>