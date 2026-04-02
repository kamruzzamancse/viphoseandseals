<?php
/**
 * Form Handler for Hose Builder
 * Version: 8.0 - Fixed assembly data storage
 */

// Use wp_loaded instead of init for better WooCommerce compatibility
add_action('wp_loaded', function() {

    if (isset($_POST['hb_add_to_cart'])) {

        // Check if WooCommerce is active and cart is available
        if (!function_exists('WC') || !WC()->cart) {
            wc_add_notice('WooCommerce is not active or cart is not available.', 'error');
            return;
        }

        $errors = array();
        
        // Get selected product ID from main dropdown
        $selected_product_raw = isset($_POST['selected_product']) ? sanitize_text_field($_POST['selected_product']) : '';
        $selected_product_id = 0;

        if (is_numeric($selected_product_raw)) {
            $selected_product_id = intval($selected_product_raw);
        } else if (strpos($selected_product_raw, 'product_') === 0) {
            $selected_product_id = intval(str_replace('product_', '', $selected_product_raw));
        } else {
            $selected_product_id = intval($selected_product_raw);
        }
        
        if (!$selected_product_id) {
            $errors[] = 'Please select a product before adding to cart.';
        }
        
        // Get Hose End selections
        $hose_end_1_selected = isset($_POST['hose_end_1_selected']) ? sanitize_text_field($_POST['hose_end_1_selected']) : '';
        $hose_end_2_selected = isset($_POST['hose_end_2_selected']) ? sanitize_text_field($_POST['hose_end_2_selected']) : '';
        
        $hose_end_1_type = isset($_POST['hose_end_1_type']) ? sanitize_text_field($_POST['hose_end_1_type']) : '';
        $hose_end_2_type = isset($_POST['hose_end_2_type']) ? sanitize_text_field($_POST['hose_end_2_type']) : '';
        
        // Extract product IDs from hose end selections
        $left_product_id = 0;
        $right_product_id = 0;
        
        // Process Left Hose End
        if ($hose_end_1_type === 'carbon_steel') {
            if (!empty($hose_end_1_selected) && is_numeric($hose_end_1_selected)) {
                $left_product_id = intval($hose_end_1_selected);
            } else if (!empty($hose_end_1_selected) && ctype_digit($hose_end_1_selected)) {
                $left_product_id = intval($hose_end_1_selected);
            } else if (!empty($hose_end_1_selected) && strpos($hose_end_1_selected, 'product_') === 0) {
                $left_product_id = intval(str_replace('product_', '', $hose_end_1_selected));
            } else if (!empty($hose_end_1_selected) && strpos($hose_end_1_selected, '[') === 0) {
                $decoded = json_decode($hose_end_1_selected, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $last_item = end($decoded);
                    if (is_numeric($last_item)) {
                        $left_product_id = intval($last_item);
                    } else if (strpos($last_item, 'product_') === 0) {
                        $left_product_id = intval(str_replace('product_', '', $last_item));
                    }
                }
            }
        }
        
        // Process Right Hose End
        if ($hose_end_2_type === 'carbon_steel') {
            if (!empty($hose_end_2_selected) && is_numeric($hose_end_2_selected)) {
                $right_product_id = intval($hose_end_2_selected);
            } else if (!empty($hose_end_2_selected) && ctype_digit($hose_end_2_selected)) {
                $right_product_id = intval($hose_end_2_selected);
            } else if (!empty($hose_end_2_selected) && strpos($hose_end_2_selected, 'product_') === 0) {
                $right_product_id = intval(str_replace('product_', '', $hose_end_2_selected));
            } else if (!empty($hose_end_2_selected) && strpos($hose_end_2_selected, '[') === 0) {
                $decoded = json_decode($hose_end_2_selected, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $last_item = end($decoded);
                    if (is_numeric($last_item)) {
                        $right_product_id = intval($last_item);
                    } else if (strpos($last_item, 'product_') === 0) {
                        $right_product_id = intval(str_replace('product_', '', $last_item));
                    }
                }
            }
        }
        
        // Validation
        $left_selected = false;
        $right_selected = false;
        
        if ($selected_product_id) {
            if ($hose_end_1_type === 'carbon_steel') {
                if ($left_product_id > 0) $left_selected = true;
            } elseif ($hose_end_1_type === 'open') {
                $left_selected = true;
            }
            
            if ($hose_end_2_type === 'carbon_steel') {
                if ($right_product_id > 0) $right_selected = true;
            } elseif ($hose_end_2_type === 'open') {
                $right_selected = true;
            }
            
            if (!$left_selected && !$right_selected) {
                $errors[] = 'Please select a left-side hose end for this assembly.';
                $errors[] = 'Please select a right-side hose end for this assembly.';
            } elseif (!$left_selected) {
                $errors[] = 'Please select a left-side hose end for this assembly.';
            } elseif (!$right_selected) {
                $errors[] = 'Please select a right-side hose end for this assembly.';
            }
        }
        
        if (!empty($errors)) {
            foreach ($errors as $error) {
                wc_add_notice($error, 'error');
            }
            return;
        }

        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        if ($quantity < 1) $quantity = 1;

        // Get additional form data
        $your_part_number = isset($_POST['part_number']) ? sanitize_text_field($_POST['part_number']) : '';
        $set_bom = isset($_POST['set_bom']) ? 'Yes' : 'No';
        $instructions = isset($_POST['instructions']) ? sanitize_textarea_field($_POST['instructions']) : '';
        $length_value = isset($_POST['length_value']) ? sanitize_text_field($_POST['length_value']) : '5000';
        $length_unit = isset($_POST['length_unit']) ? sanitize_text_field($_POST['length_unit']) : 'inches';
        
        // Create assembly group ID
        $assembly_group = 'hose_assembly_' . time() . '_' . uniqid();
        
        $cart_updated = false;
        
        // Get Department name
        $department_name = '';
        $hose_category = get_term_by('slug', 'hose', 'product_cat');
        if ($hose_category) {
            $product_cats = wp_get_post_terms($selected_product_id, 'product_cat');
            foreach ($product_cats as $cat) {
                if ($cat->parent == $hose_category->term_id) {
                    $department_name = $cat->name;
                }
            }
        }
        
        // Get Hose Type name
        $hose_type_name = '';
        $hose_type_terms = wp_get_post_terms($selected_product_id, 'product_cat');
        foreach ($hose_type_terms as $term) {
            if ($term->parent != 0 && (!isset($hose_category) || $term->parent != $hose_category->term_id)) {
                $hose_type_name = $term->name;
            }
        }
        
        // Get selected product name
        $selected_product_obj = wc_get_product($selected_product_id);
        $selected_product_name = $selected_product_obj ? $selected_product_obj->get_name() : '';
        
        // Get left product name
        $left_product_name = '';
        if ($left_product_id > 0) {
            $left_product_obj = wc_get_product($left_product_id);
            $left_product_name = $left_product_obj ? $left_product_obj->get_name() : '';
        }
        
        // Get right product name
        $right_product_name = '';
        if ($right_product_id > 0) {
            $right_product_obj = wc_get_product($right_product_id);
            $right_product_name = $right_product_obj ? $right_product_obj->get_name() : '';
        }
        
        // Assembly data for display
        $assembly_data = array(
            'assembly_group' => $assembly_group,
            'quantity' => $quantity,
            'department' => $department_name,
            'hose_type' => $hose_type_name,
            'selected_product' => $selected_product_name,
            'left_hose_end_id' => $left_product_id,
            'right_hose_end_id' => $right_product_id,
            'left_hose_end_name' => $left_product_name,
            'right_hose_end_name' => $right_product_name,
            'left_hose_end_type' => $hose_end_1_type,
            'right_hose_end_type' => $hose_end_2_type,
            'your_part_number' => $your_part_number,
            'set_bom' => $set_bom,
            'instructions' => $instructions,
            'length_value' => $length_value,
            'length_unit' => $length_unit,
        );
        
        // 1. Add Main Hose Product with assembly data
        if ($selected_product_id > 0) {
            $cart_item_key = WC()->cart->add_to_cart($selected_product_id, $quantity);
            if ($cart_item_key) {
                $cart_updated = true;
                // Store assembly metadata with the main product
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_data'] = $assembly_data;
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_group'] = $assembly_group;
                WC()->cart->cart_contents[$cart_item_key]['hb_is_main'] = true;
            }
        }
        
        // 2. Add Left Hose End Product (if selected and not open)
        if ($hose_end_1_type === 'carbon_steel' && $left_product_id > 0) {
            $cart_item_key = WC()->cart->add_to_cart($left_product_id, $quantity);
            if ($cart_item_key) {
                $cart_updated = true;
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_component'] = 'left_end';
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_group'] = $assembly_group;
            }
        }
        
        // 3. Add Right Hose End Product (if selected and not open)
        if ($hose_end_2_type === 'carbon_steel' && $right_product_id > 0) {
            $cart_item_key = WC()->cart->add_to_cart($right_product_id, $quantity);
            if ($cart_item_key) {
                $cart_updated = true;
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_component'] = 'right_end';
                WC()->cart->cart_contents[$cart_item_key]['hb_assembly_group'] = $assembly_group;
            }
        }
        
        // Check if products were added successfully
        if ($cart_updated) {
            wc_add_notice('Hose assembly has been added to your cart.', 'success');
            wp_redirect(wc_get_cart_url());
            exit;
        } else {
            wc_add_notice('Failed to add products to cart. Please try again.', 'error');
            return;
        }
    }
});

