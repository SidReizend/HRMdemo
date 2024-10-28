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
        .notitle>td:nth-of-type(2):before { content: "Talent"; } 
        .notitle>td:nth-of-type(3):before { content: "Settlement"; } 
        .notitle>td:nth-of-type(4):before { content: "Project"; } 
        .notitle>td:nth-of-type(5):before { content: "Amount Credicted"; } 
        .notitle>td:nth-of-type(6):before { content: "Amount Debited"; } 
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
                            <h3 class="box-title">Transaction Tracks</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'searchForm'
                            ));
                            echo anchor('TranTracking/addNewTransaction', 'Add New', array(
                                'class' => 'btn btn-small btn-primary'
                            ));
                            ?>
                            &nbsp;&nbsp;
                            <?php
                            echo anchor('TranTracking/uploadTransaction', 'Upload Transaction', array(
                                'class' => 'btn btn-small btn-warning'
                            ));
                            ?>
                            <br/>
                            <br/>
                            <div class="form-group">
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
                                    echo form_dropdown('company', $companyDropdown, $selectedCompany, array(
                                        'class' => 'form-control select2 search',
                                        'required' => 'required',
                                        'id' => 'company',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo form_dropdown('project', $projectDropdown, $selectedProject, array(
                                        'class' => 'form-control select2 search',
                                        'required' => 'required',
                                        'id' => 'project',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2    ">
                                    <?php
                                    echo form_dropdown('month', $monthDropdown, $selectedMonth, array(
                                        'class' => 'form-control select2 selectOptions',
                                        'data-validation' => 'required',
                                        'id' => 'month',
                                    ));
                                    ?>
                                    <span class="text-danger"> <?php echo form_error('role'); ?> </span>
                                </div>
                            </div>
                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Company</th>
                                        <th>Project</th>
                                        <th>Credit Amount</th>
                                        <th>Debit Amount</th>
                                        <th>Date of transaction</th>
                                        <th>Particulars</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    $totalCreditAmount = 0;
                                    $totalDebitAmount = 0;
                                    foreach ($claims as $claim) {
                                        ?>
                                        <tr class="notitle" data-toggle="tooltip" 
                                            title="" data-original-title="<?php echo $claim['remarks'] ? 'Remark : ' . $claim['remarks'] : ''; ?>">
                                            <td>
                                                <?php
                                                echo ++$serialNumber;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['company_name'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['project'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $this->claim->commaSeperated($claim['credit_amount']);
                                                $totalCreditAmount +=$claim['credit_amount'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $this->claim->commaSeperated($claim['debit_amount']);
                                                $totalDebitAmount +=$claim['debit_amount'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo date('d/m/Y', strtotime($claim['date_of_approval']));
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $claim['note'];
                                                $claimsMapped = $this->claim->getMappedClaimsToTransaction($claim['id']);
                                                if (!empty($claimsMapped)) {
                                                    ?>
                                                    <table style="display:none;" class="claimTable">
                                                        <tr>
                                                            <th>
                                                                Claim Id
                                                            </th>
                                                            <th>
                                                                Payment
                                                            </th>
                                                            <th>
                                                                Project Code
                                                            </th>
                                                            <th>
                                                                Amount
                                                            </th>
                                                        </tr>
                                                        <?php
                                                        foreach ($claimsMapped as $mapped) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    echo $mapped['claim_id'];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $mapped['option'] == 1 ? 'Full' : 'Partial';
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $mapped['project_code'];
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo $mapped['transaction_amount'];
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </table>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('TranTracking/editTransaction/' . $claim['id'], '<i class="fa fa-fw fa-edit"></i>') . '<br/>';
                                                if (!empty($claimsMapped)) {
                                                    ?>
                                                    <span style="cursor: pointer;" class="toggleClaim">
                                                        +
                                                    </span>
                                                    <?php
                                                }
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
                                        <td style="width: 60%">Total Credit Amount</td>
                                        <td>
                                            <span class="badge bg-yellow">
                                                <a href="#" data-toggle="modal" style="color: inherit;" data-target="#catgoryWiseSettled">
                                                    <?php echo $this->claim->commaSeperated($totalCreditAmount); ?>
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td style="width: 60%">Total Debit Amount</td>
                                        <td>
                                            <span class="badge bg-yellow">
                                                <a href="#" data-toggle="modal" style="color: inherit;" data-target="#catgoryWiseSettled">
                                                    <?php echo $this->claim->commaSeperated($totalDebitAmount); ?>
                                                </a>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div>
                                <center>
                                    <button type="button" id="excel" class="btn btn-small btn-primary">
                                        Excel
                                    </button>
                                </center>
                            </div>
                            <div id="catgoryWiseSettled" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">
                                                Total Amount
                                            </h4>
                                            <br/>
                                            <div class="form-group" id="catDiv">
                                                <table class="table table-bordered table-hover">
                                                    <tr>
                                                        <th>
                                                            Company
                                                        </th>
                                                        <th>
                                                            Credit
                                                        </th>
                                                        <th>
                                                            Debit
                                                        </th>
                                                    </tr>
                                                    <?php
                                                    foreach ($companies as $company) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                echo $company['company_name'];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $this->claim->getCreditTranByCompanyYearStatus($selectedYear, $company['id'], $selectedProject, $selectedMonth);
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                echo $this->claim->getDebitTranByCompanyYearStatus($selectedYear, $company['id'], $selectedProject, $selectedMonth);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
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
    $("body").on("click", "#excel", function (e) {
        var year = $("#year").val();
        var company = $("#company").val();
        var project = $("#project").val();
        var month = $("#month").val();
        window.open('tranTracking/excelDownload?year=' + year + '&company=' + company + '&project=' + project + '&month=' + month);
    });
    $("body").on("click", ".toggleClaim", function (e) {
        $(this).text(function (_, value) {
            return value == '-' ? '+' : '-'
        });
        $(this).closest("tr").find(".claimTable").toggle();
    });
</script>