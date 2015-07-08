<?php
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
$localeStrings = QuipInvoices::getInstance()->get_locale_strings();
?>
<div class="wrap">
    <img src="<?php echo plugins_url('/img/logo.png', dirname(__FILE__)); ?>" alt="Quip Invoices"/>
    <div id="updateDiv" style="display:none;"></div>
    <h2 class="nav-tab-wrapper">
        <a href="?page=quip-invoices-clients&tab=view" class="nav-tab <?php echo $active_tab == 'view' ? 'nav-tab-active' : ''; ?>"><?php _e('Clients', 'quip-invoices'); ?></a>
        <a href="?page=quip-invoices-clients&tab=create" class="nav-tab <?php echo $active_tab == 'create' ? 'nav-tab-active' : ''; ?>"><?php _e('Create New Client', 'quip-invoices'); ?></a>
        <?php do_action('quip_invoices_clients_page_tabs', $active_tab); ?>
    </h2>
    <div class="tab-content">
        <?php if ($active_tab == 'view'): ?>
            <div class="qu-list-table">
                <?php $table->display(); ?>
            </div>
        <?php elseif ($active_tab == 'create'): ?>
            <form action="" method="post" id="quip-invoices-create-client-form">
                <input type="hidden" name="action" value="quip_invoices_create_client"/>
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
                            <label for="clientContactName"><?php _e('Client Contact Name', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="clientContactName" id="clientContactName" class="regular-text">
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
                    <tr valign="top">
                        <th scope="row">
                            <label for="clientAltEmails"><?php _e('Client Alternate Email(s)', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="clientAltEmails" id="clientAltEmails" class="regular-text">
                            <p class="description"><?php _e('You can add multiple email addresses using a comma to separate. i.e. joe@example.com,sally@example.com', 'quip-invoices'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="clientPhone"><?php _e('Client Phone', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="clientPhone" id="clientPhone" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressLine1"><?php _e('Address Line 1', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressLine1" id="addressLine1" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressLine2"><?php _e('Address Line 2', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressLine2" id="addressLine2" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressCity"><?php _e('City', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressCity" id="addressCity" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressState"><?php echo $localeStrings['state']; ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressState" id="addressState" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressZip"><?php echo $localeStrings['zip']; ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressZip" id="addressZip" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="addressCountry"><?php _e('Country', 'quip-invoices'); ?>:</label>
                        </th>
                        <td>
                            <input type="text" name="addressCountry" id="addressCountry" class="regular-text">
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Create Client', 'quip-invoices'); ?></button>
                    <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                </p>
            </form>
        <?php endif; ?>

        <?php do_action('quip_invoices_clients_page_tab_content', $active_tab); ?>
    </div>
</div>

<!-- dialog -->
<div id="deleteClientDialog" title="<?php _e('Delete Client?', 'quip-invoices'); ?>">
    <p><?php _e('This will delete this client. Are you sure?', 'quip-invoices'); ?></p>
</div>