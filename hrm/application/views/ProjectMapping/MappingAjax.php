<?php
if (isset($home_page)) {
    ?>
    <div  class="col-sm-12">
        Description : <?php echo $getProjectMapped[0]['description']; ?>

    </div>
    <div  class="col-sm-12 demo-table">
        Importance: <ul>
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $selected = "";
                if (!empty($getProjectMapped[0]["rating"]) && $i <= $getProjectMapped[0]["rating"]) {
                    $selected = "selected";
                }
                ?>
                <li class='<?php echo $selected; ?>' >&#9733;</li>  
            <?php } ?>
        </ul>

    </div>
    <?php
}

