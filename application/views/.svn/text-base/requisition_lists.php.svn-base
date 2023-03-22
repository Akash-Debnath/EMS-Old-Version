<?php
$isMyself = (empty($rid) || $rid == $myInfo->userId);

?>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">REQUISITION LIST</h3>
        <a href='<?php echo base_url()?>requisition/form' class="btn btn-info pull-right btn_add_right">New Requisition Form</a>
    </div>
    <div class="box-body">
        <div class='table-responsive'>
            <table class="col-xs-12 table-bordered table-striped table-condensed">
        		<thead>
        			<tr>
        				<th class='hidden-xs' >Sn.</th>
        				<th>Date</th>
        				<?php if(!$isMyself) echo "<th>Requested By</th>"?>
        				<th>Approved By</th>
        				<th>Verified By</th>
        				<th>Action</th>
        			</tr>
        		</thead>
        		<tbody id='tBody'>
        		<?php
        		$i = 0;
        		foreach ($requisitions as $obj){
        		    echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
        		    echo ++$i;
        		    echo "</td><td data-title='Date'>";
        		    echo $obj->date;
        		    if(!$isMyself){
        		        echo "</td><td data-title='Requested By'>";
        		        echo $obj->requested_by;
        		    }
        		    
                    echo "</td><td data-title='Approved By'>";
    		        if(empty($obj->approver_name))
    		            echo "<span class='text-red'>pending</span>";
    		        else
    		            echo $obj->approver_name;
    		        
    		        echo "</td><td data-title='Verified By'>";
    		        if(empty($obj->verifier_name))
    		            echo "<span class='text-red'>pending</span>";
    		        else
    		            echo $obj->verifier_name;
    		        
        		    echo "</td><td data-title='Action'>";
        		    echo "<a href='".base_url()."requisition/form/".$obj->voucher_id."' class='holidayEdit' >View</a> </td>";
        		}?>

        		</tbody>
    	    </table>
        </div>
    </div>
</div>

