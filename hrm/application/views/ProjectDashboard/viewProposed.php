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
                        
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <table id="table_responsive" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Ongoing Marketing Efforts</th>
                                    <th>Stage</th>
                                    <th>Proposed Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($allProjects as $project) {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo $project['ongoing_projects'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo $project['stage'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo number_format($project['value']);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
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
