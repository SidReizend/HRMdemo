<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class docUrl extends CI_Controller {

    public function docUrlGenerator() {
        //using url for get id
        $this->load->helper('url');
        $docName = $this->uri->segment(3);
        $extension = $this->findExtension($docName);
        $type = Array(1 => 'jpg', 2 => 'jpeg', 3 => 'png', 4 => 'gif', 5 => 'pdf');
        if (!in_array($extension, $type)) { //check image extension not in the array $type
            $url = "https://view.officeapps.live.com/op/view.aspx?src=" . base_url() . "uploads/" . $docName;
        } else {
            $url = base_url() . "uploads/" . $docName;
        }
        $data['url'] = $url;
//        echo $url;
//        die;
        $this->load->view('docManagement/viewDoc', $data);
    }
    public function docUrlGeneratorForAssestsDoc() {
        //using url for get id
        $this->load->helper('url');
        $docName = $this->uri->segment(3);
        $extension = $this->findExtension($docName);
        $type = Array(1 => 'jpg', 2 => 'jpeg', 3 => 'png', 4 => 'gif', 5 => 'pdf');
        if (!in_array($extension, $type)) { //check image extension not in the array $type
            $url = "https://view.officeapps.live.com/op/view.aspx?src=" . base_url() . "assets/document/" . $docName;
        } else {
            $url = base_url() . "assets/document/" . $docName;
        }
        $data['url'] = $url;
//        echo $url;
//        die;
        $this->load->view('docManagement/viewDoc', $data);
    }

    public function findExtension($docName) {
        $path_parts = pathinfo($docName);
        //var_dump($path_parts);
       // var_dump($path_parts);
        return $path_parts['extension'];
    }

}
