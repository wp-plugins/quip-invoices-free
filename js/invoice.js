jQuery(document).ready(function ($)
{
    var $loading = $(".showLoading");
    $loading.hide();

    function ajax_post_payment(ajaxurl, form, doRedirect)
    {
        $loading.show();
        $('#error-message').hide();
        // Disable the submit button
        form.find('button').prop('disabled', true);
        $('#payInvoiceButton').text(quip_invoices.strings.processingPayment);

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

                form.find('button').prop('disabled', false);
                $('#payInvoiceButton').text(quip_invoices.strings.payNowByCreditCard);

                if (data.success)
                {
                    $('#success-message-text').text(quip_invoices.strings.paymentSuccessMsg);
                    $('#success-message').show();

                    if (doRedirect)
                    {
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1500);
                    }
                }
                else
                {
                    // show the errors on the form
                    $('#error-message-text').text(data.msg);
                    $('#error-message').show();
                }
            }
        });
    }
    ////////////////////////////


    /////////////// Payment ///////////////////
    var handler = StripeCheckout.configure({
        key: quip_invoices.stripePublicKey,
        token: function (token)
        {
            var $form = $("#invoicePaymentForm");
            $form.append("<input type='hidden' name='stripeToken' value='" + token.id + "' />");
            //post form via ajax
            ajax_post_payment(quip_invoices.ajaxurl, $form, true);
        }
    });

    $("#invoicePaymentForm").submit(function (e)
    {
        e.preventDefault();

        var $amount = quip_invoices.invoiceAmount;

        handler.open({
            name: quip_invoices.companyName,
            description: quip_invoices.strings.invoicePaymentDesc + " " + quip_invoices.invoiceNumber,
            amount: $amount,
            panelLabel: quip_invoices.strings.panelLabelPayNow,
            billingAddress: false,
            allowRememberMe: false,
            currency: quip_invoices.invoiceCurrency,
            email: quip_invoices.invoiceEmail
        });

        return false;
    });

});