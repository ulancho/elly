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
			
			<h3 id = "header company">Гистограмма ТЕСТ 1</h3>
			<div id="graf-test">
			<img src="controllers/test.controller.php"> 
			</div>
			<h3 id = "header company">Гистограмма ТЕСТ 2</h3>
			<div id="graf-test2">
			<img src="<?php echo ImagePng($graf);?>"> 
			</div>

			</div>		
			
        </div>
		
</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
	


	
	
	var users;
	var company_id;
	
	$('#btnuser').click(function() {
		$('#modelWindow').modal('show');
		
	});
	
	$(function(){
       $('add-user-form').on('submit', function(e){
            e.preventDefault();
            EllyCore.ajax({
				method:'POST',
                url: EllyCore.url('index', 'add_user'), 
                data: $("add-user-form").serialize(),
                success: function(response){
                    alert("Successfully submitted.")
                }
            });
       }); 
    });


   $(document).ready(function() {
   
   
   
   
		var elms = document.getElementsByClassName('ssilka');
		console.log(elms);
		for (var i = 0; i < elms.length; i++) {
			
			elms[i].removeAttribute("href");
			
		}


      
	  
	   $(document).on('click', '.turnbutton', function(event) {
	       EllyCore.ajax({
                    method:'POST',
    				url: EllyCore.url('index', 'azamat'),
    				//data: {id : id, turn : turn},
    				success: function(data) {
    				    }
    			});
                    
				
				var turn = $(this).attr('turn');
				var id = $(this).attr('id');
                var th = $(this);
                /*EllyCore.ajax({
                    method:'POST',
    				url: EllyCore.url('index', 'turn_user'),
    				data: {id : id, turn : turn},
    				success: function(data) {
					console.log($(this));
						if(turn == 'OFF' && data.result == 1){
							document.getElementById(id).innerHTML = 'ON';
							th.attr("turn","ON");
						}else if(turn == 'ON' && data.result == 1){
							document.getElementById(id).innerHTML = 'OFF';
							th.attr("turn","OFF");
						}
						
    				}
    			});*/
      });
	  
	  $(document).on('click', '.save', function(event) {
				
				
				var id = $(this).attr('id');
				var password = $(this).attr('id');
				var login = $(this).attr('id');
				
				var obj = $(this);
				var tr = obj.parents('tr');
				var login = tr.find('input[type=login]').val();
				var password = tr.find('input[type=text]').val();
				
				<!-- for(int i = 0; i < arr.length; i++){ -->
					<!-- if(i == 2 || i == 3){ -->
						<!-- input = arr[i].find('input').val(); -->
					<!-- } -->
					<!-- console.log(input); -->
				<!-- } -->
				<!-- throw new Error("Something went badly wrong!"); -->
				
                <!-- var th = $(this); -->
                EllyCore.ajax({
                    method:'POST',
    				url: EllyCore.url('index', 'edit_user'),
    				data: {id : id, login : login, password : password},
    				success: function(data) {
						if(data.result == 1){
							elly.msg('Изменения были внесены');
							console.log(company_id);
							<!-- throw new Error("Something went badly wrong!"); -->
							$("[company=" + company_id + "]").click();
							<!-- tr.innerHTML = ''; -->
							<!-- tr.innerHTML += '<td><div class="input-group">2</div></td>'; -->
							<!-- tr.innerHTML += '<td><div class="input-group">2</div></td>'; -->
							<!-- tr.innerHTML += '<td><div class="input-group"><input type="login" value="' + login + '" id = "login" class="form-control" type="text"></div></td>'; -->
							<!-- tr.innerHTML += '<td><div class="input-group"><input type="text" value="' + password + '" id = "password" class="form-control" type="text"></div></td>'; -->
							<!-- tr.innerHTML += '<td><i class="fa fa-save save" id = "' + id + '" style="float: right; margin-right: 2em; margin-top: 10px;"> Save</i></td>'; -->
							
							
							<!-- document.querySelectorAll('[company]').click(); -->
							<!-- return; -->
						}else{
							elly.msg('Произошла ошибка');
						}
						
    				}
    			});
      });
	  
	  $(document).on('click', '.edituser', function(event) {
				

				var id = $(this).attr('id');
				var index = $(this).attr('index');
				<!-- console.log(users) -->
				<!-- window.alert(index); -->
				
				
				
				
				<!-- document.getElementById('trid').innerHTML = 'a'; -->
				var stroka = document.querySelectorAll('[trid="'+id+'"]');
				for (var i = 0; i < stroka.length; i++) {
					
					<!-- window.alert(stroka[i]); -->
					stroka[i].innerHTML = '';
					stroka[i].innerHTML += '<td><div class="input-group"></div></td>';
					stroka[i].innerHTML += '<td><div class="input-group"></div></td>';
					stroka[i].innerHTML += '<td><div class="input-group"><input type="login" value="' + users.rows[index]["login"] + '" id = "login" class="form-control" type="text"></div></td>';
					stroka[i].innerHTML += '<td><div class="input-group"><input type="text" value="' + users.rows[index]["password"] + '" id = "password" class="form-control" type="text"></div></td>';
					stroka[i].innerHTML += '<td><i class="fa fa-save save" id = "' + users.rows[index]["codeid"] + '" style="float: right; margin-right: 2em; margin-top: 10px;"> Save</i></td>';
					
					<!-- '<tr class="data-heading"><td>ID</td><td>COMPANY_ID</td><td>LOGIN</td><td>PASSWORD</td><td>STATUS</td></tr>'; -->
			
				}
				
      });
	  
	   $(document).on('click', '.showhistory', function(event) {
				

				var id = $(this).attr('id');
				var login = $(this).attr('login');
				<!-- var index = $(this).attr('index'); -->
				
				<!-- window.alert(index); -->
				
				
				
				
				<!-- document.getElementById('trid').innerHTML = 'a'; -->
				var stroka = document.querySelectorAll('[trid="'+id+'"]');
				
				EllyCore.ajax({
                    method:'GET',
    				url: EllyCore.url('index', 'get_history'),
    				data: {id : id, login : login},
    				success: function(data) {
						if(data.result == 1){
							
							$('#modelWindowHistory').modal('show');
							
							var content = '<table class="table table-bordered" cellspacing="0" width="100%">';
							content += '<tr class="data-heading"><td>id</td><td>date</td><td>login</td><td>password</td><td>company</td><td>ip</td><td>version</td><td>status</td><td>comment</td></tr>';
							for(var row in data.rows){
								content += "<tr>";
								for(var item in data.rows[row]){
									content += "<td>" + data.rows[row][item] + "</td>";
								}
								content += "</tr>";
							}
							content += "</table>";
							
							
							document.getElementById('userhistory').innerHTML = content;
							
						}else{
							elly.msg('Произошла ошибка');
						}
						
    				}
    			});
				
				<!-- for (var i = 0; i < stroka.length; i++) { -->
					
					<!-- stroka[i].innerHTML = ''; -->
					<!-- stroka[i].innerHTML += '<td><div class="input-group">2</div></td>'; -->
					<!-- stroka[i].innerHTML += '<td><div class="input-group">2</div></td>'; -->
					<!-- stroka[i].innerHTML += '<td><div class="input-group"><input type="login" value="' + users.rows[index]["login"] + '" id = "login" class="form-control" type="text"></div></td>'; -->
					<!-- stroka[i].innerHTML += '<td><div class="input-group"><input type="text" value="' + users.rows[index]["password"] + '" id = "password" class="form-control" type="text"></div></td>'; -->
					<!-- stroka[i].innerHTML += '<td><a class="fa fa-save save" id = "' + users.rows[index]["codeid"] + '" style="float: right"></i></td>'; -->
					
			
				<!-- } -->
				
      });
	  
	  $(document).on('click', '.qwe', function(event) {
	  
				company_id = $(this).attr('company');
				var company = $(this).attr('company');
				var name = $(this).attr('name');
				
				var icon = "fa fa-arrow-right"
	  
				var arr = document.getElementsByClassName(icon);
				for(var ar in arr){
					arr[ar].className = "fa fa-circle-o";
					<!-- console.log(ar); -->
				}
				
				document.getElementById("fa " + company).className = icon;
				
				
				document.getElementById("header company").innerHTML = name;
				
                EllyCore.ajax({
                    method:'GET',
    				url: EllyCore.url('index', 'get_users'),
    				data: {company: company},
    				success: function(data) {
					
					
					
					users = data;
					
					<!-- var table = '<table id = "tablica" class="table table-bordered" cellspacing="0" width="100%">'; -->
					var table = '<table class="table table-bordered" cellspacing="0" width="100%">';
					table += '<tr class="data-heading"><td>ID</td><td>COMPANY_ID</td><td>LOGIN</td><td>PASSWORD</td><td>STATUS</td></tr>';
					
					for (var index in data.rows) {
						table += '<tr trid = ' + data.rows[index]['codeid'] + '>';
						
						
						for(var key in data.rows[index]){
							if(key == 'nameid') continue;
							// console.log(key);
							// console.log(row[key]);
							table += '<td>';
							
							
							if(key != 'status'){
								table += data.rows[index][key];
							}else{
								if(data.rows[index][key] == '1'){
									table += '<a class="btn btn-default turnbutton" turn = "OFF" id = "' + data.rows[index]['codeid'] + '">OFF</a>';
								}else{
									table += '<a class="btn btn-default turnbutton" turn = "ON" id = "' + data.rows[index]['codeid'] + '">ON</a>';
								}
								table += '<i class="fa fa-history showhistory" style="float: right; margin-right: 2em; margin-top: 10px;" id = "' + data.rows[index]['codeid'] + '" login = "' + data.rows[index]['login'] + '">History </i>';
								table += '<i class="fa fa-edit edituser" style="float: right; margin-right: 2em; margin-top: 10px;" id = "' + data.rows[index]['codeid'] + '" index = "' + index + '">Edit </i>';
								
							}
							
							table += '</td>';
							
						}
						table += '</tr>';
					}
					
					
					table += '</table>';
					
					<!-- table += '</table>'; -->
					
					
					<!-- document.getElementById('tablica').innerHTML = table; -->
					var temp = document.getElementById("tablica").innerHTML;
					if(tablica){
						document.getElementById("tablica").innerHTML = table;
					}else{
						<!-- location.reload(); -->
					}


    				}
    			});
      });
	  
	  

	});
</script>

<script>
function refresh_my_img()
{
time = new Date();
var rand = time.getTime();
var new_src = "controllers/test.controller.php?"+"&add_param="+rand;
$("#graf-test").empty();
var i = new Image();
i.src = new_src;
$("#graf-test").append(i);
}

$(document).ready(function(){  
        refresh_my_img();  
        setInterval('refresh_my_img()',3000);  
    }); 
</script>

