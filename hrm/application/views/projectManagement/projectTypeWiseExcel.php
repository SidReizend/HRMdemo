<?php
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=project type wise report.xls");
header("Content-Transfer-Encoding: binary ");
?>
<style>
    table {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        border-collapse: collapse;
        border-spacing: 0;
        font-weight: bold;
    }

    th, td {
        border-color: #ccc;
        border-style: solid;
    }

    th {
        background: #E5E5E5;
        height: 30px;
    }

    body, table, th, td {
        color: #000000;
    }

    td {
        padding-left: 3px;
        height: 20px;
        word-wrap: break-all;
    }
</style>
<table id="table_responsive1" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="4">Project Type Wise Report ( <?php echo $fromdate; ?> - <?php echo $todate; ?>)</th>
        </tr>
    </thead>
</table>
<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl No.</th>
            <th>Revenue Type</th>
            <th>Project Type</th>
            <th>Total Hrs</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 0;
        foreach ($results as $result) {
            $rowspan = count($result['types']);
            if($rowspan == 0){
                $rowspan = 1;
            }
        ?>
            <tr>
                <td rowspan="<?php echo $rowspan; ?>">
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td rowspan="<?php echo $rowspan; ?>">
                    <?php
                    echo $result['name'];
                    $loopindex = 0;
                    ?>
                </td>
            <?php foreach ($result['types'] as $res_data) { ?>
                <?php if($loopindex != 0) { ?>
                    <tr>
                <?php } $loopindex = 1; ?>
                <td>
                    <?php
                    echo $res_data['name'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $res_data['time'];
                    ?>
                </td>
                </tr>
            <?php  } ?>
        <?php  } ?>
    </tbody>
</table>