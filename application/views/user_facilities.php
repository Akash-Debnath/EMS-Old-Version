

<table class="facilityTable table table-condensed tableAction" id="tblGrid">
	<thead id="tblHead">
		<tr>
			<th></th>
			<th>Facility</th>
			<th>From</th>
			<th>To</th>
			<th width="250">Remark</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody> 
	<?php 
    $i=0;
    if (is_array($facilities) || is_object($facilities)){

        foreach ($facilities as $obj) { ?>
        	<tr>
        		<td><?php echo ++$i; ?></td>
        		<td><?php echo $obj->facility; ?></td>
        		<td><?php echo date("Y-m-d", strtotime($obj->from_date)); ?></td>
        		<td><?php echo ($obj->to_date == "0000-00-00 00:00:00")? "Continuing" :date("Y-m-d", strtotime($obj->to_date)); ?></td>
        		<td width="250"><?php echo $obj->remark; ?></td>
        		<td nowrap>
        		<button type="button" class="btn_edit_facility btn btn-warning btn-xs"
        			data-toggle="modal"
        			data-id="<?php echo $obj->id; ?>"
        			data-facility = "<?php echo $obj->facility_id;?>"
        			data-fdate = "<?php echo date("Y-m-d", strtotime($obj->from_date)); ?>"
        			data-tdate = "<?php if($obj->to_date == "0000-00-00 00:00:00") echo ""; else echo date("Y-m-d", strtotime($obj->to_date)); ?>"
        			data-remark = "<?php echo $obj->remark; ?>"         
        			title="Editing this facility" data-target="#addFacilityModal">
        			<span class="glyphicon glyphicon-edit"></span> Edit
        		</button>
        		<button class="btn_delete_facility btn btn-xs btn-danger" type="button"
        			data-toggle="modal" 
        			data-target="#deleteFacilityModal"
        			data-id="<?php echo $obj->id; ?>"
        			data-empid="<?php echo $obj->emp_id; ?>"
        			title="deleting this facility ?">
        			<i class="glyphicon glyphicon-trash"></i> Delete
        		</button>
        		</td>
        	</tr>
    <?php }
    } 
    ?>
    
	</tbody>
</table>
