<?php

add_action('init', function() {

    if (isset($_POST['hb_add_to_cart'])) {

        if (!function_exists('WC')) {
            wc_add_notice('WooCommerce is not active.', 'error');
            return;
        }

        $errors = array();
        
        // Get selected product ID
        $selected_product_id = isset($_POST['selected_product']) ? intval($_POST['selected_product']) : 0;
        
        if (!$selected_product_id) {
            $errors[] = 'Please select a product before adding to cart.';
        }
        
        // Get Hose End selections
        $hose_end_1_selected = isset($_POST['hose_end_1_selected']) ? $_POST['hose_end_1_selected'] : '';
        $hose_end_2_selected = isset($_POST['hose_end_2_selected']) ? $_POST['hose_end_2_selected'] : '';
        
        $hose_end_1_type = isset($_POST['hose_end_1_type']) ? $_POST['hose_end_1_type'] : '';
        $hose_end_2_type = isset($_POST['hose_end_2_type']) ? $_POST['hose_end_2_type'] : '';
        
        // Check Left Hose End (only if product is selected)
        $left_selected = false;
        $right_selected = false;
        
        if ($selected_product_id) {
            // Check Left Hose End
            if ($hose_end_1_type === 'carbon_steel' && !empty($hose_end_1_selected) && $hose_end_1_selected !== '') {
                $left_selected = true;
            } elseif ($hose_end_1_type === 'open') {
                $left_selected = true;
            }
            
            // Check Right Hose End
            if ($hose_end_2_type === 'carbon_steel' && !empty($hose_end_2_selected) && $hose_end_2_selected !== '') {
                $right_selected = true;
            } elseif ($hose_end_2_type === 'open') {
                $right_selected = true;
            }
            
            // Validation messages
            if (!$left_selected && !$right_selected) {
                $errors[] = 'Please select a left-side hose end for this assembly.';
                $errors[] = 'Please select a right-side hose end for this assembly.';
            } elseif (!$left_selected) {
                $errors[] = 'Please select a left-side hose end for this assembly.';
            } elseif (!$right_selected) {
                $errors[] = 'Please select a right-side hose end for this assembly.';
            }
        }
        
        // If there are errors, show them and stay on the same page
        if (!empty($errors)) {
            foreach ($errors as $error) {
                wc_add_notice($error, 'error');
            }
            // Stay on the same page - no redirect
            return;
        }

        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        if ($quantity < 1) $quantity = 1;

        // Get Department and Hose Type names (for display)
        $department_name = '';
        $hose_type_name = '';
        
        if (isset($_POST['department']) && !empty($_POST['department'])) {
            $department_term = get_term_by('id', intval($_POST['department']), 'product_cat');
            if ($department_term) {
                $department_name = $department_term->name;
            }
        }
        
        if (isset($_POST['hose_type']) && !empty($_POST['hose_type'])) {
            $hose_type_term = get_term_by('id', intval($_POST['hose_type']), 'product_cat');
            if ($hose_type_term) {
                $hose_type_name = $hose_type_term->name;
            }
        }

        // Get selected product name
        $selected_product_name = '';
        $selected_product = wc_get_product($selected_product_id);
        if ($selected_product) {
            $selected_product_name = $selected_product->get_name();
        }

        // Format Hose End selections for display
        $hose_end_left_display = '';
        $hose_end_right_display = '';
        
        // Format Left Hose End
        if ($hose_end_1_type === 'open') {
            $hose_end_left_display = 'Open (No End)';
        } elseif ($hose_end_1_type === 'carbon_steel' && $hose_end_1_selected && $hose_end_1_selected !== '') {
            $decoded = json_decode($hose_end_1_selected, true);
            if (is_array($decoded) && !empty($decoded)) {
                $names = [];
                foreach ($decoded as $item_id) {
                    $term = get_term_by('id', $item_id, 'product_cat');
                    if ($term) {
                        $names[] = $term->name;
                    } else {
                        $product = wc_get_product($item_id);
                        if ($product) {
                            $names[] = $product->get_name();
                        }
                    }
                }
                $hose_end_left_display = 'Carbon Steel → ' . implode(' → ', $names);
            } elseif (is_numeric($hose_end_1_selected)) {
                $product = wc_get_product($hose_end_1_selected);
                if ($product) {
                    $hose_end_left_display = 'Carbon Steel → ' . $product->get_name();
                }
            } else {
                $hose_end_left_display = 'Carbon Steel → ' . $hose_end_1_selected;
            }
        }
        
        // Format Right Hose End
        if ($hose_end_2_type === 'open') {
            $hose_end_right_display = 'Open (No End)';
        } elseif ($hose_end_2_type === 'carbon_steel' && $hose_end_2_selected && $hose_end_2_selected !== '') {
            $decoded = json_decode($hose_end_2_selected, true);
            if (is_array($decoded) && !empty($decoded)) {
                $names = [];
                foreach ($decoded as $item_id) {
                    $term = get_term_by('id', $item_id, 'product_cat');
                    if ($term) {
                        $names[] = $term->name;
                    } else {
                        $product = wc_get_product($item_id);
                        if ($product) {
                            $names[] = $product->get_name();
                        }
                    }
                }
                $hose_end_right_display = 'Carbon Steel → ' . implode(' → ', $names);
            } elseif (is_numeric($hose_end_2_selected)) {
                $product = wc_get_product($hose_end_2_selected);
                if ($product) {
                    $hose_end_right_display = 'Carbon Steel → ' . $product->get_name();
                }
            } else {
                $hose_end_right_display = 'Carbon Steel → ' . $hose_end_2_selected;
            }
        }

        // Prepare custom data for cart
        $custom_data = [
            'Department' => $department_name,
            'Hose Type' => $hose_type_name,
            'Selected Product' => $selected_product_name,
            'Hose End (Left)' => $hose_end_left_display,
            'Hose End (Right)' => $hose_end_right_display,
            'Your Part Number' => sanitize_text_field($_POST['part_number']),
            'Set BOM' => isset($_POST['set_bom']) ? 'Yes' : 'No',
            'Instructions/Comments' => sanitize_textarea_field($_POST['instructions']),
            'End to End Length' => sanitize_text_field($_POST['length_value']) . ' inches',
            'Quantity' => $quantity,
        ];

        // Remove empty values
        $custom_data = array_filter($custom_data, function($value) {
            return $value !== '' && $value !== null;
        });

        // Add to cart
        $cart_item_key = WC()->cart->add_to_cart($selected_product_id, $quantity, 0, [], [
            'hb_data' => $custom_data
        ]);

        // Check if product was added successfully
        if ($cart_item_key) {
            // Add success notice
            wc_add_notice('Hose assembly has been added to your cart.', 'success');
            wp_redirect(wc_get_cart_url());
            exit;
        } else {
            wc_add_notice('Failed to add product to cart. Please try again.', 'error');
            return;
        }
    }
});