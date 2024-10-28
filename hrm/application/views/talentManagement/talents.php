<?php
$datatables = 1;

include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        td:nth-of-type(1):before { content: "Sl No."; } 
        td:nth-of-type(2):before { content: "Talent Code"; } 
        td:nth-of-type(3):before { content: "Name"; } 
        td:nth-of-type(4):before { content: "Role"; } 
        td:nth-of-type(5):before { content: "Mobile"; } 
        td:nth-of-type(6):before { content: "Email"; } 
        td:nth-of-type(7):before { content: "reporting_manager"; } 
        td:nth-of-type(8):before { content: "Action"; } 
    }
</style>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Talents</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tabbable">
                                        <ul class="nav nav-tabs">

                                            <li class="active">
                                                <a href="#formcontrols" data-toggle="tab">Sign On</a>
                                            </li>
                                            <li>
                                                <a href="#jscontrols" data-toggle="tab">Resigned</a>
                                            </li>
                                        </ul>

                                        <br>

                                        <div class="tab-content">
                                            <div class="tab-pane active" id="formcontrols">
                                                <form id="edit-profile" class="form-horizontal" method="post" action="">
                                                    <div class="box-body">
                                                        <?php
                                                        echo anchor('talents/addNewTalent', 'Add New', array(
                                                            'class' => 'btn btn-small col-sm-1 btn-primary'
                                                        ));
                                                        ?>
                                                        <table id="table_responsive" class="table table-bordered table-hover table_responsive">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sl No.</th>
                                                                    <th>Talent Code</th>
                                                                    <th>Name</th>
                                                                    <th>Role</th>
                                                                    <th>Mobile</th>
                                                                    <th>Email </th>
                                                                    <th>reporting_manager</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $serialNumber = 0;
                                                                foreach ($talentDetails as $talent) {
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php
                                                                            echo ++$serialNumber;
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['talent_code'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['role_name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['mobile'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['email'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if (isset($reportingManagers[$talent['reporting_manager']])) {
                                                                                echo $reportingManagers[$talent['reporting_manager']];
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo anchor('talents/editTalent/talentId/' . $talent['id'], '<i class="fa fa-fw fa-edit"></i>');
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>

                                            </div>

                                            <div class="tab-pane"  id="jscontrols">
                                                <form id="edit-profile2" class="form-vertical">

                                                    <div class="box-body">
                                                        <table id="table_responsive" class="table table-bordered table-hover table_responsive">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sl No.</th>
                                                                    <th>Talent Code</th>
                                                                    <th>Name</th>
                                                                    <th>Role</th>
                                                                    <th>Mobile</th>
                                                                    <th>Email </th>
                                                                    <th>reporting_manager</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $serialNumber = 0;
                                                                foreach ($resignedTalentDetails as $talent) {
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php
                                                                            echo ++$serialNumber;
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['talent_code'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['role_name'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['mobile'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo $talent['email'];
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            if (isset($reportingManagers[$talent['reporting_manager']])) {
                                                                                echo $reportingManagers[$talent['reporting_manager']];
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            echo anchor('talents/editTalent/talentId/' . $talent['id'], '<i class="fa fa-fw fa-edit"></i>');
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.box -->

            </div>
            <!--/.col (left) -->
            <!-- Content Header (Page header) -->


    </div>
</section>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>
<script>
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
    $('.table_responsive').DataTable();
</script>

