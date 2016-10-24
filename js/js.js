/**
 *
 */
// jshint unused:false, undef:false

function set_value(op) {

    if (op === 'set') {
        $("input[name^='limit']").val($("#id_value").val());
    } else if (op === 'add') {
        $("input[name^='limit']").each( function(index) {

            $(this).val(Number($(this).val()) + Number($("#id_value").val()));
        });
    } else if (op === 'sub') {
        $("input[name^='limit']").each( function(index) {

            var newval = Number($(this).val()) - Number($("#id_value").val());

            if (newval < 0) {
                newval = 0;
            }
            $(this).val(newval);
        });
    }
}

function preview_value(op) {

    if (op === 'set') {
        $("[class^='html']").html(" >> " + $("#id_value").val());
    } else if (op === 'add') {

        $("[class^='html']").each( function(index) {
            userid = $(this).attr('class').replace('html', '');

            $(this).html(">> "+ (Number($('#id_limit' + userid).val()) + Number($("#id_value").val())));
        });
    } else if (op === 'sub') {
        $("[class^='html']").each( function(index) {
            userid = $(this).attr('class').replace('html', '');

            var newval = Number($('#id_limit' + userid).val()) - Number($("#id_value").val());
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