<?php
include_once 'base.controller.php';

if (!class_exists('QuipInvoicesPayment'))
{
    class QuipInvoicesPayment extends QuipInvoicesController
    {
        public function __construct()
        {
            parent::__construct();

            add_action('wp_ajax_quip_invoices_create_manual_payment', array($this, 'create_manual_payment'));
            add_action('wp_ajax_quip_invoices_delete_payment', array($this, 'delete_payment'));
            //pay invoice
            add_action('wp_ajax_quip_invoices_pay_invoice', array($this, 'pay_invoice'));
            add_action('wp_ajax_nopriv_quip_invoices_pay_invoice', array($this, 'pay_invoice'));
        }

        /**
         * Handle add payment form submission to manually add a new payment
         */
        public function create_manual_payment()
        {
            $this->json_exit(true, '', '');
        }

        /**
         * Handle submission via Stripe Checkout payment directly from HTML invoice
         * template page.
         */
        public function pay_invoice()
        {
            $invoice = $this->db->get_full_invoice(sanitize_text_field($_POST['invoiceID']));

            if ($invoice)
            {
                //remember Stripe expects amount in cents
                $amount = $invoice->owed * 100;
                //check if it's a custom partial payment
                if (isset($_POST['invoiceMakePartialPayment']) && $_POST['invoiceMakePartialPayment'] == 1)
                {
                    $amount = floatval(sanitize_text_field($_POST['customAmount'])) * 100;
                }

                // get client data
                $client = $this->db->get_client($invoice->clientID);
                $card = $_POST['stripeToken'];

                try
                {
                    //check if the related client is already a Stripe customer
                    $stripeCustomerID = null;
                    if ($client->stripeCustomerID)
                    {
                        //update the card on record to the one just used
                        $this->update_customer_card($client->stripeCustomerID, $card);
                        $stripeCustomerID = $client->stripeCustomerID;
                    }
                    else
                    {
                        $stripeCustomer = $this->create_customer(
                            $card,
                            $client->clientEmail,
                            array(
                                'name' => $client->clientName,
                                'addressLine1' => $client->addressLine1,
                                'addressLine2' => $client->addressLine2,
                                'addressCity' => $client->addressCity,
                                'addressState' => $client->addressState,
                                'addressZip' => $client->addressZip,
                                'addressCountry' => $client->addressCountry,
                            ));

                        //Add the ID to the client
                        $this->db->update_client($client->id, array('stripeCustomerID' => $stripeCustomer->id));

                        $stripeCustomerID = $stripeCustomer->id;
                    }

                    $options = get_option('quip_invoices_options');

                    //We now have the StripeCustomer, lets charge them
                    $paymentResult = $this->charge_customer(
                        $stripeCustomerID,
                        $amount,
                        'Payment for Invoice: ' . $invoice->invoiceNumber,
                        $options['currency'],
                        array('invoice_number' => $invoice->invoiceNumber),
                        $client->clientEmail);

                    //No exception means we were successful, save the payment data
                    $paymentData = array(
                        'invoiceID' => $invoice->invoiceID,
                        'amount' => $amount,
                        'paymentType' => 1, //Credit Card
                        'paymentDate' => date('Y-m-d', $paymentResult->created),
                        'stripePaymentID' => $paymentResult->id,
                        'livemode' => $paymentResult->livemode,
                        'created' => date('Y-m-d H:i:s')
                    );

                    $this->db->insert_payment($paymentData);

                    //update invoice owed
                    $owed = $invoice->owed - floatval($amount / 100);
                    $this->db->update_invoice($invoice->invoiceID, array('owed' => $owed));

                    //send notification
                    if ($options['sendNotifications'] == 1)
                        QuipInvoices::getInstance()->send_notification(__('Payment Received', 'quip-invoices'), $invoice);

                    //We're all done!  PAYMENT SUCCESS.

                }
                catch (Exception $e)
                {
                    header("Content-Type: application/json");
                    echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
                    exit;
                }

            }

            // Success exit, just refresh the page so payment appears on invoice.
            header("Content-Type: application/json");
            echo json_encode(array('success' => true, 'redirectURL' => site_url() . "?qinvoice=$invoice->hash"));
            exit;
        }


        /**
         * Permanently delete a payment and recalculate the invoice owed amount
         * after deletion.
         */
        public function delete_payment()
        {
            $id = sanitize_text_field($_POST['id']);
            $payment = $this->db->get_payment($id);
            if ($payment)
            {
                //update invoice owed
                $invoice = $this->db->get_invoice($payment->invoiceID);
                // make sure to delete payment before recalculating amount owed
                $this->db->delete_payment($id);
                $this->db->update_invoice(
                    $invoice->invoiceID,
                    array('owed' => $this->db->get_invoice_amount_outstanding($invoice->invoiceID)));

            }

            $this->json_exit(true, '', '');
        }

        /**
         * Create a new Stripe customer object in the users Stripe account
         *
         * @param $card
         * @param $email
         * @param $metadata
         * @return \Stripe\Customer
         */
        private function create_customer($card, $email, $metadata)
        {
            $customer = array(
                "card" => $card,
                "email" => $email,
                "metadata" => $metadata
            );

            return \Stripe\Customer::create($customer);
        }

        /**
         * Charge a Stripe Customer
         *
         * @param $customerId
         * @param $amount
         * @param $description
         * @param $currency
         * @param null $metadata
         * @param null $stripeEmail
         * @return \Stripe\Charge
         */
        private function charge_customer($customerId, $amount, $description, $currency, $metadata = null, $stripeEmail = null)
        {
            $charge = array(
                'customer' => $customerId,
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                'receipt_email' => $stripeEmail
            );

            if ($metadata)
                $charge['metadata'] = $metadata;

            $result = \Stripe\Charge::create($charge);

            return $result;
        }

        /**
         * Retrieve Stripe Customer details from users Stripe account
         *
         * @param $customerID
         * @return \Stripe\Customer
         */
        private function retrieve_customer($customerID)
        {
            return \Stripe\Customer::retrieve($customerID);
        }

        /**
         * Update a Stripe Customers default credit card
         *
         * @param $customerID
         * @param $card
         * @return \Stripe\Customer
         */
        private function update_customer_card($customerID, $card)
        {
            $cu = \Stripe\Customer::retrieve($customerID);
            $cu->card = $card;
            $cu->save();
            return \Stripe\Customer::retrieve($customerID);
        }
    }
}
