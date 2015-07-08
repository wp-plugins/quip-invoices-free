<?php

class QuipInvoicesController
{
    protected $db;

    public function __construct()
    {
        include_once 'database.php';
        $this->db = new QuipInvoicesDatabase();
    }

    public function json_exit($success, $message, $redirectURL)
    {
        header("Content-Type: application/json");
        echo json_encode(array('success' => $success, 'redirectURL' => $redirectURL, 'msg' => $message));
        exit;
    }
}