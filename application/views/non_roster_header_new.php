<style>
    <!--
    .margin-bottom{
        margin-bottom: 8px;
    }
    -->
</style>
<link href="<?php echo base_url()?>assets/lib/bootstrap/css/bootstrap-select.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/main.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/roster.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url()?>assets/lib/tipsy/tipsy.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-timepicker.min.css">
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet" />

<form class="form-horizontal" id = 'departmentSelectForm' action="" method='post'>
    <div class = 'row'>
        <div class='col-sm-4'>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="selected_dept">Department: </label>
                <div class="col-sm-8">
                    <select name="selected_dept" class="selectpicker" id='selected_dept' data-width="100%" data-live-search="true">
                        <?php

                        foreach ($rosterDepartments  as $dept_code) {
                            if( $dept_code == $selectedDeptCode){
                                echo "<option value='".$dept_code."' selected='selected' >".$departments[$dept_code]."</option>";
                            }else{
                                echo "<option value='".$dept_code."'>",$departments[$dept_code],"</option>";
                            }
                        } ?>

                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal">
    <div class='row'>
        <div class="col-sm-4 col-sm-12">
            <div class="form-group">
                <label class="col-sm-4 control-label" for="select_dept">Select Staff: </label>
                <div class="col-sm-8">
                    <select name='staff' id='staff' class='selectpicker form-control'
                            data-live-search='true' multiple required>
                        <?php
                        //print_r($rosterStatus);
                        if(count($rosterStatus) > 0) {
                            echo "<option value='all'>All</option>";
                            foreach ($staffArray as $emp_id => $name) {
                                if (in_array($emp_id, $rosterStatus)) {
                                    echo "<option value='$emp_id'>$name</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="dateForm" action="<?php echo base_url()?>roster_new/officeTimeSetup/<?php echo $selectedDeptCode;?>" method="post" class="form-horizontal">
    <div class="row">
        <div class='col-sm-4'>
            <div  class='form-group'>
                <label class="col-sm-4 control-label" for="sdate">From:</label>
                <div class="col-sm-8">
                    <input type='text' id='sdate' name='sdate' value='<?php echo $sdate; ?>' class="form-control">
                </div>
            </div>
        </div>
        <div class='col-sm-4'>
            <div  class='form-group'>
                <label class="col-sm-4 control-label" for="edate">To:</label>
                <div class="col-sm-8">
                    <input type='text' id='edate' name='edate' value='<?php echo $edate; ?>' class="form-control">
                </div>
            </div>
        </div>
        <div class='col-sm-4'>
            <div  class='form-group'>
                <div class="col-sm-9">
                    <input type='submit' name='search' value='Show' class="btn btn-primary">
                </div>
            </div>
        </div>
    </div>
</form>
<div class='clearfix'></div>

<script type="text/javascript">
    $(document).ready(function(){
        var base_url = "<?php echo base_url();?>";
        $('#sdate, #edate').datepicker({
            format: 'yyyy-mm-dd'
        });

        $('#edate').on('changeDate', function(ev){
            $(this).datepicker('hide');
            $('#sdate').datepicker('setEndDate',$(this).val());
            $("#dateForm").submit();
        });

        $('#selected_dept').on('change', function(){
            var selected = $("#selected_dept").find("option:selected").val();
            var action_url = base_url+ "roster_new/officeTimeSetup/"+selected;
            $('#departmentSelectForm').attr("action", action_url);
            $('#departmentSelectForm').submit();
        });
    });


</script>