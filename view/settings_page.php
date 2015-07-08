<?php
$options = get_option('quip_invoices_options');
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'basics';
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
$demo = defined('QUIP_INVOICES_DEMO_MODE') ? 'disabled="disabled"' : '';

?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <div id="updateDiv" style="display:none;"></div>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-invoices-settings&tab=basics" class="nav-tab <?php echo $active_tab == 'basics' ? 'nav-tab-active' : ''; ?>"><?php _e('Basic', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-settings&tab=email" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php _e('Email', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-settings&tab=payment" class="nav-tab <?php echo $active_tab == 'payment' ? 'nav-tab-active' : ''; ?>"><?php _e('Payment', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-settings&tab=export" class="nav-tab <?php echo $active_tab == 'export' ? 'nav-tab-active' : ''; ?>"><?php _e('Export', 'quip-invoices'); ?></a>
        <?php do_action('quip_invoices_settings_page_tabs', $active_tab); ?>
    </h2>
    <div class="tab-content">
        <?php if ($active_tab == 'basics'): ?>
            <form action="" method="post" id="quip-invoices-settings-form">
                <input type="hidden" name="action" value="quip_invoice_update_settings"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyName"><?php _e('Company Name', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyName" id="companyName" class="regular-text" value="<?php echo stripslashes(htmlspecialchars($options['companyName'])); ?>">
                            <p class="description"><?php _e('Your default company name to be shown on invoices (can be customized per invoice)', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyEmail"><?php _e('Company Email', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyEmail" id="companyEmail" class="regular-text" value="<?php echo $options['companyEmail']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyPhone"><?php _e('Company Phone', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyPhone" id="companyPhone" class="regular-text" value="<?php echo $options['companyPhone']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyAddress1"><?php _e('Address Line 1', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyAddress1" id="companyAddress1" class="regular-text" value="<?php echo $options['companyAddress1']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyAddress2"><?php _e('Address Line 2', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyAddress2" id="companyAddress2" class="regular-text" value="<?php echo $options['companyAddress2']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyCity"><?php _e('City', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyCity" id="companyCity" class="regular-text" value="<?php echo $options['companyCity']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyState"><?php echo $localeStrings['state']; ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyState" id="companyState" class="regular-text" value="<?php echo $options['companyState']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyZip"><?php echo $localeStrings['zip']; ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyZip" id="companyZip" class="regular-text" value="<?php echo $options['companyZip']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="companyCountry"><?php _e('Country', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="companyCountry" id="companyCountry" class="regular-text" value="<?php echo $options['companyCountry']; ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label><?php _e('Company Logo', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <div id="companyLogoImage" style="display:none;">
                                <img id="companyLogoSrc" src="<?php echo $options['companyLogo']; ?>"/>
                            </div>
                            <input id="companyLogo" type="text" name="companyLogo" value="<?php echo $options['companyLogo']; ?>"/>
                            <button id="uploadImageButton" class="button" type="button" value="Upload Image"><?php _e('Upload Image', 'quip-invoices'); ?></button>
                            <a href="clear" id="clearLogo"><?php _e('Clear Logo', 'quip-invoices'); ?></a>
                            <p class="description"><?php _e('Your logo to be included on invoices and quotes, max 300px wide.  Leave blank to use Company Name instead.', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" <?php echo $demo; ?>><?php _e('Save Settings', 'quip-invoices'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php elseif ($active_tab == 'email'): ?>
            <div class="error"><p><?php _e('This feature is only available in the premium version of Quip Invoices', 'quip-invoices'); ?></p></div>
            <a href="http://invoicingplugin.com" class="button button-primary"><?php _e("Buy Quip Invoices Premium", 'quip-invoices'); ?></a>

            <form action="" method="post" id="quip-invoices-email-settings-form">
                <input type="hidden" name="action" value="quip_invoice_update_email_settings"/>
                <h3 class="title"><?php _e('Notifications', 'quip-invoices'); ?></h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="sendNotifications"><?php _e('Send Notifications', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <label class="radio">
                                <input type="radio" name="sendNotifications" value="1" <?php echo ($options['sendNotifications'] == 1) ? 'checked="checked"' : '' ?> > <?php _e('Yes', 'quip-invoices'); ?>
                            </label> <label class="radio">
                                <input type="radio" name="sendNotifications" value="0" <?php echo ($options['sendNotifications'] == 0) ? 'checked="checked"' : '' ?>> <?php _e('No', 'quip-invoices'); ?>
                            </label>
                            <p class="description"><?php _e('Send email notifications to the Admin when clients view and pay invoices', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                </table>
                <h3 class="title"><?php _e('Invoice Email', 'quip-invoices'); ?></h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultSubject"><?php _e('Default Subject', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="emailDefaultSubject" id="emailDefaultSubject" class="regular-text" value="<?php echo $options['emailDefaultSubject']; ?>">
                            <p class="description"><?php _e('Default email subject line. Can be customized per invoice.', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultMessage"><?php _e('Email Message', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['emailDefaultMessage'])), 'emailDefaultMessage', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description"><?php _e('Default HTML email when sending an invoice to a client for the first time. You can use the following dynamic tags', 'quip-invoices'); ?>:
                                <br/>
                                <code>%%INVOICE_AMOUNT%%</code> - <?php _e('The invoice total amount value', 'quip-invoices'); ?>
                                <br/>
                                <code>%%INVOICE_DUE_DATE%%</code> - <?php _e('The invoice due date', 'quip-invoices'); ?>
                                <br/>
                                <code>%%INVOICE_LINK%%</code> - <?php _e('A link to view the invoice online', 'quip-invoices'); ?>
                                <br/>
                                <code>%%COMPANY_DETAILS%%</code> - <?php _e('Your company details for this invoice (Name, Address, Phone, Email)', 'quip-invoices'); ?>
                                <br/>
                            </p>
                        </td>
                    </tr>
                </table>
                <h3 class="title"><?php _e('Invoice Reminder Email', 'quip-invoices'); ?></h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultReminderSubject"><?php _e('Default Subject', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="emailDefaultReminderSubject" id="emailDefaultReminderSubject" class="regular-text" value="<?php echo $options['emailDefaultReminderSubject']; ?>">
                            <p class="description"><?php _e('Default email subject line', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultReminderMessage"><?php _e('Reminder Email Message', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['emailDefaultReminderMessage'])), 'emailDefaultReminderMessage', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description"><?php _e('Default HTML email when sending an invoice reminder email. You can use the following dynamic tags', 'quip-invoices'); ?>:
                                <br/>
                                <code>%%INVOICE_AMOUNT%%</code> - <?php _e('The invoice total amount value', 'quip-invoices'); ?>
                                <br/>
                                <code>%%INVOICE_DUE_DATE%%</code> - <?php _e('The invoice due date', 'quip-invoices'); ?>
                                <br/>
                                <code>%%INVOICE_LINK%%</code> - <?php _e('A link to view the invoice online', 'quip-invoices'); ?>
                                <br/>
                                <code>%%COMPANY_DETAILS%%</code> - <?php _e('Your company details for this invoice (Name, Address, Phone, Email)', 'quip-invoices'); ?>
                                <br/>
                            </p>
                        </td>
                    </tr>
                </table>
                <h3 class="title"><?php _e('Quote Email', 'quip-invoices'); ?></h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultQuoteSubject"><?php _e('Default Subject', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="emailDefaultQuoteSubject" id="emailDefaultQuoteSubject" class="regular-text" value="<?php echo $options['emailDefaultQuoteSubject']; ?>">
                            <p class="description"><?php _e('Default email subject line for quotes', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="emailDefaultQuoteMessage"><?php _e('Email Message', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <?php wp_editor(stripslashes(base64_decode($options['emailDefaultQuoteMessage'])), 'emailDefaultQuoteMessage', array('media_buttons' => false, 'teeny' => true)); ?>
                            <p class="description"><?php _e('Default HTML email when sending a quote. You can use the following dynamic tags', 'quip-invoices'); ?>:
                                <br/>
                                <code>%%INVOICE_AMOUNT%%</code> - <?php _e('The quote grand total amount', 'quip-invoices'); ?>
                                <br/>
                                <code>%%INVOICE_LINK%%</code> - <?php _e('A link to view the quote online', 'quip-invoices'); ?>
                                <br/>
                                <code>%%COMPANY_DETAILS%%</code> - <?php _e('Your company details for this quote (Name, Address, Phone, Email)', 'quip-invoices'); ?>
                                <br/>
                            </p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" disabled="disabled" ><?php _e('Save Settings', 'quip-invoices'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php elseif ($active_tab == 'payment'): ?>
            <form action="" method="post" id="quip-invoices-payment-settings-form">
                <input type="hidden" name="action" value="quip_invoice_update_payment_settings"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="secretKey_test">Stripe <?php _e("Test Secret Key: ", 'quip-invoices'); ?> </label>
                        </th>
                        <td>
                            <input type="text" name="secretKey_test" id="secretKey_test" value="<?php echo $options['secretKey_test']; ?>" class="regular-text code">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="publishKey_test">Stripe <?php _e("Test Publishable Key: ", 'quip-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="publishKey_test" name="publishKey_test" value="<?php echo $options['publishKey_test']; ?>" class="regular-text code">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="secretKey_live">Stripe <?php _e("Live Secret Key: ", 'quip-invoices'); ?> </label>
                        </th>
                        <td>
                            <input type="text" name="secretKey_live" id="secretKey_live" value="<?php echo $options['secretKey_live']; ?>" class="regular-text code">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="publishKey_live">Stripe <?php _e("Live Publishable Key: ", 'quip-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="publishKey_live" name="publishKey_live" value="<?php echo $options['publishKey_live']; ?>" class="regular-text code">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label>Stripe <?php _e("API mode: ", 'quip-invoices'); ?> </label>
                        </th>
                        <td>
                            <label class="radio">
                                <input type="radio" name="apiMode" id="modeTest" value="test" <?php echo ($options['apiMode'] == 'test') ? 'checked' : '' ?> > <?php _e('Test', 'quip-invoices'); ?>
                            </label> <label class="radio">
                                <input type="radio" name="apiMode" id="modeLive" value="live" <?php echo ($options['apiMode'] == 'live') ? 'checked' : '' ?>> <?php _e('Live', 'quip-invoices'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="currency"><?php _e("Payment Currency: ", 'quip-invoices'); ?></label>
                        </th>
                        <td>
                            <select id="currency" name="currency">
                                <option value="usd" <?php echo ($options['currency'] == 'usd') ? 'selected="selected"' : '' ?>><?php _e('United States Dollar', 'quip-invoices'); ?></option>
                                <option value="cad" <?php echo ($options['currency'] == 'cad') ? 'selected="selected"' : '' ?>><?php _e('Canadian Dollar', 'quip-invoices'); ?></option>
                                <option value="eur" <?php echo ($options['currency'] == 'eur') ? 'selected="selected"' : '' ?>><?php _e('Euro', 'quip-invoices'); ?></option>
                                <option value="gbp" <?php echo ($options['currency'] == 'gbp') ? 'selected="selected"' : '' ?>><?php _e('British Pound Sterling', 'quip-invoices'); ?></option>
                                <option value="aud" <?php echo ($options['currency'] == 'aud') ? 'selected="selected"' : '' ?>><?php _e('Australian Dollar', 'quip-invoices'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" <?php echo $demo; ?> ><?php echo __('Save Settings', 'quip-invoices') ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php elseif ($active_tab == 'export'): ?>
            <div class="error"><p><?php _e('This feature is only available in the premium version of Quip Invoices', 'quip-invoices'); ?></p></div>
            <a href="http://invoicingplugin.com" class="button button-primary"><?php _e("Buy Quip Invoices Premium", 'quip-invoices'); ?></a>
        <?php endif; ?>

        <?php do_action('quip_invoices_settings_page_tab_content', $active_tab); ?>
    </div>
</div>