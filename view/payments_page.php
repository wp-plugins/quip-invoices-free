<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$options = get_option('quip_invoices_options');
$invoices = QuipInvoices::getInstance()->db->get_invoices();
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-invoices-payments&tab=view" class="nav-tab <?php echo $active_tab == 'view' ? 'nav-tab-active' : ''; ?>"><?php _e('Payments', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-payments&tab=create" class="nav-tab <?php echo $active_tab == 'create' ? 'nav-tab-active' : ''; ?>"><?php _e('Add Payment', 'quip-invoices'); ?></a>
        <?php do_action('quip_invoices_payments_page_tabs', $active_tab); ?>
    </h2>
    <div class="tab-content">
        <?php if ($active_tab == 'view'): ?>
            <div class="qu-list-table">
                <?php $table->display(); ?>
            </div>
        <?php elseif ($active_tab == 'create'): ?>
            <div class="error"><p><?php _e('This feature is only available in the premium version of Quip Invoices', 'quip-invoices'); ?></p></div>
            <a href="http://invoicingplugin.com" class="button button-primary"><?php _e("Buy Quip Invoices Premium", 'quip-invoices'); ?></a>
            <p><?php _e('Use this form to manually add a payment. Useful for updating invoices that received payments offline, such as in-person, by mail or by phone.', 'quip-invoices'); ?></p>
            <form action="" method="post" id="quip-invoices-create-payment-form">
                <input type="hidden" name="action" value="quip_invoices_create_manual_payment"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceID"><?php _e('Invoice', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <select id="invoiceID" name="invoiceID">
                                <?php
                                foreach ($invoices as $in)
                                {
                                    $client = QuipInvoices::getInstance()->db->get_client($in->clientID);
                                    $name = stripslashes($client->clientName);
                                    $outstanding = QuipInvoices::getInstance()->db->get_invoice_amount_outstanding($in->invoiceID);
                                    echo "<option value='{$in->invoiceID}' data-amount='{$outstanding}'>{$in->invoiceNumber} ($name)</option>";
                                }
                                ?>
                            </select>
                            <p class="description"><?php _e('This invoice has', 'quip-invoices'); ?>
                                <strong><span id="invoiceOwed"><?php echo $localeStrings['symbol']; ?>0.00</span></strong> <?php _e('outstanding', 'quip-invoices'); ?>.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="paymentAmount"><?php _e('Payment Amount', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="paymentAmount" id="paymentAmount" class="regular-text">
                            <p class="description"><?php _e('Please add amount in cents/pence', 'quip-invoices'); ?> e.g. <?php echo $localeStrings['symbol']; ?>14.99 = 1499</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="paymentDate"><?php _e('Date Received', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="paymentDate" id="paymentDate" class="regular-text datepicker">
                            <input type="hidden" name="paymentDateDB" id="paymentDateDB" value="">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="paymentType"><?php _e('Payment Type', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <select id="paymentType" name="paymentType">
                                <option value="4"><?php _e('In Person Payment', 'quip-invoices'); ?></option>
                                <option value="2"><?php _e('Mail Payment', 'quip-invoices'); ?></option>
                                <option value="3"><?php _e('Phone Payment', 'quip-invoices'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                        </th>
                        <td>
                            <p><?php _e('Adding this payment will leave', 'quip-invoices'); ?>
                                <strong><span id="invoiceOwedRemaining"><?php echo $localeStrings['symbol']; ?>0.00</span></strong> <?php _e('remaining to be paid on this invoice', 'quip-invoices'); ?>.
                            </p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" disabled="disabled"><?php _e('Add Payment', 'quip-invoices'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php endif; ?>

        <?php do_action('quip_invoices_paymets_page_tab_content', $active_tab); ?>
    </div>
</div>

<!-- dialog -->
<div id="deletePaymentDialog" title="Delete Payment?">
    <p><?php _e('This will', 'quip-invoices'); ?> <strong><?php _e('permanently delete', 'quip-invoices'); ?></strong> <?php _e('this payment and update the related invoice owed amount. Are you sure?', 'quip-invoices'); ?></p>
</div>