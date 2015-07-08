<div class="wrap about-wrap">
    <h1><?php _e('Quip Invoices Help', 'quip-invoices'); ?></h1>
    <div class="about-text"><?php _e("We're here to help.", 'quip-invoices'); ?> <?php _e('This section contains all you need to know to get started using Quip Invoices Free.', 'quip-invoices'); ?>
        If you need assistance, please upgrade to Quip Invoices Premium and we'll be happy to help.  Support for the free version is limited.
    </div>
    <p>
        <a href="http://quipcode.com" class="button button-primary"><?php _e("Visit our website", 'quip-invoices'); ?></a>
        <a href="http://invoicingplugin.com" class="button button-primary"><?php _e("Buy Quip Invoices Premium", 'quip-invoices'); ?></a>
    </p>
    <div style="padding-top: 20px;"></div>
    <div id="contextual-help-wrap" tabindex="-1">

        <div id="contextual-help-columns">
            <div class="contextual-help-tabs">
                <ul>
                    <li id="tab-link-quick_start" class="active">
                        <a href="#tab-panel-quick_start" aria-controls="tab-panel-quick_start"> <?php _e("Quick Start", 'quip-invoices'); ?></a>
                    </li>
                    <li id="tab-link-invoices" class="">
                        <a href="#tab-panel-invoices" aria-controls="tab-panel-glossary"> <?php _e("Invoices & Quotes", 'quip-invoices'); ?></a>
                    </li>
                    <li id="tab-link-clients" class="">
                        <a href="#tab-panel-clients" aria-controls="tab-panel-glossary"> <?php _e("Clients", 'quip-invoices'); ?></a>
                    </li>
                    <li id="tab-link-payments" class="">
                        <a href="#tab-panel-payments" aria-controls="tab-panel-glossary"> <?php _e("Payments", 'quip-invoices'); ?></a>
                    </li>
                    <li id="tab-link-settings" class="">
                        <a href="#tab-panel-settings" aria-controls="tab-panel-glossary"> <?php _e("Settings", 'quip-invoices'); ?></a>
                    </li>
                </ul>
            </div>
            <div class="contextual-help-tabs-wrap" style="background-color: #f6fbfd">
                <div id="tab-panel-quick_start" class="help-tab-content active" >
                    <h3 class="title"><?php _e("Quick Start", 'quip-invoices'); ?></h3>
                    <p><?php _e("The following steps are the minimum you need to get started using Quip Invoices", 'quip-invoices'); ?>:</p>
                    <ul>
                        <li><?php _e("Setup your company name and email address from the settings page.", 'quip-invoices'); ?>  <a href="<?php echo admin_url("admin.php?page=quip-invoices-settings"); ?>"><?php _e("View settings", 'quip-invoices'); ?></a></li>
                        <li><?php _e("If you want to accept credit cards, fill in your Stripe API details.", 'quip-invoices'); ?>  <a href="<?php echo admin_url("admin.php?page=quip-invoices-settings&tab=payment"); ?>"><?php _e("Payment settings", 'quip-invoices'); ?></a></li>
                        <li><?php _e("Also on the payment settings page, select the currency you wish to use.", 'quip-invoices'); ?></li>
                        <li><?php _e("Check the email default settings to make sure you're happy with them. ", 'quip-invoices'); ?>  <a href="<?php echo admin_url("admin.php?page=quip-invoices-settings&tab=email"); ?>"><?php _e("Email settings", 'quip-invoices'); ?></a></li>
                        <li><?php _e("Now you can create your first invoice from the create invoice page! ", 'quip-invoices'); ?>  <a href="<?php echo admin_url("admin.php?page=quip-invoices-invoices&tab=create"); ?>"><?php _e("Create Invoice", 'quip-invoices'); ?></a></li>
                        <li><?php _e("Happy Invoicing!", 'quip-invoices'); ?></li>
                    </ul>
                </div>
                <div id="tab-panel-invoices" class="help-tab-content">
                    <h3 class="title"><?php _e("Invoices & Quotes", 'quip-invoices'); ?></h3>
                    <p><?php _e("Invoices and Quotes are central to Quip Invoices and allow you to quickly and easily create and send detailed invoices and quotes to your customers.", 'quip-invoices'); ?></p>
                    <h4><?php _e("Invoices", 'quip-invoices'); ?></h4>
                    <p><?php _e("Invoices in Quip Invoices consist of the following data:", 'quip-invoices'); ?></p>
                    <ul>
                        <li><strong><?php _e("Invoice Number", 'quip-invoices'); ?>:</strong> <?php _e("A unique identifier for your invoice, can be any combination of text, symbols and numbers and should not be the same as another invoice or quote.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Invoice Date", 'quip-invoices'); ?>:</strong> <?php _e("The date the invoice was created.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Due Date", 'quip-invoices'); ?>:</strong> <?php _e("The deadline for payment of this invoice.  After this date the invoice will be marked as PAST DUE.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Notes", 'quip-invoices'); ?>:</strong> <?php _e("You can add notes to the invoice for your customer.  These can be about anything you like and are added to the bottom of the invoice.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Client", 'quip-invoices'); ?>:</strong> <?php _e("This is the client/customer the invoice is for.  All invoices must be for a specific client and you can quickly create new clients when creating your invoices.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Your Details", 'quip-invoices'); ?>:</strong> <?php _e("These are your company/personal details added to the invoice to show who is issuing the invoice.  They default to your company settings but you can customize per invoice too.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Partial Payments", 'quip-invoices'); ?>:</strong> <?php _e("You can choose to allow or disallow partial payment of invoices.  If allowed, when viewing the invoice your customer is able to select a custom payment amount when paying by credit card.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Payment Types", 'quip-invoices'); ?>:</strong> <?php _e("Choose which payment types you accept.  These will be shown on the invoice the customer sees.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Payment Instructions", 'quip-invoices'); ?>:</strong> <?php _e("If you choose mail or phone payments you can give extra instructions on how to make payment, such as bank details or times to call.", 'quip-invoices'); ?></li>
                        <li>
                            <strong><?php _e("Line Items", 'quip-invoices'); ?>:</strong>
                            <?php _e("These are the products or services you are invoicing your customer for.", 'quip-invoices'); ?>  <?php _e("Line items have the following data", 'quip-invoices'); ?>:
                            <ul>
                                <li><strong><?php _e("Item", 'quip-invoices'); ?>:</strong><?php _e("This is the name of the product or service you are billing for", 'quip-invoices'); ?></li>
                                <li><strong><?php _e("Rate", 'quip-invoices'); ?>:</strong><?php _e("The base rate you charge for this product or service", 'quip-invoices'); ?></li>
                                <li><strong><?php _e("Quantity", 'quip-invoices'); ?>:</strong><?php _e("The quantity of the product or service", 'quip-invoices'); ?></li>
                                <li><strong><?php _e("Adjustment", 'quip-invoices'); ?>:</strong><?php _e("A percentage adjustment of the line item amount.  For example a discount or per-item tax.", 'quip-invoices'); ?></li>
                            </ul>
                        </li>
                        <li><strong><?php _e("Tax Rate", 'quip-invoices'); ?>:</strong> <?php _e("You can set the tax rate (as a percentage) for the invoice which is calculated automatically.", 'quip-invoices'); ?></li>
                    </ul>
                    <h4><?php _e("Invoice Actions", 'quip-invoices'); ?></h4>
                    <p><?php _e("Once created, you can easily view the invoice from the Invoice page where it will be displayed in a sortable table.", 'quip-invoices'); ?>
                        <?php _e("From this table you may perform the following actions ", 'quip-invoices'); ?>:</p>
                    <ul>
                        <li><strong><?php _e("Edit", 'quip-invoices'); ?></strong>: <?php _e("Edit the invoice by clicking the invoice number.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("View", 'quip-invoices'); ?></strong>: <?php _e("View the invoice as the customer will see it.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Details", 'quip-invoices'); ?></strong>: <?php _e("View details about the invoice including emails sent and payments received.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Send", 'quip-invoices'); ?></strong>: <?php _e("Send the invoice via email.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Copy", 'quip-invoices'); ?></strong>: <?php _e("Make an exact copy of this invoice.  Useful for quickly creating new invoices.", 'quip-invoices'); ?></li>
                        <li><strong><?php _e("Delete", 'quip-invoices'); ?></strong>: <?php _e("This will delete the invoice and all line items.", 'quip-invoices'); ?></li>
                    </ul>
                    <h4><?php _e("Sending Invoices", 'quip-invoices'); ?></h4>
                    <p><?php _e("Once created, you can send invoices via email by clicking the Send link for the invoice from the Invoices page.", 'quip-invoices'); ?>
                        <?php _e("This will take you to a page where you can decide on the email content as well as choose to send to multiple email addresses.  The client data is pre-filled for your convenience, as are the email defaults taken from your settings.", 'quip-invoices'); ?></p>
                    <?php _e("Once sent, the invoice status will be updated to Sent and Quip Invoices will track when the client views the invoice.", 'quip-invoices'); ?>
                    <h4><?php _e("Quotes", 'quip-invoices'); ?></h4>
                    <p><?php _e("Quotes are very similar to invoices (internally they are treated almost the same) except they are only used to give prices to clients, not accept payments.", 'quip-invoices'); ?>
                        <?php _e("This means that all the data and actions you can do with Invoices you can do with Quotes except choosing payment options.", 'quip-invoices'); ?>
                        <?php _e("Also, Quotes will not be able to accept payments from customers, simply showing the grand total amount for the line items on the quote.", 'quip-invoices'); ?></p>
                    <h4><?php _e("Quote Actions", 'quip-invoices'); ?></h4>
                    <p><?php _e("Quotes have the same actions as Invoices, plus", 'quip-invoices'); ?>: </p>
                    <ul>
                        <li><strong><?php _e("Convert to Invoice", 'quip-invoices'); ?></strong>: <?php _e("This will convert a Quote to an Invoice, adding the ability to accept payment.", 'quip-invoices'); ?></li>
                    </ul>
                </div>
                <div id="tab-panel-clients" class="help-tab-content">
                    <h3 class="title"><?php _e("Clients", 'quip-invoices'); ?></h3>
                    <p><?php _e("Quip Invoices lets you store client details to make it easier to create invoices and track what is owed.", 'quip-invoices'); ?>  
                        <?php _e("Creating a new client is straightforward, simply click the Create New Client tab from the Clients page and fill out information such as name, email and address.", 'quip-invoices'); ?>
                        <?php _e("You can also create new clients during creation of an invoice by clicking the 'Add New' link and adding the client name and email.", 'quip-invoices'); ?></p>
                    <h4><?php _e("Client Details", 'quip-invoices'); ?></h4>
                    <p><?php _e("Clients have a details page, accessed from the Clients list action 'Details', which shows all the client invoices and any outstanding balances. ", 'quip-invoices'); ?></p>
                </div>
                <div id="tab-panel-payments" class="help-tab-content">
                    <h3 class="title"><?php _e("Payments", 'quip-invoices'); ?></h3>
                    <p><?php _e("The payments page keeps track of all received payments.", 'quip-invoices'); ?>  <?php _e("When a client pays an invoice via credit card directly, the payment data is stored and available from the Payments page.", 'quip-invoices'); ?>
                        <?php _e("Manually added payments are also stored here and you can easily view and sort payments as well as view the related invoice details.", 'quip-invoices'); ?></p>
                    <h4><?php _e("Manual Payments", 'quip-invoices'); ?></h4>
                    <p><?php _e("For invoice payments you receive via mail, phone or in person you can add a manual payment by clicking the 'Add Payment' tab.", 'quip-invoices'); ?>
                        <?php _e("Simply fill out the payment amount (in cents), the date and the type of payment as well as selecting the invoice to apply the payment too.  The invoice will be updated to show the payment received.", 'quip-invoices'); ?></p>
                </div>
                <div id="tab-panel-settings" class="help-tab-content">
                    <h3 class="title"><?php _e("Settings", 'quip-invoices'); ?></h3>
                    <p><?php _e("Setting up Quip Invoices is easy!", 'quip-invoices'); ?></p>
                    <h4><?php _e("Basic Settings", 'quip-invoices'); ?></h4>
                    <p><?php _e("Basic settings include adding values for your company name, email, phone and address which will be included on your invoices by default.  Only the company name and email are required however.", 'quip-invoices'); ?>
                        <?php _e("You can also set an image for company logo here, uploaded from your WordPress Media Library.  Recommended size around 200px wide by 50px height.", 'quip-invoices'); ?>
                        <?php _e("Leaving the logo image blank will use your company name on invoices instead.", 'quip-invoices'); ?></p>
                    <h4><?php _e("Email Settings", 'quip-invoices'); ?></h4>
                    <p><?php _e("The email settings allow you to set defaults for the subject line and message content of your invoice related emails.", 'quip-invoices'); ?>
                        <?php _e("You can use basic HTML in your email messages (use the Text tab on the editor) and there are also several dynamic tags", 'quip-invoices'); ?>:</p>
                    <ul>
                        <li><strong>%%INVOICE_AMOUNT%%</strong> - <?php _e('The invoice total amount value', 'quip-invoices'); ?></li>
                        <li><strong>%%INVOICE_DUE_DATE%%</strong> - <?php _e('The invoice due date', 'quip-invoices'); ?></li>
                        <li><strong>%%INVOICE_LINK%%</strong> - <?php _e('A link to view the invoice online', 'quip-invoices'); ?></li>
                        <li><strong>%%COMPANY_DETAILS%%</strong> - <?php _e('Your company details for this invoice (Name, Address, Phone, Email)', 'quip-invoices'); ?></li>
                    </ul>
                    <?php _e("Wherever in the email message you place one of the dynamic tags, Quip Invoices will automatically substitute the correct invoice/quote information before sending the email.", 'quip-invoices'); ?>
                    <h4><?php _e("Payment Settings", 'quip-invoices'); ?></h4>
                    <p><?php _e("If you'd like to accept credit cards, you first need a free account from Stripe", 'quip-invoices'); ?>. <a href="https://stripe.com"><?php _e("Get a free Stripe account here", 'quip-invoices'); ?></a>.
                            <?php _e("Once you have an account, fill out the API key fields on the payment settings page and Quip Invoices will take care of the rest.", 'quip-invoices'); ?>
                        <?php _e("Here you can also set the currency your account is using, which also determines the currency shown throughout the plugin and on invoices and quotes.", 'quip-invoices'); ?>
                        <?php _e("Finally, you can choose to put the plugin in Test mode which will only run test credit card payments.  Once you are ready to accept real payments please switch this to Live mode.", 'quip-invoices'); ?>
                    </p>
                    <h4><?php _e("Export", 'quip-invoices'); ?></h4>
                    <p><?php _e("Use the buttons on the Export page to download CSV files containing all of your invoices, payments and clients.", 'quip-invoices'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>