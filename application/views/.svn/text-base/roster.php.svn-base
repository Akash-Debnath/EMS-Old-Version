<link href="<?php echo base_url();?>assets/css/main.css"
		type="text/css" rel="stylesheet" />
		<link href="<?php echo base_url();?>assets/css/roster.css"
		type="text/css" rel="stylesheet" />
<link
	href="<?php echo base_url()?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
<link href="<?php echo base_url()?>assets/lib/tipsy/tipsy.css"
	type="text/css" rel="stylesheet" />

<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-timepicker.min.css">

<div>Departments: 
<?php 
if(count($rosterDepartments)>1) {
	foreach ($rosterDepartments as $dept_code) {
		if($selectedDeptCode==$dept_code) {
			echo $departments[$dept_code]." | ";
		} else {
			echo "<a href='".base_url()."roster/show/$dept_code'>$departments[$dept_code]</a> | ";
		}
	} 
}
?>
</div>

<div>
	From:<input type='text' name='from' value='<?php echo $sdate; ?>'> To:<input
		type='text' name='to' value='<?php echo $edate; ?>'> <input
		type='submit' name='search' value='Show'>
</div>

<?php if(count($rosterSlot)==0){ ?>
No Slot Found.
<button href="#rosterSlotModal" id="openBtn" data-toggle="modal"
	class="btn btn-default">Add Roster Slot</button>
<?php } else { ?>


<table class='table table-striped table-bordered'>
<?php
echo "<tr>";
foreach ($rosterSlot as $key=>$array) {
	$from = date("h:i a",strtotime($array["from"]));
	$to =  date("h:i a",strtotime($array["to"]));

	echo "<td>";
	echo "<b>".$from."</b> to <b>".$to."</b>";
	if(count($rosterSlot)-1==$key) echo "<button href='#rosterSlotModal' id='openBtn' data-toggle='modal' class='btn btn-default'>Add Roster Slot</button>";
	echo "</td>";
}
?>
</tr>

<?php 

//echo $myInfo->userDeptCode."++++++++++++";
for($idate=$sdate; $idate<=$edate; ) {
	
	$nexDate = date("Y-m-d", strtotime($idate." +1 day"));
	echo "<tr><td colspan='".count($rosterSlot)."'><b>".$idate."</b>(<i>".date("l",strtotime($idate))."</i>) &nbsp;&nbsp;&nbsp;&nbsp; to &nbsp;&nbsp;&nbsp;&nbsp; <b>".$nexDate."</b>(<i>".date("l",strtotime($nexDate))."</i>)</td>";
	
	echo "<tr>";
	foreach ($rosterSlot as $array) {
		$from = date("h:i:s a",strtotime($array["from"]));
		$to =  date("h:i a",strtotime($array["to"]));
		
		echo "<td class='topTips' title='<b>$from</b> to <b>$to</b>'>";
		if(isset($rosterData[$idate][$from])) {
			$staff = $rosterData[$idate][$from];
			foreach ($staff as $obj) {
				//print_r($obj);

				if($isManager && $myInfo->userDeptCode=="SY") {
					//echo $obj->name." X<br>";
					echo "<div>".$obj->name."<span class='staffDel btn btn-warning btn-xs' pkid='".$obj->id."'>X</span></div>";
				} else {
					echo $obj->name."<br>";
				}
				
			}
			
		}
		
		$dtfrom = $idate." ".$array["from"];
		$dtto = strtotime($idate." ".$array["from"]) > strtotime($idate." ".$array["to"]) ? $nexDate." ".$array["to"] : $idate." ".$array["to"];
		
		echo "<select class='staffPicker selectpicker' data-live-search='true' from=\"$dtfrom\"' to=\"$dtto\"'>";
		echo "<option value=''>---Select---</option>";
		foreach ($staffArray as $emp_id=>$name) {
			echo "<option value='$emp_id'>$name</option>";
		}
		echo "</select>";
		
		echo "</td>";
	}
	echo "</tr>";
	
	$idate = date("Y-m-d",strtotime($idate." +1 day"));
}
}
?>

</table>





<div class="bootstrap-timepicker">
		<input id="timepicker4" type="text" value="10:35 AM" class="input-small">
