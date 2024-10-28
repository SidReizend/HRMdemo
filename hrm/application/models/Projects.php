<?php

Class Projects extends CI_Model {
    /*
     * controller:projects
     */

    function getProjects() {
        $this->db->select('p.id,p.project_code,p.from_date,p.to_date,p.name,p.url,p.description,p.billable,'
                . 't.type_name,s.status_name');
        $this->db->from('project p');
        $this->db->join('master_project_type t', 't.id=p.type', 'left');
        $this->db->join('master_project_status s', 's.id=p.status', 'left');
        $this->db->where('p.is_head', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    function timeSplittingMaster() {
        $this->db->select('tsm.*,pp.project_code as parent_project_code, pp.name as parent_project_name, '
                . ' ts.task_code,'
                . 'ts.task_desc, t.first_name as created_by');
        $this->db->from('timesheet_split tsm');
        $this->db->join('project pp ', 'pp.id=tsm.parent_project', 'left');
        $this->db->join('task_code ts ', 'ts.id=tsm.task', 'left');
        $this->db->join('talents t ', 't.id=tsm.added_by', 'left');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function generateSplitReport($year, $month) {
        $fromDate = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
        $toDateTemp = date($year . '-' . $month . '-t');
        $toDate = date('Y-m-d', strtotime($toDateTemp));
        $splitMasters = $this->timeSplittingMaster();
        $projectWiseTimeSplit = [];
        $i = 0;
        echo $toDate;
        foreach ($splitMasters as $splitMaster) {
            $totalTimeOfTask = $this->getTotalTimeTask($splitMaster['parent_project'], $splitMaster['task'], $fromDate, $toDate);
            $projectsList = explode(",", $splitMaster['to_project']);
            $projectTimeArry = [];
            $totalTimeSpend = 0;
            foreach ($projectsList as $project) {
                if ($project) {
                    $totalTimeOfProject = $this->getTotalTimeTask($project, "", $fromDate, $toDate);
                    $projectTimeArry[$project] = $totalTimeOfProject;
                    $totalTimeSpend += $totalTimeOfProject;
                }
            }
            foreach ($projectTimeArry as $project => $time) {
                $projectWiseTimeSplit[$i]['project_code'] = $this->getProjectById($project)->project_code;
                $projectWiseTimeSplit[$i]['task_code'] = $this->getTaskById($splitMaster['task'])->task_code;
                $projectWiseTimeSplit[$i]['task'] = $splitMaster['task'];
                $projectWiseTimeSplit[$i]['time'] = $this->secondsToMinitues(round(($time / $totalTimeSpend) * $totalTimeOfTask));
                $i++;
            }
        }
        return $projectWiseTimeSplit;
    }

    public function secondsToMinitues($inputSeconds) {
        $hours = floor($inputSeconds / 3600);
        $minutes = floor(($inputSeconds / 60) % 60);
        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        $seconds = $inputSeconds % 60;
        if ($seconds < 10) {
            $seconds = '0' . $seconds;
        }
        if ($hours < 10) {
            $hours = '0' . $hours;
        }
        $inhour = $hours . ':' . $minutes . ':' . $seconds;
        return $inhour;
    }

    function HoursDecimal($time) {
        $hms = explode(".", $time);
        $min = $hms[1] * 60;
        $time = $hms[0] . "." . $min;
        $time = number_format((float) $time, 2, '.', '');
        return str_replace(".", ":", $time);
    }

    function sum_time($array) {
        if (count($array) == 1) {
            return $array[0];
        } else {
            $i = 0;
            foreach ($array as $time) {
                sscanf($time, '%d:%d:%d', $hour, $min, $sec);
                $i += ($hour * 60 + $min) * 60 + $sec;
            }
            if ($i == 0) {
                $h = 0;
                $m = 0;
            } else {
                $h = floor($i / 3600);
                $m = floor(($i % 3600) / 60);
                $i = floor(($i % 3600) % 60);
            }

            return sprintf('%02d:%02d:%02d', $h, $m, $i);
        }
    }

    public function getTotalTimeTask($project, $task, $fromDate, $toDate) {
        $this->db->select('( (SUM( TIME_TO_SEC( `time` ) ) )) AS time');
        $this->db->from('time_prediction tp');
        if ($project != "") {
            $this->db->where('tp.project', $project);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        $this->db->where("date BETWEEN '$fromDate' AND '$toDate'");
        $query = $this->db->get();
        $result = $query->row();
        return $result->time;
    }

    /*
     * controller:projects
     */

    function getProjectsAndHeads() {
        $this->db->select('p.id,p.project_code,p.from_date,p.to_date,p.name,p.url,p.description,p.billable,'
                . 't.type_name,s.status_name');
        $this->db->from('project p');
        $this->db->join('master_project_type t', 't.id=p.type', 'left');
        $this->db->join('master_project_status s', 's.id=p.status', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:Expenseheads
     */

    function getAllHeads() {
        $this->db->select('p.id,p.project_code,p.name');
        $this->db->from('project p');
        $this->db->where('p.is_head', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:Expenseheads
     */

    function getheadDetauksById($id) {
        $this->db->select('p.id,p.project_code,p.name');
        $this->db->from('project p');
        $this->db->where('p.is_head', 1);
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:projects
     */

    function getStatuses() {
        $this->db->select('*');
        $this->db->from('master_project_status');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:projects
     */

    function getTypes($exclude = True) {
        $this->db->select('*');
        $this->db->from('master_project_type');
        if ($exclude) {
            $this->db->where('master_project_type.type_name !=', 'EXCLUDE');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:projects
     */

    function getProjectById($id) {
        $this->db->select('p.*,h.*');
        $this->db->from('project p');
        $this->db->join('project_heads h', 'h.id=p.head', 'left');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getTaskIdByProjectId($id) {
        $this->db->select('ptm.task_id');
        $this->db->from('project_task_mapping ptm');
        $this->db->where('ptm.project_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getTaskById($id) {
        $this->db->select('p.*');
        $this->db->from('task_code p');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    /*
     * controller:projects
     */

    function getProjectsById($id) {
        $this->db->select('p.*');
        $this->db->from('project p');
        $this->db->where_in('p.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller : claimTrackingForms/subProjectMapping
     */

    function getSubProjectStatus($project) {
        $this->db->select('p.sub_project');
        $this->db->from('project p');
        $this->db->where('p.id', $project);
        $query = $this->db->get();
        $result = $query->row();
        if ($result->sub_project == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * get project types
     */
    function getProjectTypes() {
        $this->db->select('p.id,p.type_name as name,t.type_name');
        $this->db->from('master_project_type p');
        $this->db->join('master_project_type_types t', 't.id=p.type_id', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * get project bill types
     */
    function getProjectBillTypes($id='') {
        $this->db->select('*');
        $this->db->from('master_project_type_types');
        if ($id != '') {
            $this->db->where('id', $id);
        }
        $this->db->order_by('type_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * get project type data
     */
    function getProjectTypeData($id) {
        $this->db->select('master_project_type.*');
        $this->db->from('master_project_type');
        $this->db->where('master_project_type.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectsList() {
        $this->db->select('p.*');
        $this->db->from('project p');
        $this->db->where('p.is_head', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTypesSorted($id = '', $revenue_id) {
        $this->db->select('p.*');
        $this->db->from('master_project_type p');
        $this->db->join('master_project_type_types t', 't.id=p.type_id', 'left');
        if ($id != '') {
            $this->db->where('p.id', $id);
        }
        if ($revenue_id != '') {
            $this->db->where('p.type_id', $revenue_id);
        }
        $this->db->where('p.type_name !=', 'EXCLUDE');
        $this->db->order_by('p.type_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectsByType($type, $id = '') {
        $this->db->select('p.*');
        $this->db->from('project p');
        $this->db->where('p.type', $type);
        if ($id != '') {
            $this->db->where('p.id', $id);
        }
        $this->db->order_by('p.project_code', 'ASC');
        $query = $this->db->get();
        //  echo $this->db->last_query();
        // die;
        return $query->result_array();
    }

    function getProjectDetailById($id) {
        $this->db->select('p.*');
        $this->db->from('project p');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        //    echo $this->db->last_query();
        // die;
        return $query->row();
    }

    function getMapping() {
        $this->db->select('p.id,p.assigned_tasks');
        $this->db->from('project p');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     *  get all projects heads
     */

    function getProjectHeads() {
        $this->db->select('*');
        $this->db->from('project_heads');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get all projects
     */

    function getAllProjects($selectedProjectArray = '') {
        $this->db->select('*');
        $this->db->from('project');
        if ($selectedProjectArray != '') {
            $this->db->where_not_in('id', $selectedProjectArray);
        }
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get all Monthlyplan
     */

    function getMonthlyPlan($year = '', $month = '', $projectId = '') {
        $this->db->select('m.id,p.project_code,h.head_name,m.hours,m.project,p.id as projectid,m.year,m.month,m.group_name,m.group_hours');
        $this->db->from('monthly_plan m');
        $this->db->join('project p', 'p.id=m.project', 'left');
        $this->db->join('project_heads h', 'h.id=p.head', 'left');
        if ($year != "") {
            $this->db->where('year', $year);
        }
        if ($month != "") {
            $this->db->where('month', $month);
        }
        if ($projectId != "") {
            $this->db->where('m.id', $projectId);
        }
        $this->db->where('m.is_deleted', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * check monthly plan duplicate entry
     */

    function checkMonthlyPlanMappingExist($projectId, $year, $month) {
        $this->db->select('*');
        $this->db->from('monthly_plan m');
        $this->db->where('project', $projectId);
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $this->db->where('is_deleted', 0);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * get all task of current project
     */

    function getTaskOfProject($projectId) {
        $this->db->select('t.id,concat(t.task_code," - ",t.task_desc) as task');
        $this->db->from('project_task_mapping m');
        $this->db->join('task_code t', 't.id=m.task_id', 'left');
        $this->db->where('project_id', $projectId);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get monthly task plan against the monthly plan
     */

    function getMonthlyTaskPlan($monthlyplan) {
        $this->db->select('m.id,tc.task_desc,tc.task_code,m.time,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,t.id as talent,tc.id as taskid');
        $this->db->from('monthly_task_plan m');
        $this->db->join('talents t', 't.id=m.talent', 'left');
        $this->db->join('task_code tc', 'tc.id=m.task', 'left');
        $this->db->where('m.monthlyplan', $monthlyplan);
        $this->db->where('m.is_deleted', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * check monthly plan already exist
     */

    function checkMonthlyPlanTaskMappingExist($task, $monthlyplan, $talent = '') {
        $this->db->select('*');
        $this->db->from('monthly_task_plan m');
        $this->db->where('task', $task);
        if ($talent != '') {
            $this->db->where('talent', $talent);
        }
        $this->db->where('monthlyplan', $monthlyplan);
        $this->db->where('is_deleted', 0);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * get tottal monthly hours of a selected months
     */

    function getMonthlyhours($dateFrom, $dateTo, $projectid) {
        $this->db->select('time,Project');
        $this->db->from('time_prediction m');
        $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        $this->db->where('Project', $projectid);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get addend task in current monthly plan
     */

    function getAddedTaskInMonthlyPlan($monthlyPlan) {
        $this->db->select('task');
        $this->db->from('monthly_task_plan');
        $this->db->where('monthlyplan', $monthlyPlan);
        $this->db->where('is_deleted', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get not added task in monthly plan get from time prediction
     */

    function getNotAddedTask($dateFrom, $dateTo, $projectid, $selectedtaskArray = '') {
        $this->db->select('task');
        $this->db->from('time_prediction m');
        $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        $this->db->where('Project', $projectid);
        if ($selectedtaskArray != '') {
            $this->db->where_not_in('task', $selectedtaskArray);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get task name
     */

    function getTaskName($taskcode) {
        $this->db->select('task_code,task_desc');
        $this->db->from('task_code');
        $this->db->where('id', $taskcode);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:Task status
     */

    function getTaskStatuses() {
        $this->db->select('*');
        $this->db->from('master_task_status');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get not added project in monthly plan get from time prediction
     */

    function getNotAddedProject($dateFrom, $dateTo, $selectedProjectArray = '') {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS time,p.project_code as project,tp.project as project_id,h.head_name');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'tp.project = p.id', 'inner');
        $this->db->join('project_heads h', 'h.id=p.head', 'left');
        $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        if ($selectedProjectArray != '') {
            $this->db->where_not_in('tp.Project', $selectedProjectArray);
        }
        $this->db->group_by('tp.project');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * get tottal monthly hours of a selected months
     */

    function getMonthlyhoursSum($dateFrom, $dateTo, $projectid) {
        $this->db->select('( SUM( TIME_TO_SEC( `time` ) ) ) AS time,Project');
        $this->db->from('time_prediction m');
        $this->db->where('date BETWEEN "' . date('Y-m-d', strtotime($dateFrom)) . '" and "' . date('Y-m-d', strtotime($dateTo)) . '"');
        $this->db->where('Project', $projectid);
        $query = $this->db->get();
        return $query->result_array();
    }
    /*
     * get invoice Status
     */
    function getInvoiceStatuses() {
        $this->db->select('*');
        $this->db->from('master_invoice_status');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getExcludeType() {
        $this->db->select('id');
        $this->db->from('master_project_type');
        $this->db->where('master_project_type.type_name', 'EXCLUDE');
        $query = $this->db->get();
        return $query->row();
    }

    public function getTypeTotalTimeTask($type, $task, $fromDate, $toDate) {
        $this->db->select('( (SUM( TIME_TO_SEC( `time` ) ) )) AS time');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'p.id=tp.project', 'left');
        if ($type != "") {
            $this->db->where('p.type', $type);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        $this->db->where("date BETWEEN '$fromDate' AND '$toDate'");
        $query = $this->db->get();
        $result = $query->row();
        return $result->time;
    }

    public function getRevTypeTotalTimeTask($type, $task, $fromDate, $toDate) {
        $this->db->select('( (SUM( TIME_TO_SEC( `time` ) ) )) AS time');
        $this->db->from('time_prediction tp');
        $this->db->join('project p', 'p.id=tp.project', 'left');
        $this->db->join('master_project_type pt', 'pt.id=p.type', 'left');
        $this->db->join('master_project_type_types t', 't.id=pt.type_id', 'left');
        if ($type != "") {
            $this->db->where('pt.type_id', $type);
        }
        if ($task != "") {
            $this->db->where('tp.task', $task);
        }
        $this->db->where('pt.type_name !=', 'EXCLUDE');
        $this->db->where('tp.project IS NOT NULL');
        $this->db->where('p.type IS NOT NULL');
        $this->db->where("date BETWEEN '$fromDate' AND '$toDate'");
        $query = $this->db->get();
        $result = $query->row();
        return $result->time;
        
    }

    



}
