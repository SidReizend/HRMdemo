<?php

Class TaskManagementModel extends CI_Model {

    function getAllTask() {
        $this->db->select('t.*,p.project_code,tc.task_code as parent_task_code');
        $this->db->from('task_code t');
        $this->db->join('project p', 't.parent_project=p.id', 'left');
        $this->db->join('task_code tc', 't.parent_task=tc.id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTotalTask($search) {
        $this->db->select('t.*,p.project_code,tc.task_code as parent_task_code');
        $this->db->from('task_code t');
        $this->db->join('project p', 't.parent_project=p.id', 'left');
        $this->db->join('task_code tc', 't.parent_task=tc.id', 'left');
        if ($search) {
            $this->db->where("t.task_code LIKE '%$search%'");
            $this->db->or_where("t.task_desc LIKE '%$search%'");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getTaskByLimit($start, $length, $search) {
        $this->db->select('t.*,p.project_code,tc.task_code as parent_task_code');
        $this->db->from('task_code t');
        $this->db->join('project p', 't.parent_project=p.id', 'left');
        $this->db->join('task_code tc', 't.parent_task=tc.id', 'left');
        if ($search) {
            $this->db->where("t.task_code LIKE '%$search%'");
            $this->db->or_where("t.task_desc LIKE '%$search%'");
        }
        $this->db->limit($length, $start);
        $query = $this->db->get();
        return $query->result_array();
    }

    function gettaskData($task_id) {
        $this->db->select('*');
        $this->db->from('task_code');
        $this->db->where('id', $task_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkTask($task_code, $task_id = '') {
        $this->db->select('*');
        $this->db->from('task_code');
        if ($task_id != "") {
            $this->db->where('id !=', $task_id);
        }
        $this->db->where('task_code', $task_code);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getTaskConsolidatedData($taskCode) {
        $taskCodeQry = $this->db->query("SELECT id FROM `task_code` WHERE `task_code` = '" . $taskCode . "'");
        $taskCodeObj = $taskCodeQry->row();
        //date wise data
        $query = $this->db->query("SELECT p.project_code,p.name,YEAR(`date`) as Y, MONTHNAME(`date`) as MN, MONTH(`date`) as M,  ( SUM( TIME_TO_SEC( a.`time` ) ) ) as tot_time
            FROM `time_prediction` a
            left join project p on a.Project=p.id
            where task='" . $taskCodeObj->id . "'
            group by p.project_code,Y, M
            order by p.project_code,Y,M
        ");
        $dateWiseData = $query->result_array();
        //talent wise data
        $query2 = $this->db->query("SELECT t.talent_code,concat(t.first_name, t.middle_name,t.last_name) as `talent_name`,YEAR(`date`) as Y, MONTHNAME(`date`) as MN, MONTH(`date`) as M,  ( SUM( TIME_TO_SEC( a.`time` ) ) ) as tot_time
            FROM `time_prediction` a
            left join talents t on a.talent=t.id
            where task='" . $taskCodeObj->id . "'
            group by t.talent_code,Y, M
            order by t.talent_code,Y,M
        ");
        $talentWiseData = $query2->result_array();
        return ['dateWiseData' => [$dateWiseData], 'talentWiseData' => [$talentWiseData]];
    }
    /*
     * get all task of  selected project  and empty of project get all task
     */
    function getAllTaskDropDown($search, $projectId = []) {
        $this->db->select('t.*');
        $this->db->from('task_code t');
        $this->db->join("project_task_mapping pm", "t.id = pm.task_id", "inner");
        if ($search) {
            $this->db->group_start();
            $this->db->like("t.task_code", $search);
            $this->db->or_like("t.task_desc", $search);
            $this->db->group_end();
        }
        if (!empty($projectId)) {
            $this->db->where_in('pm.project_id', $projectId);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

}
