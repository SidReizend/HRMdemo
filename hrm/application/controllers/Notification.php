<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('holiday', '', TRUE);
        $this->load->model('myOd', '', TRUE);
    }

    public function index() {
        $this->load->model('notifications');

        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $user_id = $session_data['id'];
            $data['username'] = $session_data['username'];



            /* GET NOTIFICATIONS TO USER */
            $notifications = $this->notifications->getNotificationByUser($user_id);
            $data['notifications'] = $notifications;



            $this->load->view('home/notification', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
            //$this->load->view('home');
        }
    }

    public function checknew() {
        /* GET UNREAD NOTIFICATIONS COUNTER */
        $this->load->model('notifications');

        $session_data = $this->session->userdata('logged_in');
        $user_id = $session_data['id'];

        $notifications = $this->notifications->getUnreadNotification($user_id);

        echo $notifications->cnt;
    }

    public function getMessages() {
        /* GET UNREAD NOTIFICATIONS COUNTER */
        $this->load->model('notifications');

        $session_data = $this->session->userdata('logged_in');
        $user_id = $session_data['id'];

        $notifications = $this->notifications->getUnreadNotification($user_id);

        if (0 == $notifications->cnt) {
            $notifications_array = $this->notifications->getNotificationByUser($user_id, false, 3);
        } else {
            $notifications_array = $this->notifications->getNotificationByUser($user_id, true);
        }
        $notifications_html = "";

        foreach ($notifications_array as $notif) {

            $notifications_html .= '
                <li>
                <div style="margin:2px;background-color:#ccc; padding: 6px;">
                      <small><i class="fa fa-clock-o"></i> ' . $notif['message_time_formatted'] . '</small>
                       <p style="color:' . strtolower($notif['type']) . '; font-size:15px;"> <strong>' . $notif['message'] . '</strong></p>
                    </div>
                   
</li>                    
';
        }

        $data = array('read' => 1);
        $this->db->where('user', $user_id);
        $this->db->update('notifications', $data);

        echo $notifications_html;
    }

}
