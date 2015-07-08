<?php
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
?>

<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <div id="updateDiv" style="display:none;"></div>
    <?php if ($type == 'invoice'): ?>
        <h2><?php _e('Invoice Details', 'quip-invoices'); ?></h2>
        <?php if ($invoice->active == 0): ?>
            <div class="error">
                <p><?php _e('DELETED INVOICE.  Data shown here for record keeping purposes.', 'quip-invoices'); ?></p>
            </div>
        <?php endif; ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Invoice Number', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo $invoice->invoiceNumber; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Due Date', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo date('l, d F, Y', strtotime($invoice->dueDate)); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Invoice Total', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo $localeStrings['symbol'] . $invoice->total; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Amount Due', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo $localeStrings['symbol'] . $invoice->owed; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Invoice Status', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <?php
                    $invoiceStatus = QuipInvoices::getInstance()->invoice->calculate_invoice_status($invoice->invoiceID);
                    $statusClass = '';
                    if ($invoiceStatus->pastDue) $statusClass = 'class="qi-past-due"';
                    if ($invoiceStatus->paid) $statusClass = 'class="qi-paid"';
                    ?>
                    <span <?php echo $statusClass; ?>><?php echo $invoiceStatus->displayStatus; ?></span>
                </td>
            </tr>
        </table>
        <h3 class="title"><?php _e('Payments Received', 'quip-invoices'); ?></h3>
        <?php if (!count($payments)): ?>
            <p><?php _e('No payments have been received.', 'quip-invoices'); ?></p>
        <?php else: ?>
            <table class="widefat">
                <thead>
                <tr>
                    <th><?php _e('Date', 'quip-invoices'); ?></th>
                    <th><?php _e('Amount', 'quip-invoices'); ?></th>
                    <th><?php _e('Type', 'quip-invoices'); ?></th>
                    <th><?php _e('Stripe ID', 'quip-invoices'); ?>*</th>
                </tr>
                </thead>
                <tbody>
                <?php $totalReceived = 0.0; ?>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td>
                            <?php echo date('F jS Y', strtotime($p->paymentDate)); ?>
                        </td>
                        <td>
                            <?php echo $localeStrings['symbol'] . sprintf('%0.2f', $p->amount / 100); ?>
                        </td>
                        <td>
                            <?php
                            $type = '';
                            if ($p->paymentType == 1) $type = __('Credit Card', 'quip-invoices');
                            else if ($p->paymentType == 2) $type = __('Mail', 'quip-invoices');
                            else if ($p->paymentType == 3) $type = __('Phone', 'quip-invoices');
                            else if ($p->paymentType == 4) $type = __('In Person', 'quip-invoices');
                            echo $type;
                            ?>
                        </td>
                        <td>
                            <?php if ($p->paymentType == 1): ?>
                                <?php
                                $stripeLink = "<a href='https://dashboard.stripe.com/";
                                if ($p->livemode == 0) $stripeLink .= 'test/';
                                $stripeLink .= "charges/" . $p->stripePaymentID . "'>$p->stripePaymentID</a>";
                                echo $stripeLink;
                                ?>
                            <?php else: ?>
                                *<?php _e('Stripe ID for credit card payments only.', 'quip_invoices'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $totalReceived += floatval($p->amount / 100); ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td><strong><?php _e('Total Payments Received', 'quip-invoices'); ?>: </strong></td>
                    <td><strong><?php echo $localeStrings['symbol'] . $totalReceived; ?></strong></td>
                    <td></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        <h3 class="title"><?php _e('Emails Sent', 'quip-invoices'); ?></h3>
        <?php if (!$invoice->sent): ?>
            <p><?php _e('This invoice has not been sent to the client via email', 'quip-invoices'); ?></p>
        <?php else: ?>
            <table class="widefat">
                <thead>
                <tr>
                    <th><?php _e('Date', 'quip-invoices'); ?></th>
                    <th><?php _e('Type', 'quip-invoices'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo date('F jS Y', strtotime($invoice->sent)); ?></td>
                    <td><?php _e('Invoice Email', 'quip-invoices'); ?></td>
                </tr>
                <?php if ($invoice->reminderSent): ?>
                    <tr>
                        <td><?php echo date('F jS Y', strtotime($invoice->reminderSent)); ?></td>
                        <td><?php _e('Invoice Reminder Email', 'quip-invoices'); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <hr/>
        <p>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-edit&type=invoice&id=' . $invoice->invoiceID); ?>" class="button button-primary"><?php _e('Edit Invoice', 'quip-invoices'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-send&type=invoice&id=' . $invoice->invoiceID); ?>" class="button button-primary"><?php _e('Send Invoice', 'quip-invoices'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-details&type=client&id=' . $invoice->clientID) ?>" class="button button-primary"><?php _e('Go to Client', 'quip-invoices'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-invoices'); ?>" class="button"><?php _e('Back to Invoices', 'quip-invoices'); ?></a>
        </p>
    <?php elseif ($type == 'client'): ?>
        <h2><?php _e('Client Details', 'quip-invoices'); ?></h2>
        <?php if ($client->active == 0): ?>
            <div class="error">
                <p><?php _e('DELETED CLIENT.  Data shown here for record keeping purposes.', 'quip-invoices'); ?></p>
            </div>
        <?php endif; ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Client', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo stripslashes($client->clientName); ?></span><br/>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Contact Details', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <?php
                    $email = "<a href='mailto:{$client->clientEmail}'>{$client->clientEmail}</a>";
                    $phone = ($client->clientPhone) ? "<br />" . $client->clientPhone : "";
                    echo $email . $phone . '</td>';
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Total Outstanding', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo $localeStrings['symbol'] . sprintf('%0.2f', QuipInvoices::getInstance()->db->get_total_owed_for_client($client->id)) ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label><?php _e('Total Paid', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <span><?php echo $localeStrings['symbol'] . sprintf('%0.2f', QuipInvoices::getInstance()->db->get_total_paid_by_client($client->id)) ?></span>
                </td>
            </tr>
        </table>
        <h3 class="title"><?php _e('Invoices', 'quip-invoices'); ?></h3>
        <?php if (!count($invoices)): ?>
            <p><?php _e('No invoices for this client.', 'quip-invoices'); ?></p>
        <?php else: ?>
            <table class="widefat">
                <thead>
                <tr>
                    <th><?php _e('Invoice Number', 'quip-invoices'); ?></th>
                    <th><?php _e('Due Date', 'quip-invoices'); ?></th>
                    <th><?php _e('Invoice Total', 'quip-invoices'); ?></th>
                    <th><?php _e('Amount Due', 'quip-invoices'); ?></th>
                    <th><?php _e('Invoice Status', 'quip-invoices'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr valign="top">
                        <td>
                            <strong>
                                <?php echo '<a href="' . admin_url('admin.php?page=quip-invoices-edit&type=invoice&id=' . $invoice->invoiceID) . '" >' . $invoice->invoiceNumber . '</a>'; ?>
                            </strong><br/>
                            <a href="<?php echo admin_url('admin.php?page=quip-invoices-details&type=invoice&id=' . $invoice->invoiceID); ?>">Details</a>
                        </td>
                        <td>
                            <span><?php echo date('l, d F, Y', strtotime($invoice->dueDate)); ?></span>
                        </td>
                        <td>
                            <span><?php echo $localeStrings['symbol'] . $invoice->total; ?></span>
                        </td>
                        <td>
                            <span><?php echo $localeStrings['symbol'] . $invoice->owed; ?></span>
                        </td>
                        <td>
                            <span><?php echo QuipInvoices::getInstance()->invoice->calculate_invoice_status($invoice->invoiceID)->displayStatus; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <hr/>
        <p>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-edit&type=client&id=' . $client->id); ?>" class="button button-primary"><?php _e('Edit Client', 'quip-invoices'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-clients'); ?>" class="button"><?php _e('Back to Clients', 'quip-invoices'); ?></a>
        </p>
    <?php endif; ?>
</div>