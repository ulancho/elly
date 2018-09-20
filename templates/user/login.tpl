<div class="container login-container">
	<div class="row">
		<center>
			<!-- <div class="login-panel panel panel-default" style="border: none; background: #d4675f;"> -->

				<div class="panel-body">
					<form id="login_form" method="post" action="<?=$this->link('user', 'login');?>" >
						<fieldset>

						
                            <a href="<?php print $this->link();?>"><img class="logo" src="img/logo_cs.png" style="margin-bottom:30px;margin-top:100px "></a>

							<div class="form-group">
								<input style="width:25%" class="form-control" placeholder="Логин" name="login" type="login" autofocus>
							</div>
							
							<div class="form-group">
								<input style="width:25%" class="form-control" placeholder="Пароль" name="password" type="password" value="">
							</div>
                                <?php if ( $error ) { ?>
        							<div class="form-group has-error">
        								<span class="help-block error"><?php echo $error;?></span>
        							</div>
						      <?php } ?>
							<button style="width:25%" data-ajax="false" class="btn btn-large btn-primary" data-disabled="false">Войти</button>
						</fieldset>
					</form>
				</div>
			<!-- </div> -->
		</center>
	</div>
</div>

<style type="text/css">
/*	body { background: url(https://png.pngtree.com/thumb_back/fw800/back_pic/00/06/36/6856299993ea2f8.jpg); } */
body { background: url(https://png.pngtree.com/thumb_back/fw800/back_pic/00/06/36/6856299993ea2f8.jpg); }
</style>