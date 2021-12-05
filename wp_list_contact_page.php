<?php
add_action('admin_menu', 'add_menu_item_contacter');

function add_menu_item_contacter()
{
    add_menu_page(
        'Contact', // Page Title
        'Callers list <span class="awaiting-mod">Here Count Of contact witout reply</span>', // Menu Title
        'manage_options', // Capabiliy
        'callers', // Menu_slug
        'do_function', // function
        'dashicons-buddicons-pm', // icon_url
        9  // position
    );


}
function own_data_list(){
    $own=new Events_List_Table();
    $own->prepare_items();
    $own->display();
    
    
    }

function do_function()
{
    own_data_list();
}
  







if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Events_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular'  => 'wp_list_event',
            'plural'    => 'wp_list_events',
            'ajax'      => false
        ));
    }

    function column_default($item, $column_name)
    {
        switch($column_name) {
            case 'id':
                case 'full_name':
                case 'adrees':
                case 'ville':
                case 'phone':
                case 'sujet':
                case 'email':
                case 'message':
                return ucfirst($item[$column_name]);
            default:
                return print_r($item,true);
        }
    }

    function column_title($item)
    {
        $actions = array(
            'edit'          => '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'edit', 'id' => $item['id'])).'">Edit</a>',
            'registrants'   => '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'registrants', 'id' => $item['id'])).'">Registrants</a>',
            'export'        => '<a href="' . admin_url() . 'downloads/registrants?id=' . $item['id'] . '">Export CSV</a>',
        );

        if ($item['full_name'] === 'new') {
            $actions['cancel'] = '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'cancel', 'id' => $item['id'])).'">Cancel</a>';
        } else {
            $actions['uncancel'] = '<a href="'.MvcRouter::admin_url(array('controller' => 'events', 'action' => 'uncancel', 'id' => $item['id'])).'">Uncancel</a>';
        }

        return sprintf(
            '%1$s %3$s',
            $item['full_name'],
            $item['id'],
            $this->row_actions($actions)
        );
    }

    function column_start_date($item)
    {
        return date('F jS, Y h:i a', strtotime($item['phone']));
    }

    function column_end_date($item)
    {
        return date('F jS, Y h:i a', strtotime($item['ville']));
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'id'          => '#',
            'full_name'       => 'full ',
            'adrees' => 'Adress',
            'ville'        => 'City',
            'phone'    => 'Phone Numbers',
            'sujet'      => 'Subject',
            'email'    => 'Mail',
            'message'    => 'Content Of Message',
        );

        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id'          => array('id' , false),
            'full_name'          => array('full_name' , false),
            'adrees'          => array('adrees' , false),
            'ville'          => array('ville' , false),
            'phone'          => array('phone' , false),
            'sujet'          => array('sujet' , false),
            'email'          => array('email' , false),
            'message'          => array('message' , false),
        );

        return $sortable_columns;
    }

    function get_bulk_actions()
    {
      // add action will include
        $actions = array(
            'cancel'    => 'Cancel Events',
            'delete'    => 'Delete',
        );

        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;

        if ('delete' === $this->current_action()) {
            foreach ($_GET['wp_list_event'] as $event) {
                // $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            }
        }

        if ('cancel' === $this->current_action()) {
            // blah blah
        }
    }

    function custom_bulk_admin_notices()
    {
        echo 'Hello.';
    }

    function prepare_items()
    {
      // get data from data base and sort in table
        global $wpdb;
$table_name=$wpdb->prefix."contact";
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'full_name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM `".$table_name."` ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items'   => $total_items,
            'per_page'      => $per_page,
            'total_pages'   => ceil($total_items / $per_page),
        ));
    }
}



 
