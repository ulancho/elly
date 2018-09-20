var printBillLastDate = null;

Handlebars.registerHelper('btnPrintBill', function(date_system, code_patient) {
    var print = false;
    var date = new Date(date_system * 1000);
    var date_system = date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();

    if (printBillLastDate === null) {
        print = true;
        printBillLastDate = date_system;
    } else if (printBillLastDate !== date_system) {
        print = true;
        printBillLastDate = date_system;
    }

    return (print) ? '<a href="javascript://" class="btnPrintBillPrinter" data-date="' + date_system + '" data-code_patient="' + code_patient + '"><img src="img/icons/printer.png" style="vertical-align: middle; padding-right: 2px;"></a>' : '';

});

$(document).on('click', '.btnExcel', function() {
    var table = $(this).attr('data-table');
    var name = $(this).attr('data-name') ? $(this).attr('data-name') : 'report';
    if (!table) {
        alert('Не указанна таблица');
        return;
    }
    var tableToExcel = (function() {
        var utf8 = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        var fileName = '';
        var uri = 'data:application/vnd.ms-excel;' + fileName + 'base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head>' + utf8 + '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            },
            format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            }
        return function(table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    })()

    tableToExcel(table, 'test');
});

$(document).on("change", '.datepicker', function (e){
    $(this).val($(this).val().replace(new RegExp("/",'g'), '.'));
});

$(document).on("change", 'input', function (e){
    $(this).val($(this).val().replace(new RegExp(",",'g'), '.'));
});
