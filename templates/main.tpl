<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="favicon.ico" />

		<?php echo $this->getMetatags(); ?>

		<title><?php echo $this->getTitle(); ?></title>

	<?php // Подключение стилей. Все стили подключаются через функцию headStyle. Если режим не дебаг, то перед отправкой в браузер, они собираются в один файл и архивируются
		echo $this->headStyle(
			array(
				'css/vendor/ui-lightness/jquery-ui-1.10.4.custom.css',
				'css/bootstrap.min.css',
				'css/vendor/font-awesome.min.css',
				'css/vendor/chosen.min.css',
				'css/vendor/tipsy.css',
				//'css/system.css',
				'css/system_icons.css',
				'css/main.css',
				'css/AdminLTE.min.css',
				'css/skins/_all-skins.min.css',
				'css/plugins/select2/select2.min.css',
				'css/plugins/iCheck/all.css',
				'css/plugins/daterangepicker/daterangepicker-bs3.css',
				'css/vendor/jstree/themes/default/style.min.css',
				'css/vendor/easyautocomplete/easy-autocomplete.min.css',
				'css/vendor/easyautocomplete/easy-autocomplete.themes.min.css',
                'css/ionicons.min.css',
                'css/plugins/datepicker/datepicker3.css',
			)
		);

			// Аналогично с JS. Подключаются через функцию headScript
		$this->headScript(
			array(
                'js/jquery-2.1.4.min.js',
                'js/vendor/jquery-ui.toggleSwitch.js',
				'js/vendor/main.js',
                'js/vendor/handlebars.js',
				'js/vendor/moment.min.js',
                'js/vendor/moment.ru.js',
				'js/vendor/jquery.tipsy.js',
				'js/vendor/jquery.contextmenu.min.js',
                'js/vendor/nicEdit.js',
				'js/vendor/jquery-ui-1.10.4.custom.js',
				'js/vendor/jquery.chosen.js',
				'js/vendor/jquery-ajax-form.js',
				'js/vendor/jquery.hidescroll.js',
				'js/vendor/jquery.layout.js',
				'js/vendor/jquery.mousewheel.min.js',
				'js/vendor/jquery.popup.js',
				'js/vendor/jquery.slidepanels.js',
				'js/vendor/jquery.stretch.js',
				'js/vendor/jquery.tablist.js',
				'js/vendor/jquery.tools.js',
				'js/vendor/jquery.PrintArea.js',
				'js/vendor/fullcalendar/fullcalendar.js',
				'js/vendor/jplot/jquery.jqplot.min.js',
				'js/vendor/jplot/shCore.min.js',
				'js/vendor/jplot/shBrushJScript.min.js',
				'js/vendor/jplot/shBrushXml.min.js',
				'js/vendor/jplot/jqplot.canvasAxisLabelRenderer.min.js',
				'js/vendor/jplot/jqplot.canvasTextRenderer.min.js',
				'js/vendor/jplot/example.min.js',
                'css/plugins/select2/select2.full.min.js',
                'css/plugins/input-mask/jquery.inputmask.js',
                'css/plugins/input-mask/jquery.inputmask.date.extensions.js',
                'css/plugins/input-mask/jquery.inputmask.extensions.js',
                'css/plugins/daterangepicker/daterangepicker.js',
                'css/plugins/iCheck/icheck.min.js',
                'css/plugins/fastclick/fastclick.min.js',
                'css/plugins/slimScroll/jquery.slimscroll.min.js',
                'css/plugins/datatables/jquery.dataTables.min.js',
                'css/plugins/datatables/dataTables.bootstrap.min.js',
				'css/vendor/jstree/jstree.min.js',
				'css/vendor/jstree/jstree.actions.min.js',
				'css/vendor/easyautocomplete/jquery.easy-autocomplete.min.js',
                'js/elly-core.js',
                'js/elly.js',
				'js/main.js',
                'js/select2.ru.js',
				'js/bootstrap.min.js',
				'js/fastclick.min.js',
				'js/app.min.js',
				'js/demo.js',
                'js/modernizr-2.6.2-respond-1.1.0.min.js',
                'js/elly-ui.js',
			)
		);



		echo $this->attachStyle();
		echo $this->attachScript();
	?>
</head>

