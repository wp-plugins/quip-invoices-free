jQuery(document).ready(function ($)
{
    var $loading = $(".showLoading");
    $loading.hide();

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

    $('#quip-invoices-create-client-form').submit(function ()
    {
        fsa_clearUpdateAndError();
        if (!fsa_validField($('#clientName'), quip_invoices.strings.clientName) || !fsa_validField($('#clientEmail'), quip_invoices.strings.clientEmail))
        {
            return false;
        }

        var $form = $(this);
        //post form via ajax
        fsa_do_ajax_post(quip_invoices.ajaxurl, $form, quip_invoices.strings.clientMsgSaved, true);
        return false;
    });

    function delete_client()
    {
        $loading.show();
        var id = $(document).data('clientID');

        $.ajax({
            type: "POST",
            url: quip_invoices.ajaxurl,
            data: {"action": "quip_invoices_delete_client", "id": id},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#deleteClientDialog").dialog("close");
                $loading.hide();
                fsa_showUpdate(quip_invoices.strings.clientMsgDeleted);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#deleteClientDialog").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        buttons: [
            {
                text: quip_invoices.strings.yes,
                click: delete_client
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

    $('.delete-client').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('clientID', id);
        $("#deleteClientDialog").dialog("open");
        return false;
    });

});