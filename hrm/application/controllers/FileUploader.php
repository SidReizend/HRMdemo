<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class FileUploader extends CI_Controller {

    public function index() {
        /*         * *****************************************************
         * Only these origins will be allowed to upload images *
         * **************************************************** */
        $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://cashbills.in");

        /*         * *******************************************
         * Change this line to set the upload folder *
         * ******************************************* */
        $imageFolder = "uploads/";

        reset($_FILES);
        $temp = current($_FILES);
        if (is_uploaded_file($temp['tmp_name'])) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                // same-origin requests won't set an origin. If the origin is set, it must be valid.
                if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
                    //return new Response('alert("Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN'].'");');
                } else {
                    echo "alert('Origin Denied');";
                }
            }

            /*
              If your script needs to receive cookies, set images_upload_credentials : true in
              the configuration and enable the following two headers.
             */
            // header('Access-Control-Allow-Credentials: true');
            // header('P3P: CP="There is no P3P policy."');
            // Sanitize input
            if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                echo "alert('Invalid file name.');";
            }

            // Verify extension
            if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                echo "alert('Invalid extension.[GIF, JPG, PNG are only alloweded!!]');";
            }

            // Accept upload if there was no origin, or if it is an accepted origin
            $extension = pathinfo($temp['name'], PATHINFO_EXTENSION);
            $uid = uniqid();
            $filetowrite = $imageFolder . $uid . "." . $extension;
            move_uploaded_file($temp['tmp_name'], $filetowrite);
            $site_base_path = base_url();

            // Respond to the successful upload with JSON.
            // Use a location key to specify the path to the saved image resource.
            // { location : '/your/uploaded/image/file'}
            // 
            // 
            //
            $filename = $site_base_path . "/" . $filetowrite;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($filename));

// Format the image SRC:  data:{mime};base64,{data};
            //  $src = 'data: ' . mime_content_type($filename) . ';base64,' . $imgData;


            $source = "data:image/" . $extension . ";base64," . $imgData;

            echo "top.$('.mce-btn.mce-open').parent().find('.mce-textbox').val('" . $source . "');";
//echo "top.$('.mce-btn.mce-open').parent().find('.mce-textbox').val('" . $source . "').closest('.mce-window').find('.mce-primary').click();";
        } else {
            // Notify editor that the upload failed
            echo "alert('Server Error');";
        }
    }

}
