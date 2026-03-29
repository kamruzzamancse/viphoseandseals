<?php

// Show in Cart
add_filter('woocommerce_get_item_data', function($item_data, $cart_item) {

    if (isset($cart_item['hb_data'])) {
        foreach ($cart_item['hb_data'] as $key => $value) {
            if (!empty($value)) {
                $item_data[] = [
                    'name' => $key,
                    'value' => $value
                ];
            }
        }
    }

    return $item_data;

}, 10, 2);

// Save to Order
add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values) {

    if (isset($values['hb_data'])) {
        foreach ($values['hb_data'] as $key => $value) {
            if (!empty($value)) {
                $item->add_meta_data($key, $value);
            }
        }
    }

}, 10, 3);