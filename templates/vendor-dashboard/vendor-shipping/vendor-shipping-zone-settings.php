<?php global $MVX; ?>

<div class="zone-component panel-content-padding">
	<div class="shipping-zone-list">
		<a href="javascript:void(0);" ><i class="mvx-font ico-back-arrow"></i> <?php  esc_html_e('Shipping Zones', 'multivendorx'); ?></a>
		<hr>
	</div>
	<div class="form-group"> 
		<label class="control-label col-sm-3 col-md-3"><?php esc_html_e('Zone Name', 'multivendorx'); ?></label>
		<div class="col-md-6 col-sm-9"><?php echo esc_attr($zones['data']['zone_name']); ?></div>
	</div>
	<div class="form-group"> 
		<label class="control-label col-sm-3 col-md-3"><?php esc_html_e('Zone region', 'multivendorx'); ?></label>
		<div class="col-md-6 col-sm-9"><?php echo esc_attr($zones['formatted_zone_location']); ?></div>
	</div>		
	<div class="form-group">
	   	<div class="col-md-6 col-sm-9">
	    	<input id="zone_id" class="form-control" type="hidden" name="<?php echo esc_attr('mvx_shipping_zone['. $zone_id .'][_zone_id]'); ?>" value="<?php echo esc_attr($zone_id); ?>">
	   	</div>
	</div>
	<?php if ($show_limit_location_link && $zone_id !== 0) { ?>
		<div class="form-group">
		   	<label for="" class="control-label col-sm-3 col-md-3"><?php esc_html_e( 'Limit Zone Location', 'multivendorx' ); ?></label>
		   	<div class="col-md-6 col-sm-9">
		    	<input id="limit_zone_location" class="form-control" type="checkbox" name="<?php echo esc_attr('mvx_shipping_zone['. $zone_id .'][_limit_zone_location]'); ?>" value="1" <?php checked( $want_to_limit_location, 1 ); ?>>
		   	</div>
		</div>
	<?php } ?>
	<?php if ($show_state_list) { ?>
		<div class="form-group hide_if_zone_not_limited">
		   	<label for="" class="control-label col-sm-3 col-md-3"><?php esc_html_e( 'Select specific states', 'multivendorx' ); ?></label>
		   	<div class="col-md-6 col-sm-9">
		    	<select id="select_zone_states" class="form-control" name="<?php echo esc_attr('mvx_shipping_zone['. $zone_id .'][_select_zone_states][]'); ?>" multiple>
		    		<?php if ($state_key_by_country) {
		    			foreach ($state_key_by_country as $key => $value) { ?>
		    				<option value="<?php echo $key; ?>" <?php selected( in_array( $key, $states ), true ); ?>><?php echo esc_html($value); ?></option>
		    			<?php 
		    			}
		    		} ?>
		    	</select>
		   	</div>
		</div>
	<?php } ?>
	<?php if ($show_post_code_list) { ?>
		<div class="form-group hide_if_zone_not_limited">
		   	<label for="" class="control-label col-sm-3 col-md-3"><?php esc_html_e( 'Set your postcode', 'multivendorx' ); ?></label>
		   	<div class="col-md-6 col-sm-9">
		    	<input id="select_zone_postcodes" class="form-control" type="text" name="<?php echo esc_attr('mvx_shipping_zone['. $zone_id .'][_select_zone_postcodes]'); ?>" value="<?php echo esc_attr($postcodes); ?>" placeholder="<?php esc_attr_e( 'Postcodes need to be comma separated', 'multivendorx' ); ?>">
		   	</div>
		</div>
	<?php } ?>
	<div class="mvx-zone-method-wrapper form-group mt-10">
		<label class="control-label col-sm-3 col-md-3 mvx-zone-method-heading" for="_sku">
			<?php esc_html_e( 'Shipping methods', 'multivendorx' ); ?>
            <div class="form-text mt-10 small"><?php esc_html_e('Add your shipping method for appropiate zone', 'multivendorx'); ?></div>
        </label> 
		<div class="mvx-zone-method-content col-md-9 col-sm-9">
			<table class="table mvx-table zone-method-table table-bordered">
				<thead>
					<tr>
						<th class="title"><?php esc_html_e('Title', 'multivendorx'); ?></th>
						<th class="enabled"><?php esc_html_e('Enabled', 'multivendorx'); ?></th> 
						<th class="description"><?php esc_html_e('Description', 'multivendorx'); ?></th>
						<th class="action"><?php esc_html_e('Action', 'multivendorx'); ?></th>
					</tr>
				</thead> 
				<tbody class="sortable">
					<?php 
					if (empty($vendor_shipping_methods)) { ?> 
						<tr>
							<td colspan="4"><?php esc_html_e( 'You can add multiple shipping methods within this zone. Only customers within the zone will see them.', 'multivendorx' ); ?></td>
						</tr>
						<?php 
					} else { 
						foreach ($vendor_shipping_methods as $vendor_shipping_method) { ?>
							<tr id="item-<?php echo $vendor_shipping_method['instance_id'] ?>">
								<td><?php echo esc_html($vendor_shipping_method['title']); ?>
									<div data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>' class="row-actions edit_del_actions">
									</div>
								</td>
								<td> 
									<input id="method_status" class="form-control method-status" type="checkbox" name="<?php echo 'method_status'; ?>" value="<?php echo esc_attr($vendor_shipping_method['instance_id']); ?>" <?php checked( ( $vendor_shipping_method['enabled'] == "yes" ), true ); ?>>
								</td>
								<td><?php echo esc_html($vendor_shipping_method['settings']['description']); ?></td>
								<td>
									<div class="col-actions edit_del_actions" data-zone_id="<?php echo esc_attr($zone_id); ?>" data-instance_id="<?php echo esc_attr($vendor_shipping_method['instance_id']); ?>" data-method_id="<?php echo esc_attr($vendor_shipping_method['id']); ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>'>
										<span class="edit"><a href="javascript:void(0);" class="edit-shipping-method" title="<?php esc_attr_e( 'Edit', 'multivendorx' ) ?>"><i class="mvx-font ico-edit-pencil-icon"></i></a>
										</span>|
										<span class="delete"><a class="delete-shipping-method" href="javascript:void(0);" title="<?php esc_attr_e( 'Delete', 'multivendorx' ) ?>"><i class="mvx-font ico-delete-icon"></i></a></span>
									</div>
								</td>
							</tr>
						<?php 
					}
				}
				?>
				</tbody>
			</table>
			<a href="javascript:void(0);" class="btn btn-default mvx-zone-method-add-btn show-shipping-methods"><i class="fa fa-plus"></i><?php esc_html_e( 'Add Shipping Method', 'multivendorx' ) ?></a>
		</div>
		<?php 

		$MVX->template->get_template( 'vendor-dashboard/vendor-shipping/vendor-edit-shipping-method.php' );
		$MVX->template->get_template( 'vendor-dashboard/vendor-shipping/vendor-add-shipping-method.php' );
		?>
	</div>
</div>

<script>
	jQuery(document).ready(function($) {
		$(".sortable").sortable({
			axis: 'y',
			update: function (event, ui) {
				var data_detail = $(this).sortable('serialize');
				var data = {
					action: 'mvx_vendor_zone_shipping_order',
					data_detail: data_detail,
					security: '<?php echo wp_create_nonce('mvx-shipping-zone'); ?>',
				};
				$.post("<?php echo admin_url('admin-ajax.php') ?>", data, function () {});
			}
		});
	});
</script>