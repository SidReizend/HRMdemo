<?php

//$con = mysql_connect('localhost', 'nizam_ahmed', 'nizam1234');
//if (!$con) {
//    die('Failed to connect to server: ' . mysql_error());
//}
////--------------connecting db-------------//
//$db = mysql_select_db("reizend_hrm");
//if (!$db) {
//    die("Unable to select database");
//}
//
//$sql_select_talents = "SELECT `id` FROM `talents` WHERE `is_resigned`='0'";
//$result_select_talents = mysql_query($sql_select_talents);
//while ($row_select_talents = mysql_fetch_array($result_select_talents)) {
//    $sql_update_talent_el = "UPDATE `talent_leave_left` SET `leave_left`=(`leave_left`/2)+5 "
//            . "WHERE `talent`='" . $row_select_talents['id'] . "' AND `leave_type`='2'";
//    $result_update_talent_el = mysql_query($sql_update_talent_el);
//    $sql_update_talent_compoff = "UPDATE `talent_leave_left` SET `leave_left`=0 "
//            . "WHERE `talent`='" . $row_select_talents['id'] . "' AND `leave_type`='8'";
//    $result_update_talent_compoff = mysql_query($sql_update_talent_compoff);
//}

?>