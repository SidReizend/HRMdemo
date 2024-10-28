<?php
$wysihtml5 = 1;

include_once APPPATH . 'views/header.php';
?>

<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container">
        <section class="content">

            <div class="row">

                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Set Dashboard Message</h3>
                        </div>
                        <?php
                        echo form_open('', array(
                            'class' => 'form-horizontal',
                            'id' => 'form',
                        ));
                        ?>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="form-group">
                                <textarea id="compose-textarea" class="form-control" name="message" style="height: 300px">
                                    <?php echo $message; ?>
                                </textarea>
                                <?php echo form_error('message'); ?>
                            </div>

                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <div class="pull-right">
                                <!--<button type="button" class="btn btn-success <?php // echo $show == 0 ? "" : "disabled"; ?>"><i class="fa fa-check-circle"></i> Show</button>-->
                                <!--<button type="button" class="btn btn-danger <?php // echo $show == 1 ? "" : "disabled"; ?>"><i class="fa fa-eject"></i> Hide</button>-->
                                <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Modify</button>
                            </div>

                        </div>

                        <!-- /.box-footer -->
                    </div>
                    <?php
                    echo form_close();
                    ?>
                </div>
            </div>
        </section>
    </div>
</div>
<iframe id="form_target" name="form_target" style="display:none"></iframe>
<form id="my_form" action="<?php echo base_url() . 'index.php/fileUploader'; ?>" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
    <input name="image" type="file" onchange="$('#my_form').ajaxSubmit({success: function (d) {
                    eval(d);
                }});
            this.value = '';">
</form>
<!-- /.content -->
<!-- /.container -->
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>

<script type="text/javascript" >
    tinymce.init({
        selector: '#compose-textarea',
        plugins: ["image"],
        convert_urls: false,
        remove_script_host: true,
        file_browser_callback: function (field_name, url, type, win) {
            if (type == 'image')
                $('#my_form input').click();
        }
    });
    
//    $(document).ready(function(){
//        $("#form").submit(function(e){
//            e.preventDefault();
//            console.debug(tinyMCE.activeEditor.getContent());
//        });
//        
//
//    });
</script>