<?php
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=Project Consolidate Report.xls");
header("Content-Transfer-Encoding: binary ");
?>
<table  id="example" style="width: 100%;" border="1" >
    <thead style="background-color:#1fa1e1;">
        <tr>
            <th colspan="<?php echo sizeof($heading); ?>" align="center"><?php echo $tableName; ?></th>
        </tr>
        <tr>
            <?php
            foreach ($heading as $head) {
                echo '<th>' . $head . '</th>';
            }
            ?>

        </tr>
    </thead>

    <tbody>
        <?php
        $i = 0;
        foreach ($content as $row) {
            ?>
            <tr>
                <?php
                foreach ($row as $x) {
                    ?>
                    <td>
                        <?php
                        echo $x;
                        ?>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
            $i++;
        }
        ?>
    </tbody>
</table>