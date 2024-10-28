<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class certificates extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('certificate', '', TRUE);
    }

    public function index() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        $talent = $sess_data['talentId'];
        $cerificateTypes = $this->certificate->getCerificateTypes();
        $cerificateTypesArray = array(
            '' => '[SELECT]'
        );
        foreach ($cerificateTypes as $cerificateType) {
            $cerificateTypesArray[$cerificateType['id']] = $cerificateType['type'];
        }
        $data['cerificateTypes'] = $cerificateTypesArray;
        $data['selectedCerificateType'] = $this->input->post('cerificateType');
        if ($this->input->post()) {
            $this->load->library('form_validation');
            //Validation Success.  
            // Do insert operation here
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|pdf|docx|doc';
            $this->load->library('upload', $config);
            $upload_data = $this->upload->data();
            $upload_error = false;
            if ($_FILES['uploadFile']['error'] != 4) {
                if (!$this->upload->do_upload('uploadFile')) {
                    $upload_error = true;
                    $this->session->set_flashdata('flashError', $this->upload->display_errors());
                } else {
                    $upload_data = $this->upload->data();
                    $fileName = $upload_data['file_name'];
                }
            }
            if (false === $upload_error) {
                $cerificateType = $this->input->post('cerificateType');
                $nameOfCertificates = $this->input->post('nameOfCertificates');
                $insertCertificateData = array(
                    'type' => $cerificateType,
                    'name_of_certificate' => $nameOfCertificates,
                    'filename' => $fileName,
                    'talent' => $talent,
                    'date_of_upload' => date('Y-m-d')
                );
                $queryResult = $this->db->insert('certificates', $insertCertificateData);
                $this->session->set_flashdata('flashSuccess', 'File Uploaded successfully');
                redirect('certificates', 'refresh');
            } else {
                //$this->session->set_flashdata('flashError', 'File Upload Failed');
            }
        }
        $data['myCertificates'] = $this->certificate->getMyCerificates($talent);
        $this->load->view('certificatesManagement/certificates', $data);
    }

}
