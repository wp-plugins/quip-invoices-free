<?php

class QuipInvoicesTableClients extends WP_List_Table
{
    function __construct()
    {
        parent::__construct(array(
            'singular' => __('Client', 'quip-invoices'), //Singular label
            'plural' => __('Clients', 'quip-invoices'), //plural label, also this well be one of the table css class
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
            'clientName' => __('Client', 'quip-invoices'),
            'clientEmail' => __('Contact Details', 'quip-invoices'),
            'address' => __('Address', 'quip-invoices'),
            'owed' => __('Amount Owed', 'quip-invoices'),
            'paid' => __('Amount Paid', 'quip-invoices'),
        );
    }

    /**
     * Decide which columns to activate the sorting functionality on
     * @return array $sortable, the array of columns that can be sorted by the user
     */
    public function get_sortable_columns()
    {
        //TODO: allow sort by owed, paid
        return $sortable = array(
            'clientName' => array('clientName', false),
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
        $query = "SELECT * FROM " . $wpdb->prefix . 'qi_clients' . ' WHERE active=1';

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
                        case "clientName":
                            $row = '<td ' . $attributes . '><strong><a href="' . admin_url('admin.php?page=quip-invoices-edit&type=client&id=' . $rec->id) . '" >' . stripslashes($rec->clientName) . '</a></strong>';
                            $row .= '<div class="row-actions visible">';
                            $row .= '<span><a href="' . admin_url('admin.php?page=quip-invoices-details&type=client&id=' . $rec->id)  .'" >' . __('Details', 'quip-invoices') . '</a> | </span>';
                            $row .= '<span class="delete" ><a href="delete" data-id="' . $rec->id. '" class="delete-client">' . __('Delete', 'quip-invoices') . '</a></span>';
                            $row .= '</div>';
                            $row .= '</td>';
                            echo $row;
                            break;
                        case "clientEmail":
                            $email = "<a href='mailto:{$rec->clientEmail}'>{$rec->clientEmail}</a>";
                            $phone = ($rec->clientPhone) ? "<br />" . $rec->clientPhone : "";
                            echo '<td ' . $attributes . '>' . $email . $phone . '</td>';
                            break;
                        case "address":
                            $address = $this->format_address($rec);
                            echo '<td ' . $attributes . '><address>' . $address  . '</address></td>';
                            break;
                        case "owed":
                            echo '<td ' . $attributes . '>' . $localeStrings['symbol'] . sprintf('%0.2f', QuipInvoices::getInstance()->db->get_total_owed_for_client($rec->id)) . '</td>';
                            break;
                        case "paid":
                            echo '<td ' . $attributes . '>' . $localeStrings['symbol'] . sprintf('%0.2f', QuipInvoices::getInstance()->db->get_total_paid_by_client($rec->id))  . '</td>';
                            break;
                    }
                }

                //Close the line
                echo'</tr>';
            }
        }
    }

    private function format_address($rec)
    {
        if ($rec->addressLine1 == "") return "";

        $address = $rec->addressLine1 . ($rec->addressLine2 == "" ? "" : ", $rec->addressLine2");
        $address .= $rec->addressCity == "" ? "" : ", $rec->addressCity";
        $address .= $rec->addressState == "" ? "" : ", $rec->addressState";
        $address .= $rec->addressZip == "" ? "" : ", $rec->addressZip";
        $address .= $rec->addressCountry == "" ? "" : ", $rec->addressCountry";

        return $address;

    }
}
