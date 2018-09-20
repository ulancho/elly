
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header" id="header_main">
		
      <ol class="breadcrumb">
    <!-- ссылки перехода ?name_controller@function как php-->      
        <li><a href=""><i class="fa fa-fw fa-users"></i>Клиенты компании</a></li>
        <li><a href="#">Клиент</a></li>
        <li class="active_page">Основное</li>
      </ol>
      <br />
      <div class="row" style="margin-top: 10px;">
          <div class="col-md-12">
                <div class="box" id="box_name">

                </div><!-- /.box- -->
            </div>
        </div>
    </section>
   
     <!-- Main content -->
	<section class="content"> 
        <div class="box" id="box_content_name">
		</div><!-- /.box- -->
    </section>
	<!-- /.content -->

</div>
<!-- /.content-wrapper -->


<div class="hidden">
	<!-- Модальное окно с выводом формы для создания и редактирования блока name -->
	<div id="div_modal_name">
        <form class="form-horizontal row" role="form" id="modal_form_name" ><!-- method="post" get -->
        </form>
    </div>
</div>


<!-- ******** ШАБЛОН ******** -->
<!-- with список элементов с вложенным полем со списком - (если внутри формы =(создание редактирование для модальных окон)))-->
<!-- each талибца-вид с вложенным полем со списком - (если внутри формы отправка как php js))-->
<!-- selectedS это помощник для пересчета или выбора из списка нужного значения(в данном случае устанавливаем selected)
<!-- 
<script type="text/template" id="script_name">
{{#with view_list1}} 

	<input type="number" name="codeid1" id="codeid1" class="form-control" value="{{codeid1}}"  >
	<input type="number" name="name1" id="name1" class="form-control" value="{{name1}}"  >
	<input type="text" name="date_system1" data-inputmask="'alias': 'DD.MM.YYYY'" data-mask class="form-control datepicker" value="{{dateFormat date_system1 format="DD.MM.YYYY"}}" >
	
	<select name="code_select1" class="form-control full_access" style="width: 100%;" aria-hidden="true" disabled>
		 {{#each ../view_list2.list}}
			<option value="{{codeid2}}" {{selectedS codeid2 ../this/code_select1}}>{{name2}}</option>
		 {{/each}}
	</select>
{{/with}}

{{#each view_list3.list}}
	<input type="number" name="codeid3" id="codeid3" class="form-control" value="{{codeid3}}"  >
	<input type="number" name="name3" id="name1" class="form-control" value="{{name3}}"  >
	<input type="text" name="date_system3" data-inputmask="'alias': 'DD.MM.YYYY'" data-mask class="form-control datepicker" value="{{dateFormat date_system3 format="DD.MM.YYYY"}}" >
	
	<select name="code_select3" class="form-control full_access" style="width: 100%;" aria-hidden="true" disabled>
		 {{#each ../view_list2.list}}
			<option value="{{codeid2}}" {{selectedS codeid2 ../this/code_select3}}>{{name2}}</option>
		 {{/each}}
	</select>
{{/each}}
</script>
-->


<script type="text/javascript">
//функция загрузки данных в шаблон
function renderScript_name() {

//EllyModel
//	if ( Object.prototype.toString.call(view_list1)=='[object Array]' ) {
//        view_list1 = new EllyModel(view_list1,'codeid');
//    }

// если при загрузке страницы из php первоначально получили данные view_list1 EllyModel не создаем
//пример получения из php
//		$view_list1 = view_list1::find();   	//таблица
//		$view_list2 = view_list2::get($codeid); //одна строка 
//		$view_list3 = view_list3::find($codeid);//таблица   
//		return array(
//			'view_list1' =>$view_list1 //получаем если нужно в php 
//			'toJson'=>array(
//              'view_list1' =>$view_list1,
//				'view_list2' =>$view_list2,
//				'view_list3' =>$view_list3
//            ),
//        );
 
// если не получили, создаем здесь либо при первом вызове функции копируем туда

//Проверка если view_list1 пришел пустой создаем обьект с такимже именем, чтобы отобразить пустой скрипт шаблон input 
//    if($.isEmptyObject(view_list1))
//        view_list1 = {};

//заполнение шаблона        
//    EllyCore.template({
//		element: $('#box_name'), //либо box_content_name
//		source: $('#script_name'),
//        values: { view_list1: view_list1,
//					view_list2:view_list2,
//					view_list3:view_list3					
//				   },
//	});
    

//после заполнения шаблонизатора доступны поля html document
var codeid1 = $('#box_name').find('input[name="codeid1"]').val();
    
};

    
$(document).ready(function() {
    renderScript_name();	

    /*-------Обработка нажатия кнопок по классу------------ */
    $(document).on('click', '.add_name', function(event) {
        $('#box_name').addClass('hidden');
    });
	
    /*-------Обработка нажатия кнопок по id------------*/
    $(document).on('click', '#del_name', function(event) {
        $('#box_name').removeClass('hidden');
    });	

    
    /*-------Обработка нажатия кнопок по классу*/
    $(document).on('click', '.add_name', function(event) {
        //если обработка формы для передачи JSON
//		var sDataTemp = {};
//		$('#modal_form_name').find('input, select, textarea').serializeArray().forEach(function(obj, i)
//		{
//			sDataTemp[obj.name] = obj.value; 
//		});

//var codeid1 = $('#box_name').find('input[name="codeid1"]').val();

//передача формы в php как JSON не submit           
		EllyCore.ajax({
			url: EllyCore.url('name_controller','function'),
			data:{sDataTemp:sDataTemp,
                  codeid1:codeid1}, //либо отдельно поля 
			type: 'POST', //по умолчанию GET
            success: function(data){ //ответ data.view_list1
            
                    elly.msg('Сообщение');
                    //если ответ view_list1::get как toJson и первоначально получали данные
                    //view_list1 = data.view_list1;

//пример php если в первую загрузку не получали view_list2 
//                  $this->setContext('json');                            
//                  $view2=view_list2::find();
//                  view_list2=array();
//                  foreach ($view2 as $key => $list) { view_list2[] = $list;} 
//                  return array(
//                    	'view_list2' =>$view_list2
//                  );
//js если в первую загрузку не получали view_list2
//        			view_list2=[];
//                  if ( Object.prototype.toString.call(view_list2)=='[object Array]' ) {
//                      view_list2 = new EllyModel(view_list2,'codeid');
//                  }
//                  view_list2.list=data.view_list2;                            
                            
                            
//пример php если был и получили в php как
                    //$this->setContext('json');
                    //view_list2 = view_list2::get(codeid);
                    //return array(
                    //	   'view_list2' =>$view_list2
                    //);

//js подменяем
                    //client_main = data.view_list2;;   
                            
//заполнение шаблона                                 
                    EllyCore.template({
                		element: $('#box_content_name'),
                		source: $('#script_name'),
                        values: {   view_list1: view_list1,
                                    view_list2:view_list2, //без перезагрузки страницы можем использовать полученные ранее
                                    view_list3:data.view_list3  //можно сразу в шаблон если ответ как setContext('json') и ::get без создания локальной копии			
                				},
                	});

                            //если ответ пустой обьект можем скрыть box
//                            if($.isEmptyObject(view_list1)){
//                                $('.box_content_name').addClass('hidden');
//                            }

            }//function(data)

            }//EllyCore.ajax
    });//function(event)

       
 
     /*-------открытие на редактирование модального окна с заполненными полями (поиск данных по codeid)*/
    $(document).on('click', '.button_class', function(event) {  

        var codeid = $(this).data('codeid');
        //отправка ajax
        EllyCore.ajax({
				url: EllyCore.url('name_controller', 'function'),
				data: {codeid: codeid},
				success: function(data) {
				    //первоначально таких данных не было
				    view_list2=[];
                	if ( Object.prototype.toString.call(view_list2)=='[object Array]' ) {
                        view_list2 = new EllyModel(view_list2,'codeid');
                    }
                    //заполнение шаблона модального окна
                    EllyCore.template({
                		element: $('#modal_form_name'),
                		source: $('#script_name'),
                        values: {   view_list1: view_list1,
                                    view_list2:view_list2, //без перезагрузки страницы можем использовать полученные ранее
                                    view_list3:data.view_list3  //можно сразу в шаблон если ответ как setContext('json') и ::get без создания локальной копии			
                				},
                	});
                    //открываем заполненное модальное окно
					EllyCore.showAjaxForm({
            			element: $('#div_modal_name'),
            			url: EllyCore.url('name_controller', 'function'),//если отправка формы по submit указываем куда
            			title: "Заголовок модального окна",
            		});
                    //console.log(data.country_list);
                    
				},
			});

    });
    
    //обработка модального окна после submit
	EllyCore.ajaxForm({
		element: $('#modal_form_name'),
		success: function(data){
            elly.msg('Добавили, отредактировали, удалили');
            
            //закрываем модальное окно
			EllyCore.modalClose();
            
		}
	});  
    
    //обработка модального окна после submit
	EllyCore.ajaxForm({
		element: $('#contact_form'),
		success: function(data){
            elly.msg('Контакт обновлен');
//пример php
//        $this->setContext('json');
//        $contact = view_clients_contact::get($codeid);
//        return array(
//            'contact' => $contact
//        );

            //строим для документа
            html = '\
            		<td>' + data.contact['codeid'] + '</td>\
                    \';
            //подменяем 
            //вставка после
            $('#body .info').after(html);
            //удаляем старый
            $('#body_client_contact').find('.info').remove();
            
            //закрываем модальное окно
			EllyCore.modalClose();
		}
	});  
    
     /*-------Модальное окно подтверждения для удаления */
    $(document).on('click', '.del_contact', function(event) {  
        $('#body').find('tr').removeClass('danger'); 
        var codeid = $(this).parents('tr').attr('data-code_id');
           
        $(this).parents('tr').addClass('danger');
        
        elly.confirm('Удалить  данные ?', function(ok){
            if(ok){ 
                EllyCore.ajax({
    				url: EllyCore.url('name_controller', 'del_codeid'),
    				data: {codeid: codeid},
    				success: function(data) {
    				    //если успешно удалили в базе 
    				   //удаляем старый в виде 
                       $('#body').find('.danger').remove();
                       $('#body').find('tr').removeClass('danger');
                       elly.msg('Удален');
    				}
    			});
            }    
        });
    });
        
});
    
Handlebars.registerHelper('dateFormat2', function(date) {
	return moment(date,'DD.MM.YYYY HH:mm').format('DD.MM.YYYY hh:mm');
});    

Handlebars.registerHelper("selectedS", function(codeid, code) {
    if(codeid==code)
		return 'selected';

});


</script>


