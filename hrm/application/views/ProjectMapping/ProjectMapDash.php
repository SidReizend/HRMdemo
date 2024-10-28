<?php
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
                            <h3 class="box-title">Project Mapping</h3>
                        </div>
                        <div class="box-body">

                            <div class="col-sm-12"  style="padding:10px;">
                                <?php
                                echo anchor('ProjectMappingCntrl/ProjectWiseView', 'Project Wise View', array(
                                    'class' => 'col-sm-12 btn btn-small btn-primary'
                                ));
                                ?>

                            </div> 
                            <div class="col-sm-12"  style="padding:10px;">
                                <?php
                                echo anchor('ProjectMappingCntrl/TalentWiseView', 'Talent Wise View', array(
                                    'class' => 'col-sm-12 btn btn-small btn-primary'
                                ));
                                ?>

                            </div> 
                            <div class="col-sm-12" style="padding:10px;">
                                <?php
                                echo anchor('ProjectMappingCntrl/TalentWiseMapping', 'Talent Wise Mapping', array(
                                    'class' => 'col-sm-12 btn btn-small btn-primary'
                                ));
                                ?>

                            </div> 
                            <div class="col-sm-12"  style="padding:10px;">
                                <?php
                                echo anchor('ProjectMappingCntrl/ProjectWiseMapping', 'Project Wise Mapping', array(
                                    'class' => 'col-sm-12 btn btn-small btn-primary'
                                ));
                                ?>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
echo form_close();
?>
<?php
include_once APPPATH . 'views/footer.php';
?>