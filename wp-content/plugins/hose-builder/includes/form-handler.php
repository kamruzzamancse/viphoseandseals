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