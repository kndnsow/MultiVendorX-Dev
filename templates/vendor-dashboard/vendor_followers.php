<?php
$vendor_followers_list_table_headers = apply_filters('mvx_vendor_followers_list_table_headers', array(
    'customer_name'   => array('label' => __( 'Customer Name', 'multivendorx' )),
    'date'  => array('label' => __( 'Date', 'multivendorx' )),
    'email'  => array('label' => __( 'Email', 'multivendorx' )),
), get_current_user_id());

$mvx_vendor_followed_by_customer = get_user_meta( get_current_vendor_id(), 'mvx_vendor_followed_by_customer', true ) ? get_user_meta( get_current_vendor_id(), 'mvx_vendor_followed_by_customer', true ) : array();
if ( !empty($mvx_vendor_followed_by_customer) ) {
    ?>
    <table class="table table-striped table-bordered" width="100%">
        <tr>
            <?php 
                if ( $vendor_followers_list_table_headers ) :
                    foreach ( $vendor_followers_list_table_headers as $header ) { ?>
                        <th><?php if (isset($header['label'])) echo $header['label']; ?></th>         
                    <?php }
                endif;
            ?>
        </tr>
    <?php
    foreach ( $mvx_vendor_followed_by_customer as $value_followed ) {
        if ( !empty($value_followed) ) {
            $row = array();
            $user_details = get_user_by( 'ID', $value_followed['user_id'] );
            if ( !$user_details ) continue;
            echo '<tr><td>' . $row ['customer_name'] = $user_details->data->display_name . '</td>';
            echo '<td>' . $row ['date'] = esc_html(human_time_diff(strtotime($value_followed['timestamp']))) . esc_html(' ago', 'multivendorx') . '</td>';
            echo '<td>' . $row ['customer_name'] = $user_details->user_email . '</td></tr>';
        }
    }
}else {
    echo esc_html_e('No customer follows you till now.', 'multivendorx');          
}