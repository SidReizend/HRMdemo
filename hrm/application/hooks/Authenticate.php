<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Myclass
 *
 * @author nizam
 */
class Authenticate {

    function __construct() {
        $this->ci = & get_instance();
    }

    public function checkLogin() {

        $publicRoots = array('login', 'verifyLogin', 'passwordChangeRequest', 'passwordChangeRequest/auth', 'weeklyCronJob', 'monthlyCronJob', 'test');


        $uri_string = $this->ci->router->fetch_class();


        if (null === $this->ci->session->userdata('logged_in')) {
            /* If not logged in and acess secure page */
            if (!in_array($uri_string, $publicRoots)) {
                redirect('login', 'refresh');
                die;
            }
        } else {

            /* If  logged then check whether enabled or resigned */
            if (!in_array($uri_string, $publicRoots)) {
                $this->ci->load->model('user', '', TRUE);
                $this->ci->load->model('talent', '', TRUE);

                $logged_in = $this->ci->session->userdata('logged_in');
                $user_id = $logged_in['id'];

                $check_login_access = $this->ci->user->is_enabled($user_id);
                $check_resign = $this->ci->talent->is_resigned($user_id);

                if (0 == $check_login_access->is_enabled) {
                    $this->ci->session->set_flashdata('flashError', 'User access is disabled now.');
                    redirect('login', 'refresh');
                    die;
                }

                if (isset($check_resign->is_resigned) && 1 == $check_resign->is_resigned) {
                    $this->ci->session->set_flashdata('flashError', 'You can not access the application.');
                    redirect('login', 'refresh');
                    die;
                }

                $user_roles = $logged_in['user_roles'];

                $menu = $this->get_menu($user_roles);
                $this->ci->menu = $menu;
            }
            $all_links = [];
            $this->ci->db->select('*');
            $this->ci->db->from('master_menu');
            $this->ci->db->where('link!=', '#');
            $query = $this->ci->db->get();
            $result = $query->result();
            foreach ($result as $result_menu) {
                $all_links[] = $result_menu->link;
            }
            $currentUrl = (explode(base_url() . 'index.php/', current_url()));
            $url = $currentUrl[1];
            if (in_array($url, $all_links) && !in_array($url, $this->ci->links)) {
                redirect('home', 'refresh');
                die;
            }
        }
    }

    public function get_menu($role) {
        $menu_all = array();
        $all_menus = [];
        $links = array();
        foreach ($role as $rl) {
            $this->ci->db->select('menu');
            $this->ci->db->from('role_menu');
            $this->ci->db->join('master_menu', 'master_menu.id=role_menu.menu', 'left');
            $this->ci->db->where('role', $rl);
            $this->ci->db->order_by('master_menu.priority');
            $query = $this->ci->db->get();
            $result = $query->result();
            foreach ($result as $result_menu) {
                $menu_id = $result_menu->menu;
                if (!in_array($menu_id, $menu_all)) {
                    $menu_details = $this->get_menu_details($menu_id);
                    if ($menu_details) {
                        $parent = $menu_details->parent;

                        if (0 == $parent) {
                            $menu[$menu_id]['name'] = $menu_details->menu_name;
                            $menu[$menu_id]['link'] = $menu_details->link;
                        } else {
                            $menu_details_parent = $this->get_menu_details($parent);

                            if ($menu_details_parent) {
                                $menu_id_parent = $menu_details_parent->id;
                                $menu_all[] = $menu_id_parent;

                                $menu[$menu_id_parent]['name'] = $menu_details_parent->menu_name;
                                $menu[$menu_id_parent]['link'] = $menu_details_parent->link;
                                $all_menus[] = $menu_details_parent->link;
                                $children = array(
                                    'name' => $menu_details->menu_name,
                                    'link' => $menu_details->link,
                                    'id' => $menu_details->id,
                                );
                                $links[] = $menu_details->link;
                                $menu[$menu_id_parent]['children'][] = $children;
                            }
                        }
                    }
                    $menu_all[] = $menu_id;
                }
            }
        }
        $this->ci->links = $links;
        return $menu;
    }

    private function get_menu_details($menu_id) {
        //id	menu_name	link	is_enabled	parent
        $this->ci->db->select('id,menu_name, link, parent');
        $this->ci->db->from('master_menu');
        $this->ci->db->where('is_enabled', 1);
        $this->ci->db->where('id', $menu_id);
        $query = $this->ci->db->get();
        $result = $query->row();
        return $result;
    }

}
