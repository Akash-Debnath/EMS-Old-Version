<link href="<?php echo base_url()?>assets/lib/bootstrap/css/bootstrap-select.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/main.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/roster.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url()?>assets/lib/tipsy/tipsy.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-timepicker.min.css">
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet" />


<style type="text/css">
    #sdate,#edate{
        cursor: pointer;
    }
    .minus_css{
        display: none;
        padding-top: 3px;
        color:white;
    }

    .minus_css_1{
        padding-top: 3px;
        color:white;
    }

    .x{
        display: inline-block;
        cursor: move;
        width: auto;
        background-color: #1c2d3f;
        color:white;
        padding: 3px 10px 6px 10px;
        border-radius: 5px;
        margin-bottom: 2px;
        opacity: 1.0;
    }
    .weekendEmpList{
        display: inline-block;
        width: auto;
        background-color: #1c2d3f;
        color:white;
        padding: 3px 10px 6px 10px;
        border-radius: 5px;
        margin-bottom: 2px;
        display: block;
    }
    .x a{float: right;}

    table tr td .x{
        width: 100%;
        cursor: pointer;
    }

    table tr .drop-box{
        width: 220px;
    }

    .nav{
        top:-10px;
    }


</style>
<div class="row">
<div class = 'col-sm-10'>
    <div class='col-sm-3'>
        <form class="form-horizontal" id = 'departmentSelectForm' action="" method='post'>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="selected_dept">Department: </label>
                <div class="col-sm-8">
                    <select name="selected_dept" class="selectpicker" id='selected_dept' data-width="100%" data-live-search="true">
                        <?php
                            foreach ($rosterDepartments  as $dept_code) {
                                if( $dept_code == $selectedDeptCode){
                                    echo "<option value='".$dept_code."' selected='selected' >".$departments[$dept_code]."</option>";
                                } else {
                                    echo "<option value='".$dept_code."'>",$departments[$dept_code],"</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <form action="<?php echo base_url()?>roster/rosterSet/<?php echo $selectedDeptCode;?>" method="post">
        <div class='col-sm-3'>
            <div class='form-group'>
                <label class="col-sm-2 control-label" for="sdate">From:</label>
                <div class="col-sm-8">
                    <input type='text' id='sdate' name='sdate' value='<?php echo $sdate; ?>' readonly class="form-control">
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div  class='form-group'>
                <label class="col-sm-2 control-label" for="edate">To:</label>
                <div class="col-sm-8">
                    <input type='text' id='edate' name='edate' value='<?php echo $edate; ?>' readonly class="form-control">
                </div>
            </div>
        </div>
        <div class='col-sm-3'>
            <div  class='form-group'>
                <div class="col-sm-2">
                    <input type='submit' id="show" name='search' value='Show' class="btn btn-primary">
                </div>
            </div>

        </div>
    </form>
</div>

<div class="col-sm-2">
    <button href='#rosterSlotModal' id="openBtn" data-toggle="modal" class="btn btn-primary pull-right">Add Roster Slot</button>
</div>
</div>
<!--
<div class="col-sm-8">
    <button  href='#rosterSlotModal' id="openBtn" data-toggle="modal" class="btn btn-primary pull-left">Add Roster Slot</button>
</div> -->
<div class="nav nav-bar" data-spy="affix" data-offset-top="50">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                    $empListStr = "";
                    $empListStrWeekend = "";
                    foreach($staffArray as $emp_id=>$name){
                        $empListStr .= "<div draggable='true' class='x' id='d_".$emp_id."'><b>$name</b><a class='minus minus_css glyphicon glyphicon-trash' data-eid='".$emp_id."'></a></div> ";
                        $empListStrWeekend .= "<div class='weekendEmpList' id='".$emp_id."'><b>$name</b></div> ";
                    }
                    echo $empListStr;
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$slot_count = count($rosterSlot);  $colspan = $slot_count + 3; ?>
<div class="row">
    <div class="panel panel-default">
        <table class="table table-bordered" id="time_grid">
            <thead>
            <tr>
                <th>Day Name</th>
                <?php
                    $data_ary = array();
                    $schedule_grid = "";
                    $i = 0;
                    $slot_ary = array();
                    foreach ($rosterSlot as $key=>$value){
                        $from = date("h:i a",strtotime($value["from"]));
                        $to =  date("h:i a",strtotime($value["to"]));
                        $time_slot = $from." ".$to;
                        $schedule_grid .= "<th><b>".$from."</b> to <b>".$to."</b></th>";
                        $slot_ary[] = $time_slot;
                        $data_ary[$i] = "data-from='$from' data-to='$to'";
                        $i++;
                    }
                    echo $schedule_grid;
                ?>
                <th>Govt. Holiday</th>
                <th>Weekend</th>
            </tr>
            </thead>

            <tbody>
                <?php
                    $weekdays = array( "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" );
                    for ($idate=$sdate;$idate<=$edate;){
                        $dayNo = date("w",strtotime($idate));
                        /*$date_ary = explode("-",$idate);
                        $month = $date_ary[1];
                        $day = $date_ary[2];*/

                        $dayName = $weekdays[$dayNo];

                        if(isset($rosterData[$idate])) {
                            echo "<tr>";
                            echo "<td><b>$dayName</b><br>$idate</td>";
                            $slot_count = count($slot_ary);
                            for ($j = 0; $j < $slot_count; $j++) {
                                echo "<td class='drop-box' id = 'time_cell' $data_ary[$j] data-date='$idate'>";
                                if(isset($rosterData[$idate][$slot_ary[$j]])){
                                    $empData = $rosterData[$idate][$slot_ary[$j]];
                                    foreach($empData as $key=>$empObj){
                                        echo "<div draggable='true' class='x' id='d_".$empObj->emp_id."'><b>$empObj->name</b><a class='minus minus_css_1 glyphicon glyphicon-trash' data-eid='".$empObj->emp_id."'></a></div> ";
                                    }
                                }
                                echo "</td>";
                            }

                            echo "<td class='drop-box' id = 'govt_holiday' data-date='$idate'>";
                            if(isset($rosterData[$idate]['G'])) {
                                $empGovHoliday = $rosterData[$idate]['G'];
                                foreach ($empGovHoliday as $key=>$empObj){
                                    echo "<div draggable='true' class='x' id='d_".$empObj->emp_id."'><b>$empObj->name</b><a class='minus minus_css_1 glyphicon glyphicon-trash' data-eid='".$empObj->emp_id."'></a></div> ";
                                }
                            }
                            echo "</td>";

                            echo "<td id = 'weekendEmployee' data-date='$idate'>";
                            if(isset($rosterData[$idate]['W'])) {
                                $weekends = $rosterData[$idate]['W'];
                                foreach ($weekends as $key=>$empObj){
                                    echo "<div class='weekendEmpList' id='".$empObj->emp_id."'><b>$empObj->name</b></div>";
                                }
                            }
                            echo "</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr>";
                            echo "<td><b>$dayName</b><br>$idate</td>";
                            for ($j = 0; $j < $slot_count; $j++) {
                                echo "<td class='drop-box' id = 'time_cell' $data_ary[$j] data-date='$idate'></td>";
                            }
                            echo "<td class='drop-box' id = 'govt_holiday' data-date='$idate'></td>";
                            echo "<td id = 'weekendEmployee' data-date='$idate'>$empListStrWeekend</td>";
                            echo "</tr>";
                        }
                        $idate = date("Y-m-d",strtotime($idate." +1 day"));
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class='clearfix'></div>

<div class="modal fade" id="addFacilityModal" tabindex="-1"
     role="dialog" aria-labelledby="addFacilityModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="facilityForm" class="form-horizontal" role="form"
                  method="post" action="#">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Assigning Time Slot to Employee</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <a id="btn_save_facility" type="submit" class="btn btn-primary">Save</a>
                    <button id="btn_add_close" type="button" class="btn btn-default"
                            data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--
<div class="modal fade" id="rosterSlotModalNew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">x</button>
                <h3 class="modal-title">Roster Slot List</h3>
            </div>
            <div class="modal-body">
                <h5 class="text-center">Department: <?php echo $departments[$selectedDeptCode]; ?></h5>
                <form id='rosterSlotForm' name='rosterSlotForm' class=''
                      method="post" action="">
                    <table class="table table-bordered table-condensed"
                           id="rosterTable">

                    </table>
                </form>
            </div>
            <div class="modal-footer">

                <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
            </div>

        </div>
    </div>
</div> -->

<div class="modal fade" id="rosterSlotModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">x</button>
                <h3 class="modal-title">Roster Slot List</h3>
            </div>


            <div class="modal-body">
                <h5 class="text-center">Department: <?php echo $departments[$selectedDeptCode]; ?></h5>
                <form id='rosterSlotForm' name='rosterSlotForm' class=''
                      method="post" action="">
                    <table class="table table-bordered table-condensed"
                           id="rosterTable">

                        <thead id="tblHead">
                        <tr>
                            <th>Slot No.</th>
                            <th>From</th>
                            <th>To</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($rosterSlot as $ary){
                            echo "<tr><td>";
                            echo "<div class ='tableData'>$ary[slot_no]</div>";
                            //hidden
                            echo "<div class='tableEdit hidden'><select class='slotNo selectpicker form-control btn-sm' name='slotNo' data-width='100%'>";
                            for($i=1; $i<=10; $i++) {
                                if($i == $ary['slot_no']) echo "<option value='".$i."' selected >".$i."</option>";
                                else echo "<option value='".$i."' >".$i."</option>";

                            }
                            echo "</select></div>";

                            echo "</td><td>";
                            echo "<div class='tableData'>$ary[from]</div>";
                            echo "<div class='tableEdit hidden bootstrap-timepicker'>                   			     	
                                	<input type='text' class='rosterSlotFrom form-control' name='rosterSlotFrom' value='$ary[from]' placeholder='hh:mm:ss'>
                                </div>";

                            echo "</td><td>";
                            echo "<div class='tableData'>$ary[to]</div>";
                            echo "<div class='tableEdit hidden bootstrap-timepicker'>                   			     	
                                	<input type='text' class='rosterSlotTo form-control' name='rosterSlotTo' value ='$ary[to]' placeholder='hh:mm:ss'>
                                </div>";

                            echo "</td><td class='text-center'>";
                            echo "<div class='tableData'><a class='rosterSlotEdit btn btn-warning btn-xs' data-id='".$ary['id']."' >Edit</a> | <a class='rosterSlotDelete btn btn-danger btn-xs' data-id='".$ary['id']."' >Delete</a></div>";
                            echo "<div class='tableEdit hidden'><input class='updateRosterSlot btn btn-primary btn-xs' data-id='$ary[id]' value='Update'></div>";

                            echo "</td></tr>";
                        } ?>

                        <tr id='rosterRow'>

                            <td><select class='selectpicker form-control btn-sm'
                                        name='slotNo' id='slotNo' data-width='100%'>
                                    <?php for($i=1; $i<=10; $i++) {
                                        echo "<option value='".$i."' >".$i."</option>";
                                    } ?>
                                </select></td>
                            <td><div class='bootstrap-timepicker'>
                                    <input type='text' class='form-control' id='rosterSlotFrom'
                                           name='rosterSlotFrom' placeholder='hh:mm:ss'>
                                </div></td>
                            <td><div class='bootstrap-timepicker'>
                                    <input type='text' class='form-control' id='rosterSlotTo'
                                           name='rosterSlotTo' placeholder='hh:mm:ss'>
                                </div></td>
                            <td class="text-center"><input id='addRosterSlot'
                                                           class='btn btn-primary btn-sm' value='Add' type='Submit'></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script src="<?php echo base_url()?>assets/lib/tipsy/jquery.tipsy.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/lib/bootstrap/js/bootstrap-select.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>


<script type="text/javascript">
    $(document).ready(function(){
        var base_url = "<?php echo base_url();?>";
        var dept_code = "<?php echo $selectedDeptCode; ?>";

        $('.x').bind('dragstart', function(e) {
            var id = $(this).attr('id');
            e.originalEvent.dataTransfer.effectAllowed = 'copy';
            e.originalEvent.dataTransfer.setData('Text', '#'+id);
        });

        $('.drop-box').bind('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var select_data_id = $(e.originalEvent.dataTransfer.getData('Text')).clone().attr('id');
            if(select_data_id != $(this).find('#'+select_data_id).attr('id')) {
                var employeeData = $(e.originalEvent.dataTransfer.getData('Text')).clone();
                var col = ($(this).parent().children('td:last').index() - 1);
                var govtHoliday = $(this).parent().children('td:eq('+col+')');
                var countEmployeeInTimeSlotOne = 0;

                govtHoliday.find('div').each(function () {
                    var empId = $(this).attr('id');
                    if(empId == select_data_id){
                        countEmployeeInTimeSlotOne++;
                    }
                });

                var is_holiday = "";
                var cell_id = $(this).attr('id');
                var parentRowCell = $(this).parent('tr').children('td');
                var countEmployeeInTimeSlotTwo = 0;

                if(cell_id == 'govt_holiday') {
                    is_holiday = "G";
                    parentRowCell.find('div').each(function () {
                        var current_cell_id = $(this).parent('td').attr('id');
                        if(current_cell_id != 'weekendEmployee' && current_cell_id != 'govt_holiday') {
                            var empId = $(this).attr('id');
                            if(select_data_id == empId){
                                countEmployeeInTimeSlotTwo++;
                            }
                        }
                    });
                }

                if(countEmployeeInTimeSlotTwo != 0 || countEmployeeInTimeSlotOne != 0){
                    $(this).append("");
                } else {
                    $(this).append(employeeData);
                }

                var emp_id = $(this).children('div:last').attr('id').split("_")[1];
                var cell = $(this).parent('tr').children('td:last');
                var ddate = $(this).data('date');
                var from = $(this).data('from');
                var to = $(this).data('to');

                $(".table tr td div a").css('display','block');
                $(".table tr td div").css('display','block');
                $(".table tr td div").css('cursor','pointer');
                removeEmployee();

                var col = $(this).parent().children('td:last').index();
                var currentRowWeekendCell = $(this).parent().children().eq(col);

                var emp_id = select_data_id.split("_")[1];
                $(currentRowWeekendCell).find('div').each(function () {
                    var t_emp_id = $(this).attr("id");
                    if (emp_id == t_emp_id) $(this).remove();
                });

                var employeeSchedule = "[{\"emp_id\":\"" + emp_id + "\",\"ddate\":\"" + ddate + "\",\"start_time\":\"" + from + "\",\"end_time\":\"" + to + "\",\"entry_time\":\"\",\"out_time\":\"\",\"dept_code\":\""+dept_code+"\",\"comment\":\"\",\"is_holiday\":\""+is_holiday+"\"}]";

                var employeeWeekendList = "[";
                cell.find('div').each(function () {
                    emp_id = $(this).attr('id');
                    from = "";
                    to = "";
                    is_holiday = "W";
                    if (employeeWeekendList != "" && employeeWeekendList != "[") employeeWeekendList += ",";
                    employeeWeekendList += "{\"emp_id\":\"" + emp_id + "\",\"ddate\":\"" + ddate + "\",\"start_time\":\"" + from + "\",\"end_time\":\"" + to + "\",\"entry_time\":\"\",\"out_time\":\"\",\"dept_code\":\"" + dept_code + "\",\"comment\":\"\",\"is_holiday\":\"" + is_holiday + "\"}";
                });

                employeeWeekendList += "]";
                if(!(countEmployeeInTimeSlotTwo != 0 || countEmployeeInTimeSlotOne != 0)) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url() . 'roster/setData'; ?>",
                        dataType: 'json',
                        data: {
                            employeeSchedule: employeeSchedule,
                            employeeWeekendList: employeeWeekendList,
                            ddate: ddate,
                            is_govt_holiday: is_holiday,
                            dept_code:dept_code
                        },
                        success: function (resp) {
                            alert(resp);
                            console.log(resp);
                            if(resp.status == true) {
                                $.ajax({
                                    type:'POST',
                                    url:'<?php echo base_url() . 'roster/adjustRosterAttendanceWithChangedEmployeeTimeSlotForPreviousDate';?>',
                                    data:{ ddate:ddate,dept_code:dept_code },
                                    success:function () { }
                                });
                            }
                        }
                    });
                }
            }
            return false;
        }).bind('dragover', false);

        function removeEmployee(){  
            $(".minus").unbind('click').bind('click',function () {
                if(confirm('Are you sure to delete this item!!!')) {
                    var cell = $(this).parent('div').parent('td').parent('tr').children('td:last').index();

                    var emp_id = $(this).parent('div').attr('id').split("_")[1];
                    var ddate = $(this).parent('div').parent('td').data('date');
                    var start_time = $(this).parent('div').parent('td').data('from');
                    var end_time = $(this).parent('div').parent('td').data('to');
                    var hStatus = "";

                    var flag_count = 0;
                    var limit = cell - 2;
                    var td_identifier = $(this).parent('div').parent('td').attr('id');

                    if(td_identifier == 'govt_holiday') hStatus = 'G';

                    $(this).parent('div').parent('td').parent('tr').children().each(function () {
                        if ($(this).index() > 0 && $(this).index() <= limit) {
                            $(this).find('div').each(function () {
                                var eid = $(this).attr('id').split("_")[1];
                                if (eid == emp_id) {
                                    flag_count++;
                                }
                            });
                        }
                    });

                    if (flag_count <= 1) {
                        var newHtml = "<div class='weekendEmpList' id='" + emp_id + "'><b>" + $(this).parent('div').find('b').html() + "</b></div>";
                        var oldHtml = $(this).parent('div').parent('td').parent('tr').children().eq(cell).html();
                        $(this).parent('div').parent('td').parent('tr').children().eq(cell).html(newHtml + oldHtml);
                    }

                    $.ajax({
                        type:"POST",
                        url:"<?php echo base_url().'roster/deleteData'; ?>",
                        dataType:'json',
                        data:{emp_id:emp_id,ddate:ddate,start_time:start_time,end_time:end_time,is_holiday:hStatus},
                        success:function(resp){

                        }
                    });
                    $(this).parent('div').remove();
                }
            });
        }
        removeEmployee();


        /*
        $("#save").on('click',function () {
            var employeeScheduleList = "";
            var employeeGovtHolidayList = "";
            var employeeWeekendList = "";
            $("#time_grid tr").each(function(){
               $(this).find('td').each(function () {
                   var cell_id = $(this).attr('id');
                   var ddate = $(this).data('date');

                   if(cell_id != undefined){
                       $(this).find('div').each(function(){
                           var emp_id = $(this).attr('id').split("_")[1];
                           if(cell_id == 'govt_holiday'){
                               if(employeeGovtHolidayList != ""){
                                   employeeGovtHolidayList +=",";
                               } else {
                                   employeeGovtHolidayList = "[";
                               }
                               employeeGovtHolidayList += "{\"emp_id\":\"" + emp_id + "\",\"ddate\":\""+ddate+"\",\"dept_code\":\""+dept_code+"\",\"is_holiday\":\"G\"}";
                           } else if(cell_id == 'weekendEmployee'){
                               var emp_id = $(this).attr('id');
                               if(employeeWeekendList != ""){
                                   employeeWeekendList +=",";
                               } else {
                                   employeeWeekendList = "[";
                               }
                               employeeWeekendList += "{\"emp_id\":\"" + emp_id + "\",\"ddate\":\""+ddate+"\",\"dept_code\":\""+dept_code+"\",\"is_holiday\":\"W\"}";
                           } else {
                               var from = $(this).parent('td').data('from');
                               var to = $(this).parent('td').data('to');
                               if (employeeScheduleList != '') {
                                   employeeScheduleList += ",";
                               } else {
                                   employeeScheduleList = "[";
                               }
                               employeeScheduleList += "{\"emp_id\":\"" + emp_id + "\",\"start_time\":\"" + from + "\",\"end_time\":\"" + to + "\",\"ddate\":\""+ddate+"\",\"dept_code\":\""+dept_code+"\",\"is_holiday\":\"\"}";
                           }
                       });
                   }
               });
            });
            if(employeeScheduleList != "")  employeeScheduleList += "]";
            if(employeeGovtHolidayList != "") employeeGovtHolidayList += "]";
            if(employeeWeekendList != "") employeeWeekendList += "]";
            alert(employeeScheduleList);
            alert(employeeGovtHolidayList);
            alert(employeeWeekendList);
            $.ajax({
                type:"POST",
                url:"<?php echo base_url().'roster/setData'; ?>",
                dataType:'json',
                data:{slots:employeeScheduleList,govtHolidyList:employeeGovtHolidayList,weekendList:employeeWeekendList},
                success:function(resp){

                }
            })
        }); */

////$(this).parent('div').hide('explode', {"pieces": 50}, 900);
        /*
         var row = $(this).parent().parent().children().index($(this).parent());
         var col = $(this).parent().children().index($(this));
         //var t_row = $(this).parent();
         alert(t_row:eq(5));
         alert(select_data_id.split("_")[1]);
         $( "td:eq( 2 )" ).css( "color", "red" );
        */
        /*
        var mousedown = false;
        $(document).on('mousedown',function () {
            mousedown = true;
           //alert('test');
        });

        /*
        $(".drop-box").on('mouseover',function () {
            if(mousedown) alert('ddd');

            mousedown = false;
            /*
            if(drapOn){
                $(this).animate({backgroundColor:'#1abc9c'},550,function () {
                    $(this).animate({backgroundColor:'white'},350);
                });
            }
            drapOn = false;

        }); */

        $('#sdate, #edate').datepicker({
            format: 'yyyy-mm-dd',
            ignoreReadonly: true
        });

        $('#show').on('click', function(){
            var sdate = $("#sdate").val();
            var edate = $("#edate").val();
            var oneDay = 24*60*60*1000;
            var firstDate = new Date(sdate);
            var secondDate = new Date(edate);

            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
            //alert(sdate+" "+edate);
            //alert(diffDays);
            if(diffDays > 14){
                alert('Please select date range within 15 days');
                $(this).val(edate);
                e.preventDefault();
            }
        });

        $("#selected_dept").on('change',function () {
            var dept_code = $("#selected_dept").val();
            location.href = '<?php echo base_url(); ?>roster/rosterSet/'+dept_code;
        });




        $('#rosterSlotFrom, #rosterSlotTo, .rosterSlotFrom, .rosterSlotTo').timepicker({
            defaultTime: false,
            showMeridian: false,
            minuteStep: 5,
            showSeconds: true,
            disableFocus: true,
            modalBackdrop: true,
            template: 'dropdown'
        });

        $("#rosterSlotForm").validate({
            rules: {
                slotNo: {
                    required: true,
                },
                rosterSlotFrom: "required time",
                rosterSlotTo: "required time",
            },
            submitHandler : function(event) {
                var rosterSlotModal = $('#rosterSlotModal');
                var slotNo = rosterSlotModal.find('#slotNo').val();
                var from = rosterSlotModal.find('#rosterSlotFrom').val();
                var to = rosterSlotModal.find('#rosterSlotTo').val();

                  $.ajax({
                    type:"POST",
                    url:"<?php echo base_url()?>roster/add_roster_slot/<?php echo $selectedDeptCode; ?>",
                    data: {slotNo:slotNo,rosterSlotFrom:from,rosterSlotTo:to},
                    dataType:"json",
                    success:function(response) {
                        if(response.status) {

                            var part1 = "<tr><td>\
                          		                    <div class ='tableData'>"+slotNo+"</div><div class='tableEdit hidden'>\
                          	                            <select class='slotNo selectpicker form-control btn-sm' name='slotNo' data-width='100%'>";

                            var option ="";
                            for(var i=1; i<=10; i++) {
                                if(i == slotNo)	option += "<option value='"+i+"' selected >"+i+"</option>";
                                else option += "<option value='"+i+"'>"+i+"</option>";
                            }

                            var part2 = "</select></div></td>\
                                    		<td>\
                      			                <div class='tableData'>"+from+"</div>\
                                    		    <div class='tableEdit hidden bootstrap-timepicker'>\
                                    		        <input type='text' class='rosterSlotFrom form-control' name='rosterSlotFrom' value='"+from+"' placeholder='hh:mm:ss'>\
                                    		    </div>\
                                    		</td>\
                                    		<td>\
                                        		<div class='tableData'>"+to+"</div>\
                                        		<div class='tableEdit hidden bootstrap-timepicker'>\
                                        			<input type='text' class='rosterSlotTo form-control' name='rosterSlotTo' value ='"+to+"' placeholder='hh:mm:ss'>\
                                        		</div>\
                                    		</td>\
                                    		<td class='text-center'>\
                                    		<div class='tableData'>\
                                        		<a class='rosterSlotEdit btn btn-warning btn-xs' data-id='"+response.insert_id+"' >Edit</a> | <a class='rosterSlotDelete btn btn-danger btn-xs' data-id='"+response.insert_id+"' >Delete</a></div>\
                                        		<div class='tableEdit hidden'><input type class='updateRosterSlot btn btn-primary btn-xs' data-id='"+response.insert_id+"' value='Update'></div>\
                                    		</td>\
                                    		</tr>";
                            var myRow = part1 + option + part2;

                            $("#rosterRow").before(myRow);
                            $('.slotNo').selectpicker('refresh');
                            rosterSlotModal.find('#rosterSlotFrom').val("");
                            rosterSlotModal.find('#rosterSlotTo').val("");
                            //rosterSlotModal.find('#SlotNo option:selected').prop("selected", false);


                            $('.rosterSlotDelete').unbind("click").bind("click",function(){
                                bindDeleteEvent(this);
                            });

                            $('.updateRosterSlot').unbind("click").bind("click",function(){
                                bindUpdateEvent(this);
                            });

                            $('.rosterSlotFrom, .rosterSlotTo').timepicker({
                                defaultTime: false,
                                showMeridian: false,
                                minuteStep: 5,
                                showSeconds: true,
                                disableFocus: true,
                                modalBackdrop: true,
                                template: 'dropdown'
                            });

                        } else {
                            alert(response.msg);
                            return;
                        }
                    }
                });
            }
        });

        $('.rosterSlotDelete').unbind("click").bind("click",function(){
            bindDeleteEvent(this);
        });

        var parentRow;
        $("#rosterSlotModal").on("click",'.rosterSlotEdit',function(){
            parentRow = $(this).parent().parent().parent();

            parentRow.find('.tableData').addClass('hidden');
            parentRow.find('.tableEdit').removeClass('hidden');

            return;
        });

        $('#rosterSlotModal').on('hidden.bs.modal', function () {
            $(this).find('.tableData').removeClass('hidden');
            $(this).find('.tableEdit').addClass('hidden');

            $(this).find('#rosterSlotFrom').val("");
            $(this).find('#rosterSlotTo').val("");
            location.href = '<?php echo base_url(); ?>roster/rosterSet/'+dept_code;
        });

        $('.updateRosterSlot').unbind("click").bind("click",function(){
            bindUpdateEvent(this);
        });

        function bindUpdateEvent(it){

            parentRow = $(it).parent().parent().parent();
            var rosterId = $(it).attr('data-id');
            var slotNo = parentRow.find('.slotNo').val();
            var from = parentRow.find('.rosterSlotFrom').val();
            var to = parentRow.find('.rosterSlotTo').val();

            $.ajax({
                type:"POST",
                url:"<?php echo base_url()?>roster/update_roster_slot/<?php echo $selectedDeptCode; ?>",
                data:{
                    rosterId:rosterId,
                    slotNo:slotNo,
                    rosterSlotFrom:from,
                    rosterSlotTo:to},
                dataType:"json",
                success:function(response) {
                    if(response.status) {
                        parentRow.find("td:nth-child(1) .tableData").text(slotNo);
                        parentRow.find("td:nth-child(2) .tableData").text(from);
                        parentRow.find("td:nth-child(3) .tableData").text(to);

                        $('#rosterSlotModal').find('.tableData').removeClass('hidden');
                        $('#rosterSlotModal').find('.tableEdit').addClass('hidden');
                    } else {
                        alert(response.msg);
                        return;
                    }
                }
            });
        }

        function bindDeleteEvent(it){
            var rosterId = $(it).attr('data-id');

            $.ajax({
                type:"POST",
                url:"<?php echo base_url()?>roster/del_roster_slot/"+rosterId,
                data:{},
                dataType:"json",
                success:function(response) {
                    if(response.status) {
                        $(it).parents('tr').remove();
                    } else {
                        alert(response.msg);
                        return;
                    }
                }
            });
        }

    });


</script>