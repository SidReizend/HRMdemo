<?php

Class PrevilageModel extends CI_Model
{
    function getAllUsers()
    {
        $this->db->select('id,role,username');
        $this->db->from('users');
        $this->db->where('is_enabled=',1);
        $query = $this->db->get();
        return $query->result_array();
    }
    function getallSystemRoles()
    {
        $this->db->select('id,role_name');
        $this->db->from('master_system_roles');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getnumberMenu(){
        $this->db->select('id,menu_name');
        $this->db->from('master_menu');
        $this->db->where('parent!=',0);
        $query = $this->db->get();
        return $query->num_rows();
    }
    function getNumberSystemRoles(){
        $this->db->select('id');
        $this->db->from('master_system_roles');
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getallMenu($limit,$start){
        $this->db->select('id,menu_name');
        $this->db->from('master_menu');
        $this->db->where('parent!=',0);
        $this->db->limit($limit,$start);
        $this->db->order_by('priority asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    function geAllRole($limit,$start){
        $this->db->select('id,role_name');
        $this->db->from('master_system_roles');
        $this->db->limit($limit,$start);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getallRoleMenus($id)
    {
        
        $this->db->select('r.menu');
        $this->db->from('role_menu r');
        $this->db->where('role',$id);
        $query = $this->db->get();
        return $query->result_array();
    }
    function getAllAdditionalRoles($id)
    {
        
        $this->db->select('role');
        $this->db->from('users');
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function checkRoleMenu($role,$menu){
        $this->db->select('id');
        $this->db->from('role_menu');
        $this->db->where('role',$role);
        $this->db->where('menu',$menu);
        $query = $this->db->get();
        return $query->num_rows();
    }
}
