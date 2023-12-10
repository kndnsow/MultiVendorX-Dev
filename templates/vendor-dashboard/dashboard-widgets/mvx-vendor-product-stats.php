<?php

$total = '100';
if($total_products == 0){
     $publish_products_percentage = '0';
     $pending_products_percentage = '0';
     $draft_products_percentage = '0';
     $trashed_products_percentage = '0';
}else {
    $publish_products_percentage = round((float)$publish_products_count / $total_products * $total, 2);
    $pending_products_percentage = round((float)$pending_products_count / $total_products * $total, 2);
    $draft_products_percentage = round((float)$draft_products_count / $total_products * $total, 2);
    $trashed_products_percentage = round((float)$trashed_products_count / $total_products * $total, 2);
}
if($total_products == 0) {
    _e('No products Available.', 'multivendorx');
} else {
?>
<div class="mvx_product_stats_wrap">
    <div class="product-stat-chart">
        <div class="align-self-end product-stat-bar publish-stat">
            <div class="stat-percentage-holder" style="height: <?php echo $publish_products_percentage; ?>%; background-color: #c25244">
                <span class="stat-percentage-count" style="background-color: #af3b2e;"><?php echo $publish_products_percentage; ?>%</span>
            </div>
        </div>
        <div class="align-self-end product-stat-bar pending-stat">
            <div class="stat-percentage-holder" style="height: <?php echo $pending_products_percentage; ?>%; background-color: #a65478">
                <span class="stat-percentage-count" style="background-color: #914164;"><?php echo $pending_products_percentage; ?>%</span>
            </div>
        </div>
        <div class="align-self-end product-stat-bar draft-stat">
            <div class="stat-percentage-holder" style="height: <?php echo $draft_products_percentage; ?>%; background-color: #d38c4e">
                <span class="stat-percentage-count" style="background-color: #ba7237;"><?php echo $draft_products_percentage; ?>%</span>
            </div>
        </div>
        <div class="align-self-end product-stat-bar not-approved-stat">
            <div class="stat-percentage-holder" style="height: <?php echo $trashed_products_percentage; ?>%; background-color: #519b9e">
                <span class="stat-percentage-count" style="background-color: #358085;"><?php echo $trashed_products_percentage; ?>%</span>
            </div>
        </div>
    </div>
    <div class="p_stats_data">
        <ul class="list-group">
            <li class="list-group-item justify-content-between">
                <div class="stat-left-border" style="background-color:#c35244;"></div>
                <p><?php _e('Published', 'multivendorx');?></p>
                <span class="badge badge-default badge-pill"><?php echo $publish_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between">
                <div class="stat-left-border" style="background-color:#a75579;"></div>
                <p><?php _e('Pending', 'multivendorx');?></p>
                <span class="badge badge-default badge-pill"><?php echo $pending_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between">
                <div class="stat-left-border" style="background-color:#d28c4d;"></div>
                <p><?php _e('Draft', 'multivendorx');?></p>
                <span class="badge badge-default badge-pill"><?php echo $draft_products_count; ?></span>
            </li>
            <li class="list-group-item justify-content-between">
                <div class="stat-left-border" style="background-color:#519a9e;"></div>
                <p><?php _e('Not Approved', 'multivendorx');?></p>
                <span class="badge badge-default badge-pill"><?php echo $trashed_products_count; ?></span>
            </li>
        </ul>
    </div>
</div>
<?php } 