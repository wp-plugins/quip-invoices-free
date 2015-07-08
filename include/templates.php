<?php

/** Helper functions for creating invoice templates */

if (!function_exists('qu_in_standard_js_vars'))
{
    /**
     * Get standard values needed by javascript code
     *
     * @return mixed|void
     */
    function qu_in_standard_js_vars()
    {
        $options = get_option('quip_invoices_options');
        $localeStrings = QuipInvoices::getInstance()->get_locale_strings();

        return apply_filters('quip_invoices_standard_js_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'companyName' => stripslashes($options['companyName']),
            'symbol' => $localeStrings['symbol']
        ));
    }
}

if (!function_exists('qu_in_invoice_js_vars'))
{
    /**
     * Get data needed by javascript code for the invoice template page
     *
     * @param $invoice
     * @return mixed|void
     */
    function qu_in_invoice_js_vars($invoice)
    {
        $options = get_option('quip_invoices_options');
        $localeStrings = QuipInvoices::getInstance()->get_locale_strings();
        $publicKey = $options['publishKey_live'];
        if ($options['apiMode'] == 'test') $publicKey = $options['publishKey_test'];

        $client = QuipInvoices::getInstance()->db->get_client($invoice->clientID);
        return apply_filters('quip_invoices_invoice_js_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'symbol' => $localeStrings['symbol'],
            'stripePublicKey' => $publicKey,
            'companyName' => stripslashes($options['companyName']),
            'invoiceNumber' => $invoice->invoiceNumber,
            'invoiceAmount' => floatval($invoice->owed * 100),
            'invoiceCurrency' => $options['currency'],
            'invoiceEmail' => $client->clientEmail,
            'strings' => QuipInvoices::getInstance()->get_invoice_template_strings()
        ));
    }
}

if (!function_exists('qu_in_invoice_header'))
{
    /**
     * Output the headers for the invoice template
     *
     * @param bool $invoice
     */
    function qu_in_invoice_header($invoice = false)
    {
        ?>
        <link rel="stylesheet" href="<?php echo QUIP_INVOICES_CSS_DIR ?>bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo QUIP_INVOICES_CSS_DIR ?>bootstrap-theme.min.css">
        <link rel="stylesheet" href="<?php echo QUIP_INVOICES_CSS_DIR ?>quip-invoices.css" type="text/css" media="screen">
        <link rel="stylesheet" href="<?php echo QUIP_INVOICES_CSS_DIR ?>quip-invoices-print.css" type="text/css" media="print">
        <?php
            do_action('quip_invoices_invoice_header_styles');
        ?>
        <script type="text/javascript" src="<?php echo site_url() ?>/wp-includes/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo site_url() ?>/wp-includes/js/jquery/jquery-migrate.min.js"></script>
        <?php
            do_action('quip_invoices_invoice_header_scripts');
        ?>
        <script type="text/javascript" src="<?php echo QUIP_INVOICES_JS_DIR ?>invoice.js"></script>
        <script type="text/javascript">
            /* <![CDATA[ */
            var quip_invoices = <?php echo wp_json_encode( (($invoice) ? qu_in_invoice_js_vars($invoice) : qu_in_standard_js_vars()) ); ?>;
            /* ]]> */
        </script>
    <?php

        do_action('quip_invoices_invoice_header');
    }
}

if (!function_exists('qu_in_default_email_message'))
{

    /**
     * Default html email message for invoices
     *
     * @return string
     */
    function qu_in_default_email_message()
    {
        $html = "<h2>" . __("Your invoice is now available", 'quip-invoices') . "</h2>\n";
        $html .= "<p>" . __("Please click on the link below to view and pay the invoice.", 'quip-invoices') . "</p>\n";
        $html .= "<span><strong>" . __("Amount", 'quip-invoices') . ": </strong> %%INVOICE_AMOUNT%% </span><br />\n";
        $html .= "<span><strong>" . __("Due Date", 'quip-invoices') . ": </strong> %%INVOICE_DUE_DATE%% </span><br />\n";
        $html .= "<p>%%INVOICE_LINK%%</p>\n";
        $html .= "<p>" . __("Thank you for your business", 'quip-invoices') . "</p>\n";
        $html .= "<p>%%COMPANY_DETAILS%%</p>\n";

        return $html;
    }
}

if (!function_exists('qu_in_default_email_reminder_message'))
{
    /**
     * Default html email message for invoice reminders
     *
     * @return string
     */
    function qu_in_default_email_reminder_message()
    {
        $html = "<h2>" . __("Reminder: You have an invoice waiting to be paid", 'quip-invoices') . "</h2>\n";
        $html .= "<p>" . __("Please click on the link below to view and pay the invoice.", 'quip-invoices') . "</p>\n";
        $html .= "<span><strong>" . __("Amount", 'quip-invoices') . ": </strong> %%INVOICE_AMOUNT%% </span><br />\n";
        $html .= "<span><strong>" . __("Due Date", 'quip-invoices') . ": </strong> %%INVOICE_DUE_DATE%% </span><br />\n";
        $html .= "<p>%%INVOICE_LINK%%</p>\n";
        $html .= "<p>" . __("Thank you for your business", 'quip-invoices') . "</p>\n";
        $html .= "<p>%%COMPANY_DETAILS%%</p>\n";

        return $html;
    }
}

if (!function_exists('qu_in_default_quote_email_message'))
{
    /**
     * Default html email message for quote emails
     *
     * @return string
     */
    function qu_in_default_quote_email_message()
    {
        $html = "<h2>" . __("Your quote is now available", 'quip-invoices') . "</h2>\n";
        $html .= "<p>" . __("Please click on the link below to view the quote.", 'quip-invoices') . "</p>\n";
        $html .= "<span><strong>" . __("Grand Total", 'quip-invoices') . ": </strong> %%INVOICE_AMOUNT%% </span><br />\n";
        $html .= "<p>%%INVOICE_LINK%%</p>\n";
        $html .= "<p>" . __("We look forward to working together", 'quip-invoices') . "</p>\n";
        $html .= "<p>%%COMPANY_DETAILS%%</p>\n";

        return $html;
    }
}

if (!function_exists('qu_in_notification_email_message'))
{
    function qu_in_notification_email_message()
    {
        $html = "<h2>" . __("Invoice Notification", 'quip-invoices') . "</h2>\n";
        $html .= "<p>" . __("We're just letting you know that the following recently occurred", 'quip-invoices') . ":</p>\n";
        $html .= "<span><strong>" . __("Event", 'quip-invoices') . ": </strong> %%NOTIFICATION%% </span><br />\n";
        $html .= "<span><strong>" . __("Invoice Number", 'quip-invoices') . ": </strong> %%INVOICE_NUMBER%% </span><br />\n";
        $html .= "<span><strong>" . __("Date", 'quip-invoices') . ": </strong> %%NOTIFICATION_DATE%% </span><br />\n";
        $html .= "<p>%%NOTIFICATION_LINK%%</p>\n";
        $html .= "<p>" . __("Sent from", 'quip-invoices') . ' ' . get_bloginfo() . ' ' . __("by Quip Invoices", 'quip-invoices') . "</p>\n";

        return $html;
    }
}