// Save assembly data when order is created
add_action('woocommerce_checkout_create_order_line_item', 'hb_save_assembly_data_to_order_item', 10, 4);

function hb_save_assembly_data_to_order_item($item, $cart_item_key, $values, $order) {
    // Check if this cart item has assembly data
    if (isset($values['hb_assembly_data']) && !empty($values['hb_assembly_data'])) {
        $assembly_data = $values['hb_assembly_data'];
        
        // Save each field as order item meta
        foreach ($assembly_data as $key => $value) {
            if (!empty($value)) {
                $item->add_meta_data('_hb_' . $key, $value);
            }
        }
        
        // Also save a flag to identify this is a hose assembly item
        $item->add_meta_data('_hb_is_assembly_item', 'yes');
        
        // Save assembly group for grouping items
        if (isset($values['hb_assembly_group'])) {
            $item->add_meta_data('_hb_assembly_group', $values['hb_assembly_group']);
        }
        
        // Save component type if applicable
        if (isset($values['hb_assembly_component'])) {
            $item->add_meta_data('_hb_component_type', $values['hb_assembly_component']);
        }
        
        // Save main product flag
        if (isset($values['hb_is_main'])) {
            $item->add_meta_data('_hb_is_main', $values['hb_is_main']);
        }
    }
}

