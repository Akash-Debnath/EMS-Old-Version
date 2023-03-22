
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">PURCHASE REQUESTED FORM</h3>
    </div>
    <div class="box-body">
        <div class='table-responsive'>
            <table class="col-xs-12 table-bordered table-striped table-condensed">
        		<thead>
        			<tr>
        				<th class='hidden-xs' >Sn.</th>
        				<th>Item</th>
        				<th>Quantity</th>
        				<th>Unit Price</th>
        				<th>Total Price</th>
        			</tr>
        		</thead>
        		<tbody >
        		<?php
        		$i = 0; 
        		foreach ($ledgers as $obj){
        		    echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
        		    echo ++$i;
        		    echo "</td><td data-title='Item'>";
        		    echo $obj->item;
        		    echo "</td><td data-title='Quantity'>";
        		    echo $obj->quantity;
        		    echo "</td><td data-title='Unit Price'>";
        		    echo $obj->unit_price;
        		    echo "</td><td data-title='Total Price'>";
        		    echo $obj->total_price;
        		    echo "</td></tr>";
        		}
        		echo "<tr><td class='hidden-xs'></td><td></td><td></td><td></td><td><b>$total</b></td>";
        		?>
        		</tbody>
    	    </table>
        </div>
    </div>
    <div class="box-footer">
    <?php
        if( $controller->purchase_approver_access ){
            
            if(empty($voucher['approved_by'])){
                
                echo "<button id='approvePurchase' class='btn btn-primary'> Approve</button>";
                echo "<button id='refuseApprove' class='btn btn-danger'> Refuse</button>";
                
            } else{
                
                echo "<button class='btn btn-primary' disabled> Approved</button>";
            }
                
            
        } else if( $controller->purchase_verifier_access  && !empty($voucher['approved_by'])){
            
            if(empty($voucher['verified_by'])){
                
                echo "<button id='verifyPurchase' class='btn btn-primary'> Verify</button>";
                echo "<button id='refuseVerify' class='btn btn-danger'> Refuse</button>";
                
            } else{
                echo "<button class='btn btn-primary' disabled> Verified</button>";
            }
                
            
        }    
    ?>
		
	</div>
</div>


<!--Confirm Modal Dialog -->
<div class="modal fade" id="confirmModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		    <input type="hidden" id='row_id' value=''>
		    
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>text text text</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
	
	
<!-- Refuse Modal Dialog -->
<div class="modal fade" id="refuseModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Give an excuse to
							refuse this Purchase request:</label>
						<textarea class="form-control" name="excuseText" id="excuseText"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="refuseCancel">Cancel</button>
				<button type="button" class="btn btn-danger" id="refuseOk">Refuse</button>
			</div>
		</div>
	</div>
</div>

<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />

<script type="text/javascript">
var vid = "<?php echo $voucher['voucher_id']?>";

$(document).ready(function(){

	$.btnFlag = false;

    function bindRefuseModal (url, avBtn, refuseBtn){
        
        $('#refuseModal').find('.modal-footer #refuseOk').off('click').on('click', function () {
    		var excuse = $('#excuseText').val();
    				
    		if(excuse.length <= 6){
    			alert('please give him convincing excuse.');
    			return;
    		}
    		
    		$.btnFlag = true;
    		$('#refuseModal').modal('hide');
    		$("body").append("<div class='loader'>Please wait&hellip;</div>");
            
    		$.ajax({
    			type:"POST",
    			url:url,
    			data: {excuse:excuse},
    			dataType:"json",
    			success:function(response) {
        			
    				$(".loader").remove();
        			$('#confirmModal').find('div.modal-body p').text(response.msg);
        			$('#confirmModal').modal('show');
        			
     			    if(response.status){
     	 			    
     			    	refuseBtn.html('Refused'); 
     			    	$('#confirmModal').on('hidden.bs.modal', function(){
     			    		location.href = "<?php echo base_url();?>requisition/purchase/";
     	 			    });
     	 			    
     	 			}else{
     	 				$.btnFlag = false;
     	 				avBtn.prop('disabled', false);
     	 				refuseBtn.prop('disabled', false);
     	 	 		}	
    			}
    		});

    	});

        $('#refuseModal').on('hidden.bs.modal', function () {
            
            avBtn.prop('disabled', $.btnFlag);
            refuseBtn.prop('disabled', $.btnFlag);
            
        	$(this).find('#excuseText').val("");
        });
    }
	
    $('#approvePurchase').on('click', function(){

        var aButton = $(this); 
               
        aButton.prop('disabled', true);
        $('#refuseApprove').prop('disabled', true);
        
        $("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$.ajax({
    		type: 'POST',
    		url: '<?php echo base_url()?>requisition/approve_purchase/'+vid,
    		data:{},
    		dataType:'json',
    		success: function(response){
        		
    			$(".loader").remove();
    			$('#confirmModal').find('div.modal-body p').text(response.msg);
    			$('#confirmModal').modal('show');
 			    if(response.status){
 			    	aButton.html('Approved'); 
 			    	$('#confirmModal').on('hidden.bs.modal', function(){
 			    		location.href = "<?php echo base_url();?>requisition/purchase/";
 	 			    });
 	 			    	 			    
 	 			}else{
 	 				aButton.prop('disabled', false);
 	 		        $('#refuseApprove').prop('disabled', false);
 	 	 		}	
        	}
    	});	
    });

    $('#refuseApprove').on('click', function(){
        
    	var avBtn =  $('#approvePurchase');
        var refuseBtn = $(this);
        
        var url = '<?php echo base_url()?>requisition/refuse_approve/'+vid;    

        $.btnFlag = true;
        
        avBtn.prop('disabled', true);
        refuseBtn.prop('disabled', true);
        
        $('#refuseModal').modal('show');

        bindRefuseModal (url, avBtn, refuseBtn);
    });
    

    $('#verifyPurchase').on('click', function(){

        var vButton = $(this);
                
        vButton.prop('disabled', true);
        $('#refuseVerify').prop('disabled', true);
        
        $("body").append("<div class='loader'>Please wait&hellip;</div>");
        
    	$.ajax({
    		type: 'POST',
    		url: '<?php echo base_url()?>requisition/verify_purchase/'+vid,
    		data:{},
    		dataType:'json',
    		success: function(response){
        		
        		$('.loader').remove();
    			$('#confirmModal').find('div.modal-body p').text(response.msg);
    			$('#confirmModal').modal('show');
    			
 			    if(response.status){
 			    	aButton.html('Verified'); 	

 			    	$('#confirmModal').on('hidden.bs.modal', function(){

 			    		location.href = "<?php echo base_url();?>requisition/purchase/";
 	 			    });			    	 
 	 			}else{
 	 	 			
 	 				vButton.prop('disabled', false);
 	 		        $('#refuseVerify').prop('disabled', false);
 	 	 		}	
        	}
    	});	
    });

    $('#refuseVerify').on('click', function(){
        
        var avBtn = $('#verifyPurchase');
        var refuseBtn = $(this);    
        var url = '<?php echo base_url()?>requisition/refuse_verify/'+vid;

        avBtn.prop('disabled', true); 
        refuseBtn.prop('disabled', true);
        
        $('#refuseModal').modal('show');
        
        bindRefuseModal (url, avBtn, refuseBtn);
    });
    
    
});
       	
</script>
