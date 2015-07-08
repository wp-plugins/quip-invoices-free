<?php

class QuipInvoicesTablePayments extends WP_List_Table
{
    function __construct()
    {
        parent::__construct(array(
            'singular' => __('Payment', 'quip-invoices'), //Singular label
            'plural' => __('Payments', 'quip-invoices'), //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ));
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    function extra_tablenav($which)
    {
        if ($which == "top")
        {
            //The code that goes before the table is here
            echo '<div class="wrap">';
        }
        if ($which == "bottom")
        {
            //The code that goes after the table is there
            echo '</div>';
        }
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns()
    {
        return $columns = array(
            'invoiceID' => __('Invoice', 'quip-invoices'),
            'amount' => __('Amount', 'quip-invoices'),
            'paymentType' => __('Type', 'quip-invoices'),
            'paymentDate' => __('Date', 'quip-invoices'),
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns()
    {
        return $sortable = array(
            'invoiceID' => array('invoiceID', false),
            'amount' => array('amount', false),
            'paymentType' => array('paymentType', false),
            'paymentDate' => array('paymentDate', false)
        );
    }

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */
    function prepare_items()
    {
        global $wpdb;
        $screen = get_current_screen();

        // Preparing your query
        $query = "SELECT * FROM " . $wpdb->prefix . 'qi_payments';

        //Parameters that are going to be used to order the result
        $orderby = !empty($_REQUEST["orderby"]) ? esc_sql($_REQUEST["orderby"]) : 'ASC';
        $order = !empty($_REQUEST["order"]) ? esc_sql($_REQUEST["order"]) : '';
        if (!empty($orderby) && !empty($order))
        {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        //Number of elements in your table?
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        //How many to display per page?
        $perpage = 10;
        //Which page is this?
        $paged = !empty($_GET["paged"]) ? esc_sql($_GET["paged"]) : '';
        //Page Number
        if (empty($paged) || !is_numeric($paged) || $paged <= 0)
        {
            $paged = 1;
        }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems / $perpage);
        //adjust the query to take pagination into account
        if (!empty($paged) && !empty($perpage))
        {
            $offset = ($paged - 1) * $perpage;
            $query .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
        }

        // Register the pagination
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));
        //The pagination links are automatically built according to those parameters

        //Register the Columns
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // Fetch the items
        $this->items = $wpdb->get_results($query);
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows()
    {
        //Get the records registered in the prepare_items method
        $records = $this->items;

        //Get the columns registered in the get_columns and get_sortable_columns methods
        list($columns, $hidden) = $this->get_column_info();

        //Loop for each record
        if (!empty($records))
        {
            $localeStrings = QuipInvoices::getInstance()->get_locale_strings();

            foreach ($records as $rec)
            {
                //Open the line
                echo '<tr id="record_' . $rec->id . '">';
                foreach ($columns as $column_name => $column_display_name)
                {
                    //Style attributes for each col
                    $class = "class='$column_name column-$column_name'";
                    $style = "";
                    if (in_array($column_name, $hidden)) $style = ' style="display:none;"';
                    $attributes = $class . $style;

                    //Display the cell
                    switch ($column_name)
                    {
                        case "invoiceID":
                            $invoice = QuipInvoices::getInstance()->db->get_invoice($rec->invoiceID);
                            $row = '<td ' . $attributes . '><strong><a href="' . admin_url('admin.php?page=quip-invoices-edit&type=invoice&id=' . $rec->invoiceID) . '" >' . stripslashes($invoice->invoiceNumber) . '</a></strong>';
                            $row .= '<div class="row-actions visible">';
                            $row .= '<span><a href="' . admin_url('admin.php?page=quip-invoices-details&type=invoice&id=' . $rec->invoiceID)  .'" >' . __('Details', 'quip-invoices') . '</a> | </span>';
                            $row .= '<span class="delete" ><a href="delete" data-id="' . $rec->id. '" class="delete-payment">' . __('Delete', 'quip-invoices') . '</a></span>';
                            $row .= '</div>';
                            $row .= '</td>';
                            echo $row;
                            break;
                        case "amount":
                            echo '<td ' . $attributes . '>' . $localeStrings['symbol'] . sprintf('%0.2f', $rec->amount/100) . '</td>';
                            break;
                        case "paymentType":
                            $type = '';
                            if ($rec->paymentType == 1)
                            {
                                $type = __('Credit Card', 'quip-invoices');

                                $stripeLink = "<a href='https://dashboard.stripe.com/";
                                if ($rec->livemode == 0) $stripeLink .= 'test/';
                                $stripeLink .= "charges/" . $rec->stripePaymentID . "'>$rec->stripePaymentID</a>";
                                $type .= '<div><small>' . $stripeLink . '</small></div>';
                            }
                            else if ($rec->paymentType == 2) $type = __('Mail', 'quip-invoices');
                            else if ($rec->paymentType == 3) $type = __('Phone', 'quip-invoices');
                            else if ($rec->paymentType == 4) $type = __('In Person', 'quip-invoices');
                            echo '<td ' . $attributes . '>' . $type . '</td>';
                            break;
                        case "paymentDate":
                            echo '<td ' . $attributes . '>' . date('F jS Y', strtotime($rec->paymentDate))  . '</td>';
                            break;
                    }
                }

                //Close the line
                echo'</tr>';
            }
        }
    }

}
