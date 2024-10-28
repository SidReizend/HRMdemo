<?php

Class ProjectMappingModel extends CI_Model {

    function getProjectTaskMapping($start, $length, $search) {

        $this->db->select('pm.*,p.project_code,t.task_code');
        $this->db->from('project_task_mapping pm');
        $this->db->join('project p', 'pm.project_id=p.id', 'left');
        $this->db->join('task_code t', 'pm.task_id=t.id', 'left');
        if ($search) {
            $this->db->where("p.project_code LIKE '%$search%'");
            $this->db->or_where("t.task_code LIKE '%$search%'");
        }
        $this->db->order_by('id desc');
        $this->db->limit($length, $start);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTasksbyProject($project_id) {

        $this->db->select('t.*');
        $this->db->from('task_code t');
        $this->db->join("project_task_mapping pm", "t.id=pm.task_id AND pm.project_id='$project_id'", "inner");
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectTaskMappingCount($start, $length, $search) {

        $this->db->select('pm.*,p.project_code,t.task_code');
        $this->db->from('project_task_mapping pm');
        $this->db->join('project p', 'pm.project_id=p.id', 'left');
        $this->db->join('task_code t', 'pm.task_id=t.id', 'left');
        if ($search) {
            $this->db->where("p.project_code LIKE '%$search%'");
            $this->db->or_where("t.task_code LIKE '%$search%'");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function deleteTaskMapping($id) {
        if ($this->db->delete('project_task_mapping', array('id' => $id))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function getProjectMapped($id) {
        $this->db->select('*');
        $this->db->from('project_mapping');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalents($talent, $is_management, $is_pmo, $editProject = FALSE, $search = '') {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talents t');
//        $this->db->where('t.is_resigned!=', 1);
        if (false === $is_management && FALSE === $is_pmo && FALSE === $editProject) {
            $this->db->where('timesheet_manager', $talent);
        }
        if ($search) {
            $this->db->or_where("t.first_name LIKE '%$search%'");
            $this->db->or_where("t.middle_name LIKE '%$search%'");
            $this->db->or_where("t.last_name LIKE '%$search%'");
        }
        $this->db->order_by('name asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentsByJoiningAndResignDate($talent, $is_management, $date) {
        ;
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talents t');
        $this->db->where('t.date_of_joining<=', $date);
        $this->db->where("(t.date_of_resignation = '0000-00-00' OR t.date_of_resignation >= '$date')");
        if (false === $is_management) {
            $this->db->where('timesheet_manager', $talent);
        }
        $this->db->order_by('name asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTalentbyId($id) {
        $this->db->select('t.id,t.talent_code,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('talents t');
        $this->db->where('t.is_resigned!=', 1);
        $this->db->where('t.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectbyID($id) {
        $this->db->select('id,name,project_code');
        $this->db->from('project');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkTalentMapped($talent, $date) {
        $this->db->select('pm.project_id, p.project_code,pm.id,pm.rating');
        $this->db->from('project_mapping pm');
        $this->db->join('project p', 'pm.project_id = p.id', 'inner');
        $this->db->where('pm.talent_id', $talent);
        $this->db->where('pm.date', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkProjectMapped($project, $date) {
        $this->db->select('t.first_name as name,pm.id,pm.rating');
        $this->db->from('project_mapping pm');
        $this->db->join('talents t', 'pm.talent_id = t.id', 'inner');
        $this->db->where('pm.project_id', $project);
        $this->db->where('pm.date', $date);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllProjects() {
        $this->db->select('id,name,project_code');
        $this->db->from('project');
        $this->db->where('is_head', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllProjectsNotClosed() {
        $this->db->select('id,name,project_code');
        $this->db->from('project');
        $this->db->where('is_head', 0);
        $this->db->where('status!=', 2);
        $query = $this->db->get();
        return $query->result_array();
    }

    function CheckProjectExistMapping($talent, $date, $project) {
        $this->db->select('*');
        $this->db->from('project_mapping');
        $this->db->where('project_id', $project);
        $this->db->where('talent_id', $talent);
        $this->db->where('date', $date);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function checkProjectToDate($project, $date) {
        $this->db->select('to_date');
        $this->db->from('project');
        $this->db->where('id', $project);
        $query = $this->db->get();
        $row = $query->row();
        if ($row->to_date < $date) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function getHomePageProjects($fromdate, $todate) {
        $this->db->select('p.id,p.name,p.project_code');
        $this->db->from('project p');
        $this->db->join('project_mapping pm', 'pm.project_id = p.id', 'inner');
        $this->db->where("pm.date BETWEEN '$fromdate' AND '$todate'");
        $this->db->group_by('p.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function draw_calendarProject($month, $year, $project_id, $mapping) {

        /* draw table */
        $calendar = '<table  class="table calendar">';

        /* table headings */
        $headings = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++):
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
            $current_day = date('Y-m-d', strtotime($year . "-" . $month . "-" . $list_day));
            $calendar .= '<td class="calendar-day">';

            $calendar .= '<span style="float:right;padding:10px;font-weight:bold;font-size:20px;">' . $list_day . '</span>';

            if ($mapping == 1) {
                $calendar .= '<button class="btn btn-default initiate_adding" data-toggle="modal"'
                        . ' data-project_id=' . $project_id . ' data-talent_date=' . $current_day . '  '
                        . ' data-target="#modelbody"  style="float:left;margin-bottom:10px;" title="Add new" type="button">'
                        . '<i class="fa fa-align-center"></i>'
                        . '</button>';
            }

            $projects = $this->checkProjectMapped($project_id, $current_day);
            //var_dump($projects);
            //echo $this->db->last_query();
            foreach ($projects as $project) {
                $class = "btn-twitter";
                if ($project['rating'] == 3) {
                    $class = "bg-light-blue";
                }
                if ($project['rating'] > 3) {
                    $class = "btn-warning";
                }
                if ($mapping == 1) {
                    $calendar .= '<div class="btn-group">
                        <button class="btn ' . $class . ' click_show" data-shw="' . $project['id'] . '" type="button">' . $project['name'] . '</button>
                        <button class="btn ' . $class . ' click_delete" data-dlt="' . $project['id'] . '" type="button">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>';
                } else {
                    $calendar .= '<button class="btn ' . $class . ' click_show" data-shw="' . $project['id'] . '" type="button">' . $project['name'] . '</button>';
                }
            }



            /* add in the day number */


            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! * */
            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($running_day == 6):
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month):
                    $calendar .= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8):
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar .= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    function draw_calendarTalent($month, $year, $talent_id, $mapping) {

        /* draw table */
        $calendar = '<table  class="table calendar">';

        /* table headings */
        $headings = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();
        //Check Holiday
        $monthYear = $year . "-" . $month;
        $fromDate = $monthYear . '-01';
        $toDate = date('Y-m-t', strtotime($fromDate));
        $holidayArray = array();
        $holidays = $this->holiday->getHolidayStatus($fromDate, $toDate);
        foreach ($holidays as $holiday) {
            $holidayArray[] = $holiday['date_of_holiday'];
        }
        $weekendArray = array();
        $weekends = $this->holidaysWeek->getHolidayTalent($talent_id);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++):
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
            $current_day = date('Y-m-d', strtotime($year . "-" . $month . "-" . $list_day));
            $date = date('Y-m-d');
            $day = strtolower(date("l", strtotime($current_day)));
            if ($current_day < date('Y-m-d', strtotime($date . ' -1 day'))) {
                $worked = $this->checkWorkedOrNot($talent_id, $current_day);
                if ($worked > 0) {
                    $calendar .= '<td class="calendar-day">';
                    //echo $this->db->last_query();
                } else {
                    $calendar .= '<td class="calendar-day" style="background:gray;">';
                }
            } else {

                $leaveStatus = $this->GetLeaveStatusForProjectMapping($current_day, $talent_id);
                if ((count($leaveStatus) > 0) || (in_array($current_day, $holidayArray)) || (in_array($day, $weekendArray))) {
                    $calendar .= '<td class="calendar-day" style="background:gray;">';
                } else {
                    $calendar .= '<td class="calendar-day">';
                }
            }
            $calendar .= '<span style="float:right;padding:10px;font-weight:bold;font-size:20px;">' . $list_day . '</span>';
            if ($mapping == 1) {
                $calendar .= '<button class="btn btn-default initiate_adding" data-toggle="modal"'
                        . ' data-talent_id=' . $talent_id . ' data-talent_date=' . $current_day . '  '
                        . ' data-target="#modelbody"  style="float:left;margin-bottom:10px;"  title="Add new" type="button">'
                        . '<i class="fa fa-align-center"></i>'
                        . '</button>';
            }
            $projects = $this->checkTalentMapped($talent_id, $current_day);
            //var_dump($projects);
            //echo $this->db->last_query();
            foreach ($projects as $project) {
                $class = "btn-twitter";
                if ($project['rating'] == 3) {
                    $class = "bg-light-blue";
                }
                if ($project['rating'] > 3) {
                    $class = "btn-warning";
                }
                if ($mapping == 1) {
                    $calendar .= '<div class="btn-group">
                        <button class="btn ' . $class . ' click_show" data-shw="' . $project['id'] . '" type="button">' . $project['project_code'] . '</button>
                        <button class="btn ' . $class . ' click_delete" data-dlt="' . $project['id'] . '" type="button">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>';
                } else {
                    $calendar .= '<button class="btn ' . $class . ' click_show" data-shw="' . $project['id'] . '" type="button">' . $project['project_code'] . '</button>';
                }
            }


            /* add in the day number */


            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! * */
            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($running_day == 6):
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month):
                    $calendar .= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8):
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar .= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    function GetLeaveStatusForProjectMapping($dateOfAttendence, $talentId) {//Project Mapping Module
        $categories = array('0', '1');
        $this->db->select('tld.is_approved');
        $this->db->from('talent_leave_date tld');
        $this->db->join('talent_leave tl', 'tl.id=tld.talent_leave', 'left');
        $this->db->where('tl.talent', $talentId);
        $this->db->where('tld.date_of_leave', $dateOfAttendence);
        $this->db->where_in('tld.is_approved', $categories);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkWorkedOrNot($talent, $date) {
        $this->db->select('*');
        $this->db->from('talent_attendance');
        $this->db->where('talent_id', $talent);
        $this->db->where('attendance_date', $date);
        $this->db->where("TIME_TO_SEC(ADDTIME(`actual_hours_spend`, `od_hours`)) > 0", NULL, FALSE);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function projectMappedUserAfterTheDate($project, $toDate) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('project_mapping pm');
        $this->db->join('talents t', 't.id=pm.talent_id', 'left');
        $this->db->where('pm.project_id', $project);
        $this->db->where('pm.date>', $toDate);
        $this->db->group_by('pm.talent_id');
        $query = $this->db->get();
//        echo $this->db->last_query();
//        die();
        return $query->result_array();
    }

    function timesheetIsCreatedUsersAfterTheDate($project, $toDate) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('time_prediction tp');
        $this->db->join('talents t', 't.id=tp.talent', 'left');
        $this->db->where('tp.project', $project);
        $this->db->where('tp.date>', $toDate);
        $this->db->group_by('tp.talent');
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkProjectTaskMappingExist($projectCode, $assignedTask) {
        $this->db->select('*');
        $this->db->from('project_task_mapping');
        $this->db->where('project_id =', $projectCode);
        $this->db->where('task_id =', $assignedTask);
        $query = $this->db->get();
        return $query->num_rows();
    }

    /*
     * get all employess who's role stream not in management
     */

    function getAllTalents($talent, $is_management, $is_pmo, $editProject = FALSE) {
        $this->db->select('t.id, t.talent_code, CONCAT(t.first_name, " ", t.middle_name, " ", t.last_name) AS name');
        $this->db->from('talents t');
        $this->db->join('role r', 't.role = r.id', 'left');
        $this->db->join('role_stream rs', 'r.role_stream = rs.id', 'left');
        $this->db->where('rs.id !=', "4");
        if (false === $is_management && FALSE === $is_pmo && FALSE === $editProject) {
            $this->db->where('t.timesheet_manager', $talent);
        }
        $this->db->order_by('name ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
     /*
      * get task invoice status  for time entry block
      */   
    function getTaskInvoiceStatus($taskId){
        $this->db->select('*');
        $this->db->from('task_code t');
        $this->db->where('t.id', $taskId);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getProjectTypeMapping($start, $length, $search) {
        $this->db->select('p.id,p.project_code,t.type_name, s.status_name');
        $this->db->from('project p');
        $this->db->join('master_project_status s', 's.id=p.status', 'left');
        $this->db->join('master_project_type t', 'p.type=t.id', 'left');
        if ($search) {
            $this->db->where("p.project_code LIKE '%$search%'");
            $this->db->or_where("t.type_name LIKE '%$search%'");
        }
        $this->db->order_by('p.status asc');
        $this->db->order_by('p.type asc');
        $this->db->limit($length, $start);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectTypeMappingCount($start, $length, $search) {
        $this->db->select('p.id,p.project_code,t.type_name, s.status_name');
        $this->db->from('project p');
        $this->db->join('master_project_status s', 's.id=p.status', 'left');
        $this->db->join('master_project_type t', 'p.type=t.id', 'left');
        if ($search) {
            $this->db->where("p.project_code LIKE '%$search%'");
            $this->db->or_where("t.type_name LIKE '%$search%'");
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    function deleteTypeMapping($id) {
        $updateData = array(
            'type' => NULL
        );
        $this->db->trans_begin();
        $this->db->where('id', $id);
        $queryResultRoleUpdate = $this->db->update('project', $updateData);
        if ($queryResultRoleUpdate) {
            $this->db->trans_commit();
            return TRUE;
        } else {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    function checkProjectTypeMappingExist($projectCode, $assignedTask) {
        $this->db->select('*');
        $this->db->from('project');
        $this->db->where('id =', $projectCode);
        $this->db->where('type =', $assignedTask);
        $query = $this->db->get();
        return $query->num_rows();
    }

}
