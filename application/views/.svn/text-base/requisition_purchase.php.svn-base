
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">VOUCHER LIST</h3>        
        <?php if($uType == "A"){?>      
        <a href='<?php echo base_url()?>requisition/voucher_form'
			id="purchase_item" class="btn btn-primary pull-right btn_add_right">
			<span class="glyphicon glyphicon-plus"></span> A New Voucher
		</a>
        <?php }?>
    </div>
	<div class="box-body">
		<div class='table-responsive'>
			<table class="col-xs-12 table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th class='hidden-xs'>Sn.</th>
						<th>Date</th>
						<th>Requested By</th>
						<th>Total Price</th>
						<th>Approved By</th>
						<th>Verified By</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id='tBody'>
                <?php
                    $i = 0;
                    foreach ($vouchers as $obj) {
                        echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
                        echo ++ $i;
                        echo "</td><td data-title='Date'>";
                        echo $obj->date;
                        echo "</td><td data-title='Requested By'>";
                        echo $obj->requested_by;
                        echo "</td><td data-title='Total Price'>";
                        echo $obj->total;
                        
                        echo "</td><td data-title='Approved By'>";
                        if (empty($obj->approved_by))
                            echo "<span class='text-red'>Pending</span>";
                        else
                            echo $obj->approver_name;
                        
                        echo "</td><td data-title='Verified By'>";
                        if (empty($obj->verified_by))
                            echo "<span class='text-red'>Pending</span>";
                        else
                            echo $obj->verifier_name;
                        
                        echo "</td><td data-title='Action'>";
                        echo "<a href='" . base_url() . "requisition/voucher_form/" . $obj->voucher_id . "' class='holidayEdit' >View</a> </td>";
                    }
                    ?>
            	</tbody>
			</table>
		</div>
	</div>
</div>

