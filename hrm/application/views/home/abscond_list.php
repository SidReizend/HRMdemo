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
                            <h3 class="box-title">Abscond Talent List</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Reporting Manager</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $slno = 1;
                                    foreach ($absconding_list as $ab) {
                                        ?>
                                        <tr>
                                            <td><?php echo $slno; ?></td>
                                            <td><?php echo $ab->first_name; ?></td>
                                            <td><?php echo $ab->middle_name; ?></td>
                                            <td><?php echo $ab->last_name; ?></td>
                                            <td><?php echo $ab->r_first_name ." " . $ab->r_middle_name ." " . $ab->r_last_name; ?></td>
                                        </tr>

                                        <?php
                                        $slno++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

                        </div>
                        <!-- /.box-footer -->
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
?>
<script>
    $(function () {
        //Add text editor
        $("#compose-textarea").wysihtml5();
    });
</script>