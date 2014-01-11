$(function() {

    $( "#store" ).bind( "click", function(e) {
        e.preventDefault();

        if ($(".sender-type-cb").is(':checked')) {
            localStorage.setItem('sender-type-cb', 1);
        } else {
            localStorage.setItem('sender-type-cb', 0);
        }

        var fields = $( ".sender-type" ).serializeArray();
        jQuery.each( fields, function( i, field ) {
            localStorage.setItem(field.name, field.value);
        });

        $("#saved-success").html("Tiedot tallennettu selaimen välimuistiin!");
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
            $('#' + field.name).val(localStorage.getItem(field.name));
        });

        $("#loaded-success").html("Tiedot ladattu selaimen välimuistista!");
        $("#saved-success").html("");
    });

    $("#billNumber").change(function() {
        var createRef = $( "#createReference" );
        var number = $( "#billNumber" ).val();
        var intRegex = /^\d+$/;

        if ( createRef.is(':checked') && intRegex.test(number) ) {
            var number = $( "#billNumber" ).val();
            var ind1, ind2 = 0, sum = 0, mult = [ 7, 3, 1 ];

            for (ind1 = number.length; ind1--; ind2++) {
                sum += (number.charCodeAt(ind1) - 48) * mult[ind2 % 3];
            }

            var ref = number += (1000 - sum) % 10;
            $("#billReference").val(ref);
        }
    });

    $("#billReference").attr('readonly','readonly');

    $('#createReference').change(function() {
        if($(this).is(":checked")) {
            $("#billReference").attr('readonly','readonly');
        } else {
            $("#billReference").removeAttr('readonly');
        }

    });

    // validate signup form on keyup and submit
    // http://jqueryvalidation.org/documentation
    $("#billform").validate({
        rules: {
            senderName: {
                required: true,
                minlength: 3
            },
            senderEmail: {
                required: false,
                email: true
            },
            senderIban: {
                required: true
            },
            senderSwift: {
                required: true
            },
            payerName: {
                required: true
            },
            payerEmail: {
                required: false,
                email: true
            },
            billNumber: {
                required: true,
                minlength: 3,
                number: true
            },
            billDescription: {
                required: true
            },
            billTotal: {
                required: true,
                number: true
            }
        },
        messages: {
            senderName: {
                required: "Syötä lähettäjän nimi",
                minlength: "Nimi on liian lyhyt"
            },
            senderIban: {
                required: "Syötä lähettäjän IBAN tilinumero"
            },
            senderSwift: {
                required: "Syötä lähettäjän pankin SWIFT/BIC tunnus"
            },
            payerName: {
                required: "Syötä maksajan nimi"
            },
            billlNumber: {
                required: "Syötä laskun numero",
                minlength: "Numero on liian lyhyt",
                number: "Vain numerot sallittu"
            },
            billDescription: {
                required: "Syötä kuvaus"
            },
            billTotal: {
                required: "Syötä laskun summa",
                number: "Vain numerot ja piste sallittu"
            }
        }
    });
});