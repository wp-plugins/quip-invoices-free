<?php

/*
Plugin Name: Quip Invoices Free
Plugin URI: http://invoicingplugin.com
Description: Free version of our fully featured invoicing system built directly into your WordPress website
Author: QuipCode
Version: 1.0.0
Author URI: http://quipcode.com
Text Domain: quip-invoices
*/

//defines
if (!defined('QUIP_INVOICES_NAME'))
    define('QUIP_INVOICES_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('QUIP_INVOICES_BASENAME'))
    define('QUIP_INVOICES_BASENAME', plugin_basename(__FILE__));

if (!defined('QUIP_INVOICES_DIR'))
    define('QUIP_INVOICES_DIR', WP_PLUGIN_DIR . '/' . QUIP_INVOICES_NAME);

if (!defined('QUIP_INVOICES_JS_DIR'))
    define('QUIP_INVOICES_JS_DIR', plugins_url('js/', __FILE__));

if (!defined('QUIP_INVOICES_CSS_DIR'))
    define('QUIP_INVOICES_CSS_DIR', plugins_url('css/', __FILE__));

if (!class_exists('QuipInvoices'))
{
    class QuipInvoices
    {
        public static $instance;
        public static $VERSION = '1.0.0';
        public $adminMenu, $db, $invoice, $client, $payment, $quote;
        private $options = 'quip_invoices_options';

        /**
         * Get the singleton class instance
         *
         * @return QuipInvoices singleton
         */
        public static function getInstance()
        {
            if (is_null(self::$instance))
            {
                self::$instance = new QuipInvoices();
            }
            return self::$instance;
        }

        /**
         * constructor for QuipInvoices
         */
        function __construct()
        {
            $this->includes();
            $this->hooks();

            $this->adminMenu = new QuipInvoicesAdminMenu();
            $this->db = new QuipInvoicesDatabase();
            $this->invoice = new QuipInvoicesInvoice();
            $this->client = new QuipInvoicesClient();
            $this->payment = new QuipInvoicesPayment();

            $options = get_option('quip_invoices_options');
            //set API key
            if ($options['apiMode'] === 'test')
            {
                $this->set_stripe_api_key($options['secretKey_test']);
            }
            else
            {
                $this->set_stripe_api_key($options['secretKey_live']);
            }
        }

        /**
         * Include any required files
         */
        function includes()
        {
            include_once 'include/database.php';
            include_once 'include/admin-menu.php';
            include_once 'include/templates.php';
            include_once 'include/invoice.php';
            include_once 'include/client.php';
            include_once 'include/payment.php';

            do_action('quip_invoices_includes');
        }

        /**
         * Define our hooks and filters
         */
        function hooks()
        {
            // Hook to show welcome screen if new activation
            add_action('admin_init', array($this, 'redirect_welcome_screen'), 1);
            //catch invoice requests
            add_action('template_redirect', array($this, 'invoice_listener'));
            //ajax hooks for saving settings
            add_action('wp_ajax_quip_invoice_update_settings', array($this, 'update_settings'));
            add_action('wp_ajax_quip_invoice_update_email_settings', array($this, 'update_email_settings'));
            add_action('wp_ajax_quip_invoice_update_payment_settings', array($this, 'update_payment_settings'));
            // Load the plugin text domain for translations
            add_action('plugins_loaded', array($this, 'load_textdomain'));
            // Action to load payment processor header script(s)
            add_action('quip_invoices_invoice_header_scripts', array($this, 'include_payment_processor_scripts'));
            // action for email notification scheduled events
            add_action('quip_notification_hook', array($this, 'send_email'), 10, 3);

            do_action('quip_invoices_hooks');
        }

        /**
         * Called on plugin activation
         */
        public static function activate()
        {
            QuipInvoices::setup_database();
            QuipInvoices::setup_plugin_options();
            // Activate our welcome page
            set_transient('qi_show_welcome_page', 1, 30);

            do_action('quip_invoices_activate');
        }

        /**
         * Called on plugin deactivation
         */
        public static function deactivate()
        {
            do_action('quip_invoices_deactivate');
        }

        /**
         * Set the option defaults on activation
         */
        public static function setup_plugin_options()
        {
            $options = get_option('quip_invoices_options');
            if (!$options)
            {
                $options['companyName'] = get_bloginfo('name');
                $options['companyEmail'] = get_bloginfo('admin_email');
                $options['companyPhone'] = '';
                $options['companyAddress1'] = '';
                $options['companyAddress2'] = '';
                $options['companyCity'] = '';
                $options['companyState'] = '';
                $options['companyZip'] = '';
                $options['companyCountry'] = '';
                $options['companyLogo'] = '';
                $options['emailDefaultSubject'] = __('Your invoice from', 'quip-invoices') . ' ' . get_bloginfo('name');
                $options['emailDefaultReminderSubject'] = __('Reminder: Your invoice from', 'quip-invoices') . ' ' . get_bloginfo('name');
                $options['emailDefaultMessage'] = base64_encode(qu_in_default_email_message());
                $options['emailDefaultReminderMessage'] = base64_encode(qu_in_default_email_reminder_message());
                $options['emailDefaultQuoteSubject'] = __('Your quote from', 'quip-invoices') . ' ' . get_bloginfo('name');
                $options['emailDefaultQuoteMessage'] = base64_encode(qu_in_default_quote_email_message());
                $options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
                $options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
                $options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
                $options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
                $options['apiMode'] = 'test';
                $options['currency'] = 'usd';
                $options['nextInvoiceNumber'] = 1;
                $options['paymentProcessor'] = 'stripe';
                $options['sendNotifications'] = 0;
                $options['version'] = QuipInvoices::$VERSION;
            }
            else if ($options['version'] !== QuipInvoices::$VERSION)
            {
                if (!array_key_exists('companyName', $options)) $options['companyName'] = get_bloginfo('name');
                if (!array_key_exists('companyEmail', $options)) $options['companyEmail'] = get_bloginfo('admin_email');
                if (!array_key_exists('companyPhone', $options)) $options['companyPhone'] = '';
                if (!array_key_exists('companyAddress1', $options)) $options['companyAddress1'] = '';
                if (!array_key_exists('companyAddress2', $options)) $options['companyAddress2'] = '';
                if (!array_key_exists('companyCity', $options)) $options['companyCity'] = '';
                if (!array_key_exists('companyState', $options)) $options['companyState'] = '';
                if (!array_key_exists('companyZip', $options)) $options['companyZip'] = '';
                if (!array_key_exists('companyCountry', $options)) $options['companyCountry'] = '';
                if (!array_key_exists('companyLogo', $options)) $options['companyLogo'] = '';
                if (!array_key_exists('emailDefaultSubject', $options)) $options['emailDefaultSubject'] = __('Your invoice from', 'quip-invoices') . ' ' . get_bloginfo('name');
                if (!array_key_exists('emailDefaultReminderSubject', $options)) $options['emailDefaultReminderSubject'] = __('Reminder: Your invoice from', 'quip-invoices') . ' ' . get_bloginfo('name');
                if (!array_key_exists('emailDefaultMessage', $options)) $options['emailDefaultMessage'] = base64_encode(qu_in_default_email_message());
                if (!array_key_exists('emailDefaultReminderMessage', $options)) $options['emailDefaultReminderMessage'] = base64_encode(qu_in_default_email_reminder_message());
                if (!array_key_exists('emailDefaultQuoteSubject', $options)) $options['emailDefaultQuoteSubject'] = __('Your quote from', 'quip-invoices') . ' ' . get_bloginfo('name');
                if (!array_key_exists('emailDefaultQuoteMessage', $options)) $options['emailDefaultQuoteMessage'] = base64_encode(qu_in_default_quote_email_message());
                if (!array_key_exists('secretKey_test', $options)) $options['secretKey_test'] = 'YOUR_TEST_SECRET_KEY';
                if (!array_key_exists('publishKey_test', $options)) $options['publishKey_test'] = 'YOUR_TEST_PUBLISHABLE_KEY';
                if (!array_key_exists('secretKey_live', $options)) $options['secretKey_live'] = 'YOUR_LIVE_SECRET_KEY';
                if (!array_key_exists('publishKey_live', $options)) $options['publishKey_live'] = 'YOUR_LIVE_PUBLISHABLE_KEY';
                if (!array_key_exists('apiMode', $options)) $options['apiMode'] = 'test';
                if (!array_key_exists('currency', $options)) $options['currency'] = 'usd';
                if (!array_key_exists('nextInvoiceNumber', $options)) $options['nextInvoiceNumber'] = 1;
                if (!array_key_exists('paymentProcessor', $options)) $options['paymentProcessor'] = 'stripe';
                if (!array_key_exists('sendNotifications', $options)) $options['sendNotifications'] = 0;

                $options['version'] = QuipInvoices::$VERSION;
            }

            update_option('quip_invoices_options', $options);
        }

        /**
         * Setup the database on activation
         */
        public static function setup_database()
        {
            include_once 'include/database.php';
            QuipInvoicesDatabase::setup_db();
        }

        /**
         * Load the plugin text domain
         */
        function load_textdomain()
        {
            load_plugin_textdomain(
                'quip-invoices',
                false,
                QUIP_INVOICES_NAME . '/languages/');
        }

        /**
         * Redirect to the about/welcome page on activation
         */
        function redirect_welcome_screen()
        {
            // only do this if the user can activate plugins
            if (!current_user_can('manage_options'))
                return;

            // don't do anything if the transient isn't set
            if (!get_transient('qi_show_welcome_page'))
                return;

            delete_transient('qi_show_welcome_page');
            wp_safe_redirect(admin_url('admin.php?page=quip-invoices-about'));
            exit;
        }

        /**
         * Template redirect listener to check for our invoice query string parameter
         * and display the invoice template
         */
        public function invoice_listener()
        {
            // if this is not a request for invoice
            if (!isset($_GET['qinvoice']) || $_GET['qinvoice'] == '')
                return;

            // grab the invoice, also run it through filter for extensions
            $invoice = apply_filters('quip_invoices_full_invoice', $this->db->get_full_invoice_by_hash($_GET['qinvoice']));

            if ($invoice && $invoice->active == 1)
            {
                $options = get_option($this->options);

                //dont track or allow payment if admin view
                if (isset($_GET['view']))
                {
                    $adminView = true;
                }
                else
                {
                    $adminView = false;
                    if (!$invoice->viewed)
                    {
                        $this->db->update_invoice($invoice->invoiceID, array('viewed' => date('Y-m-d H:i:s')));
                        if ($options['sendNotifications'] == 1) $this->send_notification(__('Viewed', 'quip-invoices'), $invoice);
                    }
                }

                $localeStrings = QuipInvoices::getInstance()->get_locale_strings();

                if ($invoice->type == 'invoice')
                {
                    $payments = $this->db->get_payments($invoice->invoiceID);
                    //output invoice
                    ob_start();
                    include 'view/invoice_template.php';
                    echo apply_filters('quip_invoices_invoice_template', ob_get_clean());
                    exit;
                }
            }
        }

        /**
         * Update the plugin settings via ajax
         */
        public function update_settings()
        {
            // Save the posted value in the database
            $options = get_option($this->options);
            $options['companyName'] = sanitize_text_field($_POST['companyName']);
            $options['companyEmail'] = sanitize_text_field($_POST['companyEmail']);
            $options['companyPhone'] = sanitize_text_field($_POST['companyPhone']);
            $options['companyAddress1'] = sanitize_text_field($_POST['companyAddress1']);
            $options['companyAddress2'] = sanitize_text_field($_POST['companyAddress2']);
            $options['companyCity'] = sanitize_text_field($_POST['companyCity']);
            $options['companyState'] = sanitize_text_field($_POST['companyState']);
            $options['companyZip'] = sanitize_text_field($_POST['companyZip']);
            $options['companyCountry'] = sanitize_text_field($_POST['companyCountry']);
            $options['companyLogo'] = sanitize_text_field($_POST['companyLogo']);
            update_option($this->options, $options);

            $this->json_exit(true, '', admin_url('admin.php?page=quip-invoices-settings'));
        }

        /**
         * Update the plugin email settings via ajax
         */
        public function update_email_settings()
        {
            $this->json_exit(true, '', admin_url('admin.php?page=quip-invoices-settings&tab=email'));
        }

        /**
         * Update the plugin payment settings via ajax
         */
        public function update_payment_settings()
        {
            $options = get_option($this->options);

            $options['publishKey_test'] = sanitize_text_field($_POST['publishKey_test']);
            $options['secretKey_test'] = sanitize_text_field($_POST['secretKey_test']);
            $options['publishKey_live'] = sanitize_text_field($_POST['publishKey_live']);
            $options['secretKey_live'] = sanitize_text_field($_POST['secretKey_live']);
            $options['apiMode'] = sanitize_text_field($_POST['apiMode']);
            $options['currency'] = sanitize_text_field($_POST['currency']);

            update_option($this->options, $options);

            $this->json_exit(true, '', admin_url('admin.php?page=quip-invoices-settings&tab=payment'));
        }

        /**
         * Format company details for display inside a textarea OR for HTML pages
         *
         * @param bool $html
         * @return string
         */
        public function get_formatted_company_details($html = true)
        {
            $options = get_option($this->options);
            if ($html)
            {
                $detailsStr = ($options['companyName'] !== '') ? "<strong>" . stripslashes($options['companyName']) . "</strong><br />" : '';
                $detailsStr .= ($options['companyAddress1'] !== '') ? $options['companyAddress1'] . "<br />" : '';
                $detailsStr .= ($options['companyAddress2'] !== '') ? $options['companyAddress2'] . "<br />" : '';
                $detailsStr .= ($options['companyCity'] !== '') ? $options['companyCity'] . "<br />" : '';
                $detailsStr .= ($options['companyState'] !== '') ? $options['companyState'] . "<br />" : '';
                $detailsStr .= ($options['companyZip'] !== '') ? $options['companyZip'] . "<br />" : '';
                $detailsStr .= ($options['companyCountry'] !== '') ? $options['companyCountry'] . "<br />" : '';
                $detailsStr .= ($options['companyPhone'] !== '') ? $options['companyPhone'] . "<br />" : '';
                $detailsStr .= ($options['companyEmail'] !== '') ? $options['companyEmail'] . "<br />" : '';
            }
            else
            {
                $detailsStr = ($options['companyName'] !== '') ? stripslashes($options['companyName']) . "\n" : '';
                $detailsStr .= ($options['companyAddress1'] !== '') ? $options['companyAddress1'] . "\n" : '';
                $detailsStr .= ($options['companyAddress2'] !== '') ? $options['companyAddress2'] . "\n" : '';
                $detailsStr .= ($options['companyCity'] !== '') ? $options['companyCity'] . "\n" : '';
                $detailsStr .= ($options['companyState'] !== '') ? $options['companyState'] . "\n" : '';
                $detailsStr .= ($options['companyZip'] !== '') ? $options['companyZip'] . "\n" : '';
                $detailsStr .= ($options['companyCountry'] !== '') ? $options['companyCountry'] . "\n" : '';
                $detailsStr .= ($options['companyPhone'] !== '') ? $options['companyPhone'] . "\n" : '';
                $detailsStr .= ($options['companyEmail'] !== '') ? $options['companyEmail'] . "\n" : '';
            }

            return $detailsStr;
        }

        /**
         * HTML format client information for display on admin page/or invoice template
         * @param $clientID
         * @return string
         */
        public function get_formatted_client_details($clientID)
        {
            $details = '';
            $client = $this->db->get_client($clientID);
            if ($client)
            {
                $details .= ($client->clientContactName) ? stripslashes($client->clientContactName) . '<br />' : '';
                $details .= stripslashes($client->clientName) . '<br />';
                $details .= $client->clientEmail . '<br />';
                $details .= ($client->addressLine1) ? $client->addressLine1 . '<br />' : '';
                $details .= ($client->addressLine2) ? $client->addressLine2 . '<br />' : '';
                $details .= ($client->addressCity) ? $client->addressCity . '<br />' : '';
                $details .= ($client->addressState) ? $client->addressState . '<br />' : '';
                $details .= ($client->addressZip) ? $client->addressZip . '<br />' : '';
                $details .= ($client->addressCountry) ? $client->addressCountry : '';

            }
            return $details;
        }

        /**
         * Send a formatted HTML email using wp_mail and bloginfo
         *
         * @param $to
         * @param $subject
         * @param $message
         * @return bool Success
         */
        public function send_email($to, $subject, $message)
        {
        }

        public function send_notification($type, $invoice)
        {
        }

        /**
         * Convenience function for returning from our standard ajax request
         *
         * @param $success
         * @param $message
         * @param $redirectURL
         */
        public function json_exit($success, $message, $redirectURL)
        {
            header("Content-Type: application/json");
            echo json_encode(array('success' => $success, 'redirectURL' => $redirectURL, 'msg' => $message));
            exit;
        }

        /**
         * Helper to grab locale specific strings
         *
         * @param null|string $locale
         * @return array
         */
        public function get_locale_strings($locale = null)
        {
            if (!$locale)
            {
                $options = get_option('quip_invoices_options');
                $locale = $options['currency'];
            }

            //Default to USD
            $strings = array(
                'state' => __('State', 'quip-invoices'),
                'zip' => __('Zip', 'quip-invoices'),
                'symbol' => '$'
            );

            switch ($locale)
            {
                case 'gbp':
                    $strings['state'] = __('County', 'quip-invoices');
                    $strings['zip'] = __('Postcode', 'quip-invoices');
                    $strings['symbol'] = '£';
                    break;
                case 'cad':
                    $strings['state'] = __('Province', 'quip-invoices');
                    $strings['zip'] = __('Postal Code', 'quip-invoices');
                    break;
                case 'eur':
                    $strings['state'] = __('Region', 'quip-invoices');
                    $strings['zip'] = __('Zip / Postcode', 'quip-invoices');
                    $strings['symbol'] = '€';
                    break;
            }

            return apply_filters('quip_invoices_locale_strings', $strings);
        }

        /**
         * Get translated strings for invoice template javascript
         *
         * @return array
         */
        public function get_invoice_template_strings()
        {
            $strings = array();
            $strings['processingPayment'] = __("Processing Payment...", 'quip-invoices');
            $strings['payNowByCreditCard'] = __("Pay Now By Credit Card", 'quip-invoices');
            $strings['invoicePaymentDesc'] = __("Invoice Payment:", 'quip-invoices');
            $strings['panelLabelPayNow'] = __("Pay Now", 'quip-invoices');
            $strings['paymentSuccessMsg'] = __("Payment Success!  Thank you for your payment.", 'quip-invoices');
            return $strings;
        }

        /**
         * Simply increment the invoice number, nothing fancy here.
         *
         * @return int
         */
        public function increase_invoice_number()
        {
            $options = get_option($this->options);
            $options['nextInvoiceNumber'] += 1;
            update_option($this->options, $options);
            return $options['nextInvoiceNumber'];
        }

        /**
         * Check the options for payment processors and output the relevant scripts
         */
        public function include_payment_processor_scripts()
        {
            $options = get_option($this->options);
            // Stripe is the default included processor
            if ($options['paymentProcessor'] == 'stripe')
            {
                echo '<script src="https://checkout.stripe.com/checkout.js"></script>';
            }

            do_action('quip_invoices_include_payment_processor_scripts');
        }

        /**
         * Format the HTML input from the textarea so it will both look good in email
         * and inside the tinyMCE editor on the settings screen
         *
         * @param $emailHTML string
         * @return string
         */
        private function format_email_html_input($emailHTML)
        {
            return base64_encode($emailHTML);
        }

        private function set_stripe_api_key($key)
        {
            if ($key != '' && $key != 'YOUR_TEST_SECRET_KEY' && $key != 'YOUR_LIVE_SECRET_KEY')
            {
                try
                {
                    \Stripe\Stripe::setApiKey($key);
                }
                catch (Exception $e)
                {
                    //invalid key was set, ignore it
                }
            }
        }

    }//end class QuipInvoices
}

//Stripe PHP library
if (!class_exists('Stripe'))
{
    include_once('lib/stripe-php/init.php');
}

//Get the instance of QuipInvoices
QuipInvoices::getInstance();

// activation and deactivation hooks
register_activation_hook(__FILE__, array('QuipInvoices', 'activate'));
register_deactivation_hook(__FILE__, array('QuipInvoices', 'deactivate'));

