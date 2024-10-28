<?php
$datatables = 1;
$alertify = 1;
$select2 = 1;
$datepicker = 1;
include_once APPPATH . 'views/header.php';
?>
<style>
    @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
        /* Label the data */ 
        .notitle>td:nth-of-type(1):before { content: "Sl No."; } 
        .notitle>td:nth-of-type(2):before { content: "Date"; } 
        .notitle>td:nth-of-type(3):before { content: "Billable"; } 
        .notitle>td:nth-of-type(4):before { content: "Billable Productivity"; } 
        .notitle>td:nth-of-type(5):before { content: "Non Billable"; } 
        .notitle>td:nth-of-type(6):before { content: "Non Billable Productivity"; } 
        .notitle>td:nth-of-type(7):before { content: "Total"; } 
        .notitle>td:nth-of-type(8):before { content: "Productivity"; } 
    }
    .select2-container--default .select2-results__option[aria-disabled=true] {
        color: #999;
        display: none;
    }
    #odTypeTable th {
        background: #1470ba;
        color: white;
        font-weight: bold;
    }
    tr.group, tr.group:hover {
        background-color: #ddd !important;
    }
    .group-span{
        float: right;
        margin-right: 12.5%;
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
                            <h3 class="box-title">Project Type Wise Report</h3>
                        </div>
                        <div class="box-body">
                            <?php
                            //echo validation_errors();
                            echo form_open('', array(
                                'class' => 'form-horizontal',
                                'id' => 'searchForm'
                            ));
                            ?>
                            <div class="form-group">                                
                                <div class="col-sm-2">  <?php
                                    $fromDateText = array(
                                        'name' => 'fromDate',
                                        'id' => 'fromDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $fromDate,
                                    );

                                    echo form_input($fromDateText);
                                    ?>
                                </div>
                                <div class="col-sm-2">  <?php
                                    $toDateText = array(
                                        'name' => 'toDate',
                                        'id' => 'toDate',
                                        'placeholder' => 'dd/mm/YYYY',
                                        'class' => 'form-control datepicker daysLoad',
                                        'value' => $toDate,
                                    );

                                    echo form_input($toDateText);
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <?php
                                    echo form_dropdown('revenue_type', $projectRevenueTypesDropdown, $selectedrevenueType, array(
                                        'class' => 'form-control select2',
                                        'id' => 'revenue_type',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-3">
                                    <?php
                                    echo form_dropdown('project_type', $projectTypesDropdown, $selectedprojectType, array(
                                        'class' => 'form-control select2',
                                        'id' => 'project_type',
                                    ));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">   
                                <div class="col-sm-4">
                                    <?php
                                    echo form_dropdown('project', $projectDropdown, $selectedproject, array(
                                        'class' => 'form-control',
                                        'id' => 'project',
                                    ));
                                    ?>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $submitButton = array(
                                        'type' => 'submit',
                                        'content' => 'Calculate',
                                        'id' => 'submit',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                    <?php
                                    $submitButton = array(
                                        'type' => 'button',
                                        'content' => 'Excel',
                                        'id' => 'excel',
                                        'class' => 'btn btn-small btn-primary'
                                    );
                                    echo form_button($submitButton)
                                    ?>
                                </div>
                            </div>
                            
                            <br/>
                            <br/>
                            <table id="odTypeTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sl No.</th>
                                        <th>Revenue Type</th>
                                        <th>Project Types</th>
                                        <th>Project</th>
                                        <th>Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $serialNumber = 0;
                                    foreach ($results as $result) {
                                            $rowspan = count($result['projects']);
                                            if($rowspan == 0){
                                                $rowspan = 1;
                                            }
                                            $rowspan = 1;
                                        ?>
                                        <?php foreach ($result['types'] as $res_data) { ?>
                                        <?php foreach ($res_data['projects'] as $res) { ?>
                                            <tr>
                                                <td rowspan="<?php echo $rowspan; ?>">
                                                    <?php
                                                    echo ++$serialNumber;
                                                    ?>
                                                </td>
                                                <td rowspan="<?php echo $rowspan; ?>">
                                                    <?php
                                                    echo $result['name'];
                                                    ?>
                                                    <span class='group-span'>
                                                    <?php
                                                    echo $result['time'];
                                                    ?>
                                                    </span>
                                                </td>
                                                <td rowspan="<?php echo $rowspan; ?>">
                                                    <?php
                                                    echo $res_data['name'];
                                                    ?>
                                                    <span class='group-span'>
                                                    <?php
                                                    echo $res_data['time'];
                                                    ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $res['name'];
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $res['time'];
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php } ?>
                                    <?php  } ?>
                                </tbody>
                            </table>
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
    var prev = [];
    var prev1 = [];
    var projectTypesArr = JSON.parse('<?php echo $projectTypesArr ?>');
    $(document).ready(function () {
        $(".select2").select2();
        $("#project").select2();
        <?php if($selectedprojectType != ''){ ?> 
            $("#project_type").trigger('change');
            $("#project").val('<?php echo $selectedproject; ?>').trigger('change');
        <?php } ?>       
        var groupColumns = [1, 2]; 
        $('#odTypeTable').DataTable({
            "columnDefs": [
                { "visible": false, "targets": groupColumns }
            ],
            // "order": [[ groupColumns[0], 'asc' ]],
            "drawCallback": function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var lastGroups = [];
                var groups = [];
                groups[0] = [];
                groups[1] = [];
                lastGroups[0] = [];
                lastGroups[1] = [];

                groupColumns.forEach(function (columnIndex, j) {
                    var columnValues = api.column(columnIndex, { page: 'current' }).data();
                    columnValues.each(function (value, i) {
                        groups[j].push(value);
                    });
                    lastGroups[j][columnIndex] = groups[j][groups[j].length - 1];

                        
                        
                        columnValues.each(function (index, i) {
                            if(j == 1){
                                prev[i] = index;
                                if(i == 0 || prev[i-1] != index){
                                    $(rows).eq(i).before(
                                        '<tr class="group"><td colspan="5">' + index + '</td></tr>'
                                    );
                                } 
                            } else {
                                prev1[i] = index;
                                if(i == 0 || prev1[i-1] != prev1[i]){
                                    console.log((i, prev1[i-1], prev1[i]))
                                    $(rows).eq(i).before(
                                        '<tr class="group"><td colspan="5" style="background-color: bisque!important;">' + index + '</td></tr>'
                                    );
                                } 
                            }
                        });
                        //}
                });
            }
        });



        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"

        }).on('changeDate', function (e) {
            formSubmittion();
        });
    });

    $("body").on("change", "#project_type", function (e) {
        var project_type = $(this).val();
        $('#project').val('').trigger('change');
        $('#project').select2('destroy');
        $('#project option').attr('disabled','disabled');
        $('#project option[value=""]').removeAttr('disabled');
        if(project_type==''){
            $('#project option').removeAttr('disabled');
        }
        $.each( projectTypesArr[project_type], function( key, value ) {
            $('#project option[value="'+value+'"]').removeAttr('disabled');
        });
        $("#project").select2();
    });
    $("body").on("click", "#excel", function (e) {
        var fromdate = $("#fromDate").val();
        var todate = $("#toDate").val();
        var project_type = $("#project_type").val();
        var revenue_type = $("#revenue_type").val();
        var project = $("#project").val();
        base_url = '<?php echo base_url(); ?>';
        window.location.href = base_url + "index.php/project/projectTypewiseExcel?revenue_type=" + revenue_type + "&project_type=" + project_type + "&project=" + project + "&fromdate=" + fromdate + "&todate=" + todate;
    });
</script>