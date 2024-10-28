<?php
$form_validator = 1;
include_once APPPATH . 'views/header.php';
?>
<div class="content-wrapper" style="min-height: 267px;">
    <div class="container">
        <section class="content">
            <?php
            echo form_open_multipart('', array(
                'class' => 'form-horizontal',
                'id' => 'transactionUpload'
            ));
            ?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Upload Transaction</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" value="1" name="justVariable">
                                        <?php
                                        echo form_label('Upload File', 'uploadFile', array(
                                            'class' => 'col-sm-2 control-label',
                                        ));
                                        ?>
                                        <div class="col-sm-4">
                                            <?php
                                            $upload = Array(
                                                "name" => "uploadFile",
                                                "id" => "uploadFile",
                                                'data-validation' => 'required',
                                            );
                                            echo form_upload($upload);
                                            ?>
                                            <span class="text-danger"> <?php echo form_error('uploadFile'); ?> </span>
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-xs-2">
                                            <?php
                                            $submitButton = array(
                                                'type' => 'submit',
                                                'id' => 'save',
                                                'content' => 'Save',
                                                'class' => 'btn btn-small btn-primary'
                                            );
                                            echo form_button($submitButton)
                                            ?>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </div>
                                <?php
                                echo form_close();
                                ?>
                            </div>

                            <table id="table_responsive" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAll" ><br/>Sl No.</th>
                                        <th>Company</th>
                                        <th>Uploaded by</th>
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
                                                <input type="checkbox" value="<?php echo $claim['id']; ?>" class="transactionCheckobox" >
                                                <br/>
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
                                                echo $claim['created_by'];
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
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo anchor('TranTracking/approveTransaction/' . $claim['id'], 'Verify', array(
                                                    'class' => 'btn btn-primary'
                                                )) . '<br/><br/>';
                                                ?>
                                                <?php
                                                echo anchor('TranTracking/deleteTransaction/' . $claim['id'], 'delete', array(
                                                    'class' => 'btn btn-warning'
                                                ));
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <center>
                                <?php
                                echo form_open('tranTracking/approveSelectedTransaction', array(
                                    'class' => 'form-horizontal',
                                    'id' => 'transAprrove'
                                ));
                                ?>
                                <br/>
                                <input type="hidden" name="checkedTransactions" id="checkedTransactions">
                                <input type="button" class="btn btn-primary" value="Verify Selected" id="submitTransactions" name="submitTransactions">
                                <?php
                                echo form_close();
                                ?>
                            </center>
                            <center>
                                <?php
                                echo form_open('tranTracking/deleteSelectedTransaction', array(
                                    'class' => 'form-horizontal',
                                    'id' => 'transDelete'
                                ));
                                ?>
                                <br/>
                                <input type="hidden" name="checkedDeleteTransactions" id="checkedDeleteTransactions">
                                <input type="button" class="btn btn-primary" value="Delete Selected" id="deleteTransactions" name="deleteTransactions">
                                <?php
                                echo form_close();
                                ?>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- /.container -->
<?php
include_once APPPATH . 'views/footer.php';
?>

<script>
    $(document).ready(function () {
        $("body").on("click", "#checkAll", function (event) {
            if (this.checked) {
                $('.transactionCheckobox').prop('checked', true);
            } else {
                $('.transactionCheckobox').prop('checked', false);

            }
        });
        $("body").on("click", "#submitTransactions", function (event) {
            event.preventDefault();
            $("#checkedTransactions").val('');
            var numberOfChecked = 0;
            $('.transactionCheckobox').each(function () {
                if (this.checked) {
                    var selectedVal = $(this).val();
                    var totalVal = $("#checkedTransactions").val();
                    if (0 === numberOfChecked) {
                        $("#checkedTransactions").val(selectedVal);
                    } else {
                        $("#checkedTransactions").val(totalVal + ',' + selectedVal);
                    }
                    numberOfChecked++;
                }
            });
            if (0 != numberOfChecked) {
                $("#transAprrove").submit();
            }
        });
        $("body").on("click", "#deleteTransactions", function (event) {
            event.preventDefault();
            $("#checkedDeleteTransactions").val('');
            var numberOfChecked = 0;
            $('.transactionCheckobox').each(function () {
                if (this.checked) {
                    var selectedVal = $(this).val();
                    var totalVal = $("#checkedDeleteTransactions").val();
                    if (0 === numberOfChecked) {
                        $("#checkedDeleteTransactions").val(selectedVal);
                    } else {
                        $("#checkedDeleteTransactions").val(totalVal + ',' + selectedVal);
                    }
                    numberOfChecked++;
                }
            });
            if (0 != numberOfChecked) {
                $("#transDelete").submit();
            }
        });
    });
</script>