<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Claim Id"; } 
        .notitle>td:nth-of-type(3):before { content: "Talent"; } 
        .notitle>td:nth-of-type(4):before { content: "Settlement"; } 
        .notitle>td:nth-of-type(5):before { content: "Project"; } 
        .notitle>td:nth-of-type(6):before { content: "Amount"; } 
        .notitle>td:nth-of-type(7):before { content: "Date Approved"; } 
        .notitle>td:nth-of-type(8):before { content: "Approved By"; } 
        .notitle>td:nth-of-type(9):before { content: "Created By"; } 
        .notitle>td:nth-of-type(10):before { content: "Status"; } 
        .notitle>td:nth-of-type(11):before { content: "Action"; } 
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
                            <h3 class="box-title">Claims</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'searchForm'
                            ));
                            echo anchor('ClaimTrackingForms/addNewClaim', 'Add New', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('settlement', $settlementDropdown, $selectedSettlement, array(
                                        'class' => 'form-control search',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('settlement'); ?> </span>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('statusSearch', $statusSearchDropdown, $selectedStatusSearch, array(
                                        'class' => 'form-control search',
                                        'required' => 'required',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('settlement'); ?> </span>
                                </div>
                                <div class="col-sm-2    ">
                                    <?php
                                    echo form_dropdown('year', $years, $selectedYear, array(
                                        'class' => 'form-control select2 search',
                                        'data-validation' => 'required',
                                        'id' => 'year',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('talent', $talentsDropdown, $selectedTalent, array(
                                        'class' => 'form-control select2 search',
                                        'required' => 'required',
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="material-switch pull-right">
                                Sign On <input name="resigned" value="1" id="resigned" class="someSwitchOption001 selectOptions"
                                <?php
                                if ($isResigned == 1) {
                                    echo 'checked';
                                }
                                ?>
                                               type="checkbox" />
                                <label for="resigned" class="label-success"></label> Resigned
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Claim Id</th>
                                        <th>Talent</th>
                                        <th>Stlmnt</th>
                                        <th>Project</th>
                                        <th>Task</th>
                                        <th>Amt</th>
                                        <th>Date of approval</th>
                                        <th>Approved by</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    $plannedAmount = 0;
                                    $settledAmount = 0;
                                    $otherPlanned = 0;
                                    $otherSettled = 0;
                                    foreach ($claims as $claim) {
                                        $countMappedToTransaction = $this->claim->getCountClaimMappedToTrans($claim['id']);
                                        if ($countMappedToTransaction > 0) {
                                            $color = "gray";
                                        } else {
                                            $color = '';
                                        }
                                        ?>
                                        <tr class="notitle" data-toggle="tooltip"  bgcolor="<?php echo $color; ?>"
                                            title="" data-original-title="<?php echo $claim['note'] ? 'Note : ' . $claim['note'] : ''; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['claim_id'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['settlement'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['project'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['task_code'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $this->claim->commaSeperated($claim['amount']);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($claim['date_of_approval']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['approved_by'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['created_by'];
                                                ?>
                                            </td>
                                            <td>
                                                <div class="col-sm-1">  <?php
                                                    echo form_dropdown('status', $statusDropdown, $claim['status'], array(
                                                        'class' => 'form-control status',
                                                        'id' => $claim['id'],
                                                        'required' => 'required',
                                                    ));
                                                    ?>
                                                </div>
                                                <?php
                                                $claimCategory = $claim['category'];
                                                if ($claim['status'] == '0') {
                                                    $plannedAmount+=$claim['amount'];
                                                    if ($claimCategory) {
                                                        $categoryArray[$claimCategory]['planned'] += $claim['amount'];
                                                    } else {
                                                        $otherPlanned += $claim['amount'];
                                                    }
                                                } else {
                                                    $settledAmount+=$claim['amount'];
                                                    if ($claimCategory) {
                                                        $categoryArray[$claimCategory]['settled'] += $claim['amount'];
                                                    } else {
                                                        $otherSettled += $claim['amount'];
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('ClaimTrackingForms/editClaim/' . $claim['id'], '<i class="fa fa-fw fa-edit"></i>');
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <table class="table" style="background-color: #D9D6D6;">
                                <tbody>
                                    <tr>
                                        <td style="width: 60%">Total Amount Planned</td>
                                        <td>
                                            <span class="badge bg-green">
                                                <a href="#" data-toggle="modal" style="color: inherit;" data-target="#catgoryWisePlanned">
                                                    <?php echo $this->claim->commaSeperated($plannedAmount); ?>
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 60%">Total Amount Settled</td>
                                        <td>
                                            <span class="badge bg-yellow">
                                                <a href="#" data-toggle="modal" style="color: inherit;" data-target="#catgoryWiseSettled">
                                                    <?php echo $this->claim->commaSeperated($settledAmount); ?>
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div id="catgoryWisePlanned" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">
                                                Category Wise Planned
                                            </h4>
                                            <br/>
                                            <div class="form-group" id="catDiv">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th>
                                                            Category
                                                        </th>
                                                        <th>
                                                            Amount
                                                        </th>
                                                    </tr>
                                                    <?php
                                                    foreach ($categoryArray as $category) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                echo $category['value'];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $this->claim->commaSeperated($category['planned']);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            Other
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo $this->claim->commaSeperated($otherPlanned);
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                            <div id="catgoryWiseSettled" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">
                                                Category Wise Settled
                                            </h4>
                                            <br/>
                                            <div class="form-group" id="catDiv">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th>
                                                            Categroy
                                                        </th>
                                                        <th>
                                                            Amount
                                                        </th>
                                                    </tr>
                                                    <?php
                                                    foreach ($categoryArray as $category) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                echo $category['value'];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $this->claim->commaSeperated($category['settled']);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            Other
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo $this->claim->commaSeperated($otherSettled);
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<?php
include_once APPPATH . 'views/footer.php';
?>


<script>
    //Initialize Select2 Elements
    $(document).ready(function () {
        $(".select2").select2();
        $('#odTypeTable').DataTable();
        $("body").on("change", ".status", function (e) {
            e.preventDefault();
            var id = $(this).attr('id');
            var status = $(this).val();
            base_url = '<?php echo base_url() ?>';
            var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
            alertify.confirm("Are you sure to update status", function (e) {
                if (e) {
                    $.ajax({
                        url: base_url + "index.php/ClaimTrackingForms/statusUpdate",
                        type: "get", //send it through get method
                        data: {
                            id: id,
                            status: status,
                            '<?php echo $this->security->get_csrf_token_name(); ?>': cct
                        },
                        success: function (response) {
                            alertify.success("Success");
                        },
                        error: function (xhr) {
                            //Do Something to handle error
                            alertify.alert("some error found");
                        }
                    }).complete(function () {
                        var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
                        var cct = $.cookie("<?php echo $this->config->item("csrf_cookie_name"); ?>");
                        $('[name="' + csrf_name + '"]').val(cct);
                    });

                } else {
                    alertify.error("You've clicked Cancel");
                }
            });
        });
    });
    $('.search').on('change', function () {
        $('#searchForm').submit();
    });
    $("body").on("change", ".selectOptions", function (e) {
        if ($('#resigned').prop("checked") == true) {
            $('#resigned').val('1');
        } else {
            $('#resigned').val('0');
        }
        $("#searchForm").submit();
    });
</script>