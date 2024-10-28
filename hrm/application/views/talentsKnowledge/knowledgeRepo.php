<style>

    .widget-user-2 .widget-user-header {
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        padding: 8px !important;
    }
    .widget-user-2 .widget-user-username, .widget-user-2 .widget-user-desc {
        margin-left: 0px !important;

    }
    .widget-user-username a{
        color: white !important;
    }


</style>


<?php
$select2 = 1;
$pagination = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">


                <div class="box">
                    <div class="box-body">
                        <!-- left column -->
                        <!-- general form elements -->
                        <h3 class="box-title">Knowledge Portal</h3>
                        <div class="col-sm-12">
                            <?php
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'knowledgeRepo'
                            ));
                            echo form_label('Search By Tag', 'tag_array[]', array(
                                'class' => 'col-sm-3 control-label',
                            ));
                            ?>
                            <div class="col-sm-3">
                                <?php
                                $tags = $tag;
                                $selectedTags = array();

                                echo form_multiselect('tag_array[]', $tags, $selectedTags, array(
                                    'class' => 'form-control select2',
                                    'data-validation' => 'required',
                                    'id' => 'tag',
                                    'onChange' => 'getData(0)',
                                ));
                                ?>
                            </div>
                            <div class="col-sm-1"> </div>
                            <div class="col-sm-3">  <?php
                                $headingText = array(
                                    'name' => 'search',
                                    'id' => 'search',
                                    'class' => 'form-control',
                                    'placeholder' => 'search by keyword ',
                                    'data-validation' => 'required',
                                    'onKeyUp' => 'getData(0)'
                                );

                                echo form_input($headingText);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div id="searchedData" >


                                <div class="col-md-12">

                                    <?php
                                    foreach ($knowledgeRepo as $knowledge) {
                                        ?>
                                        <div class="col-sm-6">
                                            <div class="col-sm-12">
                                                <br>
                                                <br>
                                                <!-- Widget: user widget style 1 -->
                                                <div class="box box-widget widget-user-2">
                                                    <!-- Add the bg color to the header using any of the bg-* classes -->
                                                    <div class="widget-user-header bg-light-blue-gradient" style="border: medium none;border-radius: 2px 20px">

                                                        <!-- /.widget-user-image -->
                                                        <h3 class="widget-user-username"> <?php
                                                            echo anchor('knowledgeRepo/readMore/' . $knowledge['id'], $knowledge['heading'], array(
                                                                'target' => '_blank'
                                                            ));
                                                            ?>
                                                        </h3>
                                                        <h5 class="widget-user-desc text-right"><?php echo 'Created By : ' . $knowledge['name']; ?><br/></h5>
                                                        <?php
                                                        $tags = $this->Tag->getSelectedTagByKnowledgeId($knowledge['id']);
                                                        $tagName = "";
                                                        foreach ($tags as $tag) {
                                                            if ($tagName) {
                                                                $tagName .= ',' . $tagName;
                                                            } else {
                                                                $tagName = $tagName;
                                                            }
                                                        }
                                                        ?>
                                                        <h5 class="widget-user-desc"><?php //echo 'Tags: ' . $tagName; ?></h5>
                                                    </div>
                                                    <div class="box-footer">
                                                        <p> <?php
                                                            $reducedDescription = $this->KnowledgeRepository->reduceLength($knowledge['description'], 130);
                                                            echo $reducedDescription;
                                                            echo anchor('knowledgeRepo/readMore/' . $knowledge['id'], 'read more', array(
                                                                'target' => '_blank'
                                                            ));
                                                            ?></p>
                                                    </div>
                                                </div>
                                                <!-- /.widget-user -->
                                                <!-- prev code here -->




                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-md-12">
                                    <?php echo $this->ajax_pagination->create_links(); ?>

                                </div>

                            </div>

                        </div>
                    </div>
                    <div id="overlay" class="overlay ">

                        <i class="fa fa-spinner fa-spin fa-4x "></i>
                    </div>



                </div>
        </section>
    </div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';

$instance = & get_instance();
?>
<script>
    //Initialize Select2 Elements
    $(document).ready(function () {

        $('body').find('.overlay').hide();
        $(".select2").select2();
    });
    function getData(page) {
        var search = $("#search").val();
        var tag = $("#tag").val();
        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
        $.ajax({
            method: "POST",
            url: "<?php echo $instance->ajax_pagination->base_url; ?>" + page,
            data: {page: page, search: search, tag: tag,
                '<?php echo $this->security->get_csrf_token_name(); ?>': cct},
            beforeSend: function () {

                $('body').find('.overlay').show();
                $('#overlay').show();

                $('<?php echo $instance->ajax_pagination->loading; ?>').show();
            },
            success: function (data) {
                $('<?php echo $instance->ajax_pagination->loading; ?>').hide();
                $('<?php echo $instance->ajax_pagination->target; ?>').html(data);
            }
        }).complete(function () {
            var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
            $('[name="' + csrf_name + '"]').val(cct);
             $('body').find('.overlay').hide();
        });
    }
</script>

