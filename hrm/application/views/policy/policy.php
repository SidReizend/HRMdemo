<?php
$no_menu = 1;
include_once APPPATH . 'views/header.php';
?>
    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <div class="col-md-12">
                    <!-- PRODUCT LIST -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Policy Documents</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <ul class="list-group" style="text-align: center;">
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/1', 'Anti - Sexual Harrassment Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/2', 'Attendance & Punctuality Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/3', 'Claim Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/4', 'Dress Code Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/5', 'Grievance Handling', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/6', 'Induction  Process', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/7', 'Leave Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>
                                <li class="list-group-item">
                                    <?php echo anchor('home/policy_show_detail/8', 'Talent Engagement Policy', array('class' => 'btn bg-purple margin  btn-block')); ?>
                                </li>

                            </ul>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';