// Add to form-handler.php - Display in email
add_action('woocommerce_email_before_order_table', 'hb_add_assembly_data_to_emails', 10, 4);
function hb_add_assembly_data_to_emails($order, $sent_to_admin, $plain_text, $email) {
    if ($plain_text) return;
    
    $has_assembly = false;
    $assembly_items = array();
    
    foreach ($order->get_items() as $item) {
        $is_assembly = $item->get_meta('_hb_is_assembly_item');
        if ($is_assembly === 'yes') {
            $has_assembly = true;
            $assembly_items[] = $item;
        }
    }
    
    if (!$has_assembly) return;
    ?>
    <div style="margin: 20px 0; padding: 15px; background: #f0f9ff; border-left: 4px solid #3b82f6;">
        <h3 style="margin: 0 0 10px 0;">🔧 Hose Assembly Specifications</h3>
        <?php foreach ($assembly_items as $item): 
            $your_part = $item->get_meta('_hb_your_part_number');
            $instructions = $item->get_meta('_hb_instructions');
            $length = $item->get_meta('_hb_length_value') . ' ' . $item->get_meta('_hb_length_unit');
        ?>
            <p style="margin: 5px 0;">
                <strong>Product:</strong> <?php echo $item->get_name(); ?><br>
                <?php if ($your_part): ?><strong>Part Number:</strong> <?php echo $your_part; ?><br><?php endif; ?>
                <?php if ($length): ?><strong>Length:</strong> <?php echo $length; ?><br><?php endif; ?>
                <?php if ($instructions): ?><strong>Instructions:</strong> <?php echo $instructions; ?><?php endif; ?>
            </p>
        <?php endforeach; ?>
    </div>
    <?php
}