<body id="global_controller_<?php echo $this->getControllerName() . ' global_action_' . $this->getControllerName() . '_' . $this->getActionName(); ?>" class="hold-transition skin-black sidebar-mini">

    <div class="wrapper">
        <?php echo $this->widget('header'); ?>
        <?php echo $this->content(); ?>
        <?php echo $this->widget('footer'); ?>
        <div class="control-sidebar-bg"></div>
    </div>
    
    <!-- иконка рядом с курсором при аякс запросе -->
    <img src="img/system/ajax-mouse-loading.gif" id="elly-ajax-loading" alt="" style="display:none;" />
    
    <!-- шаблон для модального окна -->
    <div class="modal fade" id="elly-modal-container" role="dialog" aria-labelledby="elly-modal-title" aria-hidden="true">
    	<div class="modal-dialog">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h4 class="modal-title" id="elly-modal-title"></h4>
    			</div>
    			<div class="modal-body" id="elly-modal-body"></div>
    		</div>
    	</div>
    </div>

    <!-- Modal -->
        <div class="modal fade" id="elly_dialog" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <b class="modal-title">Системное сообщение</b>
                        </div>
                        <div class="modal-body">
                            <p>This is a small modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </div>
        </div>
    
    <!-- Modal -->
    <div class="modal fade" id="elly_confirm" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <b class="modal-title">Системное сообщение</b>
            </div>
            <div class="modal-body">
              <p>This is a small modal.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
              <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal">Подтвердить</button>
            </div>
          </div>
        </div>
    </div>

        <!-- Modal -->
        <div class="modal fade" id="elly_confirm_update" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <b class="modal-title">Системное сообщение</b>
                        </div>
                        <div class="modal-body">
                            <p>This is a small modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Не обновлять</button>
                            <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal">Обновить</button>
                        </div>
                    </div>
                </div>
        </div>


    <script type="text/javascript">
    $(document).ready(function(e) {
    
            
    		// Тайтлы у элементов с class="tipsy-tooltip" будут во всплывающей подсказке
    	$('.tipsy-tooltip').tipsy({gravity: 'n'});
    
    		// Плаг для работы с датами в js.
    	moment.locale('ru');
        
        
        //Initialize Select2 Elements
        $(".select2").select2();
        
        //Datemask dd/mm/yyyy
        $("#datemask").inputmask("dd.mm.yyyy", {"placeholder": "dd.mm.yyyy"});
        //Datemask2 mm/dd/yyyy
        $("#datemask2").inputmask("mm.dd.yyyy", {"placeholder": "mm.dd.yyyy"});
        //Money Euro
        $("[data-mask]").inputmask();
        
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
        });
        $('.datepicker_up').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            "drops":'up'
        });
        $('.datetimepicker').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "timePicker": true,
            "timePickerIncrement": 10,
            "timePicker12Hour":false,
            "format": 'DD.MM.YYYY HH:mm'
        });
        $('.datetimepicker_up').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "timePicker": true,
            "timePickerIncrement": 10,
            "timePicker12Hour":false,
            "format": 'DD.MM.YYYY HH:mm',
            "drops":'up'
        });
        //Date range picker
        $('#reservation').daterangepicker();
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({
            timePicker: true, 
            timePickerIncrement: 30, 
            format: 'DD.MM.YYYY h:mm A'
        });
        //Date range as a button
        $('#daterange-btn').daterangepicker(
              {
                ranges: {
                  'Сегодня': [moment(), moment()],
                  'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                  'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                  'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                  'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                  'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            	
              },
            function (start, end) {
            $('#reservation').val(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
            }
        );
        
        
    
    });
    
    var tableToExcel = (function() {
      var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
      return function(table, name) {
        if (!table.nodeType) table = document.getElementById(table)
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
        window.location.href = uri + base64(format(template, ctx))
      }
    })();
    
    function init_select2(selectid,controller,action,data_array,maxOut,minIn,full_array,search){
        if(search==1){
           search = 'Infinity';
        }
        else{
           search = 3;
        }
        var data_selected = []; //сохраненные выбранные значения id
        var data_choise = []; //сохраненные выбранные значения
        if(data_array.length != 0){//сохраненные выбранные значения
            $.each(data_array, function(id, val) {
                if(val!=undefined){
                    if(val['id']===undefined){
                       var str_id, str_val;
                       $.each(val, function(key) {
                           if(key == 'codeid') str_id = key;
                           if(key.substr(0, 4) == 'name') str_val = key;
                       });
                       data_selected.push(val[str_id]);
                       data_choise.push({id: val[str_id], text: val[str_val]});
    
                    }
                    else{
                       data_selected.push(val['id']);
                       data_choise.push(val);
                    }
                }
            });
        }
        var data_full = [];  //все значения
        if(full_array.length != 0){
                $.each(full_array, function(id, val) {
                    if(val['id']===undefined){
                       var str_id, str_val;
                       $.each(val, function(key) {
                           if(key == 'codeid') str_id = key;
                           if(key.substr(0, 4) == 'name') str_val = key;
                       });
                       data_full.push({id: val[str_id], text: val[str_val]});
                    }
                    else{
                       data_full.push(val);
                    }
                });
    
                $(selectid).select2({
                    data:data_full,
                    tags:true,
                    minimumResultsForSearch:search
                });
    
        }else{
            if(data_choise.length != 0){
                $(selectid).select2({
                    data:data_choise,
                    tags:true,
                    minimumResultsForSearch:search
                });
            }
            else{
                data_selected.push($(selectid).val())
    
            }
        }
        $(selectid).val(data_selected).trigger('change');
    
        if(full_array.length == 0 && action.length != 0){
    
            $(selectid).select2({
                ajax: {
                    url: EllyCore.url(controller,action),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            word: params.term,
                            page:params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
    
                        return {
                            results: data.data.items,
                            pagination: {
                                more: (params.page * maxOut) < data.total_count
                            }
                        };
                    },
                    cache: false
                },
                minimumInputLength: minIn,
                theme:"classic",
                tags:true,
                minimumResultsForSearch:search,
                data:[{id: 1, text: 'text'}],
            });
        }
    }
    
    </script>

</body>
</html>