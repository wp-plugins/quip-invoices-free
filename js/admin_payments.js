jQuery(document).ready(function ($)
{
    var $loading = $(".showLoading");
    $loading.hide();

    $("#paymentDate").datepicker({
        dateFormat: "DD, d MM, yy",
        altField: "#paymentDateDB",
        altFormat: "yy-mm-dd"
    }).datepicker('setDate', new Date());

    ////////////////// Helpers ///////////////////////

    function fsa_do_ajax_post(ajaxurl, form, successMessage, doRedirect)
    {
        $loading.show();
        // Disable the submit button
        form.find('button').prop('disabled', true);

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();
                document.body.scrollTop = document.documentElement.scrollTop = 0;

                if (data.success)
                {
                    fsa_showUpdate(successMessage);
                    form.find('button').prop('disabled', false);
                    fsa_resetForm(form);

                    if (doRedirect)
                    {
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1000);
                    }
                }
                else
                {
                    // re-enable the submit button
                    form.find('button').prop('disabled', false);
                    // show the errors on the form
                    fsa_showError(data.msg);
                }
            }
        });
    }

    ////////////////////////////

    $('#quip-invoices-create-payment-form').submit(function ()
    {
        fsa_clearUpdateAndError();
        if (!fsa_validField($('#paymentAmount'), quip_invoices.strings.paymentAmount))
        {
            return false;
        }

        var $form = $(this);
        //post form via ajax
        fsa_do_ajax_post(quip_invoices.ajaxurl, $form, quip_invoices.strings.paymentMsgAdded, true);
        return false;
    });


    $('#quip-invoices-create-payment-form').find('#invoiceID').change(function ()
    {
        $('#invoiceOwed').text(quip_invoices.symbol  + $('#invoiceID option:selected').attr('data-amount'));
        $('#paymentAmount').keyup();
    }).change();

    $('#paymentAmount').keyup(function ()
    {
        var decAmount = parseFloat($(this).val() / 100);
        var remaining = parseFloat($('#invoiceID option:selected').attr('data-amount')) - decAmount;
        $('#invoiceOwedRemaining').text(quip_invoices.symbol + String(remaining.toFixed(2)));
    }).keyup();

    function delete_payment()
    {
        $loading.show();
        var id = $(document).data('paymentID');

        $.ajax({
            type: "POST",
            url: quip_invoices.ajaxurl,
            data: {"action": "quip_invoices_delete_payment", "id": id},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#deletePaymentDialog").dialog("close");
                $loading.hide();
                fsa_showUpdate(quip_invoices.strings.paymentMsgDeleted);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#deletePaymentDialog").dialog({
        autoOpen: false,
        height: 220,
        width: 350,
        modal: true,
        buttons: [
            {
                text: quip_invoices.strings.yes,
                click: delete_payment
            },
            {
                text: quip_invoices.strings.no,
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('.delete-payment').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('paymentID', id);
        $("#deletePaymentDialog").dialog("open");
        return false;
    });

});