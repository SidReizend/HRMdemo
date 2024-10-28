<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PasswordChangeRequest extends CI_Controller {

    public function index() {

        $this->session->unset_userdata('logged_in');
        session_destroy();
        $this->load->helper(array('form'));
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('user', '', TRUE);

        if ($this->input->post()) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('username', 'username', 'trim|required|xss_clean');

            $username = $this->input->post('username');
            $pc_username = $this->user->getUserPasswordChangeDetails($username);

            if (false === $pc_username) {
                $this->session->set_flashdata('flashError', "Sorry!. No username as " . $pc_username . " found in our database!.");
            }

            if ($this->form_validation->run() == TRUE) {
                if ($pc_username) {
                    $email = $pc_username->email;
                    $send_token = false;
                    $resend_token = false;
                    if (null == $pc_username->p_c_token_expire_date || "0000-00-00" == $pc_username->p_c_token_expire_date) {
                        /* Can send token */
                        $send_token = true;
                    } else {
                        if (1 == $pc_username->p_c_token_used) {
                            /* Can send token */
                            $send_token = true;
                        } else {
                            /* Ckeck if token is expired */
                            $today = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                            $expire = DateTime::createFromFormat('Y-m-d', $pc_username->p_c_token_expire_date);
                            if ($today == $expire) {
                                $resend_token = true;
                            } else {
                                $send_token = true;
                            }
                        }
                    }
                    if (true === $send_token || true === $resend_token) {

                        if (true === $send_token) {
                            $key = md5(microtime() . rand());

                            $data = array('password_change_token' => $key, 'p_c_token_used' => 0, 'p_c_token_expire_date' => date('Y-m-d'));
                            $this->db->where('id', $pc_username->id);
                            $this->db->update('users', $data);
                        }

                        if (true === $resend_token) {
                            $key = $pc_username->password_change_token;
                        }
                        $config = Array(
                            'mailtype' => 'html',
                        );


                        $this->load->library('email');
                        $from = $this->defaultValues->getHrmEmail();


                        $email_dispaly = substr($pc_username->email, 0, 4) . "***************" . substr($pc_username->email, -8);

                        $password_change_token = base_url() . "index.php/passwordChangeRequest/auth/" . $key . "";
                        $data['password_change_token'] = $password_change_token;
                        $data['user_name'] = $pc_username->username;

                        $body = $this->load->view('password_change_request/password_token_mail', $data, TRUE);
                        $hrmName = $this->defaultValues->getValue(19);
                        $from = $this->defaultValues->getValue(6);
                        $subject = $hrmName . ': Password Change URL is provided';
                        $email = $pc_username->email;
                        $cc = "";
                        //sending mail
                        $this->load->library('PHPMailer');
                        $this->phpmailer->ClearAllRecipients();
                        $this->phpmailer->AddAddress($email);
                        $this->phpmailer->IsMail();
                        $this->phpmailer->From = $from;
                        $this->phpmailer->FromName = $hrmName;
                        $this->phpmailer->IsHTML(true);
                        $this->phpmailer->Subject = $subject;
                        $this->phpmailer->Body = $body;
                        $result = $this->phpmailer->Send();
                        if ($result) {
                            $this->session->set_flashdata('flashSuccess', 'Key is send to your email-id ' . $email_dispaly);
                        } else {
                            $this->session->set_flashdata('flashError', 'Failed  to send mail');
                        }
                    }
                }
            }
        }

        $this->load->view('password_change_request/home');
    }

    public function auth() {
//        die();
        $this->session->unset_userdata('logged_in');
        session_destroy();
        $this->load->helper(array('form'));


        $key = $this->uri->segment(3);

        $this->load->model('user', '', TRUE);

        $user = $this->user->getDetailsByPasswordChangeKey($key);


        $error = false;

        if (false !== $user) {
            // p_c_token_used, p_c_token_expire_date, id
            if (false === $error && 1 == $user->p_c_token_used) {
                $this->session->set_flashdata('flashError', 'This URL is used already!');
                $error = true;
            }

            $today = date('Y-m-d');

            if (false === $error && $today != $user->p_c_token_expire_date) {
                $this->session->set_flashdata('flashError', 'This URL is expired !');
                $error = true;
            }
        } else {
            $error = true;
            $this->session->set_flashdata('flashError', 'This URL is found to be invalid!');
        }

        if (false === $error && $this->input->post()) {
            $this->load->library('form_validation');

            $user_id = $user->id;

            //for form validayion, setting rules
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|matches[confpassword]');
            $this->form_validation->set_rules('confpassword', 'Confirm Password', 'trim|required|xss_clean');

            $password = $this->input->post('password');
            $password_enc = md5($password);


            if ($this->form_validation->run() == TRUE) {
                $data = array('p_c_token_used' => 1, 'force_password_change' => 0, 'password' => $password_enc);
                $this->db->where('id', $user_id);
                $this->db->update('users', $data);
                $this->session->set_flashdata('flashSuccess', 'Password reset completed successfully!. Please login to continue!');
                redirect('login');
            }
        }

        if (false === $error) {
            $this->load->view('password_change_request/change_password');
        } else {
            redirect('login');
        }
    }

}
