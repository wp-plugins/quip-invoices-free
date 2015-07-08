<?php

class QuipInvoicesAdminMenu
{
    private $capability = 'manage_options';

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        if (defined('QUIP_INVOICES_DEMO_MODE'))
            $this->capability = 'read';
    }

    /**
     * Initialize admin menu
     */
    public function init()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Create the admin menu and sub menu options, attach scripts and styles.
     */
    public function admin_menu()
    {
        // Add the top-level admin menu
        $page_title = 'Quip Invoices';
        $menu_title = 'Quip Invoices';
        $capability = $this->capability;
        $menu_slug = 'quip-invoices-invoices';
        $function = 'display_invoices';
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, $function), plugin_dir_url(dirname(__FILE__)) . '/img/icon.png');

        // Add submenu page with same slug as parent to ensure no duplicates
        $sub_menu_title = __('Invoices', 'quip-invoices');
        $menu_hook = add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, array($this, $function));
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts')); //this ensures script/styles only loaded for this plugin admin pages

        $submenu_page_title = __('Quotes', 'quip-invoices');
        $submenu_title = __('Quotes', 'quip-invoices');
        $submenu_slug = 'quip-invoices-quotes';
        $submenu_function = array($this, 'display_quotes');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('Payments', 'quip-invoices');
        $submenu_title = __('Payments', 'quip-invoices');
        $submenu_slug = 'quip-invoices-payments';
        $submenu_function = array($this, 'display_payments');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('Clients', 'quip-invoices');
        $submenu_title = __('Clients', 'quip-invoices');
        $submenu_slug = 'quip-invoices-clients';
        $submenu_function = array($this, 'display_clients');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = __('Settings', 'quip-invoices');
        $submenu_title = __('Settings', 'quip-invoices');
        $submenu_slug = 'quip-invoices-settings';
        $submenu_function = array($this, 'display_settings');
        $menu_hook = add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        // Help screen, no additional JS/CSS
        $submenu_page_title = __('Help', 'quip-invoices');
        $submenu_title = __('Help', 'quip-invoices');
        $submenu_slug = 'quip-invoices-help';
        $submenu_function = array($this, 'display_help');
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        $submenu_page_title = __('About', 'quip-invoices');
        $submenu_title = __('About', 'quip-invoices');
        $submenu_slug = 'quip-invoices-about';
        $submenu_function = array($this, 'display_about');
        add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        // don't show below on submenu (null for $menu_slug)
        $submenu_page_title = 'Edit';
        $submenu_title = 'Edit';
        $submenu_slug = 'quip-invoices-edit';
        $submenu_function = array($this, 'display_edit');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = 'Send';
        $submenu_title = 'Send';
        $submenu_slug = 'quip-invoices-send';
        $submenu_function = array($this, 'display_send');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = 'Details';
        $submenu_title = 'Details';
        $submenu_slug = 'quip-invoices-details';
        $submenu_function = array($this, 'display_details');
        $menu_hook = add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);
        add_action('admin_print_scripts-' . $menu_hook, array($this, 'admin_scripts'));

        $submenu_page_title = 'Export';
        $submenu_title = 'Export';
        $submenu_slug = 'quip-invoices-export';
        $submenu_function = array($this, 'display_export');
        add_submenu_page(null, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

        do_action('quip_invoices_admin_menu', $menu_slug);

    }

    /**
     * Attaches scripts and styles to submenu pages
     */
    public function admin_scripts()
    {
        wp_enqueue_script('date-js', plugins_url('/js/date.js', dirname(__FILE__)), array('jquery'));
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('numeral-js', plugins_url('/js/numeral.min.js', dirname(__FILE__)), array('jquery'));
        wp_enqueue_script('quip-invoices-adminutils-js', plugins_url('/js/adminutils.js', dirname(__FILE__)), array('jquery'));

        // and finally, the styles
        wp_enqueue_style('jquery-ui-css', plugins_url('/css/jquery-ui.min.css', dirname(__FILE__)));
        wp_enqueue_style('jquery-ui-structure-css', plugins_url('/css/jquery-ui.structure.min.css', dirname(__FILE__)));
        wp_enqueue_style('jquery-ui-theme-css', plugins_url('/css/jquery-ui.theme.min.css', dirname(__FILE__)));
        wp_enqueue_style('plugin-css', plugins_url('/css/quip-invoices.css', dirname(__FILE__)), false, 'screen');

        do_action('quip_invoices_admin_scripts');
    }

    /**
     * Display settings page
     */
    public function display_settings()
    {
        wp_enqueue_media();
        $this->admin_enqueue_and_localize('settings');

        include QUIP_INVOICES_DIR . '/view/settings_page.php';
    }

    /**
     * Display invoices page
     */
    public function display_invoices()
    {
        $this->admin_enqueue_and_localize('invoices');

        //load the table
        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('QuipInvoicesTableInvoices'))
        {
            require_once(QUIP_INVOICES_DIR . '/include/tables/quip-invoices-table-invoices.php');
        }

        $table = new QuipInvoicesTableInvoices();
        $table->prepare_items();

        include QUIP_INVOICES_DIR . '/view/invoices_page.php';
    }

    /**
     * Display quotes page
     */
    public function display_quotes()
    {
        include QUIP_INVOICES_DIR . '/view/quotes_page.php';
    }

    /**
     * Display payments page
     */
    public function display_payments()
    {
        $this->admin_enqueue_and_localize('payments');

        //load the table
        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('QuipInvoicesTablePayments'))
        {
            require_once(QUIP_INVOICES_DIR . '/include/tables/quip-invoices-table-payments.php');
        }

        $table = new QuipInvoicesTablePayments();
        $table->prepare_items();

        include QUIP_INVOICES_DIR . '/view/payments_page.php';
    }

    /**
     * Display clients page
     */
    public function display_clients()
    {
        $this->admin_enqueue_and_localize('clients');

        //load the table
        if (!class_exists('WP_List_Table'))
        {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }
        if (!class_exists('QuipInvoicesTableClients'))
        {
            require_once(QUIP_INVOICES_DIR . '/include/tables/quip-invoices-table-clients.php');
        }

        $table = new QuipInvoicesTableClients();
        $table->prepare_items();

        include QUIP_INVOICES_DIR . '/view/clients_page.php';
    }

    /**
     * Display edit page
     */
    public function display_edit()
    {
        if ($this->validate_id_and_type())
        {
            $type = $_GET['type'];
            $itemID = $_GET['id'];

            if ($type == 'invoice')
            {
                $invoice = QuipInvoices::getInstance()->db->get_full_invoice($itemID);
                if ($invoice)
                {
                    $this->admin_enqueue_and_localize('invoices', array('edit' => true));
                    include QUIP_INVOICES_DIR . '/view/edit_invoice_page.php';
                }
                else
                    include QUIP_INVOICES_DIR . '/view/error_page.php';
            }
            else if ($type == 'client')
            {
                $client = QuipInvoices::getInstance()->db->get_client($itemID);
                if ($client)
                {
                    $this->admin_enqueue_and_localize('clients', array('edit' => true));
                    include QUIP_INVOICES_DIR . '/view/edit_client_page.php';
                }
                else
                    include QUIP_INVOICES_DIR . '/view/error_page.php';
            }
            else if ($type == 'quote')
            {
                $invoice = QuipInvoices::getInstance()->db->get_full_invoice($itemID);
                if ($invoice)
                {
                    $this->admin_enqueue_and_localize('quotes', array('edit' => true));
                    include QUIP_INVOICES_DIR . '/view/edit_quote_page.php';
                }
                else
                    include QUIP_INVOICES_DIR . '/view/error_page.php';
            }
            else
            {
                include QUIP_INVOICES_DIR . '/view/error_page.php';
            }
        }
        else
            include QUIP_INVOICES_DIR . '/view/error_page.php';
    }

    /**
     * Display send email page
     */
    public function display_send()
    {
        if ($this->validate_id_and_type())
        {
            $type = $_GET['type'];
            $itemID = $_GET['id'];

            $invoice = QuipInvoices::getInstance()->db->get_full_invoice($itemID);

            if ($invoice)
            {
                if ($type == 'invoice')
                {
                    $this->admin_enqueue_and_localize('invoices');
                    include QUIP_INVOICES_DIR . '/view/send_invoice_page.php';
                }
                else if ($type == 'quote')
                {
                    $this->admin_enqueue_and_localize('quotes');
                    include QUIP_INVOICES_DIR . '/view/send_quote_page.php';
                }
                else
                    include QUIP_INVOICES_DIR . '/view/error_page.php';
            }
            else
                include QUIP_INVOICES_DIR . '/view/error_page.php';
        }
        else
            include QUIP_INVOICES_DIR . '/view/error_page.php';
    }

    /**
     * Display details page
     */
    public function display_details()
    {
        $valid = $this->validate_id_and_type();
        if ($valid)
        {
            $type = $_GET['type'];
            $itemID = $_GET['id'];

            if ($type == 'invoice')
            {
                $invoice = QuipInvoices::getInstance()->db->get_full_invoice($itemID);
                $payments = QuipInvoices::getInstance()->db->get_payments($itemID);
                if (!$invoice) $valid = false;
            }
            else if ($type == 'client')
            {
                $client = QuipInvoices::getInstance()->db->get_client($itemID);
                $invoices = QuipInvoices::getInstance()->db->get_invoices_for_client($itemID);
                if (!$client) $valid = false;
            }
            else
            {
                $valid = false;
            }
        }

        if ($valid)
        {
            $this->admin_enqueue_and_localize('invoices');
            $this->admin_enqueue_and_localize('clients');

            include QUIP_INVOICES_DIR . '/view/details_page.php';
        }
        else
            include QUIP_INVOICES_DIR . '/view/error_page.php';
    }

    /**
     * Display export page
     */
    public function display_export()
    {
        include QUIP_INVOICES_DIR . '/view/export.php';
    }

    /**
     * Display help page
     */
    public function display_help()
    {
        include QUIP_INVOICES_DIR . '/view/help_page.php';
    }

    /**
     * Display about page
     */
    public function display_about()
    {
        include QUIP_INVOICES_DIR . '/view/about_page.php';
    }

    /**
     * Assumes page follows naming convention of JS file, i.e. admin_{page}.js
     *
     * @param $page string Name to use follows convention
     * @param array $extraData Any extra parameters to localize
     */
    private function admin_enqueue_and_localize($page, $extraData = array())
    {
        $localeStrings = QuipInvoices::getInstance()->get_locale_strings();

        wp_enqueue_script("quip-invoices-admin-{$page}-js",
            plugins_url("/js/admin_{$page}.js", dirname(__FILE__)),
            array('quip-invoices-adminutils-js'));

        $localizeData = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pageurl' => admin_url("admin.php?page=quip-invoices-{$page}"),
            'symbol' => $localeStrings['symbol'],
            'strings' => $this->get_strings_for_page($page)
        );

        $localizeData = array_merge($localizeData, $extraData);

        wp_localize_script("quip-invoices-admin-{$page}-js", 'quip_invoices', $localizeData);
    }

    /**
     * Validate GET parameters for pages that contain different types
     *
     * @return bool
     */
    private function validate_id_and_type()
    {
        $itemID = -1;
        $type = "";
        if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT))
            $itemID = filter_var(trim($_GET['id']), FILTER_SANITIZE_NUMBER_INT);
        if (isset($_GET['type']))
            $type = filter_var(trim($_GET['type']), FILTER_SANITIZE_STRING);

        if ($itemID == -1 || $itemID == "" || !$itemID || !$type || $type == "")
            return false;

        return true;
    }

    /**
     * Get translated strings to pass down to the page for JS access
     *
     * @param $page
     * @return array
     */
    private function get_strings_for_page($page)
    {
        $strings = array();

        // general strings
        $strings['yes'] = __('Yes', 'quip-invoices');
        $strings['no'] = __('No', 'quip-invoices');
        $strings['add'] = __('Add', 'quip-invoices');
        $strings['delete'] = __('Delete', 'quip-invoices');
        $strings['noValue'] = __('must contain a value', 'quip-invoices');

        if ($page == 'invoices')
        {
            $strings['title'] = __('Title', 'quip-invoices');
            $strings['rate'] = __('Rate', 'quip-invoices');
            $strings['quantity'] = __('Quantity', 'quip-invoices');
            $strings['adj'] = __('Adj', 'quip-invoices');
            $strings['invoiceNumber'] = __('Invoice Number', 'quip-invoices');
            $strings['invoiceDate'] = __('Invoice Date', 'quip-invoices');
            $strings['dueDate'] = __('Due Date', 'quip-invoices');
            $strings['clientName'] = __('Client Name', 'quip-invoices');
            $strings['clientEmail'] = __('Client Email', 'quip-invoices');
            $strings['toAddress'] = __('To Address', 'quip-invoices');
            $strings['emailSubject'] = __('Email Subject', 'quip-invoices');
            $strings['emailMessage'] = __('Email Message', 'quip-invoices');
            $strings['invoiceMsgLineItemNumeric'] = __("Please use only numeric values for Rate, Quantity and Adjustment", 'quip-invoices');
            $strings['invoiceMsgClientSelected'] = __("You must select (or create) a client for this invoice.", 'quip-invoices');
            $strings['invoiceMsgLineItemMissing'] = __("You must have at least one line item.", 'quip-invoices');
            $strings['invoiceMsgSuccess'] = __("Invoice successful.", 'quip-invoices');
            $strings['invoiceMsgSent'] = __("Invoice sent.", 'quip-invoices');
            $strings['invoiceMsgCopied'] = __("Invoice copied.", 'quip-invoices');
            $strings['invoiceMsgDeleted'] = __("Invoice deleted.", 'quip-invoices');

        }
        else if ($page == 'quotes')
        {
            $strings['title'] = __('Title', 'quip-invoices');
            $strings['rate'] = __('Rate', 'quip-invoices');
            $strings['quantity'] = __('Quantity', 'quip-invoices');
            $strings['adj'] = __('Adj', 'quip-invoices');
            $strings['invoiceNumber'] = __('Quote Number', 'quip-invoices');
            $strings['invoiceDate'] = __('Quote Date', 'quip-invoices');
            $strings['dueDate'] = __('Due Date', 'quip-invoices');
            $strings['clientName'] = __('Client Name', 'quip-invoices');
            $strings['clientEmail'] = __('Client Email', 'quip-invoices');
            $strings['toAddress'] = __('To Address', 'quip-invoices');
            $strings['emailSubject'] = __('Email Subject', 'quip-invoices');
            $strings['emailMessage'] = __('Email Message', 'quip-invoices');
            $strings['invoiceMsgLineItemNumeric'] = __("Please use only numeric values for Rate, Quantity and Adjustment", 'quip-invoices');
            $strings['invoiceMsgClientSelected'] = __("You must select (or create) a client for this quote.", 'quip-invoices');
            $strings['invoiceMsgLineItemMissing'] = __("You must have at least one line item.", 'quip-invoices');
            $strings['invoiceMsgSuccess'] = __("Quote successful.", 'quip-invoices');
            $strings['invoiceMsgSent'] = __("Quote sent.", 'quip-invoices');
            $strings['invoiceMsgCopied'] = __("Quote copied.", 'quip-invoices');
            $strings['invoiceMsgDeleted'] = __("Quote deleted.", 'quip-invoices');
            $strings['invoiceMsgConverted'] = __("Quote converted.", 'quip-invoices');
        }
        else if ($page == 'clients')
        {
            $strings['clientName'] = __('Client Name', 'quip-invoices');
            $strings['clientEmail'] = __('Client Email', 'quip-invoices');
            $strings['clientMsgSaved'] = __('Client saved.', 'quip-invoices');
            $strings['clientMsgDeleted'] = __('Client deleted.', 'quip-invoices');
        }
        else if ($page == 'payments')
        {
            $strings['paymentAmount'] = __('Payment Amount', 'quip-invoices');
            $strings['paymentMsgAdded'] = __("Payment added.", 'quip-invoices');
            $strings['paymentMsgDeleted'] = __("Payment deleted.", 'quip-invoices');

        }
        else if ($page == 'settings')
        {
            $strings['companyName'] = __('Company Name', 'quip-invoices');
            $strings['companyEmail'] = __("Company Email", 'quip-invoices');
            $strings['emailSubject'] = __('Email Subject', 'quip-invoices');
            $strings['emailMessage'] = __('Email Message', 'quip-invoices');
            $strings['reminderEmailSubject'] = __('Reminder Email Subject', 'quip-invoices');
            $strings['reminderEmailMessage'] = __('Reminders Email Message', 'quip-invoices');
            $strings['emailQuoteSubject'] = __('Quote Email Subject', 'quip-invoices');
            $strings['emailQuoteMessage'] = __('Quote Email Message', 'quip-invoices');
            $strings['settingsMsgUpdated'] = __("Settings updated.", 'quip-invoices');
        }

        return apply_filters('quip_invoices_admin_strings_for_page', $strings);
    }
}