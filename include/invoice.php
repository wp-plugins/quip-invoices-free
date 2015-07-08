<?php
include_once 'base.controller.php';
include_once 'models/invoice_status.php';

if (!class_exists('QuipInvoicesInvoice'))
{
    class QuipInvoicesInvoice extends QuipInvoicesController
    {
        public function __construct()
        {
            parent::__construct();

            //ajax hook for create invoice form
            add_action('wp_ajax_quip_invoices_create_invoice', array($this, 'create_invoice'));
            //ajax hook for edit invoice form
            add_action('wp_ajax_quip_invoices_edit_invoice', array($this, 'edit_invoice'));
            //copy an invoice
            add_action('wp_ajax_quip_invoices_copy_invoice', array($this, 'copy_invoice'));
            //send an invoice
            add_action('wp_ajax_quip_invoices_send_invoice', array($this, 'send_invoice'));
            //delete an invoice
            add_action('wp_ajax_quip_invoices_delete_invoice', array($this, 'delete_invoice'));
        }

        /**
         * Handle create invoice form submission
         */
        public function create_invoice()
        {
            // we must have line items defined
            $lineItems = json_decode(base64_decode($_POST['lineItems']));
            if (!count($lineItems)) $this->json_exit(false, __("Please enter at least one line item.", 'quip-invoices'), '');

            // check that we've not used this invoice number before
            $invoiceNumber = filter_var(trim($_POST['invoiceNumber']), FILTER_SANITIZE_STRING);
            $invoice = $this->find_invoice($invoiceNumber);
            if ($invoice) $this->json_exit(false, __("This invoice number has already been used.", 'quip-invoices'), '');

            $clientID = sanitize_text_field($_POST['invoiceClient']);
            $taxRate = sanitize_text_field($_POST['invoiceTaxRate']);

            $invoiceData = array(
                'invoiceNumber' => $invoiceNumber,
                'invoiceDate' => date("Y-m-d", strtotime(sanitize_text_field($_POST['invoiceCreateDateDB']))),
                'dueDate' => date("Y-m-d", strtotime(sanitize_text_field($_POST['invoiceDueDateDB']))),
                'clientID' => $clientID,
                'notes' => '',
                'hash' => $this->generate_invoice_hash($invoiceNumber, $clientID),
                'companyDetails' => $this->format_company_details($_POST['companyDetails']),
                'tax' => $taxRate,
                'paymentTypes' => '1',
                'paymentInstructions' => '',
                'allowPartialPayment' => 0,
                'type' => 'invoice',
                'created' => date('Y-m-d H:i:s')
            );

            //create invoice
            $id = $this->db->insert_invoice($invoiceData);
            //create line items
            $invoiceSubTotal = 0.0;
            foreach ($lineItems as $li)
            {
                $lineItemData = json_decode($li, true);
                $lineItemData['created'] = date('Y-m-d H:i:s');
                $lineItemData['invoiceID'] = $id;
                $this->db->insert_line_item($lineItemData);
                $invoiceSubTotal = $invoiceSubTotal + floatval($lineItemData['total']);
            }

            //update totals
            $total = $invoiceSubTotal * ((100 + floatval($taxRate)) / 100);
            $this->db->update_invoice($id, array(
                'subTotal' => $invoiceSubTotal,
                'total' => $total,
                'owed' => $total
            ));

            //update the counter
            QuipInvoices::getInstance()->increase_invoice_number();

            $this->json_exit(true, '', admin_url('admin.php?page=quip-invoices-invoices&tab=view'));
        }

        /**
         * Handle edit invoice form submission.
         */
        public function edit_invoice()
        {
            $invoiceID = sanitize_text_field($_POST['invoiceID']);
            // we must have line items defined
            $lineItems = json_decode(base64_decode($_POST['lineItems']));
            if (!count($lineItems)) $this->json_exit(false, __("Please enter at least one line item.", 'quip-invoices'), '');

            // check that we've not used this invoice number on a different invoice
            $invoiceNumber = filter_var(trim($_POST['invoiceNumber']), FILTER_SANITIZE_STRING);
            $invoice = $this->find_invoice($invoiceNumber);
            if ($invoice && $invoice->invoiceID != $invoiceID)
                $this->json_exit(false, __("This invoice number is used by another invoice.", 'quip-invoices'), '');

            //delete and re-create line items
            $this->db->hard_remove_invoice_line_items($invoiceID);
            //create line items
            $invoiceSubTotal = 0.0;
            foreach ($lineItems as $li)
            {
                $lineItemData = json_decode($li, true);
                $lineItemData['created'] = date('Y-m-d H:i:s');
                $lineItemData['invoiceID'] = $invoiceID;
                $this->db->insert_line_item($lineItemData);
                $invoiceSubTotal = $invoiceSubTotal + floatval($lineItemData['total']);
            }

            //invoice data
            $taxRate = sanitize_text_field($_POST['invoiceTaxRate']);
            $total = $invoiceSubTotal * ((100 + floatval($taxRate)) / 100);
            $invoiceData = array(
                'invoiceNumber' => $invoiceNumber,
                'invoiceDate' => date("Y-m-d", strtotime(sanitize_text_field($_POST['invoiceCreateDateDB']))),
                'dueDate' => date("Y-m-d", strtotime(sanitize_text_field($_POST['invoiceDueDateDB']))),
                'notes' => '',
                'companyDetails' => $this->format_company_details($_POST['companyDetails']),
                'tax' => $taxRate,
                'paymentTypes' => '1',
                'paymentInstructions' => '',
                'allowPartialPayment' => 0,
                'type' => 'invoice',
                'subTotal' => $invoiceSubTotal,
                'total' => $total
            );

            //can't change client for certain invoice statuses
            if (isset($_POST['invoiceClient'])) $invoiceData['clientID'] = sanitize_text_field($_POST['invoiceClient']);

            //update invoice
            $this->db->update_invoice($invoiceID, $invoiceData);

            //now recalculate owed just in case payments were received before this invoice was edited
            $this->db->update_invoice($invoiceID, array('owed' => $this->db->get_invoice_amount_outstanding($invoiceID)));

            $this->json_exit(true, '', admin_url('admin.php?page=quip-invoices-invoices&tab=view'));

        }

        /**
         * Handle submission from copy invoice link.  Copy an existing invoice
         */
        public function copy_invoice()
        {
        }

        /**
         * Handle submission from send_invoice form.  Sends invoice via email.
         */
        public function send_invoice()
        {
        }

        /**
         * Handle user request delete invoice
         */
        public function delete_invoice()
        {
            $this->db->delete_invoice(sanitize_text_field($_POST['id']));
            $this->json_exit(true, '', '');
        }

        public function calculate_invoice_status($invoiceID)
        {
            $status = null;
            $invoice = $this->db->get_invoice($invoiceID);
            if ($invoice)
            {
                $payments = $this->db->get_payments($invoiceID);
                $status = new QuipInvoicesInvoiceStatus($invoice, $payments);
            }

            return $status;
        }

        /**
         * Generate a unique hash for this invoice
         *
         * @param $invoiceNumber
         * @param $clientID
         * @return string Unique Hash for invoice
         */
        private function generate_invoice_hash($invoiceNumber, $clientID)
        {
            return hash('md5', "quip_invoices_{$invoiceNumber}_{$clientID}_" . date('Y-m-d_H:i:s'));
        }

        /**
         * Format incoming textarea company details ready for database and display
         *
         * @param $details
         * @return string
         */
        private function format_company_details($details)
        {
            $companyDetails = nl2br(htmlentities($details, ENT_QUOTES, 'UTF-8'));

            return $companyDetails;
        }

        /**
         * Currently just store payment types as a comma separated string
         *
         * @param $paymentTypes array
         * @return string
         */
        private function format_payment_types($paymentTypes)
        {
            return implode(',', $paymentTypes);
        }

        /**
         * Find the invoice by invoice number
         *
         * @param $invoiceNumber
         * @return mixed
         */
        private function find_invoice($invoiceNumber)
        {
            return $this->db->get_invoice_by_invoice_number($invoiceNumber);
        }
    }
}
