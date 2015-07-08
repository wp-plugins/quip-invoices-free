<?php
include_once 'base.controller.php';

if (!class_exists('QuipInvoicesClient'))
{
    class QuipInvoicesClient extends QuipInvoicesController
    {
        public function __construct()
        {
            parent::__construct();

            //quick save new client (from invoice form)
            add_action('wp_ajax_quip_invoices_quick_create_client', array($this, 'quick_create_client'));
            //create new client
            add_action('wp_ajax_quip_invoices_create_client', array($this, 'create_client'));
            //edit client
            add_action('wp_ajax_quip_invoices_edit_client', array($this, 'edit_client'));
            //delete client
            add_action('wp_ajax_quip_invoices_delete_client', array($this, 'delete_client'));
        }

        /**
         * Create a client from only name and email address.  Used from create/edit
         * invoice form to quickly create a new client.
         */
        public function quick_create_client()
        {
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            if ($email === FALSE) $this->json_exit(false, __("Please enter a valid email address.", 'quip-invoices') , '');

            $name = sanitize_text_field($_POST['name']);

            $id = $this->db->insert_client(array(
                'clientName' => $name,
                'clientEmail' => $email,
                'created' => date('Y-m-d H:i:s')
            ));

            header("Content-Type: application/json");
            echo json_encode(array('success' => true, 'id' => $id));
            exit;
        }

        /**
         * Handle create client form submission
         */
        public function create_client()
        {
            $email = filter_var($_POST['clientEmail'], FILTER_VALIDATE_EMAIL);
            if ($email === FALSE) $this->json_exit(false, __("Please enter a valid email address.", 'quip-invoices'), '');

            $this->db->insert_client(array(
                'clientName' => sanitize_text_field($_POST['clientName']),
                'clientContactName' => sanitize_text_field($_POST['clientContactName']),
                'clientEmail' => $email,
                'clientAltEmails' => str_replace(' ', '', sanitize_text_field($_POST['clientAltEmails'])),
                'clientPhone' => sanitize_text_field($_POST['clientPhone']),
                'addressLine1' => sanitize_text_field($_POST['addressLine1']),
                'addressLine2' => sanitize_text_field($_POST['addressLine2']),
                'addressCity' => sanitize_text_field($_POST['addressCity']),
                'addressState' => sanitize_text_field($_POST['addressState']),
                'addressZip' => sanitize_text_field($_POST['addressZip']),
                'addressCountry' => sanitize_text_field($_POST['addressCountry']),
                'created' => date('Y-m-d H:i:s')
            ));

            $this->json_exit(true, "Client created.", admin_url('admin.php?page=quip-invoices-clients&tab=view'));
        }

        /**
         * Handle edit client form submission
         */
        public function edit_client()
        {
            $email = filter_var($_POST['clientEmail'], FILTER_VALIDATE_EMAIL);
            if ($email === FALSE) $this->json_exit(false, __("Please enter a valid email address.", 'quip-invoices'), '');

            $clientID = sanitize_text_field($_POST['clientID']);

            $this->db->update_client($clientID, array(
                'clientName' => sanitize_text_field($_POST['clientName']),
                'clientContactName' => sanitize_text_field($_POST['clientContactName']),
                'clientEmail' => $email,
                'clientAltEmails' => str_replace(' ', '', sanitize_text_field($_POST['clientAltEmails'])),
                'clientPhone' => sanitize_text_field($_POST['clientPhone']),
                'addressLine1' => sanitize_text_field($_POST['addressLine1']),
                'addressLine2' => sanitize_text_field($_POST['addressLine2']),
                'addressCity' => sanitize_text_field($_POST['addressCity']),
                'addressState' => sanitize_text_field($_POST['addressState']),
                'addressZip' => sanitize_text_field($_POST['addressZip']),
                'addressCountry' => sanitize_text_field($_POST['addressCountry']),
                'created' => date('Y-m-d H:i:s')
            ));

            $this->json_exit(true, "Client updated.", admin_url('admin.php?page=quip-invoices-clients&tab=view'));
        }

        /**
         * Handle soft delete of client triggered on clients table list
         */
        public function delete_client()
        {
            $this->db->delete_client(sanitize_text_field($_POST['id']));
            $this->json_exit(true, '', '');
        }

    }
}
