<?php
$datatables = 1;
$select2 = 1;
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
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Log of <?php echo $logDate; ?></h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo $first400 = substr($log, 0, 144).'<br/>';
                            echo $theRest = substr($log, 144,300).'<br/>';
                            echo $theRest = substr($log, 300,444).'<br/>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>

