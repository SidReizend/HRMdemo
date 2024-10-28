<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<!-- Full Width Column -->
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <h3 class="box-title">
                        <?php
                        echo $knowledgeRepo->heading;
                        ?>
                    </h3>
                </div>
                <div class="col-sm-12">
                    <p>
                        <?php
                        echo $knowledgeRepo->description;
                        ?>
                    </p>
                    <p>
                        <?php
                        echo 'Created on : ' . date('d/m/Y', strtotime($knowledgeRepo->created_on));
                        echo '<br/>Created By : ' . $knowledgeRepo->name;
                        echo '<br/>Tags : ' . $tagName;
                        ?>
                    </p>
                    <p>
                        <?php
                        echo 'Document : ' . anchor(base_url() . 'index.php/docUrl/docUrlGenerator/' . $knowledgeRepo->file_name, 'Click here', array(
                            'target' => '_blank'
                        ));
                        ;
                        ?>
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>

