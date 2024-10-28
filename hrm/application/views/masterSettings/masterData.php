<?php
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Name"; } 
        td:nth-of-type(2):before { content: "Description"; } 
        td:nth-of-type(3):before { content: "Value"; } 
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
                            <h3 class="box-title">Master Data</h3>
                        </div>
                        <div class="box-body">
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Value</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($masterData as $master) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $master['item'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $master['description'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $master['value'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('#', '<i class="fa fa-fw fa-edit"></i>', array(
                                                    'data-id' => $master['id'],
                                                    'data-name' => $master['item'],
                                                    'data-description' => $master['description'],
                                                    'data-value' => $master['value'],
                                                    'data-toggle' => "modal",
                                                    'data-target' => "#masterModal",
                                                    'class' => "popupModal",
                                                    'style' => 'cursor:pointer'
                                                ));
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
<div id="masterModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="item">

                </h4>
                <br/>
                <?php
                //echo validation_errors();
                echo form_open('', array(
                    'class' => 'form-horizontal',
                ));
                ?>
                <input type="hidden" name="masterId" id="masterId" value="">
                <div class="form-group" id="data">
                    <div class="form-group">
                        <?php
                        echo form_label('Name', 'name', array(
                            'class' => 'col-sm-2 control-label',
                        ));
                        ?>
                        <div class="col-sm-8">  
                            <?php
                            $text = array(
                                'name' => 'name',
                                'id' => 'name',
                                'class' => 'form-control ',
                                'readonly' => 'readonly',
                                'required' => 'required',
                            );

                            echo form_input($text);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Description', 'description', array(
                            'class' => 'col-sm-2 control-label',
                        ));
                        ?>
                        <div class="col-sm-8">  
                            <?php
                            $text = array(
                                'name' => 'description',
                                'id' => 'description',
                                'class' => 'form-control',
                                'required' => 'required',
                                'readonly' => 'readonly',
                            );

                            echo form_input($text);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_label('Value', 'content', array(
                            'class' => 'col-sm-2 control-label',
                        ));
                        ?>
                        <div class="col-sm-8">  
                            <?php
                            $text = array(
                                'name' => 'content',
                                'id' => 'content',
                                'class' => 'form-control',
                                'required' => 'required',
                            );

                            echo form_input($text);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-2 control-label">
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-10">
                            <?php
                            $submitButton = array(
                                'type' => 'submit',
                                'content' => 'Save',
                                'class' => 'btn btn-small btn-primary'
                            );
                            echo form_button($submitButton)
                            ?>
                        </div>
                        <!-- /.col -->
                    </div>

                    <?php
                    echo form_close();
                    ?>
                </div>
            </div>
            <br/>
        </div>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $("body").on("click", ".popupModal", function (event) {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var description = $(this).data('description');
        var value = $(this).data('value');
        $("#masterId").val(id);
        $("#name").val(name);
        $("#description").val(description);
        $("#content").val(value);
    });
</script>