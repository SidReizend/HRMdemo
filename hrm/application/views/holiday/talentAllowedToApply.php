<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl. No.</th>
            <th>Talent</th>
            <th>Added By</th>
            <th>Added On</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 0;
        foreach ($talentAllowedToApply as $talentAllowed) {
            ?>
            <tr>
                <td>
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td style="cursor: pointer">
                    <?php
                    echo $talentAllowed['name'];
                    ?>
                </td>
                <td style="cursor: pointer">
                    <?php
                    echo $talentAllowed['added_by'];
                    ?>
                </td>
                <td style="cursor: pointer">
                    <?php
                    echo date('d/m/Y h:i:a', strtotime($talentAllowed['created_at']));
//                    echo $leave['added_on'];
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

