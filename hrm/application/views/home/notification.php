<?php
include_once APPPATH . 'views/header.php';

$notif_count = count($notifications);
?>

<!-- Full Width Column -->
<div class="content-wrapper">
    <div class="container">
        <section class="content">

            <div class="row">

                <div class="col-md-12">
                    <!-- DIRECT CHAT -->
                    <div class="box box-warning direct-chat direct-chat-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Notification</h3>
                            <div class="box-tools pull-right">
                                <span class="badge bg-yellow" title="<?php echo $notif_count; ?> Notifications for you" data-toggle="tooltip"><?php echo $notif_count; ?></span>
                                <button data-widget="collapse" class="btn btn-box-tool" type="button"><i class="fa fa-minus"></i>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- Conversations are loaded here -->
                            <div class="direct-chat-messages" style="height:400px;">
                                <?php
                                if (0 < $notif_count) {
                                    foreach ($notifications as $notif) {
                                        ?>
                                        <div class="alert bg-<?php echo strtolower($notif['type']); ?> disabled color-palette alert-dismissible">
                                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                            <strong><?php echo $notif['message_time_formatted']; ?></strong> <?php echo $notif['message']; ?>
                                        </div>

                                        <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert bg-purple-gradient alert-dismissible">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        No notification for you!
                                    </div>
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
<!-- /.content -->
</div>
<!-- /.container -->
</div>
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
