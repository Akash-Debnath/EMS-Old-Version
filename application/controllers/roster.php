<?php

class Roster extends G_Controller
{

    public $data = array();
    public $myEmpId = '';
    public $adminFlag = false;

    public function __construct ()
    {

        parent ::__construct ();

        $this -> isLoggedIn ();

        $this -> load -> helper ( array( 'form' , 'url' ) );
        $this -> load -> library ( 'session' );
        $this -> load -> model ( 'roster_model' );
        $this -> load -> model ( 'user_model' );
        $this -> load -> model ( 'leave_model' );
        $this -> load -> library ( 'pagination' );
        $this -> load -> library ( 'mailer' );
        $this -> data[ 'myInfo' ] = $this -> session -> GetMyBriefInfo ();
        $this -> data[ 'userId' ] = $this -> session -> GetLoginId ();
        //$this->data["userName"] = $this->GetUserName();
        $this -> data[ 'departments' ] = $this -> user_model -> department ();

        $this -> data[ 'uType' ] = $this -> session -> GetUserType ();

        $this -> myEmpId                = $this -> session -> GetLoginId ();
        $this -> data[ 'isManagement' ] = $this -> session -> IsManagement ( $this -> myEmpId );
        $this -> data[ 'isAdmin' ]      = $this -> session -> IsAdmin ( $this -> myEmpId );
        $this -> data[ 'isManager' ]    = $this -> session -> IsManager ( $this -> myEmpId );

        $this -> data[ 'menu' ]      = 'Attendance';
        $this -> data[ 'title' ]     = 'ABC';
        $this -> data[ 'sub_title' ] = 'ABC';

        $this -> data[ 'controller' ] = $this;
    }

    public function index ()
    {
        $this -> rosterSet ();
    }

    public function adjustRosterAttendanceWithChangedEmployeeTimeSlotForPreviousDate ()
    {
        $edate     = $this -> input -> post ( 'ddate' );
        $dept_code = $this -> input -> post ( 'dept_code' );
        if ( ! empty( $edate ) && ! empty( $dept_code ) )
            $this -> attendance_model -> updateAttendanceFromEngineForLastDays ( $edate , $dept_code );
    }

