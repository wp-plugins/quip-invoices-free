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

    // Settings page form
    $('#quip-invoices-settings-form').submit(function ()
    {
        fsa_clearUpdateAndError();
        if (!fsa_validField($('#companyName'), quip_invoices.strings.companyName) || !fsa_validField($('#companyEmail'), quip_invoices.strings.companyEmail))
        {
            return false;
        }

        var $form = $(this);
        //post form via ajax
        fsa_do_ajax_post(quip_invoices.ajaxurl, $form, quip_invoices.strings.settingsMsgUpdated, true);
        return false;
    });

    $('#quip-invoices-email-settings-form').submit(function ()
    {
        //force the tinymce instances to save the content - in case user makes changes without clicking anything else.
        tinymce.triggerSave();

        fsa_clearUpdateAndError();
        if (!fsa_validField($('#emailDefaultSubject'), quip_invoices.strings.emailSubject) || !fsa_validField($('#emailDefaultMessage'), quip_invoices.strings.emailMessage) ||
            !fsa_validField($('#emailDefaultReminderSubject'), quip_invoices.strings.reminderEmailSubject) || !fsa_validField($('#emailDefaultReminderMessage'), quip_invoices.strings.reminderEmailMessage) ||
            !fsa_validField($('#emailDefaultQuoteSubject'), quip_invoices.strings.emailQuoteSubject) || !fsa_validField($('#emailDefaultQuoteMessage'), quip_invoices.strings.emailQuoteMessage))
        {
            return false;
        }

        var $form = $(this);
        //post form via ajax
        fsa_do_ajax_post(quip_invoices.ajaxurl, $form, quip_invoices.strings.settingsMsgUpdated, true);
        return false;
    });

    $('#quip-invoices-payment-settings-form').submit(function ()
    {
        fsa_clearUpdateAndError();
        var $form = $(this);
        //post form via ajax
        fsa_do_ajax_post(quip_invoices.ajaxurl, $form, quip_invoices.strings.settingsMsgUpdated, true);
        return false;
    });

    //for uploading images using WordPress media library
    var custom_uploader;
    function uploadImage(inputID, imgSrc, showImg, showImgID)
    {
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader)
        {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title:'Choose Image',
            button:{
                text:'Choose Image'
            },
            multiple:false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function ()
        {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $(inputID).val(attachment.url);
            if(showImg)
            {
                $(imgSrc).attr('src', attachment.url);
                $(showImgID).show();
            }
        });

        //Open the uploader dialog
        custom_uploader.open();
    }

    // show the image if it exists
    if ($('#companyLogoSrc').attr('src') != "")
        $('#companyLogoImage').show();

    //upload company logo image
    $('#uploadImageButton').click(function (e)
    {
        e.preventDefault();
        uploadImage('#companyLogo', '#companyLogoSrc', true, '#companyLogoImage');
    });

    $('#clearLogo').click(function(e)
    {
        e.preventDefault();
        $('#companyLogo').val("");
        $('#companyLogoImage').hide();
        return false;
    })
});