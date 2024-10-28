<?php
$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Role"; } 
        td:nth-of-type(3):before { content: "Description"; } 
        td:nth-of-type(4):before { content: "Holidays week"; } 
        td:nth-of-type(5):before { content: "EL"; } 
        td:nth-of-type(6):before { content: "working hr/day"; } 
        td:nth-of-type(7):before { content: "Role Stream"; } 
        td:nth-of-type(8):before { content: "Avg Hr Calcln."; } 
        td:nth-of-type(9):before { content: "Action"; } 
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
                            <h3 class="box-title">Roles</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            echo anchor('role/addNewRole', 'Add New', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Role</th>
                                        <th>Description</th>
                                        <th>Holidays in a week </th>
                                        <th>Quarterly earned leave </th>
                                        <th>Average working hour per day</th>
                                        <th>Role Stream</th>
                                        <th>Average Hour Calculation</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($roleDetails as $roles) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roles['role_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roles['description'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
//                                echo '<pre>';
//                                var_dump($roleHoliday);
//                                echo '</pre>';
//                                die();
                                                $check = 0;
                                                foreach ($roleHoliday[$roles['id']] as $day) {
                                                    if ($check) {
                                                        echo ', ' . $day['week_day'];
                                                    } else {
                                                        echo $day['week_day'];
                                                    }
                                                    $check++;
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roles['quarterly_earned_leaves'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roles['average_working_hour_per_day'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $roles['role_stream'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($roles['average_hour_calculation'] == '1') {
                                                    echo 'monthly';
                                                } else if ($roles['average_hour_calculation'] == '2') {
                                                    echo 'quarterly';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('role/editRole/roleId/' . $roles['id'], '<i class="fa fa-fw fa-edit"></i>');
//                                                echo '&nbsp&nbsp&nbsp&nbsp';
//                                echo anchor('role/deleteRole/roleId/'.$roles['id'], '<i class="fa fa-fw fa-times"></i>',array(
//                                    'onclick' => "return deleteRow()"
//                                ));
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
<!-- /.content-wrapper -->
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
    $('#roleTable').DataTable();
    function deleteRow()
    {
        alertify.confirm("Do you want to Continue", function (e) {
            if (e) {
                alertify.success("Operation done successfully");
                return true;
            } else {
                return false;
            }
        });

    }
</script>

