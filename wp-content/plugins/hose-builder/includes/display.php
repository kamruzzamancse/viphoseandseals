<?php
/**
 * Display functions for Hose Builder
 * Version: 8.0 - Dynamic product attributes from database
 */

// ============================================
// MAIN CART PAGE - Show in Cart
// ============================================

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

// ============================================
// SAVE TO ORDER
// ============================================

add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values) {

    if (isset($values['hb_data'])) {
        foreach ($values['hb_data'] as $key => $value) {
            if (!empty($value)) {
                $item->add_meta_data($key, $value);
            }
        }
    }

}, 10, 3);

// ============================================
// HELPER FUNCTION: Get product attribute value dynamically
// ============================================

function hb_get_product_attr_value($product_id, $attr_key, $attr_label = '') {
    $product = wc_get_product($product_id);
    if (!$product) return '';
    
    // 1. Try to get from product meta (ACF or custom fields)
    $meta_value = get_post_meta($product_id, '_' . $attr_key, true);
    if (!empty($meta_value) && !preg_match('/^field_/', $meta_value)) {
        return $meta_value;
    }
    
    // 2. Try to get from product short description
    $short_desc = $product->get_short_description();
    
    // Common patterns to search
    $patterns = [
        '/' . preg_quote($attr_label, '/') . ':\s*([^\s<]+)/i',
        '/' . preg_quote($attr_key, '/') . ':\s*([^\s<]+)/i',
        '/(?:' . preg_quote($attr_key, '/') . ')\s*=\s*"?([^"\s<]+)"?/i',
    ];
    
    if (!empty($attr_label)) {
        $patterns[] = '/' . preg_quote($attr_label, '/') . ':\s*([\d,\.]+)/i';
    }
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $short_desc, $matches)) {
            return $matches[1];
        }
    }
    
    // 3. Try to get from product attributes (taxonomy)
    $attributes = $product->get_attributes();
    foreach ($attributes as $attribute) {
        if ($attribute->get_name() === $attr_key || strpos($attribute->get_name(), $attr_key) !== false) {
            $terms = $product->get_attribute($attribute->get_name());
            if (!empty($terms)) {
                return $terms;
            }
        }
    }
    
    // 4. Try to get from product description
    $description = $product->get_description();
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $description, $matches)) {
            return $matches[1];
        }
    }
    
    return '';
}

// ============================================
// GET ALL PRODUCT ATTRIBUTES DYNAMICALLY
// ============================================

function hb_get_all_product_attributes($product_id) {
    $attributes = [];
    $product = wc_get_product($product_id);
    if (!$product) return $attributes;
    
    // Common attribute keys to look for
    $attr_keys = [
        'working_pressure' => 'Working Pressure (psi)',
        'burst_pressure' => 'Burst Pressure (psi)',
        'id' => 'ID (in)',
        'od' => 'OD (in)',
        'hose_id' => 'Hose ID',
        'pipe_size' => 'Pipe Size',
        'thread_size' => 'Thread Size',
        'material' => 'Material',
        'manufactured_in' => 'Manufactured In',
        'sku' => 'SKU',
    ];
    
    foreach ($attr_keys as $key => $label) {
        $value = hb_get_product_attr_value($product_id, $key, $label);
        if (!empty($value)) {
            $attributes[$key] = [
                'label' => $label,
                'value' => $value
            ];
        }
    }
    
    return $attributes;
}

// ============================================
// JAVASCRIPT FIX FOR MINI CART (Dynamic)
// ============================================

