<header class="main-header">
    <!-- Logo -->
    <a href="<?php echo $this->link('index');?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>Moni</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Monitor</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo $this->user->login; ?></span>
                    </a>
                    <ul class="dropdown-menu">

                        
                        <li class="user-header">
                            <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
                            <p>
                                <?php echo $fio; ?>
                            </p>
                        </li>

                
                        <!-- <li class="user-body"> -->
                            <!-- <div class="col-xs-4 text-center"> -->
                                <!-- <a href="#">YOUR SOUL MINE!</a> -->
                            <!-- </div> -->
                        <!-- </li> -->

                        <li class="user-footer">
                       
                            <div class="pull-right">
                                <a href="<?=$this->link('user', 'logout');?>" class="btn btn-default btn-flat">Выход</a>
                            </div>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>

<style>
.sidebar-form{
    border-radius: 3px!important;
    border: none!important;
    color: #fff!important;
    margin: 25px 22px!important;
}
</style>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!--Sidebar user panel -->
        <div class="user-panel">
                <div class="pull-left image">
                    <img src="img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info" style="font">
                    <small><p><?php echo $this->user->login; ?></p></small>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
        </div>
       
            <ul class="sidebar-menu">

                <li class="treeview active" id = "COMPANIES">
                        <a href="#">
                            <i class="fa fa-fw fa-users"></i> <span>COMPANIES</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu menu-open">
                           <li><a href="<?php echo $this->link('iman','index'); ?>"><i class="fa fa-circle-o"></i>График</a></li>
							
                        </ul>
                </li>
				
				<li class="treeview active" id = "EDIT">
                        <a href="#">
                            <i class="fa fa-fw fa-users"></i> <span>EDIT</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu menu-open">
                           
							
							
                        </ul>
                </li>

            </ul>
    </section>

</aside>


<div class="modal fade" id="modelWindow" role="dialog">
            <div class="modal-dialog modal-sm vertical-align-center">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ</h4>
                </div>
                <div class="modal-body">
                   <form id = "add-user-form" action = <?php echo $this->link('index', 'add_user'); ?> method = "post">
				<br>
				<input placeholder="Логин" type="text" name="login" id = "login">
				<br>
				
				<br>
				<input placeholder="Пароль" type="text" name="password" id = "password">
				<br>
				
				<br>
				<select placeholder="Выберите компанию" name = "company_id" id = "select">
					
				</select>
				<br>
				<br>
				<input type="submit" value="ДОБАВИТЬ">
			</form> 
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" data-dismiss="modal" class="btn btn-default">Close</button> -->
                </div>
              </div>
            </div>
        </div>

<div class="modal fade" id="modelWindowcompany" role="dialog">
            <div class="modal-dialog modal-sm vertical-align-center">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <center>
                  <h4 class="modal-title">ДОБАВИТЬ КОМПАНИЮ</h4>
				  </center>
                </div>
                <div class="modal-body">
                   <form action = <?php echo $this->link('index', 'add_company'); ?> method = "post">
				<center>
				<input type="text" name="company" placeholder="Имя компании"  style="text-align: center">
				
				<br>
				<br>
				<input type="submit" value="ДОБАВИТЬ">
				</center>
			</form> 
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" data-dismiss="modal" class="btn btn-default">Close</button> -->
                </div>
              </div>
            </div>
        </div>
		
		




<script type="text/javascript">

	
	
	$(document).ready(function() {
		$(this).find(".treeview.active").removeClass("active");
		
		
		
	});
	
	$('#btnuser').click(function() {
		$('#modelWindow').modal('show');
		
	});
	
	<!-- $(function(){ -->
       <!-- $('add-user-form').on('submit', function(e){ -->
            <!-- e.preventDefault(); -->
            <!-- EllyCore.ajax({ -->
				<!-- method:'POST', -->
                <!-- url: EllyCore.url('index', 'add_user'),  -->
                <!-- data: $("add-user-form").serialize(), -->
                <!-- success: function(data){ -->
                    <!-- alert("Successfully submitted.") -->
                <!-- } -->
            <!-- }); -->
       <!-- });  -->
    <!-- }); -->
	
	<!-- $('#submituser').click(function() { -->
		<!-- var login = document.getElementById("login"); -->
		<!-- var password = document.getElementById("password"); -->
		<!-- var company_id = document.getElementById("select");  -->
		
		<!-- EllyCore.ajax({ -->
                    <!-- method:'POST', -->
    				<!-- url: EllyCore.url('index', 'add_user'), -->
					<!-- data{login:login, password:password, company_id:company_id}, -->
    				<!-- success: function(data) { -->
						
						<!-- if(data.result == 1){ -->
							<!-- alert("Success!"); -->
						<!-- }else{ -->
							<!-- alert("Fail!"); -->
						<!-- } -->
						
    				<!-- } -->
    			<!-- }); -->
	<!-- }); -->
	
	$('#btncompany').click(function() {
		$('#modelWindowcompany').modal('show');
	});
	
	$(document).ready(function() {
		EllyCore.ajax({
                    method:'GET',
    				url: EllyCore.url('index', 'get_companies'),

    				success: function(data) {
						
						var block = '<option value="" disabled selected>Выберите компанию</option>';
						for(var i in data.companies){
							block += '<option value="' + data.companies[i]["codeid"] + '">' +  data.companies[i]["nameid"] + '</option>';
						}
						
						document.getElementById('select').innerHTML = block;
						
						console.log(block);
						
					
    				}
    			});
		
		


	});
	
	


</script>

