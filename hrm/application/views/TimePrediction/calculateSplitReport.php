<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl No.</th>
            <th>Project</th>
            <th>Task</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($tableData as $data) {
            ?>
            <tr  >
                <td>
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['project_code'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['task_code'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $data['time'];
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
