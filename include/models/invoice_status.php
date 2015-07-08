<?php

abstract class QIStatus
{
    const QIS_NEW = 1;
    const QIS_SENT = 2;
    const QIS_VIEWED = 3;
    const QIS_PARTIAL = 4;
    const QIS_PAID = 5;
    const QIS_PASTDUE = 6;
}

class QuipInvoicesInvoiceStatus
{
    public $invoiceID = 0;
    public $displayStatus = '';
    public $sent = null;
    public $viewed = null;
    public $reminderSent = null;
    public $pastDue = false;
    public $paymentsReceived = false;
    public $paid = false;
    public $statusStrings;


    public function __construct($invoice, $payments)
    {
        $this->initStrings();

        $this->invoiceID = $invoice->invoiceID;

        if ($invoice->sent) $this->sent = $invoice->sent;
        if ($invoice->viewed) $this->viewed = $invoice->viewed;

        if ($payments && count($payments))
        {
            $this->paymentsReceived = true;
            if ($invoice->owed <= 0.0) $this->paid = true;
        }

        if (time() > strtotime($invoice->dueDate) && !$this->paid )
        {
            $this->pastDue = true;
        }

        $this->update();
    }

    public function isNew()
    {
        return (!$this->sent && !$this->viewed && !$this->paid && !$this->paymentsReceived);
    }

    public function update()
    {
        $status = '';
        if ($this->paid)
        {
            $status = $this->statusStrings[QIStatus::QIS_PAID];
        }
        else
        {
            if ($this->pastDue)
            {
                $status = $this->statusStrings[QIStatus::QIS_PASTDUE] . ' ';
            }

            if ($this->paymentsReceived)
            {
                $status .= $this->statusStrings[QIStatus::QIS_PARTIAL];
            }
            else if ($this->viewed)
            {
                $status .= $this->statusStrings[QIStatus::QIS_VIEWED];
            }
            else if ($this->sent)
            {
                $status .= $this->statusStrings[QIStatus::QIS_SENT];
            }
            else
            {
                $status .= $this->statusStrings[QIStatus::QIS_NEW];
            }
        }

        $this->displayStatus = $status;

        return $this->displayStatus;
    }

    private function initStrings()
    {
        $this->statusStrings[QIStatus::QIS_NEW] = __('New', 'quip-invoices');
        $this->statusStrings[QIStatus::QIS_SENT] = __('Sent', 'quip-invoices');
        $this->statusStrings[QIStatus::QIS_VIEWED] = __('Viewed', 'quip-invoices');
        $this->statusStrings[QIStatus::QIS_PARTIAL] = __('Partially Paid', 'quip-invoices');
        $this->statusStrings[QIStatus::QIS_PAID] = __('Paid', 'quip-invoices');
        $this->statusStrings[QIStatus::QIS_PASTDUE] = __('(PAST DUE)', 'quip-invoices');
    }
}