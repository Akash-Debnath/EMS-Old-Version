<?php
?>
<link href="<?php echo base_url();?>assets/lib/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<style>
<!--
.ms-container{
	
	width: 700px;
}

.ms-container .ms-list{
	height: 400px !important;
	
	text-align: left;
}

.m-t{
	margin-top: 10px;
}
.ms-optgroup-label {
  background-color: #c6e4e0;
}
-->
</style>

<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <div class="box box-warning">
            <form class="form form-horizontal" action="<?php echo base_url()?>settings/add_prmsn_priv" method="post">
            
            	<div class="box-header with-border">
            	
    	            <div class="col-lg-10 col-lg-offset-1 m-t">
        	            <div class = 'row'>
                            <div class='col-sm-6'>
                            	<div class="form-group">
                                    <label class="col-sm-5 control-label" for="priv_group">Privilege Group</label>
                                    <div class="col-sm-7">
                    					<select name="priv_group" class="selectpicker form-control" id='priv_group'>
                    					    <option value="all"> select ...</option>
                                            <?php foreach ($permissionGroups as $group_id=>$group_name){
                                                echo "<option value='".$group_id."'>".$group_name."</option>";
                                            
                                            }?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class = 'col-sm-6'>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label" for="privType">Privilege Type</label>
                                    <div class="col-sm-8">
                    					<select name="privType" class="selectpicker form-control" id='privType' data-live-search="false" >
                    					<option value=""> select ...</option>
                                            <?php
                                            $objects = $permissionPrivType['all'];
                                            foreach ($objects as $obj){
                                                echo "<option value='".$obj->activity_id."'>".$obj->activity_name."</option>";
                                            
                                            }?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-6 pull-right'>
                            	<div class="form-group">
                                    <label class="col-sm-5 control-label" for="privileger_select">Privileger</label>
                                    <div class="col-sm-7">
                    					<select name="privileger_select" class="selectpicker form-control" id="privileger_select" data-live-search="true">
                    					    <option value=''>Select ...</option>
                    					    
                    					<?php                                                    
                                            foreach ($employees as $dept_name=>$aryOfObj){
                                                echo "<optgroup label='".$dept_name."'>";
                                                
                                                foreach ($aryOfObj as $obj){
                                                    
                                                    echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ". $obj->name."</option>";
                                                }

                                                echo "</optgroup>";
                                        }?>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                	</div>            	
            	</div>
            	<!-- /.box-header -->
            	<div class="box-body">
            	    
            	    <div class="row">
            	        <div class="col-lg-10 col-lg-offset-1">            	            
                            <div align="center">                    
                                <select multiple="multiple" id="staffs_select" name="staffs_select[]">
            					<?php                                                    
                                    foreach ($employees as $dept_name=>$aryOfObj){
                                        echo "<optgroup class='' label='".$dept_name."'>";
                                        
                                        foreach ($aryOfObj as $obj){
                                            
                                            echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ". $obj->name."</option>";
                                        }

                                        echo "</optgroup>";
                                }?>
                                </select>
                            </div>
            	        </div>        	    
            	    </div>                	
                </div>	
            	<!-- /.box-body -->            	
            	<div class="box-footer">
            	    
            	    <div class="form-group text-center">
                        <button class="btn btn-primary " type="submit" id="submitBtn">Submit</button>
                    </div>
            	    
            	</div>
            </form>
            <!-- /.form -->	        	
        </div>
        <!-- /.box -->	
    </div>
</div> 

<script src="<?php echo base_url();?>assets/lib/js/jquery.multi-select.js" type="text/javascript"></script>
<script	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>

<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />
	
<script type="text/javascript">
<!--

var privTypes = <?php echo json_encode($permissionPrivType); ?>;

$(document).ready(function() {

	$('#staffs_select').multiSelect();


    $("#priv_group").change(function(){
    	
        var group_id = $(this).val();
        

        var selectedTypes = privTypes[group_id];

    	if(selectedTypes != undefined){
    		$("#privType").empty();
    		if(group_id == "all"){
    			$("#privType").append("<option value=''> select ...</option>");
    		}	
    		 
        	
    	    for(var i=0; i<selectedTypes.length; i++) {
        	               
    	        var obj = selectedTypes[i];
    	        if(obj!=null) {
    	            var option = "<option value='"+obj.activity_id+"'>"+obj.activity_name+"</option>";
    	            $("#privType").append(option);   	    
    	        }
    	    }    		
    	}	
        
        $('#privType').selectpicker('refresh'); 
    });


	$('form').on('submit', function(e){

		$('#submitBtn').prop( "disabled", true );
		
		e.preventDefault();
		
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		
		var activity_id = $('#privType').val();
		var privileger_id = $("#privileger_select").val();
		var staff_ids = $("#staffs_select").val();

		if(activity_id == "" || privileger_id=="" || (staff_ids.length == 0)){
			
			alert("please select all three option: privilger type, privileger and staffs.");
			$('#submitBtn').prop( "disabled", false );
			$(".loader").remove();
			return;
		}else{

		    $.ajax({
		        type:"POST",
		        url:$('form').attr('action'),
		        data: $('form').serialize(),
		        dataType:"json",
		        success:function(response) {
		        			           
		      		$(".loader").remove();
		      		$('#submitBtn').prop( "disabled", false );
		      		
		      		if(!response.status) {
			      		
		      		    alert(response.msg);
		      		    return;
		      		}else{
		      			alert(response.msg);
		      			//location.href = "<?php echo base_url()."leave/pending"; ?>";
		      		}

		      			
		      	}
		    });
		}	

	});

    $('#privileger_select').change(function (){ 
    	getPrivilegedStaffs();
    });

    $('#privType').change(function (){ 
    	getPrivilegedStaffs();
    });

});

function getPrivilegedStaffs(){
	
    var privileger_id = $("#privileger_select").val();
    var activity_id = $('#privType').val();

    if(privileger_id != "" && activity_id != ""){

        $.ajax({
            type:"post",
            url:"<?php echo base_url()?>settings/getPrivById",
            data: {privileger_id: privileger_id, activity_id: activity_id},
            dataType: "json",
            success: function(response){
                
            	$('#staffs_select').multiSelect('deselect_all');
            	
                if(response.status){
                    var privileged_staffs = response.privileged_staffs;
                    console.log(privileged_staffs);

                    $('#staffs_select').multiSelect('select', privileged_staffs);

                }else{
                    
                    
                }    
            }    
        });
    }
}

//-->
</script>