    public function set ( $dept_code = '' , $roster_status = '' )
    {


        /* Privileger */
        $rosterStatusOfEmployee   = array();
        $roster_setting_accessers = $this -> leave_model -> getPermissionPrivileger ( ROSTER_SETTING );

        $sdate         = isset( $_POST[ 'sdate' ] ) ? trim ( $_POST[ 'sdate' ] ) : date ( 'Y-m-d' );
        $edate         = isset( $_POST[ 'edate' ] ) ? trim ( $_POST[ 'edate' ] ) : date ( 'Y-m-t' );
        $roster_status = isset( $_POST[ 'roster_status' ] ) ? trim ( $_POST[ 'roster_status' ] ) : $roster_status;

        $allRosterDepts              = array();
        $this -> data[ 'isManager' ] = false;
        $staffArray                  = array();

        if ( $this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId ) ) {

            $allRosterDepts = $this -> roster_model -> getAllDeptCode ();
            // print_r($allRosterDepts);
            $this -> data[ 'isManager' ] = true;

            if ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts ) ) {

            }
            else {
                $dept_code = current ( $allRosterDepts );
            }

            /* To ge tstaff Array */
            $staffRecord = $this -> roster_model -> GetStaffListByDeptCode ( $dept_code );
            ///print_r($staffRecord);
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }
            //print_r($staffArray);
        }
        else if ( $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
            if ( $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
                $empSessInfo = $this -> session -> GetMyBriefInfo ();
                if ( ! empty( $empSessInfo -> userDeptCode ) ) {
                    $allRosterDepts = array( $empSessInfo -> userDeptCode );
                }
                else {
                    $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
                }
            }
            else {
                $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
            }
            $this -> data[ 'isManager' ] = true;

            if ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts ) ) {

            }
            else {
                $dept_code = current ( $allRosterDepts );
            }

            /* To ge tstaff Array */
            $staffRecord = $this -> roster_model -> GetStaffListByDeptCode ( $dept_code );
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

        }
        else if ( isset( $roster_setting_accessers[ $this -> myEmpId ] ) ) {
            $this -> data[ 'isManager' ] = true;
            $allRosterDepts              = array();

            //$this->data['departmentLists'] = array();
            $staff_array = $this -> user_model -> getStaffArrayByIds ( $roster_setting_accessers[ $this -> myEmpId ] );
            foreach ($staff_array[ 'all' ] as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
                //print_r($staffArray);
            }

            $myInfo    = $this -> data[ 'myInfo' ];
            $dept_code = $myInfo -> userDeptCode;

        }
        else {

            $this -> data[ 'status_array' ] = $this -> status_array;
            $this -> data[ 'title' ]        = 'ABC';
            $this -> data[ 'sub_title' ]    = 'ABC';
            $this -> data[ 'message' ]      = 'You have no privilege to access this page!';
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        $rosterData = $this -> roster_model -> getRosterSlotDataOfEmployee ( $dept_code , $sdate , $edate );

        $this -> data[ 'sdate' ] = $sdate;
        $this -> data[ 'edate' ] = $edate;

        $this -> data[ 'title' ]     = 'roster_set';
        $this -> data[ 'sub_title' ] = 'Roster Settings';

        //$this->data["rosterRecord"] = $rosterRecord;
        $this -> data[ 'rosterData' ]        = $rosterData;
        $this -> data[ 'rosterDepartments' ] = $allRosterDepts;
        $this -> data[ 'selectedDeptCode' ]  = $dept_code;
        $this -> data[ 'staffArray' ]        = $staffArray;
        $this -> data[ 'max_weekend' ]       = $this -> max_holiday_in_week;
        $this -> data[ 'day_array' ]         = $this -> day_array;
        $this -> data[ 'roster_status' ]     = $roster_status;
        $this -> load -> view ( 'header' , $this -> data );
        $this -> load -> view ( 'roster_header' , $this -> data );

        if ( isset( $roster_status ) && ! empty( $roster_status ) ) {
            if ( $roster_status == 'Y' || $roster_status == 'N' ) {
                $rosterStatusOfEmployee = $this -> roster_model -> getRosterStatusOfEmployee ( $dept_code , $roster_status );
            }
        }
        elseif ( $dept_code == 'CA' ) {
            $rosterStatusOfEmployee = $this -> roster_model -> getRosterStatusOfEmployee ( $dept_code , 'Y' );
        }

        //isset($this->rosterType[$dept_code]) && $this->rosterType[$dept_code]=="S" &&
        //Making problem for Admin MLSS Employees when set roster
        if ( $dept_code == 'AM' ) {
            if ( count ( $rosterStatusOfEmployee ) > 0 ) {
                foreach ($rosterStatusOfEmployee as $key => $value) {
                    $this -> data[ 'rosterStatus' ][] = $value[ 'emp_id' ];
                }
            }
            $this -> data[ 'aDays' ] = $this -> GetDays ( $sdate , $edate );
            $this -> load -> view ( 'roster_regular' , $this -> data );
        }/*elseif($dept_code == 'CA'){
			foreach($rosterStatusOfEmployee as $key=>$value){
				$this->data['rosterStatus'][] = $value['emp_id'];
			}

			$rosterSlot = $this->roster_model->getRosterSlot($dept_code);
			//print_r($rosterSlot);
			$this->data["rosterSlot"] = $rosterSlot;

			$weekendData = $this->roster_model->getRosterSlotWeekendOfEmployee($dept_code);
			$this->data["weekendData"] = $weekendData;
			$this->load->view('roster_slot', $this->data);
		}*/
        else {
            if ( $roster_status == 'Y' ) {
                //Roster Slot

                foreach ($rosterStatusOfEmployee as $key => $value) {
                    $this -> data[ 'rosterStatus' ][] = $value[ 'emp_id' ];
                }

                $rosterSlot = $this -> roster_model -> getRosterSlot ( $dept_code );
                //print_r($rosterSlot);
                $this -> data[ 'rosterSlot' ] = $rosterSlot;

                $weekendData                   = $this -> roster_model -> getRosterSlotWeekendOfEmployee ( $dept_code );
                $holidayData                   = $this -> roster_model -> getRosterSlotHolidayOfEmployee ( $dept_code );
                $this -> data[ 'weekendData' ] = $weekendData;
                $this -> data[ 'holidayData' ] = $holidayData;
                //echo "Roster Slot";
                /* echo "<pr>";
                print_r($this->data);
                die(); */

                $this -> load -> view ( 'roster_slot' , $this -> data );

            }
            else {
                //Roster Reguler
                //print_r($rosterStatusOfEmployee);
                if ( count ( $rosterStatusOfEmployee ) > 0 ) {
                    foreach ($rosterStatusOfEmployee as $key => $value) {
                        $this -> data[ 'rosterStatus' ][] = $value[ 'emp_id' ];
                    }
                }
                $this -> data[ 'aDays' ] = $this -> GetDays ( $sdate , $edate );
                $this -> load -> view ( 'roster_regular' , $this -> data );
            }
        }

        $this -> load -> view ( 'footer' , $this -> data );
    }


    public function rosterSet ( $dept_code = '' )
    {
        $sdate = isset( $_POST[ 'sdate' ] ) ? trim ( $_POST[ 'sdate' ] ) : null;
        $edate = isset( $_POST[ 'edate' ] ) ? trim ( $_POST[ 'edate' ] ) : null;

        if ( isset( $sdate ) && isset( $edate ) ) {
            $stime    = strtotime ( $sdate );
            $etime    = strtotime ( $edate );
            $dateDiff = $etime - $stime;

            $diff = (($dateDiff / (60 * 60 * 24)) + 1);
            if ( $diff <= 15 ) {
                $this -> data[ 'sdate' ] = $sdate;
                $this -> data[ 'edate' ] = $edate;
            }
            else {
                $this -> data[ 'sdate' ] = date ( 'Y-m-d' );
                $this -> data[ 'edate' ] = date ( 'Y-m-d' , strtotime ( '+2 week' ) );
            }
        }
        else {
            $this -> data[ 'sdate' ] = date ( 'Y-m-d' );
            $this -> data[ 'edate' ] = date ( 'Y-m-d' , strtotime ( '+2 week' ) );

        }

        $roster_setting_accessers = $this -> leave_model -> getPermissionPrivileger ( ROSTER_SETTING );
        // echo "<pre>";

        // die();


        $this -> data[ 'isManager' ] = false;
        if ( $this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId ) ) {
            $this -> data[ 'isManager' ] = true;
            $allRosterDepts              = $this -> roster_model -> getAllDeptCode ();
            if ( ! ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts )) ) {
                $dept_code = current ( $allRosterDepts );
            }
            $staffRecord = $this -> roster_model -> getStaffListByDeptCode ( $dept_code , 'Y' );
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }
        }
        else if ( $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
            if ( $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
                $empSessInfo = $this -> session -> GetMyBriefInfo ();
                if ( ! empty( $empSessInfo -> userDeptCode ) ) {
                    $allRosterDepts = array( $empSessInfo -> userDeptCode );
                }
                else {
                    $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
                }
            }
            else {
                $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
            }
            $this -> data[ 'isManager' ] = true;

            if ( ! ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts )) ) {
                $dept_code = current ( $allRosterDepts );
            }

            $staffRecord = $this -> roster_model -> getStaffListByDeptCode ( $dept_code , 'Y' );
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

        }
        else if ( isset( $roster_setting_accessers[ $this -> myEmpId ] ) ) {
            $this -> data[ 'isManager' ] = true;
            $allRosterDepts              = array();

            //$staff_array = $this->user_model->getStaffArrayByIds( $roster_setting_accessers[$this->myEmpId] );
            $staff_array = $this -> roster_model -> getStaffArrayByIds ( $roster_setting_accessers[ $this -> myEmpId ] );

            foreach ($staff_array[ 'all' ] as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

            $myInfo    = $this -> data[ 'myInfo' ];
            $dept_code = $myInfo -> userDeptCode;
            $dept_code = 'CA';

        }

        $staffIDs = array();
        foreach ($staffArray as $key => $value) {
            $staffIDs[] = $key;
        }

        if ( count ( $staffArray ) == 0 ) redirect ( '/user/detail' );
        $rosterEmployee = $this -> roster_model -> getRosterStatusOfEmployee ( $dept_code , 'Y' );
        $rosterSlot     = $this -> roster_model -> getRosterSlot ( $dept_code );
        $rosterData     = $this -> roster_model -> new_getRosterSlotDataOfEmployee ( $dept_code , $this -> data[ 'sdate' ] , $this -> data[ 'edate' ] , $staffIDs );

        //$this->data["rosterHolidays"] = $rosterHolidays;
        $this -> data[ 'rosterData' ]        = $rosterData;
        $this -> data[ 'rosterEmployee' ]    = $rosterEmployee;
        $this -> data[ 'staffArray' ]        = $staffArray;
        $this -> data[ 'rosterSlot' ]        = $rosterSlot;
        $this -> data[ 'rosterDepartments' ] = $allRosterDepts;
        $this -> data[ 'selectedDeptCode' ]  = $dept_code;
        $this -> load -> view ( 'header' , $this -> data );
        $this -> load -> view ( 'new_roster_header' , $this -> data );

        $this -> load -> view ( 'footer' , $this -> data );
    }

    public function add_roster_all ()
    {

        $roster_setting_accessers = $this -> leave_model -> getPermissionPrivileger ( ROSTER_SETTING );

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ) || isset( $roster_setting_accessers[ $this -> myEmpId ] ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ))
        ) {

            return;
        }

        $flag     = false;
        $flag1    = false;
        $flag2    = false;
        $flag3    = false;
        $flag4    = false;
        $flagT    = false;
        $insertId = false;

        $dataObj    = isset( $_POST[ 'dataObj' ] ) ? json_decode ( $_POST[ 'dataObj' ] ) : array();
        $weekendObj = isset( $_POST[ 'weekendObj' ] ) ? json_decode ( $_POST[ 'weekendObj' ] ) : array();

        if ( isset( $dataObj -> toAdmin ) && is_bool ( $dataObj -> toAdmin ) && $dataObj -> toAdmin ) {
            //save roster data to temp database

            $tstamp = date ( 'Y-m-d H:i:s' );

            $data[ 'dept_code' ] = isset( $_POST[ 'selDept' ] ) ? $_POST[ 'selDept' ] : '';
            $data[ 'reason' ]    = isset( $_POST[ 'reason' ] ) ? trim ( $_POST[ 'reason' ] ) : '';

            $firstDate   = 0;
            $lastDate    = 0;
            $isFirstTime = true;

            foreach ($dataObj as $date => $obj) {
                if ( $date == 'toAdmin' || is_bool ( $obj ) ) continue;
                $isEmpty = $this -> isEmptyObj ( $obj );

                if ( isset( $obj ) && ! $isEmpty ) {
                    $curDate = strtotime ( $date );

                    if ( $isFirstTime ) {
                        $firstDate = $curDate;
                    }
                    if ( $curDate > $lastDate ) {
                        $lastDate = $curDate;
                    }
                    if ( $curDate < $firstDate ) {
                        $firstDate = $curDate;
                    }

                    $isFirstTime = false;
                }
            }

            if ( $firstDate != 0 & $lastDate != 0 ) {

                $data[ 'emp_ids' ]   = '';
                $data[ 'sdate' ]     = date ( 'Y-m-d' , $firstDate );
                $data[ 'edate' ]     = date ( 'Y-m-d' , $lastDate );
                $data[ 'tstamp' ]    = $tstamp;
                $data[ 'sender_id' ] = $this -> myEmpId;

                $insertId = $this -> roster_model -> add_rostering_control ( $data );
            }

            foreach ($dataObj as $date => $obj) {
                if ( $date == 'toAdmin' || is_bool ( $obj ) ) continue;

                if ( isset( $obj ) && (is_array ( $obj ) || is_object ( $obj )) ) {

                    foreach ($obj as $slot => $coreObj) {

                        if ( isset( $coreObj -> staff ) && count ( $coreObj -> staff ) > 0 ) {
                            $staffs = $coreObj -> staff;

                            if ( $insertId ) {

                                foreach ($staffs as $key => $emp_id) {
                                    $dat             = new stdClass();
                                    $dat -> emp_id   = $emp_id;
                                    $dat -> from     = $coreObj -> from;
                                    $dat -> to       = $coreObj -> to;
                                    $dat -> incharge = false;
                                    $dat -> tstamp   = $tstamp;
                                    if ( isset( $coreObj -> inCharge ) ) {

                                        if ( $emp_id == $coreObj -> inCharge ) {
                                            $dat -> incharge = true;
                                        }
                                    }
                                    $flagT = $this -> roster_model -> addRosterTmp ( $dat );
                                }
                            }
                        }

                        if ( isset( $obj -> weekend ) && count ( $obj -> weekend ) > 0 ) {

                            $wObj = $obj -> weekend;
                            foreach ($wObj as $emp_id => $name) {

                                $Wdata = array( 'emp_id' => $emp_id , 'date' => $date , 'tstamp' => $tstamp );
                                $flag  = $this -> roster_model -> addWeekend ( $Wdata , 'weekend_tmp' );
                            }
                        }
                    }
                }
            }

            if ( $insertId ) {
                //send mail to admin
                $data[ 'insertId' ] = $insertId;

                $this -> mailToAdmin ( $data );
            }

        }
        else {
            //save
            /* echo '<pre>';
            print_r($dataObj);
            die(); */
            foreach ($dataObj as $date => $obj) {

                if ( isset( $obj ) && (is_array ( $obj ) || is_object ( $obj )) ) {

                    foreach ($obj as $slot => $coreObj) {

                        if ( $slot == 'weekend' ) {

                            $wObj = $coreObj;
                            foreach ($wObj as $emp_id => $name) {

                                $Wdata = array( 'emp_id' => $emp_id , 'date' => $date );
                                $flag  = $this -> roster_model -> addWeekend ( $Wdata , 'weekend' );
                            }

                        }
                        elseif ( $slot == 'holiday' ) {

                            if ( isset( $coreObj -> removeStaff ) ) {

                                $removeStaff = $coreObj -> removeStaff;

                                foreach ($removeStaff as $emp_id) {

                                    $dat           = new stdClass();
                                    $dat -> date   = $coreObj -> from;
                                    $dat -> emp_id = $emp_id;

                                    $flag1 = $this -> roster_model -> deleteHoliday ( $dat );;
                                }
                            }

                            if ( isset( $coreObj -> staff ) ) {

                                $staffs = $coreObj -> staff;

                                foreach ($staffs as $key => $emp_id) {
                                    $dat               = new stdClass();
                                    $dat -> date       = $coreObj -> from;
                                    $dat -> emp_id     = $emp_id;
                                    $dat -> holiday_id = 0;
                                    $flag2             = $this -> roster_model -> addHoliday ( $dat );
                                }
                            }


                        }
                        else {
                            if ( isset( $coreObj -> removeStaff ) ) {
                                $removeStaff = $coreObj -> removeStaff;

                                foreach ($removeStaff as $emp_id) {

                                    $dat           = new stdClass();
                                    $dat -> from   = $coreObj -> from;
                                    $dat -> to     = $coreObj -> to;
                                    $dat -> emp_id = $emp_id;

                                    $flag3 = $this -> roster_model -> deleteRoster ( $dat );
                                }
                            }

                            if ( isset( $coreObj -> staff ) ) {
                                $staffs = $coreObj -> staff;

                                foreach ($staffs as $key => $emp_id) {
                                    $dat             = new stdClass();
                                    $dat -> from     = $coreObj -> from;
                                    $dat -> to       = $coreObj -> to;
                                    $dat -> incharge = false;

                                    $dat -> emp_id = $emp_id;
                                    if ( isset( $coreObj -> inCharge ) ) {

                                        if ( $emp_id == $coreObj -> inCharge ) {
                                            $dat -> incharge = true;
                                        }
                                    }
                                    $flag4 = $this -> roster_model -> addRoster ( $dat );
                                }
                            }
                        }
                    }

                }
            }
        }

        if ( $flag || $flag1 || $flag2 || $flag3 || $flag4 || $flagT || $insertId ) {
            $return[ 'status' ] = true;
            $return[ 'msg' ]    = 'Roster time has been successfully added.';
        }
        else {
            $return[ 'status' ] = false;
            $return[ 'msg' ]    = 'Adding of roster time has been failed';
        }

        echo json_encode ( $return );
    }


    public function show ()
    {

        if ( $_POST ) {
            $emp_id = isset( $_POST[ 'select_staff' ] ) ? $_POST[ 'select_staff' ] : $this -> myEmpId;
            $sdate  = $_POST[ 'dateFrom' ];
            $edate  = $_POST[ 'dateTo' ];
        }
        else {
            $emp_id = $this -> myEmpId;
            $sdate  = date ( 'Y-m-01' );
            $edate  = date ( 'Y-m-t' );
        }

        if ( $sdate > $edate ) {
            $temp  = $sdate;
            $sdate = $edate;
            $edate = $temp;
        }

        $this -> data[ 'sel_dept' ]   = isset( $_POST[ 'select_dept' ] ) ? $_POST[ 'select_dept' ] : '';
        $this -> data[ 'sel_emp_id' ] = $emp_id;
        $this -> data[ 'sel_sdate' ]  = $sdate;
        $this -> data[ 'sel_edate' ]  = $edate;

        if ( $this -> data[ 'uType' ] == 'A' || $this -> data[ 'uType' ] == 'B' ) {

            $this -> data[ 'departmentLists' ] = $this -> data[ 'departments' ];
            $staff_array                       = $this -> user_model -> getStaffArray ();
            $this -> data[ 'staff_array' ]     = $staff_array;

        }
        else if ( $this -> data[ 'uType' ] == 'M' ) {

            $this -> data[ 'departmentLists' ] = $this -> user_model -> getManagersDepts ( $this -> data[ 'userId' ] );
            $depts                             = array_keys ( $this -> data[ 'departments' ] );

            //print_r($depts);
            $staff_array                   = $this -> user_model -> getStaffArray ( $depts );
            $this -> data[ 'staff_array' ] = $staff_array;

        }
        else if ( $this -> data[ 'uType' ] == 'E' ) {

            $this -> data[ 'departmentLists' ] = array();
            $this -> data[ 'staff_array' ]     = array();
        }

        $rosterDeptCode = $this -> roster_model -> isThisStaffRoster ( $emp_id );

        $this -> data[ 'isNonRoster' ]  = false;
        $this -> data[ 'isSlotRoster' ] = false;
        $this -> data[ 'isRegRoster' ]  = false;

        if ( empty( $rosterDeptCode ) ) {
            //non roster
            //echo "Daily office Schedule";


            $this -> data[ 'isNonRoster' ]     = true;
            $this -> data[ 'officeSchedules' ] = $this -> roster_model -> getNonRosterOfficeSchedule ( $emp_id , $sdate , $edate , $this -> default_weekend );

            //print_r($this->data["officeSchedules"]);
        }
        else {
            //roster

            if ( isset( $this -> rosterType[ $rosterDeptCode ] ) && $this -> rosterType[ $rosterDeptCode ] == 'S' ) {
                //roster Slot
                $this -> data[ 'isSlotRoster' ] = true;
                $this -> data[ 'rosterSlot' ]   = $this -> roster_model -> getRosterSlot ( $rosterDeptCode );
                //print_r($this->data["rosterSlot"]);
                $rosterData                   = $this -> roster_model -> getRosterSlotDataOfEmployee ( $rosterDeptCode , $sdate , $edate );
                $this -> data[ 'rosterData' ] = $rosterData;
                //print_r($rosterData);

                $weekendData                   = $this -> roster_model -> getRosterSlotWeekendOfEmployee ( $rosterDeptCode );
                $this -> data[ 'weekendData' ] = $weekendData;

                //print_r($weekendData);


                //echo "Slot";

            }
            else {
                //roster Reguler
                //echo "Reguler";
                $this -> data[ 'isRegRoster' ] = true;

                $rosterData                   = $this -> roster_model -> getRosterRegulerDataOfEmployee ( $emp_id , $sdate , $edate );
                $this -> data[ 'rosterData' ] = $rosterData;

                $weekendData                   = $this -> roster_model -> getRosterRegulerWeekendOfEmployee ( $emp_id );
                $this -> data[ 'weekendData' ] = $weekendData;

            }
        }

        $this -> data[ 'title' ]     = 'Office_schedule';
        $this -> data[ 'sub_title' ] = 'Office Schedule';


        $this -> view ( 'roster_view' , $this -> data );

    }

    public function add ()
    {

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ))
        ) {
            redirect ( base_url () . 'user/login/' );
            //return;
        }

        $obj = new stdClass();

        $obj -> emp_id    = isset( $_POST[ 'emp_id' ] ) ? trim ( $_POST[ 'emp_id' ] ) : '';
        $obj -> from      = isset( $_POST[ 'from' ] ) ? trim ( $_POST[ 'from' ] ) : '';
        $obj -> to        = isset( $_POST[ 'to' ] ) ? trim ( $_POST[ 'to' ] ) : '';
        $obj -> dept_code = isset( $_POST[ 'dcode' ] ) ? trim ( $_POST[ 'dcode' ] ) : '';

        $inesrt_id = $this -> roster_model -> addRoster ( $obj );

        //Add record into table "rostering"
        //and echo $pkid = mysql_insert_id(); as following
        //$pkid = "1";

        //echo $insert_id;
    }

    public function del ( $pkid )
    {
        //echo $pkid;die;

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ))
        ) {
            return;
        }

        $isDelete = $this -> roster_model -> deleteRosterById ( $pkid );
        if ( $isDelete ) {
            $this -> addActivityLog ( 'D' , "id=$pkid" , "Roster($pkid) Deleted" );
        }

        //echo $isDelete;
    }

    public function add_roster_slot ( $dept_code = '' )
    {
        if ( empty( $dept_code ) ) redirect ( realpath () . '/user/login/' );

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ))
        ) {
            return;
        }

        $data[ 'dept_code' ] = $dept_code;
        $data[ 'slot_no' ]   = isset( $_POST[ 'slotNo' ] ) ? trim ( $_POST[ 'slotNo' ] ) : '';
        $data[ 'from' ]      = isset( $_POST[ 'rosterSlotFrom' ] ) ? trim ( $_POST[ 'rosterSlotFrom' ] ) : '';
        $data[ 'to' ]        = isset( $_POST[ 'rosterSlotTo' ] ) ? trim ( $_POST[ 'rosterSlotTo' ] ) : '';

        $insert_id = $this -> roster_model -> addRosterSlot ( $data );

        if ( $insert_id ) {
            $this -> addActivityLog ( 'A' , '' , 'Roster Slot (slot_no=' . $data[ 'slot_no' ] . ', from ' . $data[ 'from' ] . ' to ' . $data[ 'to' ] . ') Added' );
            $return[ 'status' ]    = true;
            $return[ 'msg' ]       = 'Roster slot has been successfully added.';
            $return[ 'insert_id' ] = $insert_id;
        }
        else {
            $return[ 'status' ] = false;
            $return[ 'msg' ]    = 'Adding of roster slot has been failed';

        }

        echo json_encode ( $return );
    }

    public function update_roster_slot ( $dept_code = '' )
    {
        if ( empty( $dept_code ) ) redirect ( realpath () . '/user/login/' );
        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ))
        ) {
            return;
        }

        $data[ 'dept_code' ] = $dept_code;
        $data[ 'slot_no' ]   = isset( $_POST[ 'slotNo' ] ) ? trim ( $_POST[ 'slotNo' ] ) : '';
        $data[ 'from' ]      = isset( $_POST[ 'rosterSlotFrom' ] ) ? trim ( $_POST[ 'rosterSlotFrom' ] ) : '';
        $data[ 'to' ]        = isset( $_POST[ 'rosterSlotTo' ] ) ? trim ( $_POST[ 'rosterSlotTo' ] ) : '';
        $roster_id           = isset( $_POST[ 'rosterId' ] ) ? trim ( $_POST[ 'rosterId' ] ) : '';

        if ( ! empty( $roster_id ) ) {

            $flag = $this -> roster_model -> updateRosterSlot ( $roster_id , $data );

            if ( $flag ) {
                $this -> addActivityLog ( 'U' , '' , 'Roster Slot (slot_no=' . $data[ 'slot_no' ] . ', from ' . $data[ 'from' ] . ' to ' . $data[ 'to' ] . ') Updated' );
                $return[ 'status' ] = true;
                $return[ 'msg' ]    = $this -> message[ 'update_s' ];
            }
            else {
                $return[ 'status' ] = false;
                $return[ 'msg' ]    = $this -> message[ 'update_f' ];

            }
        }
        else {
            $return[ 'status' ] = false;
            $return[ 'msg' ]    = $this -> message[ 'update_f' ];
        }

        echo json_encode ( $return );
    }

    public function del_roster_slot ( $roster_id = '' )
    {
        if ( empty( $roster_id ) ) redirect ( realpath () . '/user/login/' );
        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ))
        ) {
            return;
        }

        $flag = $this -> roster_model -> del_roster_slot ( $roster_id );

        if ( $flag ) {
            $this -> addActivityLog ( 'D' , '' , "Roster Slot (slot_no=$roster_id) Deleted" );
            $return[ 'status' ] = true;
            $return[ 'msg' ]    = 'Roster slot has been successfully removed.';
        }
        else {
            $return[ 'status' ] = false;
            $return[ 'msg' ]    = 'failed to remove Roster slot. try again.';
        }

        echo json_encode ( $return );
    }

    public function incharge ( $pkid )
    {

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ))
        ) {
            return;
        }

        $rowInfo = $this -> roster_model -> getRosterInfoByPkId ( $pkid );

        $this -> roster_model -> updateRoster ( $rowInfo );
    }

    private function GetDays ( $sStartDate , $sEndDate )
    {
        $sStartDate   = date ( 'Y-m-d' , strtotime ( $sStartDate ) );
        $sEndDate     = date ( 'Y-m-d' , strtotime ( $sEndDate ) );
        $aDays[]      = $sStartDate;
        $sCurrentDate = $sStartDate;
        while ($sCurrentDate < $sEndDate) {
            $sCurrentDate = date ( 'Y-m-d' , strtotime ( '+1 day' , strtotime ( $sCurrentDate ) ) );
            $aDays[]      = $sCurrentDate;
        }
        return $aDays;
    }



    // public function save() {

    //     if( ! ($this->session->IsAdmin($this->myEmpId) || $this->session->IsManagement($this->myEmpId)
    //         || $this->session->IsManager($this->myEmpId) || $this->session->HasRosterPrev($this->myEmpId))) {

    //         $this->load->view('not_found', $this->data);
    //         return;
    //     }

    //     $insert_id = false;
    //     $flag = false;
    //     $flag2 = false;
    //     $flag3 = false;
    //     $isMailSent = false;

    //     /* genaral form data */
    // 	$type = isset($_POST["type"]) ? trim($_POST["type"]) : "";
    // 	$emp_ids = isset($_POST["staffIds"]) ? trim($_POST["staffIds"],",") : "";
    // 	$empIds = !empty($emp_ids) ? explode(",", $emp_ids) : array();
    // 	$sdate = isset($_POST["sdate"]) ? trim($_POST["sdate"]) : "";
    // 	$edate = isset($_POST["edate"]) ? trim($_POST["edate"]) : "";
    // 	$actLogTxt = "Roster set for employees($emp_ids) date from $sdate to $edate";
    // 	//var_dump($_POST);
    // 	if($type == "same") {
    // 	    /* specific data of same's form */
    // 		$stime = isset($_POST["sstime"]) ? trim($_POST["sstime"]) : "";
    // 		$etime = isset($_POST["setime"]) ? trim($_POST["setime"]) : "";

    // 		$options = isset($_POST['chk']) ? $_POST['chk'] : array();

    // 		if (count($options) <= $this->max_holiday_in_week){

    // 		    if(count($empIds)>0) {
    // 		        $flag = $this->roster_model->addRosterForSameTimeNonSlot($empIds, $sdate, $edate, $options, $stime, $etime);
    // 		    }

    // 		} else{
    // 		    //add to temp database
    // 		    $data['dept_code'] = isset($_POST["d_code"]) ? trim($_POST["d_code"]) : "";
    // 		    $data['reason'] = isset($_POST["reason"]) ? trim($_POST["reason"]) : "";
    // 		    $data['emp_ids'] = $emp_ids;
    // 		    $data['sdate'] = $sdate;
    // 		    $data['edate'] = $edate;
    // 		    $data['tstamp'] = date('Y-m-d H:i:s');
    // 		    $data['sender_id'] = $this->myEmpId;

    // 		    $insert_id = $this->roster_model->add_rostering_control($data);

    // 		    if($insert_id){

    // 		        foreach ($empIds as $emp_id) {
    // 		            for($date=$sdate; $date <= $edate; ){
    // 		                $dayName = strtolower(date('D', strtotime($date)));

    // 		                if(in_array($dayName, $options)){
    // 		                    $Wdata =  array('emp_id'=>$emp_id, 'date'=> $date, 'tstamp' => $data['tstamp']) ;
    // 		                    $flag = $this->roster_model->addWeekend($Wdata, 'weekend_tmp');
    // 		                } else {
    // 		                    $isExists = $this->roster_model->getRosterRow($emp_id, $date, 'rostering_tmp');
    // 		                    if($isExists) {
    // 		                        $flag2 = $this->roster_model->updateRosterRow_tmp($emp_id, $date, $stime, $etime, $data['tstamp']);

    // 		                    } else {
    // 		                        $Rdata = array('emp_id' =>$emp_id, 'stime'=>$date." ".$stime, 'etime'=>$date." ".$etime, 'tstamp'=>$data['tstamp']);
    // 		                        $flag3 =$this->roster_model->addRosterRow_tmp($Rdata);
    // 		                    }
    // 		                }

    // 		                $date = date("Y-m-d",strtotime($date." +1 days"));
    // 		            }
    // 		        }

    // 		        //sent mail to admin
    // 		        $data['insertId'] =  $insert_id;
    // 		        $isSent = $this->mailToAdmin($data);
    //                 if($isSent) {
    //                     $return['msg'] = $this->message['mail_s'];
    //                     $return['status'] = true;
    //                 } else {
    //                     $return['msg'] = $this->message['mail_f'];
    //                     $return['status'] = false;
    //                 }
    //                 echo json_encode($return);
    //                 die;

    // 		    }
    // 		}

    // 	} else {
    // 	    //custom
    // 	    /* specific data of custom's form */
    // 	    $toAdmin = isset($_POST["toAdmin"]) ? $_POST["toAdmin"] : "";
    // 	    $options = isset($_POST['leave_chk']) ? $_POST['leave_chk'] : array();
    // 	    $date = isset($_POST["date"]) ? $_POST["date"] : "";
    // 	    $stime = isset($_POST["stime"]) ? $_POST["stime"] : "";
    // 	    $etime = isset($_POST["etime"]) ? $_POST["etime"] : "";


    // 	    if($toAdmin){
    // 	        //echo "to admin";

    // 	        //add to temp database
    // 	        $data['dept_code'] = isset($_POST["d_code"]) ? trim($_POST["d_code"]) : "";
    // 	        $data['reason'] = isset($_POST["customReason"]) ? trim($_POST["customReason"]) : "";
    // 	        $data['emp_ids'] = $emp_ids;
    // 	        $data['sdate'] = isset($_POST["sdate"]) ? trim($_POST["sdate"]) : "";
    // 	        $data['edate'] = isset($_POST["edate"]) ? trim($_POST["edate"]) : "";
    // 	        $data['tstamp'] = date('Y-m-d H:i:s');
    // 	        $data['tstamp'];
    // 	        $data['sender_id'] = $this->myEmpId;

    // 	        $insert_id = $this->roster_model->add_rostering_control($data);

    // 	        if($insert_id){
    // 	            foreach ($empIds as $emp_id) {
    // 	                foreach($date as $key=>$rdate)
    // 	                {
    // 	                    if(in_array($rdate, $options)){
    // 	                        //holiday, add to weekend
    // 	                        $Wdata =  array('emp_id'=>$emp_id, 'date'=> $rdate, 'tstamp' => $data['tstamp']) ;
    // 	                        $flag = $this->roster_model->addWeekend($Wdata, 'weekend_tmp');
    // 	                    } else{
    // 	                        //normal day
    // 	                        $stm = $stime[$key];
    // 	                        $etm = $etime[$key];

    // 	                        $isExists = $this->roster_model->getRosterRow($emp_id, $rdate, 'rostering_tmp');
    // 	                        if($isExists) {
    // 	                            $flag2 = $this->roster_model->updateRosterRow_tmp($emp_id, $rdate, $stm, $etm, $data['tstamp']);
    // 	                        } else {
    // 	                            $Rdata = array('emp_id' =>$emp_id, 'stime'=>$rdate." ".$stm, 'etime'=>$rdate." ".$etm, 'tstamp'=>$data['tstamp']);
    // 	                            $flag3 = $this->roster_model->addRosterRow_tmp($Rdata);
    // 	                        }
    // 	                    }

    // 	                }
    // 	            }
    // 	            //sent mail to admin
    // 	            $data['insertId'] =  $insert_id;
    // 		        $isMailSent = $this->mailToAdmin($data);

    // 	        }


    // 	    }else{
    // 	        //echo "normal";

    // 	        if(count($empIds)>0) {

    // 	            $flag = $this->roster_model->addRosterForCustomTimeNonSlot($empIds, $date, $options, $stime, $etime);
    // 	        }
    // 	    }
    // 	}

    // 	if($isMailSent) {

    // 	    $this->data["sub_title"] = "Message";
    // 	    $this->data['message'] = "<span style='color: green'>Mail is sent to admin successfully. wait for verification.<span>";

    // 	    $link =array();
    // 	    $link['href'] = base_url().'roster/set';
    // 	    $link['text'] = 'Go back to Roster Set page';
    // 	    $this->data['link'] = $link;

    // 	    $this->view('message_view', $this->data);

    // 	    return;
    // 	}

    // 	if($insert_id || $flag || $flag2 || $flag3){
    // 	    $this->addActivityLog('A', '', $actLogTxt);

    // 	    $this->data["sub_title"] = "Message";
    // 	    $this->data['message'] = "<span class='text-success'>Done; Record added successfully<span>";

    // 	}else{

    // 	    $this->data["sub_title"] = "Message";
    // 	    $this->data['message'] = "<span class='text-danger'>No Record is added.<br> Select all required fields first then try again.<span>";

    // 	    if(count($empIds)>0) {
    // 	        $this->data['message'] = "<span class='text-danger'>No Record is added. You Forgot to select Staff.<br> Select all required fields first then try again.<span>";
    // 	    }

    // 	}

    // 	$link =array();
    // 	$link['href'] = base_url().'roster/set';
    // 	$link['text'] = 'Go back to Roster Set page';
    // 	$this->data['link'] = $link;

    // 	$this->view('message_view', $this->data);
    // }
    public function officeTimeSetup ( $dept_code = '' )
    {
        $rosterStatusOfEmployee   = array();
        $roster_setting_accessers = $this -> leave_model -> getPermissionPrivileger ( ROSTER_SETTING );

        $sdate = isset( $_POST[ 'sdate' ] ) ? trim ( $_POST[ 'sdate' ] ) : date ( 'Y-m-d' );
        $edate = isset( $_POST[ 'edate' ] ) ? trim ( $_POST[ 'edate' ] ) : date ( 'Y-m-t' );

        $allRosterDepts              = array();
        $this -> data[ 'isManager' ] = false;
        $staffArray                  = array();

        if ( $this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId ) ) {

            $allRosterDepts = $this -> roster_model -> getAllDeptCode ();

            $this -> data[ 'isManager' ] = true;

            if ( ! ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts )) ) {
                $dept_code = current ( $allRosterDepts );
            }

            $staffRecord = $this -> roster_model -> getStaffListByDeptCode ( $dept_code , 'N' );
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

        }
        else if ( $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
            if ( $this -> session -> HasRosterPrev ( $this -> myEmpId ) ) {
                $empSessInfo = $this -> session -> GetMyBriefInfo ();
                if ( ! empty( $empSessInfo -> userDeptCode ) ) {
                    $allRosterDepts = array( $empSessInfo -> userDeptCode );
                }
                else {
                    $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
                }
            }
            else {
                $allRosterDepts = $this -> session -> getManagerDepartments ( $this -> myEmpId );
            }
            $this -> data[ 'isManager' ] = true;

            if ( ! ( ! empty( $dept_code ) && in_array ( $dept_code , $allRosterDepts )) ) {
                $dept_code = current ( $allRosterDepts );
            }

            /* To ge tstaff Array */
            $staffRecord = $this -> roster_model -> getStaffListByDeptCode ( $dept_code , 'N' );
            foreach ($staffRecord as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

        }
        else if ( isset( $roster_setting_accessers[ $this -> myEmpId ] ) ) {
            $this -> data[ 'isManager' ] = true;
            $allRosterDepts              = array();

            $staff_array = $this -> user_model -> getStaffArrayByIds ( $roster_setting_accessers[ $this -> myEmpId ] );
            foreach ($staff_array[ 'all' ] as $obj) {
                $staffArray[ $obj -> emp_id ] = $obj -> name;
            }

            $myInfo    = $this -> data[ 'myInfo' ];
            $dept_code = $myInfo -> userDeptCode;

        }
        else {
            $this -> data[ 'status_array' ] = $this -> status_array;
            $this -> data[ 'title' ]        = 'ABC';
            $this -> data[ 'sub_title' ]    = 'ABC';
            $this -> data[ 'message' ]      = 'You have no privilege to access this page!';
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        //$staffIDs = array();
        //$rosterData = $this->roster_model->getRosterSlotDataOfEmployee($dept_code, $sdate, $edate,$staffIDs);

        $this -> data[ 'sdate' ] = $sdate;
        $this -> data[ 'edate' ] = $edate;

        $this -> data[ 'title' ]     = 'Office Time Setup';
        $this -> data[ 'sub_title' ] = 'Regular Employee Schedule Settings';


        //$this->data["rosterData"] = $rosterData;
        $this -> data[ 'rosterDepartments' ] = $allRosterDepts;
        //print_r(reset($allRosterDepts));
        $this -> data[ 'selectedDeptCode' ] = $dept_code;
        $this -> data[ 'staffArray' ]       = $staffArray;
        $this -> data[ 'max_weekend' ]      = $this -> max_holiday_in_week;
        $this -> data[ 'day_array' ]        = $this -> day_array;

        $this -> load -> view ( 'header' , $this -> data );

        $rosterStatusOfEmployee = $this -> roster_model -> getRosterStatusOfEmployee ( $dept_code , 'N' );


        if ( count ( $rosterStatusOfEmployee ) > 0 ) {
            foreach ($rosterStatusOfEmployee as $key => $value) {
                $this -> data[ 'rosterStatus' ][] = $value[ 'emp_id' ];
            }
        }

        $this -> data[ 'aDays' ] = $this -> GetDays ( $sdate , $edate );

        $this -> load -> view ( 'non_roster_header_new' , $this -> data );
        //print_r($this->data['rosterStatus']);
        $this -> load -> view ( 'roster_regular_new' , $this -> data );

        $this -> load -> view ( 'footer' , $this -> data );
    }

    public function setData ()
    {
        if ( ! empty( $_POST ) ) {

            $employeeSchedule    = (array)json_decode ( $_POST[ 'employeeSchedule' ] );
            $employeeWeekendList = (array)json_decode ( $_POST[ 'employeeWeekendList' ] );
            $finalArray          = (array)array_merge ( $employeeSchedule , $employeeWeekendList );
            // echo json_encode($finalArray);die();


            $ddate     = $this -> input -> post ( 'ddate' , true );
            $dept_code = $this -> input -> post ( 'dept_code' , true );
            //$holiday_type = $this->input->post('is_govt_holiday',true);
            $this -> roster_model -> deleteWeekend ( $ddate , $dept_code );

            foreach ($finalArray as $key => &$value) $value = (array)$value;

            foreach ($finalArray as $key => &$value) {
                if ( $value[ 'start_time' ] != '' && $value[ 'end_time' ] != '' ) {
                    $date  = $value[ 'ddate' ];
                    $stime = strtotime ( $value[ 'start_time' ] );
                    $etime = strtotime ( $value[ 'end_time' ] );

                    $start_time = &$value[ 'start_time' ];
                    $end_time   = &$value[ 'end_time' ];

                    if ( $stime >= $etime ) {
                        $iDate      = date ( 'Y-m-d' , strtotime ( $date . ' +1 days' ) );
                        $start_time = $date . ' ' . date ( 'H:i:s' , strtotime ( $start_time ) );
                        $end_time   = $iDate . ' ' . date ( 'H:i:s' , strtotime ( $end_time ) );
                    }
                    else {
                        $start_time = $date . ' ' . date ( 'H:i:s' , strtotime ( $start_time ) );
                        $end_time   = $date . ' ' . date ( 'H:i:s' , strtotime ( $end_time ) );
                    }
                }
            }
            $status = $this -> roster_model -> insertRosterSchedule ( $finalArray );
            if ( $status ) {
                $msg[ 'status' ] = true;
                echo json_encode ( $msg );
            }
            else {
                $msg[ 'status' ] = false;
                echo json_encode ( $msg );
            }
        }
    }

    public function deleteData ()
    {
        $emp_id     = $this -> input -> post ( 'emp_id' , true );
        $ddate      = $this -> input -> post ( 'ddate' , true );
        $start_time = $this -> input -> post ( 'start_time' , true );
        $end_time   = $this -> input -> post ( 'end_time' , true );
        $is_holiday = $this -> input -> post ( 'is_holiday' , true );
        $data       = array(
            'emp_id' => $emp_id ,
            'ddate' => $ddate ,
            'start_time' => $start_time ,
            'end_time' => $end_time ,
            'is_holiday' => $is_holiday
        );
        //print_r($data);
        $this -> roster_model -> deleteData ( $data );
    }

    public function save ()
    {


        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ) || $this -> session -> HasRosterPrev ( $this -> myEmpId ))
        ) {
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        $insert_id  = false;
        $flag       = false;
        $flag2      = false;
        $flag3      = false;
        $isMailSent = false;

        $type = isset( $_POST[ 'type' ] ) ? trim ( $_POST[ 'type' ] ) : '';

        $emp_ids   = isset( $_POST[ 'staffIds' ] ) ? trim ( $_POST[ 'staffIds' ] , ',' ) : '';
        $empIds    = ! empty( $emp_ids ) ? explode ( ',' , $emp_ids ) : array();
        $sdate     = isset( $_POST[ 'sdate' ] ) ? trim ( $_POST[ 'sdate' ] ) : '';
        $edate     = isset( $_POST[ 'edate' ] ) ? trim ( $_POST[ 'edate' ] ) : '';
        $actLogTxt = "Roster set for employees($emp_ids) date from $sdate to $edate";

        if ( $type == 'same' ) {

            /* specific data of same's form */
            $stime     = isset( $_POST[ 'sstime' ] ) ? trim ( $_POST[ 'sstime' ] ) : '';
            $etime     = isset( $_POST[ 'setime' ] ) ? trim ( $_POST[ 'setime' ] ) : '';
            $options   = isset( $_POST[ 'chk' ] ) ? $_POST[ 'chk' ] : array();
            $dept_code = isset( $_POST[ 'd_code' ] ) ? trim ( $_POST[ 'd_code' ] ) : '';

            if ( count ( $options ) <= $this -> max_holiday_in_week ) {

                if ( count ( $empIds ) > 0 ) {

                    $flag = $this -> roster_model -> new_addRosterForSameTimeNonSlot ( $empIds , $sdate , $edate , $options , $stime , $etime , $dept_code );

                }
            }
            else {

                //add to temp database
                $data[ 'dept_code' ] = isset( $_POST[ 'd_code' ] ) ? trim ( $_POST[ 'd_code' ] ) : '';
                $data[ 'reason' ]    = isset( $_POST[ 'reason' ] ) ? trim ( $_POST[ 'reason' ] ) : '';
                $data[ 'emp_ids' ]   = $emp_ids;
                $data[ 'sdate' ]     = $sdate;
                $data[ 'edate' ]     = $edate;
                $data[ 'tstamp' ]    = date ( 'Y-m-d H:i:s' );
                $data[ 'sender_id' ] = $this -> myEmpId;

                $insert_id = $this -> roster_model -> new_add_rostering_control ( $data );

                $this -> roster_model -> deleteWeekendTempData ( $empIds , $sdate , $edate );
                $this -> roster_model -> deleteRosterTempData ( $empIds , $sdate , $edate );

                if ( $insert_id ) {

                    foreach ($empIds as $emp_id) {
                        for ($date = $sdate; $date <= $edate;) {
                            $dayName = strtolower ( date ( 'D' , strtotime ( $date ) ) );
                            if ( in_array ( $dayName , $options ) ) {
                                $Wdata = array( 'emp_id' => $emp_id , 'date' => $date , 'tstamp' => $data[ 'tstamp' ] );
                                $flag  = $this -> roster_model -> addWeekend ( $Wdata , 'weekend_tmp' );
                            }
                            else {
                                $isExists = $this -> roster_model -> getRosterRow ( $emp_id , $date , 'rostering_tmp' );
                                if ( $isExists ) {
                                    $flag2 = $this -> roster_model -> updateRosterRow_tmp ( $emp_id , $date , $stime , $etime , $data[ 'tstamp' ] );
                                }
                                else {
                                    $Rdata = array( 'emp_id' => $emp_id , 'stime' => $date . ' ' . $stime , 'etime' => $date . ' ' . $etime , 'tstamp' => $data[ 'tstamp' ] );
                                    $flag3 = $this -> roster_model -> addRosterRow_tmp ( $Rdata );
                                }
                            }
                            $date = date ( 'Y-m-d' , strtotime ( $date . ' +1 days' ) );
                        }
                    }

                    //sent mail to admin
                    $data[ 'insertId' ] = $insert_id;
                    $isSent             = $this -> mailToAdmin ( $data );
                    if ( $isSent ) {
                        $return[ 'msg' ]    = $this -> message[ 'mail_s' ];
                        $return[ 'status' ] = true;
                    }
                    else {
                        $return[ 'msg' ]    = $this -> message[ 'mail_f' ];
                        $return[ 'status' ] = false;
                    }
                    echo json_encode ( $return );
                    die;
                }
            }

        }
        else {

            //custom
            /* specific data of custom's form */
            $toAdmin   = isset( $_POST[ 'toAdmin' ] ) ? $_POST[ 'toAdmin' ] : '';
            $options   = isset( $_POST[ 'leave_chk' ] ) ? $_POST[ 'leave_chk' ] : array();
            $date      = isset( $_POST[ 'date' ] ) ? $_POST[ 'date' ] : '';
            $stime     = isset( $_POST[ 'stime' ] ) ? $_POST[ 'stime' ] : '';
            $etime     = isset( $_POST[ 'etime' ] ) ? $_POST[ 'etime' ] : '';
            $dept_code = isset( $_POST[ 'd_code' ] ) ? trim ( $_POST[ 'd_code' ] ) : '';

            if ( $toAdmin ) {


                $data[ 'dept_code' ] = isset( $_POST[ 'd_code' ] ) ? trim ( $_POST[ 'd_code' ] ) : '';
                $data[ 'reason' ]    = isset( $_POST[ 'customReason' ] ) ? trim ( $_POST[ 'customReason' ] ) : '';
                $data[ 'emp_ids' ]   = $emp_ids;
                $data[ 'sdate' ]     = isset( $_POST[ 'sdate' ] ) ? trim ( $_POST[ 'sdate' ] ) : '';
                $data[ 'edate' ]     = isset( $_POST[ 'edate' ] ) ? trim ( $_POST[ 'edate' ] ) : '';
                $data[ 'tstamp' ]    = date ( 'Y-m-d H:i:s' );
                $data[ 'tstamp' ];
                $data[ 'sender_id' ] = $this -> myEmpId;

                $insert_id = $this -> roster_model -> new_add_rostering_control ( $data );

                if ( $insert_id ) {

                    foreach ($empIds as $emp_id) {
                        foreach ($date as $key => $rdate) {
                            if ( in_array ( $rdate , $options ) ) {
                                //holiday, add to weekend
                                $Wdata = array( 'emp_id' => $emp_id , 'date' => $rdate , 'tstamp' => $data[ 'tstamp' ] );
                                $flag  = $this -> roster_model -> addWeekend ( $Wdata , 'weekend_tmp' );
                            }
                            else {
                                //normal day
                                $stm = $stime[ $key ];
                                $etm = $etime[ $key ];

                                $isExists = $this -> roster_model -> getRosterRow ( $emp_id , $rdate , 'rostering_tmp' );
                                if ( $isExists ) {
                                    $flag2 = $this -> roster_model -> updateRosterRow_tmp ( $emp_id , $rdate , $stm , $etm , $data[ 'tstamp' ] );
                                }
                                else {
                                    $Rdata = array( 'emp_id' => $emp_id , 'stime' => $rdate . ' ' . $stm , 'etime' => $rdate . ' ' . $etm , 'tstamp' => $data[ 'tstamp' ] );
                                    $flag3 = $this -> roster_model -> addRosterRow_tmp ( $Rdata );
                                }
                            }

                        }
                    }
                    $data[ 'insertId' ] = $insert_id;
                    $isMailSent         = $this -> mailToAdmin ( $data );
                }
            }
            else {
                if ( count ( $empIds ) > 0 ) {

                    $flag = $this -> roster_model -> new_addRosterForCustomTimeNonSlot ( $empIds , $date , $options , $stime , $etime , $dept_code );
                }
            }
        }

        if ( $isMailSent ) {
            $this -> data[ 'sub_title' ] = 'Message';
            $this -> data[ 'message' ]   = "<span style='color: green'>Mail is sent to admin successfully. wait for verification.<span>";
            $link                        = array();
            $link[ 'href' ]              = base_url () . 'roster/set';
            $link[ 'text' ]              = 'Go Back';
            $this -> data[ 'link' ]      = $link;
            $this -> view ( 'message_view' , $this -> data );
            return;
        }

        if ( $insert_id || $flag || $flag2 || $flag3 ) {
            $this -> addActivityLog ( 'A' , '' , $actLogTxt );
            $this -> data[ 'sub_title' ] = 'Message';
            $this -> data[ 'message' ]   = "<span class='text-success'>Done; Record added successfully<span>";
        }
        else {
            $this -> data[ 'sub_title' ] = 'Message';
            $this -> data[ 'message' ]   = "<span class='text-danger'>No Record is added.<br> Select all required fields first then try again.<span>";
            if ( count ( $empIds ) > 0 ) {
                $this -> data[ 'message' ] = "<span class='text-danger'>No Record is added. You Forgot to select Staff.<br> Select all required fields first then try again.<span>";
            }
        }

        $link                   = array();
        $link[ 'href' ]         = base_url () . 'roster/set';
        $link[ 'text' ]         = 'Go Back';
        $this -> data[ 'link' ] = $link;

        $this -> view ( 'message_view' , $this -> data );
    }

    public function request_admin ()
    {

        if ( ! ($this -> session -> IsAdmin ( $this -> myEmpId ) || $this -> session -> IsManagement ( $this -> myEmpId )
            || $this -> session -> IsManager ( $this -> myEmpId ))
        ) {
            return;
        }

        $options = isset( $_POST[ 'mod_chk' ] ) ? $_POST[ 'mod_chk' ] : array();

        if ( count ( $options ) > $this -> max_holiday_in_week ) {

            $data[ 'tstamp' ]    = date ( 'Y-m-d H:i:s' );
            $data[ 'dept_code' ] = isset( $_POST[ 'd_code' ] ) ? trim ( $_POST[ 'd_code' ] ) : '';
            $data[ 'emp_ids' ]   = isset( $_POST[ 'staffIds' ] ) ? trim ( $_POST[ 'staffIds' ] , ',' ) : '';
            $data[ 'sdate' ]     = isset( $_POST[ 'from_date' ] ) ? trim ( $_POST[ 'from_date' ] ) : '';
            $data[ 'edate' ]     = isset( $_POST[ 'to_date' ] ) ? trim ( $_POST[ 'to_date' ] ) : '';
            $data[ 'reason' ]    = isset( $_POST[ 'reason' ] ) ? trim ( $_POST[ 'reason' ] ) : '';
            $data[ 'sender_id' ] = $this -> myEmpId;

            $from_time = isset( $_POST[ 'from_time' ] ) ? trim ( $_POST[ 'from_time' ] ) : '';
            $to_time   = isset( $_POST[ 'to_time' ] ) ? trim ( $_POST[ 'to_time' ] ) : '';

            $insert_id = $this -> roster_model -> add_rostering_control ( $data );

            //echo $insert_id;

            if ( $insert_id ) {


                //($empIds,$sdate,$edate, $options, $stime, $etime )


                // sent mail to admin
                $receiver = array();
                $receiver = $this -> user_model -> getAdminInfo ();

                $sender            = array();
                $sender[ 'name' ]  = $this -> data[ 'myInfo' ] -> userName;
                $sender[ 'email' ] = $this -> data[ 'myInfo' ] -> email;
                $subject           = 'Request for more than ' . $this -> max_holiday_in_week . ' holidays in a weak slot.';

                $designation = $this -> data[ 'myInfo' ] -> userDesignation;
                $dept        = $this -> data[ 'myInfo' ] -> userDepartment;
                $time        = date ( 'h:i:s A' );
                $day         = date ( 'l' );
                $emailBody   = "<table>
                    <thead style='width:100%; color:white;' bgcolor= '#3C8DBC' >
                    <tr><th colspan=2>COMPANY_PREFIX Staff</th></tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this -> myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this -> web_url . 'user/detail/' . $this -> data[ 'myInfo' ] -> userId . "'>$sender[name]</a></b></td></tr>
                    <tr><td><i>$designation</i></td></tr>
                    <tr><td>$dept</td></tr>
                    </table>
                    </td>
                    <td width='50%' valign='center' align='right'>
                    <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this -> web_url . "roster/holiday/$insert_id'>Show The Request</a>
                    </td>
                    </tr>
                    <tr height='40'><td colspan='2'>has sent you a request for approving holidays more than as usual in a week slot. <br> <b>Reason:</b> $data[reason]</td></tr>
                    </tbody>
                    <tfoot>
                    <tr><td colspan='2' align='right' style='border-top:1px solid #D5D1B6; color:white;' bgcolor='#3C8DBC'>Powered by - <a href='" . $this -> web_url . "'>EMS</a> &nbsp;</b></td></tr>
                    </tfoot>
	                </table>";

                // echo $emailBody;

                if ( $this -> mailer -> sendMail ( $subject , $emailBody , $receiver , $sender ) ) {
                    $return[ 'msg' ]    = $this -> message[ 'mail_s' ];
                    $return[ 'status' ] = true;
                }
                else {
                    $return[ 'msg' ]    = $this -> message[ 'mail_f' ];
                    $return[ 'status' ] = false;
                }

            }
            else {
                $return[ 'msg' ]    = $this -> message[ 'insert_f' ];
                $return[ 'status' ] = false;
            }

            // $empIds = !empty($emp_ids) ? explode(",", $emp_ids) : array();
        }
        else {
            //holliday less than as usuall.
            $return[ 'msg' ]    = 'holiday are as usual.';
            $return[ 'status' ] = false;
        }


        echo json_encode ( $return );
        die();
    }


    public function holiday_request ()
    {

        if ( ! ($this -> data[ 'uType' ] == 'A' || $this -> data[ 'uType' ] == 'B') ) {
            $this -> data[ 'status_array' ] = $this -> status_array;
            $this -> data[ 'title' ]        = 'ABC';
            $this -> data[ 'sub_title' ]    = 'ABC';
            $this -> data[ 'message' ]      = 'You have no privilege to access this page!';
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        $request = $this -> roster_model -> get_holiday_request ();

        $this -> data[ 'request' ] = $request;

        $this -> data[ 'title' ]     = 'roster_request';
        $this -> data[ 'sub_title' ] = 'Pending';

        $this -> view ( 'holiday_request' , $this -> data );
    }

    public function approve_holiday ( $id = '' )
    {

        if ( ! ($this -> data[ 'uType' ] == 'A' || $this -> data[ 'uType' ] == 'B') ) {
            $this -> data[ 'status_array' ] = $this -> status_array;
            $this -> data[ 'title' ]        = 'ABC';
            $this -> data[ 'sub_title' ]    = 'ABC';
            $this -> data[ 'message' ]      = 'You have no privilege to access this page!';
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        $data[ 'admin_id' ] = $this -> myEmpId;
        $return             = $this -> roster_model -> update_rostering_control ( $data , $id );


        if ( $return[ 'flag' ] ) {
            //sent mail to requester.
            $receiver   = array();
            $receiver[] = $return[ 'requesterInfo' ];

            $sender            = array();
            $sender[ 'name' ]  = $this -> data[ 'myInfo' ] -> userName;
            $sender[ 'email' ] = $this -> data[ 'myInfo' ] -> email;
            $subject           = 'Roster Request has been approved.';

            $designation        = $this -> data[ 'myInfo' ] -> userDesignation;
            $dept               = $this -> data[ 'myInfo' ] -> userDepartment;
            $time               = date ( 'h:i:s A' );
            $day                = date ( 'l' );
            $emailBody          = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this -> myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this -> web_url . 'user/detail/' . $this -> data[ 'myInfo' ] -> userId . "'>$sender[name]</a></b></td></tr>
                    <tr><td><i>$designation</i></td></tr>
                    <tr><td>$dept</td></tr>
                    </table>
                    </td>
                    <td width='50%' valign='center' align='right'>
                    <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this -> web_url . "roster/show/$id'>Show The Request</a>
                    </td>
                    </tr>
                    <tr height='40'><td colspan='2'>has approved your request of Weekend setting in roster more than as usual in a week slot.</tr>";
            $mail               = array();
            $mail[ 'subject' ]  = $subject;
            $mail[ 'body' ]     = $emailBody;
            $mail[ 'receiver' ] = $receiver;
            $mail[ 'sender' ]   = $sender;
            $mail[ 'web_url' ]  = $this -> web_url;

            if ( $this -> mailer -> sendEmail ( $mail ) ) {
                //return true;
            }


        }
        else {

        }

        redirect ( '/roster/holiday_request' );
    }

    public function del_holiday ( $id = '' )
    {

        if ( ! ($this -> data[ 'uType' ] == 'A' || $this -> data[ 'uType' ] == 'B') ) {
            $this -> data[ 'status_array' ] = $this -> status_array;
            $this -> data[ 'title' ]        = 'ABC';
            $this -> data[ 'sub_title' ]    = 'ABC';
            $this -> data[ 'message' ]      = 'You have no privilege to access this page!';
            $this -> load -> view ( 'not_found' , $this -> data );
            return;
        }

        $flag = $this -> roster_model -> del_rostering_control ( $id );

        redirect ( '/roster/holiday_request' );
    }

    private function isEmptyObj ( $obj )
    {

        foreach ($obj as $k) {
            return false;
        }

        return true;
    }

    private function mailToAdmin ( $data )
    {

        $receiver = array();
        $receiver = $this -> user_model -> getAdminInfo ();

        $sender            = array();
        $sender[ 'name' ]  = $this -> data[ 'myInfo' ] -> userName;
        $sender[ 'email' ] = $this -> data[ 'myInfo' ] -> email;
        $subject           = 'Request for more than ' . $this -> max_holiday_in_week . ' Weeekend in a weak slot.';

        $designation = $this -> data[ 'myInfo' ] -> userDesignation;
        $dept        = $this -> data[ 'myInfo' ] -> userDepartment;
        $time        = date ( 'h:i:s A' );
        $day         = date ( 'l' );
        $emailBody   = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this -> myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this -> web_url . 'user/detail/' . $this -> data[ 'myInfo' ] -> userId . "'>$sender[name]</a></b></td></tr>
                            <tr><td><i>$designation</i></td></tr>
                            <tr><td>$dept</td></tr>
                            </table>
                            </td>
                            <td width='50%' valign='center' align='right'>
                            <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this -> web_url . 'roster/show/' . $data[ 'insertId' ] . "'>Show The Request</a>
                            </td>
                            </tr>
                            <tr height='40'><td colspan='2'>has sent you a request for approving Weekend more than as usual in a week slot. <br> <b>Reason:</b> $data[reason]</td></tr>";

        $mail               = array();
        $mail[ 'subject' ]  = $subject;
        $mail[ 'body' ]     = $emailBody;
        $mail[ 'receiver' ] = $receiver;
        $mail[ 'sender' ]   = $sender;
        $mail[ 'web_url' ]  = $this -> web_url;

        if ( $this -> mailer -> sendEmail ( $mail ) ) {
            return true;
        }

        return false;

    }

    public function delete_roster_per_day ()
    {
        $dept_code = $this -> input -> post ( 'dept_code' );
        $iDate     = $this -> input -> post ( 'iDate' );
        $nextDate  = $this -> input -> post ( 'nextDate' );
        if ( isset( $dept_code ) && isset( $iDate ) && isset( $nextDate ) ) {
            $this -> roster_model -> delete_roster_per_day ( $iDate , $nextDate , $dept_code );
            redirect ( base_url () . "roster/set/$dept_code/Y" );
        }
        else {
            redirect ( base_url () . "roster/set/$dept_code/Y" );
        }

    }
}