/**
 *
 */
// jshint unused:false, undef:false

function set_value(op) {
    var formval, newval, userid;

    if (op === 'set') {
        $("input[name^='limit']").val($("#id_value").val());
    } else if (op === 'add') {
        $("input[name^='limit']").each( function(index) {
            formval = parseInt($(this).val());
            formval = isNaN(formval) ? 0 : formval;
            newval = formval + parseInt($("#id_value").val());
            $(this).val(newval);
        });
    } else if (op === 'sub') {
        $("input[name^='limit']").each( function(index) {
            formval = parseInt($(this).val());
            formval = isNaN(formval) ? 0 : formval;
            newval = formval - parseInt($("#id_value").val());
            if (newval < 0) {
                newval = 0;
            }
            $(this).val(newval);
        });
    }
}

function preview_value(op) {

    var newval, formval, userid;

    if (op === 'set') {
        $("[class^='html']").html(">> " + $("#id_value").val());
    } else if (op === 'add') {
        $("[class^='html']").each( function(index) {
            userid = $(this).attr('class').replace('html', '');
            formval = parseInt($('#id_limit' + userid).val());
            formval = isNaN(formval) ? 0 : formval;
            newval = formval + parseInt($("#id_value").val())
            $(this).html(">> " + newval);
        });
    } else if (op === 'sub') {
        $("[class^='html']").each( function(index) {
            userid = $(this).attr('class').replace('html', '');
            formval = parseInt($('#id_limit'+userid).val());
            formval = isNaN(formval) ? 0 : formval;
            newval = formval - parseInt($("#id_value").val());
            if (newval < 0) {
                newval = 0;
            }
            $(this).html(" >> " + newval);
        });
    }
}

function cancel_preview() {
    $("[class^='html']").html('');
}