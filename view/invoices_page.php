<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$options = get_option('quip_invoices_options');
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <div id="updateDiv" style="display:none;"></div>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-invoices-invoices&tab=view" class="nav-tab <?php echo $active_tab == 'view' ? 'nav-tab-active' : ''; ?>"><?php _e('Invoices', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-invoices&tab=create" class="nav-tab <?php echo $active_tab == 'create' ? 'nav-tab-active' : ''; ?>"><?php _e('Create New Invoice', 'quip-invoices'); ?></a>
        <?php do_action('quip_invoices_invoices_page_tabs', $active_tab); ?>
    </h2>
    <div class="tab-content">
        <?php if ($active_tab == 'view'): ?>
            <div class="qu-list-table">
                <?php $table->display(); ?>
            </div>
        <?php elseif ($active_tab == 'create'): ?>
            <p><strong>** <?php _e('Some features are only available in the premium version of Quip Invoices', 'quip-invoices'); ?></strong></p>
            <a href="http://invoicingplugin.com" class="button button-primary"><?php _e("Buy Quip Invoices Premium", 'quip-invoices'); ?></a>
            <form action="" method="post" id="quip-invoices-create-invoice-form">
                <input type="hidden" name="action" value="quip_invoices_create_invoice"/>
                <input type="hidden" name="invoiceType" value="invoice"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceNumber"><?php _e('Invoice Number', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="invoiceNumber" id="invoiceNumber" class="regular-text code" value="<?php echo $options['nextInvoiceNumber'];?>">
                            <p class="description"><?php _e('An identifier for your invoice', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceCreateDate"><?php _e('Invoice Date', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="invoiceCreateDate" id="invoiceCreateDate" class="regular-text datepicker">
                            <input type="hidden" name="invoiceCreateDateDB" id="invoiceCreateDateDB" value="">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceDueDate"><?php _e('Invoice Due Date', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="invoiceDueDate" id="invoiceDueDate" class="regular-text datepicker">
                            <input type="hidden" name="invoiceDueDateDB" id="invoiceDueDateDB" value="">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceNotes">** <?php _e('Notes', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <textarea cols="50" rows="5" name="invoiceNotes" id="invoiceNotes" class="regular-text" disabled="disabled"></textarea>
                            <p class="description"><?php _e('Add notes for the client on the invoice', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="invoiceClient"><?php _e('Client', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <?php $clients = QuipInvoices::getInstance()->db->get_clients(); ?>
                            <select id="invoiceClient" name="invoiceClient" <?php echo count($clients) == 0 ? 'style="display:none;"' : '' ?> >
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo stripslashes($c->clientName); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a href="new" id="createNewClient"><?php _e('Add New', 'quip-invoices'); ?></a>
                        </td>
                    </tr>
                </table>
                <div id="createClientSection" style="display:none;">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="clientName"><?php _e('Client Name', 'quip-invoices'); ?>:</label>
                            </th>
                            <td>
                                <input type="text" name="clientName" id="clientName" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="clientEmail"><?php _e('Client Email', 'quip-invoices'); ?>:</label>
                            </th>
                            <td>
                                <input type="text" name="clientEmail" id="clientEmail" class="regular-text">
                            </td>
                        </tr>
                    </table>
                    <p>
                        <button class="button button-primary" id="createClientButton"><?php _e('Create', 'quip-invoices'); ?></button>
                        <a href="cancel" id="cancelCreateNewClient"><?php _e('Cancel', 'quip-invoices'); ?></a>
                        <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                    </p>
                </div>
                <h3><?php _e('Line Items', 'quip-invoices'); ?></h3>
                <div id="lineItemSection">
                    <table class="widefat" id="lineItemsTable">
                        <thead>
                        <tr>
                            <th><?php _e('Item', 'quip-invoices'); ?></th>
                            <th><?php _e('Rate', 'quip-invoices'); ?></th>
                            <th><?php _e('Quantity', 'quip-invoices'); ?></th>
                            <th>% <?php _e('Adjustment', 'quip-invoices'); ?></th>
                            <th><?php _e('Amount', 'quip-invoices'); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="qi-new-line-item">
                            <td>
                                <input type="text" name="liTitle" id="liTitle" class="regular-text">
                            </td>
                            <td>
                                <input type="text" name="liRate" id="liRate" class="qi-input-mini" value="0">
                            </td>
                            <td>
                                <input type="text" name="liQty" id="liQty" class="qi-input-mini" value="0">
                            </td>
                            <td>
                                <input type="text" name="liAdj" id="liAdj" class="qi-input-mini" value="0">
                            </td>
                            <td>
                                <span id="liAmount"></span>
                            </td>
                            <td>
                                <button id="addLineItemButton" class="button button-primary"><?php _e('Add', 'quip-invoices'); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="line-item-totals" class="clearfix">
                    <p style="line-height: 2.2em;">
                        <strong><?php _e('Sub Total', 'quip-invoices'); ?>: </strong><span id="liSubTotal"></span><br/>
                        <strong><?php _e('Tax', 'quip-invoices'); ?> %: </strong><input type="text" class="qi-input-mini" name="invoiceTaxRate" id="invoiceTaxRate" value="0"><br/>
                        <strong><?php _e('Total', 'quip-invoices'); ?>: </strong><span id="liTotal"></span><br/>
                    </p>
                </div>
                <div style="clear: both;"></div>
                <!-- clear the float -->
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyDetails"><?php _e('Your details', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <textarea rows="6" cols="50" id="companyDetails" name="companyDetails" style="display:none;"><?php echo QuipInvoices::getInstance()->get_formatted_company_details(false); ?></textarea>
                            <?php $companyName = $options['companyName']; ?>
                            <address id="companyDetailsDisplay">
                                <?php if ($companyName !== ''): ?>
                                    <?php echo QuipInvoices::getInstance()->get_formatted_company_details(); ?>
                                <?php else: ?>
                                    <strong><?php _e('No details set!', 'quip-invoices'); ?></strong>
                                <?php endif; ?>
                            </address>
                            <br/><a href="set-details" id="companyDetailsChange"><?php _e('Change details', 'quip-invoices'); ?></a>
                            <button id="companyDetailsChangeSave" class="button" style="display:none;"><?php _e('Save Changes', 'quip-invoices'); ?></button>
                            <a href="cancel" id="companyDetailsChangeCancel" style="display:none;"><?php _e('Cancel', 'quip-invoices'); ?></a>
                            <p class="description"><?php _e('Your details are added to the invoice.', 'quip-invoices'); ?>
                                <?php
                                $url = admin_url('admin.php?page=quip-invoices-settings');
                                $link = sprintf(
                                    wp_kses( __( 'Defaults to your <a href="%s">settings</a>, but you can customize them per invoice.', 'quip-invoices' ),
                                        array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
                                echo $link;
                                ?>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="allowPartialPayment">** <?php _e('Allow Partial Payment', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <label class="radio">
                                <input type="radio" name="allowPartialPayment" value="1" disabled="disabled"> <?php _e('Yes', 'quip-invoices'); ?>
                            </label> <label class="radio">
                                <input type="radio" name="allowPartialPayment" value="0" checked="checked"> <?php _e('No', 'quip-invoices'); ?>
                            </label>
                            <p class="description"><?php _e('For credit card payments online only, allow the client to make a payment less than the total amount due.', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="paymentTypes">** <?php _e('Payment Types Allowed', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="paymentTypes[]" id="paymentTypeCC" value="1" checked="checked" disabled="disabled"><?php _e('Credit Card', 'quip-invoices'); ?>
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="paymentTypes[]" id="paymentTypeMail" value="2" disabled="disabled"><?php _e('Pay via Mail', 'quip-invoices'); ?>
                            </label>
                            <br/><br/>
                            <label>
                                <input type="checkbox" name="paymentTypes[]" id="paymentTypePhone" value="3" disabled="disabled"><?php _e('Pay via Phone', 'quip-invoices'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <div id="paymentInstructionsSection" style="display:none;">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="paymentInstructions"><?php _e('Payment Instructions', 'quip-invoices'); ?>:</label>
                            </th>
                            <td>
                                <textarea rows="4" cols="50" name="paymentInstructions" id="paymentInstructions" class="regular-text"></textarea>
                                <p class="description"><?php _e('Extra instructions for payment via phone or mail. Leave blank to not show on invoice.', 'quip-invoices'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Create Invoice', 'quip-invoices'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php endif; ?>

        <?php do_action('quip_invoices_invoices_page_tab_content', $active_tab); ?>
    </div>
</div>
<!-- dialog -->
<div id="deleteInvoiceDialog" title="Delete Invoice?">
    <p><?php _e('This will delete this invoice and all related items. Are you sure?', 'quip-invoices'); ?></p>
</div>
<div id="copyInvoiceDialog" title="Copy Invoice">
    <p><?php _e('This featured is only available in the premium version of Quip Invoices', 'quip-invoices'); ?></p>
</div>