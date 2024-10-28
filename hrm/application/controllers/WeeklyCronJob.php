<?php

require 'MailHandler.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class WeeklyCronJob extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('Talent', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
    }

    public function index() {
        $this->checkPendingStatus();
    }

    public function checkPendingStatus() {
        //check for pending sheets in current month
        $monthYear = date('Y-m');
        $fromdate = $monthYear . '-01';
        $todate = date('Y-m-t');
        $hrm = '<a href="http://kjsolutions.ae/hrm/TimePredictionCntrl">HRM</a> ';
        $subject = "Pending Time Sheets";
        $talents = $this->Talent->getTalents();
        $talent_id_Array = array();
        foreach ($talents as $talent) {
            $date = array();
            $name = "";
            $message = "";
            $PendingSubmitReport = $this->TimePredictionModel->PendingSubmitReportTalent($talent['id'], $fromdate, $todate);
            if (count($PendingSubmitReport > 0)) {
                foreach ($PendingSubmitReport as $pending) {
                    $approval_status = $this->TimePredictionModel->ApprovalStatus($pending['talent_id'], $pending['attendance_date']);

                    if (count($approval_status) == 0) {
                        $date[] = date('d/m/Y', strtotime($pending['attendance_date']));
                        $name = $pending['name'];
                    }
                }
            }
            if (count($date) > 0) {
                $message .= "Dear " . $name . "<br>"
                        . "Find below the list of timesheets pending against you. Please submit this ASAP." . $hrm . " <br> ";
                foreach ($date as $dt) {
                    $message .=$dt . "<br>";
                }
                $message .="<p>Note : Please do practise time sheets submission without fail. "
                        . "Do this as the first activity daily after 10:00 am once the attendance for previous day is available</p>";

                $to_mail = $this->Talent->getTalentEmail($talent['id']);
                $this->sendEmailsToTalents($message, $to_mail, $subject);
            }
        }
    }

    public function sendEmailsToTalents($message, $to_mails, $subject) {
        $data['content'] = $message;
        $from = $this->defaultValues->getHrmEmail();
        //abin's and noble's mail
        $result = $this->Talent->getManagersEmails();
        $html = $this->load->view('talentManagement/mail_template', $data, TRUE);
        $hrmName = $this->defaultValues->getValue(19);
        $from = $this->defaultValues->getValue(6);

        $this->load->library('PHPMailer');
        $this->phpmailer->ClearAllRecipients();
        $this->phpmailer->AddAddress($to_mails);

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

}
