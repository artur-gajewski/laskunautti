$(function() {

    $( "#store" ).bind( "click", function(e) {
        e.preventDefault();

        if ($(".sender-type-cb").is(':checked')) {
            console.log("Checked");
            localStorage.setItem('sender-type-cb', 1);
        } else {
            console.log("NOT Checked");
            localStorage.setItem('sender-type-cb', 0);
        }

        var fields = $( ".sender-type" ).serializeArray();
        jQuery.each( fields, function( i, field ) {
            localStorage.setItem(field.name, field.value);
        });

        $("#saved-success").html("Tiedot tallennettu!");
        $("#loaded-success").html("");
    });

    $( "#load" ).bind( "click", function(e) {
        e.preventDefault();

        var cb = localStorage.getItem("sender-type-cb");
        if (cb == 1) {
            $('.sender-type-cb').prop('checked', true);
        } else {
            $('.sender-type-cb').prop('checked', false);
        }

        var fields = $( ".sender-type" ).serializeArray();
        jQuery.each( fields, function( i, field ) {
            console.log($(field.name));
            $('#' + field.name).val(localStorage.getItem(field.name));
        });

        $("#loaded-success").html("Tiedot ladattu!");
        $("#saved-success").html("");
    });

    $( "#preview" ).bind( "click", function(e) {
        e.preventDefault();
        $("#billform").attr("action", "{{ path('preview') }}");
        $("#billform").attr("target", "BLANK");
        $("#billform").submit();
    });

    $( "#submit-form" ).bind( "click", function(e) {
        e.preventDefault();
        $("#billform").attr("action", "{{ path('homepage') }}");
        $("#billform").attr("target", "");
        $("#billform").submit();
    });
});