<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Main content -->
   <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default ">
         <div class="box-header with-border">
            <h3 class="box-title"></h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
		 
			<?php
				if($result == "Company was added to database"){
					echo '<h2>Success!</h2>';
					echo '<p>'.$company.' was successfully added.</p>';
				}else{
					echo '<h2>Failure!</h2>';
					echo '<p>'.$result.'<p>';
				}
			?>
			
			
         </div>
      </div>
</div>
<!-- /.box-body -->
</div><!-- /.box -->
</section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>
   $(document).ready(function() {



	});
</script>