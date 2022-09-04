<?php
if ( ! defined('ABSPATH') ) {
      die('Direct access not permitted.');
}

if(!class_exists('WP_List_Table')){
      require_once( ABSPATH . 'wp-admin/includes/screen.php' );
      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


////////////  ----  Links Errors List Table  ---- ////////////////////////
class Links_list_table extends WP_List_Table{

      // Define table columns
      function get_columns(){
            $columns = array(
                  'url' => 'URL',
                  'state'    => 'State',
                  'source'      => 'Source'
            );
            return $columns;
      }

      // Bind table with columns, data and all
      function prepare_items(){
            $states = array();
            $state = array();
            $data =$this->get_post_data();  
            $index__ = 0;
            foreach($data as  $data_array){        
                  $states = $data_array['state'];
                  $source = $data_array['source'];        
                  foreach($states as $i => $link){
                        $state[$index__]['state'] = $link['state'];
                        $state[$index__]['url'] = $link['url'];
                        $state[$index__]['source'] =  $source ;
                        $index__++;
                  }
            }   
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $state;
      }

      // Get Post data
      private function get_post_data(){
        $args = array(
            'post_status' => 'publish',         
            'post_type' => 'post',
            'meta_query' => array(
                  'relation' => 'AND',
                 array(
                    'key' => 'id_cron_post_checked',
                    'value' => '1',
                    'compare' => '='
                ) ,
                array(
                  'key' => 'id_cron_post_links_error',
                  'value' => '1',
                  'compare' => '='
               )
            )   
        );
        $query = new WP_Query( $args );   
        $res = array();
        $index = 0;
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                  $query->the_post();
                  $id = get_the_ID();
                  $url_error = get_post_meta($id, 'url_error', true );
                  $res[$index]['state'] = $url_error;
                  $res[$index]['source'] = get_the_permalink(get_the_ID());
                  $res[$index]['name'] = get_the_title();
                  $index++;
            }         
        } else {
            $test ='no posts found' ;  
        }
        return $res;
      }

      // bind data with column
      function column_default($item, $column_name) {
            switch ($column_name) {         
                  case 'url':
                  case 'source':
                        return $item[$column_name];
                  case 'state':
                       return $item[$column_name];
                  default:
                        return print_r($item, true); 
            }
      }

      // Add sorting to columns
      protected function get_sortable_columns(){
            $sortable_columns = array(
                  'url'  => array('url', false),
                  'state' => array('state', false),
                  'source'   => array('source', true)
            );
            return $sortable_columns;
      }

      // Sorting function
      function usort_reorder($a, $b){
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'url';
            // If no order, default to asc
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            // Determine sort order
            $result = strcmp($a[$orderby], $b[$orderby]);
            // Send final sort direction to usort
            return ($order === 'asc') ? $result : -$result;
      }
}

// Adding menu
function mc_add_menu_items(){
      add_menu_page('Monitor Links - Post Content', 'Monitor Links - Post Content', 'activate_plugins', 'Links_list_table', 'mc_url_list_init');
}
add_action('admin_menu', 'mc_add_menu_items');


// Plugin menu callback function
function mc_url_list_init(){
      // Creating an instance
      $empTable = new Links_list_table();
      echo '<div class="wrap"><h2>Monitor Links - Post Content</h2>';
      echo '<h4>Show all conflicting links in POSTs Content, checked by Cron</h4>';
      // Prepare table
      $empTable->prepare_items();
      // Display table
      $empTable->display();
      echo '</div>';
}



