<br/>
<div class="col-sm-1">
    <label for="Month" class="col-sm-2 control-label">Pending <?php echo count($talents); ?></label>                     
    <span class="text-danger">  </span>
</div>
<table id="table_responsive" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Sl No.</th>
            <th>Talent Code</th>
            <th>Talent</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $serialNumber = 0;
        foreach ($talents as $talent) {
            ?>
            <tr class="talentPending" data-id="<?php echo $talent['id']; ?>">
                <td>
                    <!--<input type="checkbox">-->
                    <?php
                    echo ++$serialNumber;
                    ?>
                </td>
                <td>
                    <?php
                    echo $talent['talent_code'];
                    ?>
                </td>
                <td style="cursor: pointer">
                    <?php
                    echo $talent['name'];
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
