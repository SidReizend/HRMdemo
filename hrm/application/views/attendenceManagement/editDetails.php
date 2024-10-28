<?php
if ($status == 'Edited') {
    echo 'Old Actual Hour : ' . $oldActual . '
    ';
    echo 'Old Productive Hour : ' . $oldProductive . '
    ';
    echo 'Updated By : ' . $updatedBy . '
     ';
    echo 'Updated On : ' . $updatedOn . '';
} else {
    echo 'Not Edited';
}
?>