</div>


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
				<form id = 'rosterSlotForm' name='rosterSlotForm' class = '' method="post" action="">
				<table class="table table-bordered table-condensed" id="rosterTable">
				    
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
                        }?> 
                                               
                        <tr id ='rosterRow'>
                        
							<td><select class='selectpicker form-control btn-sm' name='slotNo' id='slotNo' data-width='100%'>
                    			<?php for($i=1; $i<=10; $i++) {                			    
                    			    echo "<option value='".$i."' >".$i."</option>";
                    			} ?>
        	               </select>
        	               </td>
							<td>
                                <div class='bootstrap-timepicker'>                   			     	
                                	<input type='text' class='form-control' id='rosterSlotFrom' name='rosterSlotFrom' placeholder='hh:mm:ss'>
                                </div>
							</td>
							<td>
                                <div class='bootstrap-timepicker'>                   			     	
                                	<input type='text' class='form-control' id='rosterSlotTo' name='rosterSlotTo' placeholder='hh:mm:ss'>
                                </div>
							</td>
							<td class="text-center"><input id='addRosterSlot' class='btn btn-primary btn-sm'
								value='Add' type='Submit'>
							</td>
						
						</tr>
					
					</tbody>
				</table>
				</form>	
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-primary">OK</button>
			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->




<script
	src="<?php echo base_url()?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/lib/tipsy/jquery.tipsy.js"
	type="text/javascript"></script>
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>
<script type="text/javascript">

var staffs = <?php echo json_encode($staffArray); ?>;

$(document).ready(function(e) {
    //$(".tips").tipsy({html: true, gravity: 'e',delayOut:10,clsStyle: 'blue'});
	$(".topTips").tipsy({html: true, gravity:'s', delayOut:10, clsStyle: 'blue'});
	//$(".leftTips").tipsy({html: true, gravity:'e', delayOut:10,clsStyle: 'blue',css: {"max-width": 300+"px"}});

	$(".staffPicker").change(function(){
		var emp_id = $(this).val();
		var from = $(this).attr('from');
		var to = $(this).attr('to');

		it = $(this);
		$.ajax({
			type:"POST",
			url:"<?php echo base_url()?>roster/add",
			data:{emp_id:emp_id,from:from,to:to,dcode:"<?php echo $selectedDeptCode;?>"},
			success:function(pkid) {
				var html = "<div>"+staffs[emp_id]+"<span class='staffDel btn btn-primary btn-xs' pkid='"+pkid+"'>X</span></div>";
				it.before(html);
			}
		});
	});

	$(document).on("click",".staffDel",function(){

		if(confirm("Do you want to delete?")) {
			var pkid = $(this).attr("pkid");
			var it = $(this);
			$.ajax({
				type:"POST",
				url:"<?php echo base_url()?>roster/del/"+pkid,
				success:function(resp) {
					if(resp) {
						it.parent().remove();
					} else {
						alert("Couldn't delete record");
					}
				}
			});
		}
	});

    //Roster Slot Related
	$('#rosterSlotFrom, #rosterSlotTo, .rosterSlotFrom, .rosterSlotTo').timepicker({
		 defaultTime: false,
		 showMeridian: false,
		 minuteStep: 5,
		 showSeconds: true,
		 disableFocus: true,
		 modalBackdrop: true,
		 template: 'dropdown'
	});	

	$.validator.addMethod("time", function(value, element) {  
		return this.optional(element) || /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(value);  
		}, "Please enter a valid time.");
	
    $("#rosterSlotForm").validate({
    		rules: {
    			slotNo: {
    			    required: true,
    			},
    			rosterSlotFrom: "required time",
    			rosterSlotTo: "required time",
    		},
    		submitHandler : function(event) {
        		//var it = $(this);
           		var slotNo = $('#slotNo').val();
           		var from = $('#rosterSlotFrom').val();
           		var to = $('#rosterSlotTo').val();
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
                                        		<div class='tableEdit hidden'><input class='updateRosterSlot btn btn-primary btn-xs' data-id='"+response.insert_id+"' value='Update'></div>\
                                    		</td>\
                                    		</tr>";
                        		var myRow = part1 + option + part2;
                        		
                            	$("#rosterRow").before(myRow);

                          		$('.rosterSlotDelete').unbind("click").bind("click",function(){
                          	   		bindDeleteEvent(this);
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
	})

	$('.updateRosterSlot').unbind("click").bind("click",function(){
		parentRow = $(this).parent().parent().parent();
		var rosterId = $(this).attr('data-id');
   		var slotNo = parentRow.find('.slotNo').val();
   		var from = parentRow.find('.rosterSlotFrom').val();
   		var to = parentRow.find('.rosterSlotTo').val();

   		$.ajax({
   	  	    type:"POST",
   	  	    url:"<?php echo base_url()?>roster/update_roster_slot/<?php echo $selectedDeptCode; ?>",
   	  	    data:{rosterId:rosterId,
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
	});		

});

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
</script>
