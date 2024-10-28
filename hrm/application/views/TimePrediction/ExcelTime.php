<?php
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=export.xls");
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
<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl No.</th>
            <th>Project</th>
            <th>Talent</th>
            <th>Task</th>
            <th>Date</th>
            <th>Time</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 0;
        $alltime = array();
        foreach ($ReportData as $data) {
            ?>
            <tr>
                <td>
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['project'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['talent'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['task'];
                    ?>
                </td>
                <td>
                    <?php
                    echo date('d/m/Y', strtotime($data['date']));
                    ?>
                </td>
                <td>
                    <?php
                    $alltime[] = $data['time'];
                    echo $data['time'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['description'];
                    ?>
                </td>

            </tr>
            <?php
        }
        ?>
    </tbody>
</table>