<!-- Content Wrapper. Contains page content -->


<div class="content-wrapper" style="height:100vh;min-height:100%">
   <!-- Main content -->
   <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default ">
         <div class="box-header with-border">
            <h3 class="box-title"></h3>
         </div>
         <!-- /.box-header -->
         <div class="box-body">
            <h2></h2>
            <div id = "tablica">
              <?php echo $table; ?>
        	 </div>         
             </div>
            </div>
</div>
</div><!-- /.box -->
</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>



   $(document).ready(function() {
   
   
        console.log(asd);
   
	  EllyCore.ajax({
                  method:'POST',
          url: EllyCore.url('table', 'table'),
          //data: {id : id, turn : turn},
          success: function(data) {
              }
        });
	  
	  
	   
	  
	  

	});

</script>

