
<div class='col-sm-8 col-sm-offset-2'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title" >General Message</h3>
		</div>
		<div class="box-body">
		    <h2 align="center"><?php echo $message; ?></h2>
		</div>
		
		<div class="box-footer text-center">
		    <?php if(isset($link)){
		        echo "<a class='btn btn-primary' href='".$link['href']."'>".$link['text']."</a>";				        
		    }
		    ?>
		</div>
		
	</div>
</div>


