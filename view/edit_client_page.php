<?php
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <div id="updateDiv" style="display:none;"></div>
    <h2><?php _e('Edit Client', 'quip-invoices'); ?></h2>
    <?php if ($client->active == 0): ?>
        <div class="error">
            <p><?php _e('DELETED CLIENT.  Data shown here for record keeping purposes.', 'quip-invoices'); ?></p>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($)
            {
                $("form :input").prop("disabled", true);
            });
        </script>
    <?php endif; ?>
    <form action="" method="post" id="quip-invoices-create-client-form">
        <input type="hidden" name="action" value="quip_invoices_edit_client"/>
        <input type="hidden" name="clientID" value="<?php echo $client->id; ?>"/>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="clientName"><?php _e('Client Name', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="clientName" id="clientName" class="regular-text" value="<?php echo stripslashes(htmlspecialchars($client->clientName)); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="clientContactName"><?php _e('Client Contact Name', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="clientContactName" id="clientContactName" class="regular-text" value="<?php echo stripslashes(htmlspecialchars($client->clientContactName)); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="clientEmail"><?php _e('Client Email', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="clientEmail" id="clientEmail" class="regular-text" value="<?php echo $client->clientEmail; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="clientAltEmails"><?php _e('Client Alternate Email(s)', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="clientAltEmails" id="clientAltEmails" class="regular-text" value="<?php echo $client->clientAltEmails; ?>">
                    <p class="description"><?php _e('You can add multiple email addresses using a comma to separate. i.e. joe@example.com,sally@example.com', 'quip-invoices'); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="clientPhone"><?php _e('Client Phone', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="clientPhone" id="clientPhone" class="regular-text" value="<?php echo $client->clientPhone; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressLine1"><?php _e('Address Line 1', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressLine1" id="addressLine1" class="regular-text" value="<?php echo $client->addressLine1; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressLine2"><?php _e('Address Line 2', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressLine2" id="addressLine2" class="regular-text" value="<?php echo $client->addressLine2; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressCity"><?php _e('City', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressCity" id="addressCity" class="regular-text" value="<?php echo $client->addressCity; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressState"><?php echo $localeStrings['state']; ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressState" id="addressState" class="regular-text" value="<?php echo $client->addressState; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressZip"><?php echo $localeStrings['zip']; ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressZip" id="addressZip" class="regular-text" value="<?php echo $client->addressZip; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="addressCountry"><?php _e('Country', 'quip-invoices'); ?>:</label>
                </th>
                <td>
                    <input type="text" name="addressCountry" id="addressCountry" class="regular-text" value="<?php echo $client->addressCountry; ?>">
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php _e('Update Client', 'quip-invoices'); ?></button>
            <a href="<?php echo admin_url('admin.php?page=quip-invoices-clients'); ?>" class="button"><?php _e('Cancel', 'quip-invoices'); ?></a>
            <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
        </p>
    </form>
</div>