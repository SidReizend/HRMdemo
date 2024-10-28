<thead>
    <tr>
        <th>Sl No.</th>
        <th>Talent Name</th>
        <th>Skills</th>
    </tr>
</thead>
<tbody>
    <?php
    $serialNumber = 1;
    if (!empty($userArray)) {
        foreach ($userArray as $user) {
            ?>
            <tr>
                <td>
                    <?php
                    echo $serialNumber++;
                    ?>
                </td>
                <td>
                    <?php
                    echo $user['name'];
                    ?>
                </td>
                <td width="70%">
                    <div class="panel">
                        <div class="panel-body">
                            <?php
                            $leghtArray = sizeof($user['skill']);
                            if (!empty($user['skill'])) {
                                for ($lengtht = 0; $lengtht < $leghtArray; $lengtht++) {
                                    if($skills === $user['skill'][$lengtht]){
                                        $class = 'btn-success';
                                    }else{
                                       $class = 'btn-info'; 
                                    }
                                    ?>
                                    <button type="button" class="btn btn-labeled <?php echo $class; ?>">
                                        <?php echo $user['skill'][$lengtht]; ?>
                                        <span class="label label-default"><?php echo $user['year'][$lengtht]; ?> Yr exp</span>
                                        <span class="label label-default"><?php echo $user['skill_level'][$lengtht]; ?></span>
                                    </button>
                                <?php if($lengtht%6 == 0 && $lengtht != 0){ ?><br><br><?php } ?>
                                    <?php
                                }
                            } else {
                                echo "No Skills Added";
                            }
                            ?>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
        }
    }else{
        ?>
            <tr>
                <td colspan="3" align="right"><h4>No data found!</h4></td>
            </tr>
   <?php } ?>
    ?>
</tbody>
