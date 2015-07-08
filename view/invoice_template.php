<html>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Invoice', 'quip-invoices'); ?>: <?php echo $invoice->invoiceNumber; ?> | <?php wp_title('|', true, 'right'); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
    <?php qu_in_invoice_header($invoice); ?>
    <meta name="robots" content="noindex, nofollow"/>
</head>
<body style="background-color: #cccccc">
<div class="row" style="padding-top: 20px"></div>
<div class="container" style="background-color: #fff; padding-top: 20px;">
    <div class="row" style="display:none" id="error-message">
        <div class="col-md-12">
            <p class="alert alert-danger" id="error-message-text"></p>
        </div>
    </div>
    <div class="row" style="display:none" id="success-message">
        <div class="col-md-12">
            <p class="alert alert-success" id="success-message-text"></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php if ($options['companyLogo'] != ''): ?>
            <img src="<?php echo $options['companyLogo']?>" alt="Logo" class="qi-logoImg"/>
            <?php else: ?>
                <h2><?php echo stripslashes($options['companyName']); ?></h2>
            <?php endif; ?>
        </div>
        <div class="col-md-offset-2 col-md-2 qi-invoice-status">
            <?php if ($invoice->owed <= 0.0): ?>
                <h2><span class="label label-success"><?php _e('PAID', 'quip-invoices'); ?></span></h2>
            <?php elseif (time() > strtotime($invoice->dueDate)): ?>
                <h2><span class="label label-danger"><?php _e('PAST DUE', 'quip-invoices'); ?></span></h2>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <div class="pull-right">
                <p style="font-size: 16px; font-weight: bold"><?php _e('Invoice', 'quip-invoices'); ?>: <?php echo $invoice->invoiceNumber; ?></p>
                <address>
                    <?php echo stripslashes($invoice->companyDetails); ?>
                </address>
                <a href="javascript:window.print()" class="print-preview btn btn-default btn-xs"><?php _e('Print This Invoice', 'quip-invoices'); ?></a>
                <div class="qi-invoice-status-print">
                    <?php if ($invoice->owed <= 0.0): ?>
                        <h2><span class="label label-success"><?php _e('PAID', 'quip-invoices'); ?></span></h2>
                    <?php elseif (time() > strtotime($invoice->dueDate)): ?>
                        <h2><span class="label label-danger"><?php _e('PAST DUE', 'quip-invoices'); ?></span></h2>
                        <strong><?php _e('Due', 'quip-invoices'); ?>: <?php echo date('F jS Y', strtotime($invoice->dueDate)); ?></strong>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <hr/>
    </div>
    <div class="row">
        <div class="col-md-2">
            <address>
                <?php echo QuipInvoices::getInstance()->get_formatted_client_details($invoice->clientID); ?>
            </address>
        </div>
        <div class="col-md-10 qi-invoice-details">
            <div class="pull-right">
                <dl class="dl-horizontal">
                    <dt><?php _e('Invoice Number', 'quip-invoices'); ?>:</dt>
                    <dd><?php echo $invoice->invoiceNumber; ?></dd>
                    <dt><?php _e('Invoice Date', 'quip-invoices'); ?>:</dt>
                    <dd><?php echo date('F jS Y', strtotime($invoice->invoiceDate)); ?></dd>
                    <dt><?php _e('Due Date', 'quip-invoices'); ?>:</dt>
                    <dd><?php echo date('F jS Y', strtotime($invoice->dueDate)); ?></dd>
                    <dt><?php _e('Invoice Total', 'quip-invoices'); ?>:</dt>
                    <dd><?php echo $localeStrings['symbol'] . $invoice->total; ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="row">
        <hr/>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th><?php _e('Product/Service', 'quip-invoices'); ?></th>
                        <th><?php _e('Rate', 'quip-invoices'); ?></th>
                        <th><?php _e('Quantity', 'quip-invoices'); ?></th>
                        <th><?php _e('Adjustment', 'quip-invoices'); ?> (%)</th>
                        <th style="text-align: right;"><?php _e('Amount', 'quip-invoices'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($invoice->lineItems as $li): ?>
                        <tr>
                            <td><?php echo $li->title; ?></td>
                            <td><?php echo $localeStrings['symbol'] . $li->rate; ?></td>
                            <td><?php echo $li->quantity; ?></td>
                            <td><?php echo $li->adjustment; ?></td>
                            <td style="text-align: right;"><?php echo $localeStrings['symbol'] . $li->total; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- totals -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <table class="table">
                                <tr>
                                    <td><strong><?php _e('Sub-total', 'quip-invoices'); ?>:</strong></td>
                                    <td style="text-align: right;"><?php echo $localeStrings['symbol'] . $invoice->subTotal; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e('Tax', 'quip-invoices'); ?> (<?php echo $invoice->tax; ?>%):</strong></td>
                                    <td style="text-align: right;"><?php echo $localeStrings['symbol'] . round($invoice->subTotal * ($invoice->tax / 100), 2);  ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php _e('Total', 'quip-invoices'); ?>:</strong></td>
                                    <td style="text-align: right;"><?php echo $localeStrings['symbol'] . $invoice->total; ?></td>
                                </tr>
                                <?php if ($payments && count($payments)): ?>
                                    <tr>
                                        <td><strong><?php _e('Payments', 'quip-invoices'); ?>:</strong></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                        <td>
                                            <small><?php echo date('F jS Y', strtotime($p->paymentDate)); ?></small>
                                        </td>
                                            <td style="text-align: right;">
                                        <small><?php echo $localeStrings['symbol'] . sprintf('%0.2f', $p->amount / 100); ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <tr>
                                    <td><h3><?php _e('Amount Due', 'quip-invoices'); ?>:</h3></td>
                                    <td style="text-align: right;"><h3><?php echo $localeStrings['symbol'] . $invoice->owed; ?></h3></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="text-align: right;">
                                        <form action="" method="POST" id="invoicePaymentForm" class="form-horizontal">
                                            <input type="hidden" name="action" value="quip_invoices_pay_invoice"/>
                                            <input type="hidden" name="invoiceID" value="<?php echo $invoice->invoiceID; ?>"/>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-xlarge" <?php echo $adminView ? 'disabled="disabled"' : '' ?> id="payInvoiceButton"><?php _e('Pay Now By Credit Card', 'quip-invoices'); ?></button>
                                                <img src="<?php echo plugins_url('/img/loader.gif', dirname(__FILE__)); ?>" alt="Loading..." class="showLoading"/>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row" style="padding-top: 20px"></div>
</body>
</html>