add_action('wp_footer', 'hb_fix_mini_cart_js_dynamic');
function hb_fix_mini_cart_js_dynamic() {
    // Preload product attributes data for all products in cart
    $cart = WC()->cart;
    $products_data = [];
    
    if ($cart && !$cart->is_empty()) {
        foreach ($cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            $attributes = hb_get_all_product_attributes($product_id);
            $products_data[$product_id] = $attributes;
        }
    }
    ?>
    <script type="text/javascript">
    // Product attributes data from PHP (dynamic)
    var hbProductAttributes = <?php echo json_encode($products_data); ?>;
    
    jQuery(document).ready(function($) {
        
        function fixMiniCartSpecs() {
            // Find all product items in mini cart
            $('.elementor-menu-cart__product').each(function() {
                var $product = $(this);
                var $productName = $product.find('.elementor-menu-cart__product-name');
                var productText = $productName.html();
                
                // Get product ID from remove button
                var $removeBtn = $product.find('.elementor_remove_from_cart_button, .remove_from_cart_button');
                var productId = $removeBtn.data('product_id');
                
                if (!productId) {
                    // Try to get from SKU
                    var sku = $removeBtn.data('product_sku');
                    // Find product ID by SKU (look through our data)
                    for (var id in hbProductAttributes) {
                        if (hbProductAttributes[id].sku && hbProductAttributes[id].sku.value === sku) {
                            productId = id;
                            break;
                        }
                    }
                }
                
                var attributes = hbProductAttributes[productId];
                if (!attributes) return;
                
                var newText = productText;
                
                // Replace each attribute dynamically
                for (var key in attributes) {
                    var attr = attributes[key];
                    var label = attr.label;
                    var value = attr.value;
                    
                    // Pattern to find the attribute in HTML
                    var pattern1 = new RegExp(label + ':\\s*field_[a-f0-9]+', 'gi');
                    var pattern2 = new RegExp(label + ':\\s*<[^>]*>[^<]*<[^>]*>', 'gi');
                    var pattern3 = new RegExp('<span[^>]*>' + label + '[^<]*<\\/span>[\\s]*<span[^>]*>field_[a-f0-9]+<\\/span>', 'gi');
                    
                    if (value) {
                        newText = newText.replace(pattern1, label + ': ' + value);
                        newText = newText.replace(pattern2, label + ': ' + value);
                        newText = newText.replace(pattern3, '<span>' + label + ':</span> <span>' + value + '</span>');
                    } else {
                        // Remove the attribute line if no value
                        newText = newText.replace(pattern1, '');
                        newText = newText.replace(pattern2, '');
                        newText = newText.replace(pattern3, '');
                    }
                }
                
                // Remove any remaining field_ patterns
                newText = newText.replace(/field_[a-f0-9]+/g, '');
                // Remove empty spans
                newText = newText.replace(/<span[^>]*><\/span>/g, '');
                // Clean up multiple spaces
                newText = newText.replace(/\s+/g, ' ').trim();
                
                if (newText !== productText) {
                    $productName.html(newText);
                }
            });
        }
        
        // Run on page load
        setTimeout(fixMiniCartSpecs, 500);
        
        // Run after cart updates
        $(document).on('elementor-pro/menu-cart/loaded wc_fragments_loaded updated_cart_totals', function() {
            setTimeout(fixMiniCartSpecs, 300);
        });
        
        // Monitor for DOM changes
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    setTimeout(fixMiniCartSpecs, 100);
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
        
    });
    </script>
    
    <style>
    /* Mini cart styling */
    .elementor-menu-cart__product-name .hb-product-specs {
        margin-top: 6px;
    }
    .elementor-menu-cart__product-name .hb-spec-item {
        display: inline-block;
        background: #f3f4f6;
        padding: 2px 8px;
        margin: 2px 4px 2px 0;
        font-size: 11px;
        border-radius: 4px;
    }
    .elementor-menu-cart__product-name .hb-spec-item strong {
        font-weight: 600;
    }
    </style>
    <?php
}

// ============================================
// ADD CSS FOR MINI CART
// ============================================

add_action('wp_head', 'hb_mini_cart_css');
function hb_mini_cart_css() {
    if (!is_admin()) {
        echo '<style>
            .hb-mini-specs {
                margin-top: 6px;
                font-size: 11px;
                color: #6b7280;
                line-height: 1.4;
            }
            .hb-mini-spec {
                display: block;
                margin: 2px 0;
            }
            .elementor-menu-cart__product-name {
                font-weight: 500;
            }
            .hb-product-specs {
                margin: 8px 0;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                font-size: 12px;
            }
            .hb-spec-item {
                background: #f3f4f6;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
            }
            .hb-spec-item strong {
                font-weight: 600;
            }
            .hb-sale-badge {
                display: inline-block;
                background: #10b981;
                color: white;
                font-size: 10px;
                padding: 2px 8px;
                margin-left: 8px;
                border-radius: 12px;
            }
            .hb-assembly-specs-wrapper {
                margin-top: 12px;
                padding: 10px;
                background: #f9fafb;
                border: 1px solid #e5e7eb;
                font-size: 12px;
            }
            .hb-assembly-header {
                font-weight: 700;
                margin-bottom: 8px;
                color: #3b82f6;
            }
            .hb-assembly-item {
                margin-bottom: 4px;
            }
            .hb-component-badge {
                display: inline-block;
                font-size: 10px;
                padding: 2px 8px;
                margin-top: 8px;
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
            }
            .hb-left-end-badge {
                background: #eef2ff;
                color: #3b82f6;
                border-color: #c7d2fe;
            }
            .hb-right-end-badge {
                background: #ecfdf5;
                color: #10b981;
                border-color: #a7f3d0;
            }
        </style>';
    }
}