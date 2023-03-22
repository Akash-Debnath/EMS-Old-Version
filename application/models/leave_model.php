<?php

class Leave_model extends G_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function getLeaveStatus($emp_id, $year)
    {
        $first_date = $year.'-01-01';
        $last_date = $year.'-12-31';
        
//         $next_year = $year + 1;
//         $next_year_first_date = $next_year.'-01-01';
//         $next_year_last_date = $next_year.'-12-31';
        
        //$con =  "`leave_start` BETWEEN '".$next_year_first_date."' AND '".$next_year_last_date."' AND leave_type = 'CA')";
        
        
//         $condition = "((`leave_end` BETWEEN '".$first_date."' AND '".$last_date."' AND leave_type !='CA' ) 
//                         OR (`leave_start` BETWEEN '".$first_date."' AND '".$last_date."' AND leave_type !='CA')
//                         OR (`leave_start` BETWEEN '".$next_year_first_date."' AND '".$next_year_last_date."' AND leave_type = 'CA')
//                        )";

        
        
        $condition = "( (`leave_end` BETWEEN '".$first_date."' AND '".$last_date."' )
                        OR (`leave_start` BETWEEN '".$first_date."' AND '".$last_date."' )                        
                       )";
        
        
        
        $this->db->select('leave_type, period, leave_start, leave_end');
        $this->db->from('leaves');
        $this->db->where('emp_id', $emp_id);
        $this->db->where($condition);
        //$this->db->where('leave_end >=', $year.'-01-01');
        //$this->db->where('leave_end <=', $year.'-12-31');
        $this->db->where('admin_approve_date IS NOT NULL', null, false);
        $query = $this->db->get();
        
        $result = $query->result();
        
        $retAry = array();
        
        foreach ($result as $obj){
            
            $leaveStartOfPrevYear = ($obj->leave_start < $first_date);
            $leaveEndOfNextYear = ($obj->leave_end > $last_date);
            
            if( ($leaveStartOfPrevYear || $leaveEndOfNextYear) ){
                
                $period = floatval($obj->period);            
                
                if($leaveStartOfPrevYear){
                    
                    for ($idate = $obj->leave_start; $idate < $first_date;) {
                        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }
                    
                    $obj->leave_start = $first_date;
                }
                
                
                if($leaveEndOfNextYear){

                    for($idate = $obj->leave_end; $idate > $last_date;){
                        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " -1 day"));
                    }
                    
                    $obj->leave_end = $last_date;
                }
                
                $obj->period = $period;
                
                $retAry[] = $obj;
                
            }else{
                
                $retAry[] = $obj;
            }
        }
                
        return $retAry;
    }
    
    public function getGenuityLeaveStatus($emp_id, $genuity_leaves_array)
    {
        $leaveTypes = array_keys($genuity_leaves_array);
        
        $this->db->select('leave_type, period, leave_end');
        $this->db->from('leaves');
        $this->db->where('emp_id', $emp_id);
        $this->db->where('admin_approve_date IS NOT NULL', null, false);
        $this->db->where_in('leave_type', $leaveTypes);
        $query = $this->db->get();
    
        $res = $query->result_array();
        $taken = array();
        foreach ($genuity_leaves_array as $key=>$val){
            $taken[$key] = 0;
        }
        
        foreach ($res as $ary){
            $taken[$ary['leave_type']] += $ary['period'];
        }

        return $taken;
    }
    
    public function getSickLeaveStatus($emp_id, $year)
    {
        $this->db->select("YEAR(leave_end) as year, SUM(IF(leave_type='SL' OR leave_type='SLM', period ,0)) as taken", false);
        $this->db->from('leaves');
        $this->db->where('emp_id', $emp_id);
        $this->db->where('YEAR(leave_end) <', $year);
        $this->db->where('admin_approve_date IS NOT NULL', null, false);
        $this->db->group_by('YEAR(leave_end)'); 
        $query = $this->db->get();
    
        return $query->result();
    }    
    
    public function getAnnualTakenLeave($emp_id, $year){
        
        $first_date = $year.'-01-01';
        $last_date = $year.'-12-31';
        
        $last_year = date("Y",strtotime($year." -1 year"));
        $prev_first_date = $last_year.'-01-01';
        $prev_last_date = $last_year.'-12-31';
        
                
        $condition = "( (`leave_end` BETWEEN '".$prev_first_date."' AND '".$prev_last_date."' AND leave_type != 'CA') 
                        OR (`leave_start` BETWEEN '".$prev_first_date."' AND '".$prev_last_date."' AND leave_type != 'CA')
                        OR (`leave_start` BETWEEN '".$first_date."' AND '".$last_date."' AND leave_type = 'CA')                     
                      )";
        
        
        $this->db->select('leave_type, period, leave_start, leave_end');
        $this->db->from('leaves');
        $this->db->where('emp_id', $emp_id);
        $this->db->where_in('leave_type', array('AL', 'HL', 'CA'));
        $this->db->where($condition);

        $this->db->where('admin_approve_date IS NOT NULL', null, false);
        $query = $this->db->get();        
        $result = $query->result();
        
        $totalAnnualTaken = 0;
        $totalForwardedAnnualTaken = 0;

        foreach($result as $obj){
            
            $leaveStartOfPrevYear = ($obj->leave_start < $prev_first_date);
            $leaveEndOfNextYear = ($obj->leave_end > $prev_last_date);
        
            if( ($leaveStartOfPrevYear || $leaveEndOfNextYear) &&  $obj->leave_type !='CA'){
        
                $period = floatval($obj->period);
                
                if($leaveStartOfPrevYear){
        
                    for ($idate = $obj->leave_start; $idate < $prev_first_date;) {
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }        
                }
        
                if($leaveEndOfNextYear){ 
        
                    for($idate = $obj->leave_end; $idate > $prev_last_date;){
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " -1 day"));
                    }
                }
                //echo $period; 
                $totalAnnualTaken += $period; 
            }else{
                
                if($obj->leave_type == 'CA'){
                    
                    $totalForwardedAnnualTaken += $obj->period;
                    
                }else{
                    $totalAnnualTaken += $obj->period;
                }
                
            }
        }
        
        $retAry = array(
            'totalAnnualTaken' => $totalAnnualTaken,
            'totalForwardedAnnualTaken' => $totalForwardedAnnualTaken
        );
        
        return $retAry;

    }
    
    public function add_request($data){

        $this->db->insert('leaves', $data);
    
        return $this->db->insert_id();
    }
    
    
    public function add_leave_file($data){
        $this->db->insert('leave_attachment', $data);

        return $this->db->insert_id();
    }
    
    public function update_leave_attachment($id, $file_name){
        $this->db->where('id', $id);
        $this->db->update('leave_attachment', array('file_name'=>$file_name));
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function add_confirm($data,  $id){
        $this->db->where('id', $id);
        $this->db->update('leaves', $data);
            
        return ($this->db->affected_rows() > 0);
    }
    
    public function addToAttachFile($data){
    
        $this->db->insert('attach_files', $data);
    
        return $this->db->insert_id();
    }
   
    public function getLeaveInfo($id)
    {
        $this->db->select('
            l.*,
            e.name as employee_name,
            
            m.name as manager_name,
            ds.designation as manager_desig,
            dp.dept_name as manager_dept,
            
            a.name as admin_name
	        ');
        $this->db->from('leaves l');
        $this->db->join('employee e', 'e.emp_id=l.emp_id', 'left');
        
        $this->db->join('employee m', 'm.emp_id=l.manager_id', 'left');
        $this->db->join('designations ds', 'ds.id=m.designation', 'left');
        $this->db->join('departments dp', 'dp.dept_code=m.dept_code', 'left');

        
        $this->db->join('employee a', 'a.emp_id=l.admin_id', 'left');
        $this->db->where('l.id', $id);
        $query = $this->db->get();
        
        return $query->row(); 
    }
    
    public function get_holiday($data){

        $ary = array();

        $this->db->select('roster');
        $this->db->from('employee');
        $this->db->where('emp_id', $data['emp_id']);
        $query = $this->db->get();
        $roster = $query->row_array();
        
        if($roster['roster'] == 'N'){
            //holiday
            $this->db->select('date');
            $this->db->from('holy_day');
            $this->db->where('date >=', $data['start']);
            $this->db->where('date <=', $data['end']);
            $query = $this->db->get();
            $holiday = $query->result();
            
            foreach ($holiday as $obj){
                $ary[$obj->date] = '';
            }
            
            //weekly leave
            $query =  $this->db->get_where('weekly_leave', array('emp_id' => $data['emp_id']));           
            $result = $query->row_array();
            
            $startTime = strtotime($data['start']);
            $endTime = strtotime($data['end']);
            for ($time = $startTime; $time <= $endTime; $time += 86400) {
            
                $dayCode = strtolower(date('D', $time));
                $date = date('Y-m-d', $time);
                
                if (empty($result)) {
                    
                    if($data['default_weekend'][$dayCode] == 'Y'){
                        $ary[$date] = '';
                    }
                }else{
                    if($result[$dayCode] == 'Y'){
                        $ary[$date] = '';
                    }
                }
            }
            
        } else if($roster['roster'] == 'Y'){
            
            //weekend
            $this->db->select('date');
            $this->db->from('weekend');
            $this->db->where('emp_id', $data['emp_id']);
            $this->db->where('date >=', $data['start']);
            $this->db->where('date <=', $data['end']);
            $query = $this->db->get();
            $weekend = $query->result();
            
            foreach ($weekend as $obj){
                $ary[$obj->date] = '';
            }
        }

        $count =  count($ary);                
        return $count;
    }
    
    public function getLeaveList($emp_id, $year)
    {
        $first_date = $year.'-01-01';
        $last_date = $year.'-12-31';
        
//         $next_year = $year + 1;
//         $next_year_first_date = $next_year.'-01-01';
//         $next_year_last_date = $next_year.'-12-31';

        //$condition = "((`leave_end` >= '".$first_date."' AND `leave_end` <= '".$last_date."') OR (`leave_start` >= '".$first_date."' AND `leave_start` <= '".$last_date."'))";
        
//         $condition = "( (`leave_end` BETWEEN '".$first_date."' AND '".$last_date."' AND leave_type !='CA') 
// 				    OR 
// 				    (`leave_start` BETWEEN '".$first_date."' AND '".$last_date."' AND leave_type !='CA')
// 					OR
// 					(`leave_start` BETWEEN '".$next_year_first_date."' AND '".$next_year_last_date."' AND leave_type='CA') )";
        
        
        $condition = "( (`leave_end` BETWEEN '".$first_date."' AND '".$last_date."' )
                        OR (`leave_start` BETWEEN '".$first_date."' AND '".$last_date."' )
                       )";
        
        
        $select = 'id,
            emp_id,
	        leave_type,
	        time_slot,
	        leave_start,
            leave_end,
            leave_date,
            period,
            m_approved_date,
            admin_approve_date,            
	        ';
        $this->db->select($select);
        $this->db->from('leaves');
        $this->db->where('emp_id', $emp_id);
        $this->db->where($condition);        
        //$this->db->where('leave_end >=', $year.'-01-01');
        //$this->db->where('leave_end <=', $year.'-12-31');        
        $this->db->order_by('id', "desc");
        $query = $this->db->get();
        
        $result = $query->result();
        
        //echo $this->db->last_query();
        
        $retAry = array();
        
        foreach ($result as $obj){
        
            $leaveStartOfPrevYear = ($obj->leave_start < $first_date);
            $leaveEndOfNextYear = ($obj->leave_end > $last_date);
        
            if( ($leaveStartOfPrevYear || $leaveEndOfNextYear) ){
        
                $period = floatval($obj->period);
        
                if($leaveStartOfPrevYear){
        
                    for ($idate = $obj->leave_start; $idate < $first_date;) {
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }
        
                    $obj->leave_start = $first_date;
                }
        
                if($leaveEndOfNextYear){
        
                    for($idate = $obj->leave_end; $idate > $last_date;){
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " -1 day"));
                    }
        
                    $obj->leave_end = $last_date;
                }
        
                $obj->period = $period;
        
                $retAry[] = $obj;
            }else{
                
                $retAry[] = $obj;
            }
        }
        
        return $retAry;
    }
    
    public function get_all_leaves($year, $dept_code, $l_type){
        
        $first_date = $year.'-01-01';
        $last_date = $year.'-12-31';
        
//         $next_year = $year + 1;
//         $next_year_first_date = $next_year.'-01-01';
//         $next_year_last_date = $next_year.'-12-31';                
        //$condition = "((l.leave_end BETWEEN '$first_date' AND '$last_date' AND l.leave_type != 'CA') OR (l.leave_start BETWEEN '$first_date' AND '$last_date' AND l.leave_type != 'CA') OR (l.leave_start BETWEEN '$next_year_first_date' AND '$next_year_last_date' AND l.leave_type = 'CA') )";
        
        $condition = "((l.leave_end BETWEEN '$first_date' AND '$last_date') OR (l.leave_start BETWEEN '$first_date 'AND '$last_date') )";
        
        $this->db->select('e.emp_id, e.dept_code, e.name, l.leave_type, l.period, l.leave_start, l.leave_end');
        $this->db->from('employee e');        
        $this->db->join('leaves l', 'e.emp_id=l.emp_id', 'left');
        $this->db->where('e.archive','N');
        if(!empty($dept_code)){ $this->db->where_in('e.dept_code', $dept_code);}
        if(!empty($l_type)){ $this->db->where_in('l.leave_type', $l_type); }        
        $this->db->where('l.admin_approve_date IS NOT NULL', null, false);
        $this->db->where($condition);
        $query = $this->db->get();
        $result = $query->result();
        
        $leavesAry = array();
        foreach ($result as $obj){
        
            $leaveStartOfPrevYear = ($obj->leave_start < $first_date);
            $leaveEndOfNextYear = ($obj->leave_end > $last_date);
        
            if(($leaveStartOfPrevYear || $leaveEndOfNextYear) ){
        
                $period = $obj->period;
        
                if($leaveStartOfPrevYear){
        
                    for ($idate = $obj->leave_start; $idate < $first_date;) {
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }
        
                    $obj->leave_start = $first_date;
                }
        
        
                if($leaveEndOfNextYear){
        
                    for($idate = $obj->leave_end; $idate > $last_date;){
        
                        --$period;
                        $idate = date("Y-m-d", strtotime($idate . " -1 day"));
                    }
        
                    $obj->leave_end = $last_date;
                }

                $leavesAry[$obj->emp_id][$obj->leave_type][] = $period;
            }else{
                
                $leavesAry[$obj->emp_id][$obj->leave_type][] = $obj->period;
                
//                 if($obj->leave_type == 'CA'){
                    
//                     $leavesAry[$obj->emp_id]['AL'][] = $obj->period;
//                 }else{
//                     $leavesAry[$obj->emp_id][$obj->leave_type][] = $obj->period;
//                 }
                
            }
        }

        
        $TotalperiodsByTypesOfEmp = array();
        
        foreach ($leavesAry as $eid=> $lp_periods){
            
            $sumArray = array();
            foreach ($lp_periods as $lp=>$periods){
                $sum = 0;
                foreach ($periods as $val){
                    
                    $sum += $val;
                }
                $sumArray[$lp] = $sum;
            }
            
            $TotalperiodsByTypesOfEmp[$eid] = $sumArray;
        }

        /* Get all employee */
        $this->db->select('emp_id, dept_code, name');        
        $this->db->from('employee');
        $this->db->where('archive','N');    
        if(!empty($dept_code)) $this->db->where_in('dept_code', $dept_code);
        $query = $this->db->get();
        $allEmployees = $query->result();

        $return = array();
        
        foreach ($allEmployees as $obj){
            
            $TotalperiodsByTypes =  isset($TotalperiodsByTypesOfEmp[$obj->emp_id]) ? $TotalperiodsByTypesOfEmp[$obj->emp_id] : array();
            
            $TotalperiodsByTypes['name'] = $obj->name;
            $ary = array();
            
            $return[$obj->dept_code][$obj->emp_id] = $TotalperiodsByTypes;
        }

        return $return;
    }
    
    public function get_all_years_leave($dept_code = array(), $staffs = array()){
        
        $this->db->select('e.emp_id, e.dept_code, e.name, l.leave_type, l.period, l.leave_start, l.leave_end');
        $this->db->from('employee e');
        $this->db->join('leaves l', 'e.emp_id=l.emp_id', 'left');
        $this->db->where('e.archive','N');
        if(!empty($dept_code)) $this->db->where_in('e.dept_code', $dept_code);
        if(!empty($staffs)) $this->db->where_in('e.emp_id', $staffs);
        $this->db->where('l.admin_approve_date IS NOT NULL', null, false);
        $query = $this->db->get();
        $result = $query->result();
        
        $leavesAry = array();
        foreach ($result as $obj){

            $start_date = substr($obj->leave_start, 0, 4);
            $last_date = substr($obj->leave_end, 0, 4);
                        
            
            if($start_date < $last_date){
                
                $firstjan = $last_date."-01-01";
                $period = floatval($obj->period);
                $count = 0;
                
                for ($idate = $obj->leave_start; $idate < $firstjan;) {
    
                    --$period;
                    ++$count;
                    $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                }
                
                $leavesAry[$obj->emp_id][$obj->leave_type][$start_date][] = $count;
                $leavesAry[$obj->emp_id][$obj->leave_type][$last_date][] = $period;
                
            }else{
                
                
                $leavesAry[$obj->emp_id][$obj->leave_type][$start_date][] = floatval($obj->period);
                
                /*
                if($obj->leave_type =='CA'){
                    $date = $start_date - 1;
                    $leavesAry[$obj->emp_id]['AL'][$date][] = floatval($obj->period);
                }else{
                    $leavesAry[$obj->emp_id][$obj->leave_type][$start_date][] = floatval($obj->period);
                }*/
            }
        }
        
        $TotalperiodsByTypesOfEmp = array();
        
        foreach ($leavesAry as $eid=> $lt_periods){
        
            $newLtAry = array();
            foreach ($lt_periods as $lt => $periodsByAry){
                
                $newYearAry = array();
                
                foreach ($periodsByAry as $year => $periods){
                    $sum = 0;
                    
                    foreach ($periods as $val){
                    
                        $sum += $val;
                    }
                    $newYearAry[$year] = $sum;
                }

                $newLtAry[$lt] = $newYearAry;
            }
        
            $TotalperiodsByTypesOfEmp[$eid] = $newLtAry;
        }

        /* Get all employee */
        $this->db->select('emp_id, dept_code, name');
        $this->db->from('employee');
        $this->db->where('archive','N');
        if(!empty($dept_code)) $this->db->where_in('dept_code', $dept_code);
        if(!empty($staffs)) $this->db->where_in('emp_id', $staffs);
        $query = $this->db->get();
        $allEmployees = $query->result();
        
        $return = array();
        
        foreach ($allEmployees as $obj){
        
            $TotalperiodsByTypes =  isset($TotalperiodsByTypesOfEmp[$obj->emp_id]) ? $TotalperiodsByTypesOfEmp[$obj->emp_id] : array();
        
            $TotalperiodsByTypes['name'] = $obj->name;
            $ary = array();
        
            $return[$obj->emp_id] = $TotalperiodsByTypes;
        }
        
        return $return;
        
        /* $this->db->select('e.emp_id, e.dept_code, e.name, l.leave_type, YEAR(l.leave_end) as year');
        $this->db->select("SUM(IF(l.admin_approve_date IS NOT NULL , l.period, 0)) AS period", false);
        
        $this->db->from('employee e');
        $this->db->where('e.archive','N');
        $this->db->join('leaves l', 'e.emp_id=l.emp_id', 'left');
        if(!empty($dept_code)) $this->db->where_in('e.dept_code', $dept_code);
        if(!empty($staffs)) $this->db->where_in('e.emp_id', $staffs);
        $this->db->group_by('e.emp_id, l.leave_type, year');
        $query = $this->db->get();
        $res = $query->result();

        $ary = Array();
        foreach ($res as $obj){
            if(!empty($obj->leave_type) && !empty($obj->year) ){
                $ary[$obj->emp_id][$obj->leave_type][$obj->year] = $obj->period;
            }
            if(!isset($ary[$obj->emp_id]['name'])){
                $ary[$obj->emp_id]['name'] = $obj->name;
            }
            
        }

        return $ary; */
        
    }
    
    
    public function getMailInfoByType($type, $dept_code=""){
    
        $data = array(
            's.emp_id',
            'e.name',
            'e.email'
        );
        $this->db->select($data);
        $this->db->from('settings s');
        $this->db->join('employee e','s.emp_id = e.emp_id','inner');
        $this->db->where('e.archive','N');
        $this->db->where('s.type',$type);
        if(!empty($dept_code)){ $this->db->where('s.dept_code', $dept_code);}
        $query = $this->db->get();
    
        return $query->result();
    }

    public function getAdminInfo(){
    
        $data = array(
            's.emp_id',
            'e.name',
            'e.email'
        );
        $this->db->select($data);
        $this->db->from('settings s');
        $this->db->join('employee e','s.emp_id = e.emp_id','inner');
        $this->db->where('e.archive','N');
        $this->db->where('s.type', 'A');
        $query = $this->db->get();
    
        return $query->result();
    }
    
    public function getDeptCode($emp_id){
        $this->db->select('dept_code');
        $this->db->from('employee');
        $this->db->where('emp_id', $emp_id);
        $query = $this->db->get();
        
        $res = $query->row_array();
        
        return $res['dept_code'];
    }
    
    public function getAttFiles($id){
        $this->db->select('id, file_name, original_file_name');
        $this->db->from('leave_attachment');
        $this->db->where('leave_id', $id);
        $query = $this->db->get();
    
        return $query->result();
    }
    
    public function getLeaveAttFileById($id){
        
        $this->db->select("*");
        $this->db->from('leave_attachment');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    public function getStaffArray(){
        $this->db->select('emp_id, name, dept_code');
        $this->db->from('employee');
        $this->db->where('archive','N');
        $this->db->order_by('emp_id', 'asc');
        //$this->db->group_by('dept_code');
        $query = $this->db->get();
         
        $results = $query->result();
    
        $staff_array = array();
        foreach($results as $obj){
            $staff_array[$obj->dept_code][] = $obj;
    
        }
        $staff_array['all'] =  $results;
         
        return $staff_array;
    }
    
    public function del_leave_file($id){
    
        $this->db->delete('leave_attachment', array('id' => $id));        
        unset($id);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function del_leave($id){
    
        $this->db->delete('leaves', array('id' => $id));
        unset($id);    
        return ($this->db->affected_rows() > 0);
    }
    
    public function getLeaveShortInfoById($id){
        $this->db->select('emp_id, leave_type, leave_start, leave_end, period');
        $this->db->from('leaves');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    public function getJoiningDateById($id){
        $this->db->select('jdate');      
        $this->db->from('employee');
        $this->db->where('emp_id', $id);
        $query = $this->db->get();
        $date = $query->row();

        return $date->jdate;
    }
    
    public function getCancelationRequest($myEmp, $dept_ary){
        
        $this->db->select('l.id AS rid, l.period, l.leave_type, l.leave_date, l.leave_start, l.leave_end, e.emp_id, e.name, dp.dept_name, ds.designation');
        $this->db->from('leaves l');
        $this->db->join('employee e', 'e.emp_id = l.emp_id', 'inner');
        $this->db->join('departments dp', 'e.dept_code = dp.dept_code', 'inner');
        $this->db->join('designations ds', 'e.designation=ds.id', 'inner');
        $this->db->where('e.emp_id !=', $myEmp);
        $this->db->where('l.cancel_req_date IS NOT NULL');
        $this->db->where_in('e.dept_code', $dept_ary);
        $this->db->order_by('l.leave_date', 'desc');
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    public function getPermissionPrivileger($activity_code, $staff_id=""){
        
        $this->db->select('x.staff_id, x.privileger_id');
        $this->db->from('activity_priv p');
        $this->db->where('p.activity_code', $activity_code);
        $this->db->join('activity_permission x', 'x.activity_id = p.activity_id', 'left');
        
        if(!empty($staff_id)){
            $this->db->where('x.staff_id', $staff_id);
        }
        
        $query = $this->db->get();
        $res = $query->result();

        $ret = array();        
        foreach ($res as $obj){
                        
            $ret[$obj->privileger_id][] =  $obj->staff_id;
        }        
        
        return $ret;
    }
    
    public function getFullPrivilege($code = ""){
    
        $this->db->select('s.emp_id, s.dept_priv');
        $this->db->from('activity_priv p');
        $this->db->where('p.activity_code', $code);
        $this->db->join('activity_stack s', 's.activity_id = p.activity_id', 'left');
        $query = $this->db->get();
    
        $res = $query->result();
    
        $ret = array();
        foreach ($res as $obj){
    
            $newAry = array();
            $dept_privs = explode(',', $obj->dept_priv);
    
            foreach ($dept_privs as $val){
    
                if (!empty($val)){
    
                    $newAry[] = trim($val);
                }
    
            }
    
            $ret[$obj->emp_id] = $newAry;
        }
    
        return $ret;
    }
    
    public function getPrivilegerStaffAry($activity_name, $eid="", $select=""){
    
    
        $this->db->select('p.staff, p.approve, p.verify');
        $this->db->from('activity_misc m');
        $this->db->where('m.activity_name', $activity_name);
        $this->db->join('activity_misc_priv  p', 'm.id = p.act_id', 'left');
        if(!empty($eid)){
            $this->db->where('p.staff', $eid);
        }
    
        $query = $this->db->get();
        $res = $query->result();
    
        $ret = array();
        foreach ($res as $obj){
    
            $ret[APPROVE][$obj->approve][] =  $obj->staff;
            $ret[VERIFY][$obj->verify][]  =  $obj->staff;
        }
    
        if(empty($select)){
    
            return $ret;
        }elseif ($select == APPROVE){
    
            return $ret[APPROVE];
        }elseif ($select == VERIFY){
    
            return $ret[VERIFY];
        }
    
        return false;
    }
    
}    