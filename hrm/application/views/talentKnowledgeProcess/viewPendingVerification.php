

<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$datatables = 1;
$alertify = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Heading"; } 
        .notitle>td:nth-of-type(3):before { content: "Description"; } 
        .notitle>td:nth-of-type(4):before { content: "Uploaded File"; } 
        .notitle>td:nth-of-type(5):before { content: "Created By"; } 
        .notitle>td:nth-of-type(6):before { content: "Last Updated By"; } 
        .notitle>td:nth-of-type(7):before { content: "Action"; } 
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
                            <h3 class="box-title">Knowledge portal assigned to me</h3>
                        </div>
                        <div class="box-body">
                           
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Heading</th>
                                        <th>Description</th>
                                        <th>Uploaded File </th>
                                        <th>Created By</th>
                                        <th>Last Updated By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($knowledge as $knowledges) {
                                        ?>
                                        <tr>
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $knowledges['heading'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $knowledges['description'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                            echo anchor(base_url() . 'index.php/docUrl/docUrlGenerator/' . $knowledges['file_name'], $knowledges['file_name'], 'target="_blank"');
                                            ?>
                            
                                            </td>
                                            <td>
                                                <?php
                                                    echo $knowledges['name']." on ". date('d/m/Y H:i',  strtotime($knowledges['created_on']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if(isset($knowledges['updated_by']))
                                                {
                                                     echo $knowledges['updated_by']."  on ".$knowledges['modified_on'];
                                                }
                                                 
                                                ?>
                                            </td>
                                            
                                            <td>
                                                <?php
                                          
                                                    
                                                     echo anchor('TalentKnowledgeProcess/editKnowledge/' . $knowledges['id'], '<i class="fa fa-fw fa-edit"></i>');
                                                     
                                                      if (0 == $knowledges['verified'] ) {
                                                          echo anchor('TalentKnowledgeProcess/verifyKnowledge/' .$knowledges['id'].'/'.$redirect_to.'/', 'Approve',
                                                    array('class' => 'btn btn-success approve_events'));
                                                echo '&nbsp;';
                                               
                                                }
 else {
     echo "Approved by ". $knowledges['verified_by'];
 }

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
        alertify.confirm("Are you sure", function (e) {
            if (e) {
                alertify.success("Operation done successfully");
                return true;
            } else {
                return false;
            }
        });

    }
    
    
    $("body").on("click", ".approve_events", function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        alertify.confirm("Are you sure to approve", function (e) {
            if (e) {
                alertify.success("You clicked okay");
                location.href = link;
            } else {
                alertify.error("You've clicked Cancel");
            }
        });
    });
</script>

