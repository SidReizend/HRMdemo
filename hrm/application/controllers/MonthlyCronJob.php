<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MonthlyCronJob extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('myOd', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
    }

    public function index() {

        die;
        $startDayOfMonth = $this->defaultValues->getValue(12);
        $endDayOfMonth = $this->defaultValues->getValue(13);

        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . $startDayOfMonth) . " -1 month"));
        $dateTo = date("Y-m-d", strtotime($year . '-' . $month . '-' . $endDayOfMonth));
        if (date('Y-m-d') <= $dateTo) {
            die('Date not reached to run');
        }

        $lastDateOfAttendanceProcessing = $this->defaultValues->getValue(2);
        $lastDateOfAttendanceMonth = $month = date('m', strtotime($lastDateOfAttendanceProcessing));
        if (date('m') == $lastDateOfAttendanceMonth) {
            die('Job done');
        }

        //get the start date and end date of monthly report to make
        $startDate = $this->defaultValues->getValue(3);
        $endDate = $this->defaultValues->getValue(4);
        $month = date('m', strtotime($endDate));
        $year = date('Y', strtotime($endDate));

        //check attendance sheet uploaded for the last date 
        //if not then generate a notification for admin
        $this->checkUploadStatus($endDate);
        /*
         * find the approvals pending for the month
         */
        $this->checkApprovalPendingStatus($startDate, $endDate);
        /*
         * check job already done
         */
        $this->checkJobRunStatus($month, $year);
        /*
         * Generate monthly report between dates
         */
        $this->generateMonthlyReport($startDate, $endDate);
    }

    public function checkUploadStatus($date) {
        //check uploaded
        $getStatusUploadedForDate = $this->talentAttendance->checkAttendanceUploadedForDate($date);
        //if not then die
        if (FALSE === $getStatusUploadedForDate) {
            //send message to admin,abin and noble sir
            $message = "Please upload " . date('d/m/Y', strtotime($date)) . " th attendance, monthly process is not done";
            $this->createNotification($message);
            //die('Please upload attendance for ' . $date);
        }
        return;
    }

    public function checkJobRunStatus($month, $year) {
        //check uploaded
        $getAlreadyDoneStatus = $this->talentAttendance->checkJobAlreadyDone($month, $year);
        //if not then die
        if (TRUE === $getAlreadyDoneStatus) {
            die('Report Already Generated');
        }
        return;
    }

    public function createNotification($message) {
        //creating for admin
        $adminsDetails = $this->defaultValues->getTalentAdminsIds();
        foreach ($adminsDetails as $admin) {
            $createNotificationStatus = $this->talentAttendance->createNotificationInTable($message, $admin['id']);
        }
        //creating for noble and abin
        $createNotificationStatus = $this->talentAttendance->createNotificationInTable($message, 2);
        $createNotificationStatus = $this->talentAttendance->createNotificationInTable($message, 3);
    }

    public function checkApprovalPendingStatus($startDate, $endDate) {
        $subject = 'Apprvals pending';
        $pendingApproval = FALSE;
        //check od pending
        $createOdNotificationStatus = $this->myOd->getStatusOdWaitingForApprovalBetweenDates($startDate, $endDate);
        if (TRUE === $createOdNotificationStatus) {
            //create notifcation for admins and noble,abin
            //create mails forreporting officers for whom od are pending
            $message = "Please refer pending od approvals, monthly process is not done";
            $this->createNotification($message);
            //get reporting managers for pending approvals
            $reportingManagersApprovalPending = $this->myOd->getReportingOdWaitingForApprovalBetweenDates($startDate, $endDate);
            $toMails = "";
            foreach ($reportingManagersApprovalPending as $reportingManager) {
                $toMails .= $toMails == "" ? $reportingManager['email'] : (',' . $reportingManager['email']);
            }
            $this->sendEmailsToTalents($message, $toMails, $subject);
            $pendingApproval = TRUE;
        }
        $createLeaveNotificationStatus = $this->leaves->getStatusLeaveWaitingForApprovalBetweenDates($startDate, $endDate);
        if (TRUE === $createLeaveNotificationStatus) {
            //create notifcation for admins and noble,abin
            //create mails forreporting officers for whom od are pending
            $message = "Please refer pending leave approvals, monthly process is not done";
            $this->createNotification($message);
            //get reporting managers for pending approvals
            $reportingManagersApprovalPending = $this->leaves->getReportingLeaveWaitingForApprovalBetweenDates($startDate, $endDate);
            $toMails = "";
            foreach ($reportingManagersApprovalPending as $reportingManager) {
                $toMails .= $toMails == "" ? $reportingManager['email'] : (',' . $reportingManager['email']);
            }
            $this->sendEmailsToTalents($message, $toMails, $subject);
            $pendingApproval = TRUE;
        }
        $createCompNotificationStatus = $this->talentAttendance->getStatusCompWaitingForApprovalBetweenDates($startDate, $endDate);
        if (TRUE === $createCompNotificationStatus) {
            //create notifcation for admins and noble,abin
            //create mails forreporting officers for whom od are pending
            $message = "Please refer pending comp-off approvals, monthly process is not done";
            $this->createNotification($message);
            //get reporting managers for pending approvals
            $reportingManagersApprovalPending = $this->talentAttendance->getReportingCompWaitingForApprovalBetweenDates($startDate, $endDate);
            $toMails = "";
            foreach ($reportingManagersApprovalPending as $reportingManager) {
                $toMails .= $toMails == "" ? $reportingManager['email'] : (',' . $reportingManager['email']);
            }
            $this->sendEmailsToTalents($message, $toMails, $subject);
            $pendingApproval = TRUE;
        }
        if ($this->defaultValues->getValue(26)) {
            $createEncashNotificationStatus = $this->talentAttendance->getStatusEncashmentWaitingForApprovalBetweenDates($startDate, $endDate);
            if (TRUE === $createEncashNotificationStatus) {
                //create notifcation for admins and noble,abin
                //create mails forreporting officers for whom od are pending
                $message = "Please refer pending encashment approvals, monthly process is not done";
                $this->createNotification($message);
                //get reporting managers for pending approvals
                $reportingManagersApprovalPending = $this->talentAttendance->getReportingEncashmentWaitingForApprovalBetweenDates($startDate, $endDate);
                $toMails = "";
                foreach ($reportingManagersApprovalPending as $reportingManager) {
                    $toMails .= $toMails == "" ? $reportingManager['email'] : (',' . $reportingManager['email']);
                }
                $this->sendEmailsToTalents($message, $toMails, $subject);
                $pendingApproval = TRUE;
            }
        }
        if (TRUE === $pendingApproval) {
            die("Pending approval");
        }
        return;
    }

    public function sendEmailsToTalents($message, $toMails, $subject) {
        $data['content'] = $message;
        $from = $this->defaultValues->getHrmEmail();
        $hrmName = $this->defaultValues->getValue(19);
        //abin's and noble's mail
        $result = $this->talent->getManagersEmails();
        $html = $this->load->view('talentManagement/mail_template', $data, TRUE);
        $this->load->library('PHPMailer');
        $this->phpmailer->ClearAllRecipients();
        $this->phpmailer->AddAddress($toMails);
        foreach ($result as $ccMails) {
            $this->phpmailer->AddCc($ccMails['email']);
        }
        $this->phpmailer->IsMail();
        $this->phpmailer->From = $from;
        $this->phpmailer->FromName = $hrmName;
        $this->phpmailer->IsHTML(true);
        $this->phpmailer->Subject = $subject;
        $this->phpmailer->Body = $html;
        $result = $this->phpmailer->Send();
    }

    public function generateMonthlyReport($dateFrom, $dateTo) {

        $currentWorkingDirectory = getcwd();
        die();
        exec("php " . $currentWorkingDirectory . "/croneJob/monthlyReportMaking.php");
        die();
        //get talents joined before joining date
        $talsntsDetails = $this->talent->getTalentsByDateOfJoining($dateTo);
        foreach ($talsntsDetails as $row_select_talents) {

            //initializing variables

            $lop_deducted = false;

            $talentId = $row_select_talents['id'];
            $roleStream = $row_select_talents['role_stream'];
            $compOffAvailable = 0;
            $elAvailable = 0;
            $shortage = 0;
            $compOffDeductedProcess = 0;
            $compOffDeductedShortage = 0;
            $elDeductedProcess = 0;
            $elDeductedShortage = 0;
            $encashment = 0;
            $lopByProcess = 0;
            $lopByShortage = 0;
            $totalProductiveSeconds = 0;
            $presentDay = 0;
            $extraAttendanceSeconds = 0;
            $extraOdSeconds = 0;
            $hourShortage = 0;
            $secondsShortage = 0;
            $encashmentForExtra = 0;

            $lopCheckStatus = 0;
            $lop_count = 0;
            $first_time = true;

            $extraAttendanceHoursInserting = 0;
            $extraOdHoursInserting = 0;
            $totalHourRequiredInserting = 0;
            $totalProductiveHoursInserting = 0;
            $hourShortageInserting = 0;

            $averageHourRequired = $row_select_talents['average_working_hour_per_day'];
            $averageHourSeconds = $this->talentAttendenceSheet->hourToSecondsConvertion($averageHourRequired);

            //find leave left for the talent

            $remainingAllLeaves = $this->leaves->getMyLeavesLeft($talentId);

            foreach ($remainingAllLeaves as $rowTalentLeaveLeft) {
                if ($rowTalentLeaveLeft['id'] == '2') {
                    $elAvailable = $rowTalentLeaveLeft['leave_left'];
                }
            }
            //generating report of each talent
            $dateCount = $dateFrom;
            while (strtotime($dateTo) >= strtotime($dateCount)) {

                $resultTalentAttendance = $this->talentAttendance->talentAttendanceForDateTalentReturnArray($dateCount, $talentId);
                foreach ($resultTalentAttendance as $rowTalentAttance) {

                    if ($rowTalentAttance['encashment'] == 1) {
                        $encashment++;
                    }
                    if ($rowTalentAttance['avg_consider'] == 0 && $rowTalentAttance['is_holiday'] == 0 && $rowTalentAttance['is_leave'] == 0) {
                        /* leave status get  0 && 1 exclude */

                        /**
                         * Get Compoff
                         */
                        $lop_insert_talent = 0;

                        /*
                         * check any comp-off credicted before the date
                         */

                        $numberOfCompOffBeforeTheDate = $this->leaves->checkCompoffAgainstDate($talentId, $dateCount);

//                        $sql_get_compoff = findDateOfCompOffBeforeTheDate($talentId, $dateCount);
//                        $res_get_compoff = mysql_fetch_array($sql_get_compoff);
//                        $num_get_compoff = mysql_num_rows($sql_get_compoff);
                        if ($numberOfCompOffBeforeTheDate > 0) {
                            $getMinIdOfCompOffAvailable = $this->leaves->findDateOfCompOffBeforeTheDate($talentId, $dateCount);
                            $compOffAvailable = 1;
                        } else {
                            $compOffAvailable = 0;
                        }
                        $lop_deducted = true;
                        $message = 'System Applied due to absent';
                        if ($row_select_talents['monthly_process'] == 1) {
                            if ($compOffAvailable > 0) {
                                //reduce a compOff count
                                $this->leaves->leaveDeduction($talentId, 1, $dateCount, $message, 0, $getMinIdOfCompOffAvailable);
                                //set compOff as used
                                $this->leaves->updateCompOffAsUsed($getMinIdOfCompOffAvailable);
                                $compOffDeductedProcess++;
                            } else if ($elAvailable > 0) {
                                $this->leaves->leaveDeduction($talentId, 2, $dateCount, $message, 0, 0);
                                $elAvailable--;
                                $elDeductedProcess++;
                            } else {
                                $this->leaves->leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                                $lop_insert_talent = 1;
                                $lopByProcess++;
                            }
                        }
                    } else {
                        if ($rowTalentAttance['avg_consider'] == 1) {
                            $presentDay++;
                            $totalProductiveSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($rowTalentAttance['productive_hrs']);
                        }
                    }
                    if ($rowTalentAttance['is_holiday'] == 1 || $rowTalentAttance['is_leave'] == 1) {
                        $extraOdSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($rowTalentAttance['extra_od_hrs']);
                        $extraAttendanceSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($rowTalentAttance['extra_attendance_hrs']);
                    }
                }

                //finding the lop exists for the date

                $is_current_lop = false;

                $final_day_lop = false;

                $sqlLopFinding = "SELECT tld.`id` "
                        . "FROM `talent_leave_date` tld "
                        . "JOIN `talent_leave` tl ON tld.talent_leave=tl.id "
                        . "WHERE `tld`.`date_of_leave`='$dateCount' "
                        . "AND `tl`.`talent`='$talentId' "
                        . "AND `tld`.`leave_type`='0' AND tld.`is_approved`='1'";
                $resultLopFinding = mysql_query($sqlLopFinding);

                $numLopFinding = $this->leaves->lopFinding($talentId, $dateCount);
                if ($numLopFinding > 0) {
                    $is_current_lop = true;
                }

                if (true === $first_time) {
                    $first_time = false;
                    if (false === $is_current_lop && false === $lop_deducted) {
                        // Check if average consider is 1 or 0
                        // if average consider is 0 then get last_day_lop of prev month 
                        // if  last_day_lop of prev month is 1  , then increment lop_count,   $final_day_lop = true
                        // else  $first_time = false;  
                        if ($rowTalentAttance['avg_consider'] == 0) {
                            $lopFinding = $this->leaves->lastDayLop($talentId);
                            if (1 == $lopFinding) {
                                $lop_count++;
                                $final_day_lop = true;
                            }
                        }
                    }
                } else {
                    if (1 == $lopCheckStatus) {

                        if (false == $is_current_lop && false === $lop_deducted) {
                            // Check if average consider is 1 or 0
                            // if 0 then increment lop_count,   $final_day_lop = true     
                            // if 1 then  $lopCheckStatus = 0 
                            if ($rowTalentAttance['avg_consider'] == 0) {
                                $lop_count++;
                                $final_day_lop = true;
                            } else {
                                $lopCheckStatus = 0;
                            }
                        }
                    }
                }

                if (true == $is_current_lop) {
                    $lopCheckStatus = 1;
                }

                // if the date is final day of month 
                //  $final_day_lop === true, then  last_day_lop of current month is 1 
                //  else  last_day_lop of current month is 0

                if ($dateCount == $dateTo) {
                    if (true == $final_day_lop) {
                        $last_day_lop = 1;
                    } else {
                        $last_day_lop = 0;
                    }
                }

                $dateCount = strtotime("+1 day", strtotime($dateCount));
                $dateCount = date("Y-m-d", $dateCount);
            }
            //calculate the shortage based on the frequency
            $calculateShortage = 1;
            $presentDayTemp = $presentDay;
            if (2 == $row_select_talents['average_hour_calculation']) {
                if (0 == ($month % 3)) {
                    /*
                     * find the two previous months
                     */
                    $monthPrev1 = date('m', strtotime(date('Y-m-d') . " -1 month"));
                    $yearPrev1 = date('Y', strtotime(date('Y-m-d') . " -1 month"));
                    $monthPrev2 = date('m', strtotime(date('Y-m-d') . " -2 month"));
                    $yearPrev2 = date('Y', strtotime(date('Y-m-d') . " -2 month"));
                    /*
                     * find the sum of present days and productive hours of previous month
                     */
                    //finds the hours earned

                    $hourEarnedForMonth = $this->talentAttendance->hourEarnedForMonthYearTalent($monthPrev1, $yearPrev1, $talentId);
                    $totalProductiveSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($hourEarnedForMonth);
                    $hourEarnedForMonth = $this->talentAttendance->hourEarnedForMonthYearTalent($monthPrev2, $yearPrev2, $talentId);
                    $totalProductiveSeconds += $this->talentAttendenceSheet->hourToSecondsConvertion($hourEarnedForMonth);
                    //finds the present Days
                    $presentDayTemp += $this->talentAttendance->presentDayForMonthYearTalent($monthPrev1, $yearPrev1, $talentId);
                    $presentDayTemp += $this->talentAttendance->presentDayForMonthYearTalent($monthPrev2, $yearPrev2, $talentId);
                } else {
                    $calculateShortage = 0;
                }
            }
            //leave due to attendance shortage
            //$totalHourRequired = ($averageHourRequired) * $presentDayTemp;
            //avoid management users from shortage calculation
            if ($roleStream == $this->defaultValues->getValue(28))
                $calculateShortage = 0;

            if (1 == $calculateShortage) {
                $totalSecondRequired = ($averageHourSeconds) * $presentDayTemp;
                if ($totalSecondRequired > $totalProductiveSeconds) {
                    //$hourShortage = $totalHourRequired - $totalProductiveHours;
                    //$secondsShortage = ($averageHourRequired * $presentDay * 3600) - $totalProductiveSeconds;

                    $secondsShortage = ($totalSecondRequired) - $totalProductiveSeconds;


                    if ($secondsShortage >= (3 * 60 * 60)) {
                        if ($secondsShortage % $averageHourSeconds == 0) {
                            $shortage = ceil($secondsShortage / $averageHourSeconds) + 2;
                        } else {
                            $shortage = ceil($secondsShortage / $averageHourSeconds) + 1;
                        }
                    }
                }
            }
            //echo $shortage;
            for ($i = 1; $i <= $shortage; $i++) {

                $lop_insert_talent = 0;

                $numberOfCompOffBeforeTheDate = $this->leaves->checkCompoffAgainstDate($talentId, $dateCount);
                if ($numberOfCompOffBeforeTheDate > 0) {
                    $getMinIdOfCompOffAvailable = $this->leaves->findDateOfCompOffBeforeTheDate($talentId, $dateCount);
                    $compOffAvailable = 1;
                } else {
                    $compOffAvailable = 0;
                }
                $lop_deducted = true;
                $message = 'System Applied due to attendance shortage';
                //if monthly leave process is enabled for the role stream then does
                if ($row_select_talents['monthly_process'] == 1) {
                    if ($compOffAvailable > 0) {
                        //reduce a compOff count
                        $this->leaves->leaveDeduction($talentId, 1, $dateCount, $message, 0, $getMinIdOfCompOffAvailable);
                        //set compOff as used
                        $this->leaves->updateCompOffAsUsed($getMinIdOfCompOffAvailable);
                        $compOffDeductedShortage++;
                    } else if ($elAvailable > 0) {
                        $this->leaves->leaveDeduction($talentId, 2, $dateCount, $message, 0, 0);
                        $elAvailable--;
                        $elDeductedShortage++;
                    } else {
                        $this->leaves->leaveDeduction($talentId, 0, $dateCount, $message, 1, 0);
                        $lop_insert_talent = 1;
                        $lopByShortage++;
                    }
                }
            }

            $sqlTalentLop = "SELECT `lop` "
                    . "FROM `talent_lop` "
                    . "WHERE `talent`='$talentId'";
            $resultTalentLop = mysql_query($sqlTalentLop);
            $rowTalentLop = mysql_fetch_array($resultTalentLop);


            //adding already updated lop
            $lopByLeaveCount = $this->leaves->findLopCount($talentId);

            $lop_count += $lopByLeaveCount;

            //washoff lops

            $this->leaves->washOffLops($talentId);

            $sqlUpdateTalentLop = "UPDATE `talent_lop` SET `lop`='0' WHERE `talent`='$talentId'";
            mysql_query($sqlUpdateTalentLop);

            //$extraAttendanceHours = $extraAttendanceSeconds / 3600;
            //$extraOdHours = $extraOdSeconds / 3600;
            if (($extraAttendanceSeconds + $extraOdSeconds) > 0) {
                if ($roleStream != $this->defaultValues->getValue(28))
                    $encashmentForExtra += floor(($extraAttendanceSeconds + $extraOdSeconds) / (8 * 60 * 60));
            }
            $extraAttendanceHoursInserting = secondsToMinitues($extraAttendanceSeconds);
            $extraOdHoursInserting = secondsToMinitues($extraOdSeconds);
            $totalExtraHoursInserting = secondsToMinitues($extraAttendanceSeconds + $extraOdSeconds);
            $totalHourRequiredInserting = secondsToMinitues($averageHourSeconds * $presentDay);
            $totalProductiveHoursInserting = secondsToMinitues($totalProductiveSeconds);
            $hourShortageInserting = secondsToMinitues($secondsShortage);
            $sqlInsertData = "INSERT INTO `talent_monthly_report`(`talent_id`, `month`, `year`, "
                    . "`lop_by_process`, `comp_off_deduction_by_process`, `el_deduction_by_process`, "
                    . "`lop_by_shortage`, `extra_working_hrs`, `extra_od_hrs`, `total_present_days`, "
                    . "`hrs_required`, `hrs_earned`, `hrs_shortage`, `encashment`, "
                    . "`comp_off_deduction_by_shorage`, `el_deduction_by_shortage`,`encashment_for_extra`,"
                    . "`last_day_lop`,`lop_due_to_lop`,`total_extra_hours`) VALUES "
                    . "('$talentId','$month','$year','$lopByProcess','$compOffDeductedProcess','$elDeductedProcess','$lopByShortage',"
                    . "'$extraAttendanceHoursInserting','$extraOdHoursInserting','$presentDay',"
                    . "'$totalHourRequiredInserting','$totalProductiveHoursInserting','$hourShortageInserting',"
                    . "'$encashment','$compOffDeductedShortage','$elDeductedShortage','$encashmentForExtra',"
                    . "'$last_day_lop','$lop_count','$totalExtraHoursInserting')";
            //die();
            $resultInsert = mysql_query($sqlInsertData);
        }
    }

}
