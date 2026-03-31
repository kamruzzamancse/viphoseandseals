<?php
/**
 * Custom Cart Page Styling and Display for Hose Builder
 * Version: 24.0 - Fixed assembly specs display
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// ============================================
// HELPER FUNCTION: Get product attribute value
// ============================================

function hb_get_product_attr_value_cart($product_id, $attr_key, $attr_label = '') {
    $product = wc_get_product($product_id);
    if (!$product) return '';
    
    // Try to get from product meta
    $meta_value = get_post_meta($product_id, '_' . $attr_key, true);
    if (!empty($meta_value) && !preg_match('/^field_/', $meta_value)) {
        return $meta_value;
    }
    
    // Try to get from short description
    $short_desc = $product->get_short_description();
    
    $patterns = [
        '/' . preg_quote($attr_label, '/') . ':\s*([^\s<]+)/i',
        '/' . preg_quote($attr_key, '/') . ':\s*([^\s<]+)/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $short_desc, $matches)) {
            return $matches[1];
        }
    }
    
    return '';
}

// ============================================
// Add custom cart page styling
// ============================================

add_action('wp_enqueue_scripts', 'hb_custom_cart_styles');
function hb_custom_cart_styles() {
    if (is_cart()) {
        if (defined('HB_URL')) {
            wp_enqueue_style('hb-custom-cart-style', HB_URL . 'assets/css/custom-cart.css', array(), '24.0');
        } else {
            wp_enqueue_style('hb-custom-cart-style', plugin_dir_url(__FILE__) . '../assets/css/custom-cart.css', array(), '24.0');
        }
    }
}

// ============================================
// Add AJAX endpoint to clear cart
// ============================================

add_action('wp_ajax_hb_clear_cart', 'hb_clear_cart');
add_action('wp_ajax_nopriv_hb_clear_cart', 'hb_clear_cart');

function hb_clear_cart() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'hb_clear_cart_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    WC()->cart->empty_cart();
    wp_send_json_success('Cart cleared');
}

// ============================================
// Add custom JavaScript for cart modifications
// ============================================

add_action('wp_footer', 'hb_custom_cart_footer_js');
function hb_custom_cart_footer_js() {
    if (is_cart()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            
            // Create AJAX nonce
            var hbAjaxNonce = '<?php echo wp_create_nonce("hb_clear_cart_nonce"); ?>';
            
            function initCartModifications() {
                if ($('.wc-block-cart-items__row').length === 0) {
                    setTimeout(initCartModifications, 500);
                    return;
                }
                
                console.log('Cart modifications initializing...');
                
                // ============================================
                // ADD CUSTOM HEADER SECTION
                // ============================================
                
                if ($('.wp-block-woocommerce-cart').length && !$('.hb-cart-header-section').length) {
                    var headerHtml = '<div class="hb-cart-header-section">';
                    headerHtml += '<h1 class="hb-cart-main-title">🛒 Shopping Cart</h1>';
                    headerHtml += '<p class="hb-cart-subtitle">Review your hose assembly items</p>';
                    headerHtml += '</div>';
                    $('.wp-block-woocommerce-cart').prepend(headerHtml);
                }
                
                // ============================================
                // ADD UPDATE & DELETE BUTTONS BELOW HEADER
                // ============================================
                
                if ($('.hb-cart-header-section').length && !$('.hb-header-buttons').length) {
                    var buttonsHtml = '<div class="hb-header-buttons">';
                    buttonsHtml += '<button id="hb-update-all-btn" class="hb-update-btn">🔄 Update Items</button>';
                    buttonsHtml += '<button id="hb-delete-all-btn" class="hb-delete-btn">🗑️ Delete Items</button>';
                    buttonsHtml += '</div>';
                    $('.hb-cart-header-section').after(buttonsHtml);
                    console.log('Added header buttons');
                }
                
                // ============================================
                // HANDLE UPDATE BUTTON CLICK - STORE COMPLETE CART DATA
                // ============================================

                $(document).on('click', '#hb-update-all-btn', function(e) {
                    e.preventDefault();
                    console.log('Update button clicked - storing complete cart data');
                    
                    var assemblyData = [];
                    
                    // Find main product rows (those with assembly specs)
                    $('.hb-assembly-specs-wrapper').each(function() {
                        var $specsWrapper = $(this);
                        var $row = $specsWrapper.closest('.wc-block-cart-items__row');
                        
                        console.log('Found assembly specs wrapper');
                        
                        // Extract data from assembly specs
                        var partNumber = '';
                        var setBOM = '';
                        var instructions = '';
                        var lengthText = '';
                        var quantity = '';
                        var department = '';
                        var hoseType = '';
                        var selectedProduct = '';
                        var leftHoseEnd = '';
                        var rightHoseEnd = '';
                        
                        // Get all assembly items
                        $specsWrapper.find('.hb-assembly-item').each(function() {
                            var text = $(this).text();
                            if (text.indexOf('Your Part Number:') !== -1) {
                                partNumber = text.replace('Your Part Number:', '').trim();
                            }
                            if (text.indexOf('Set BOM:') !== -1) {
                                setBOM = text.replace('Set BOM:', '').trim();
                            }
                            if (text.indexOf('Instructions/Comments:') !== -1) {
                                instructions = text.replace('Instructions/Comments:', '').trim();
                            }
                            if (text.indexOf('End to End Length:') !== -1) {
                                lengthText = text.replace('End to End Length:', '').trim();
                            }
                            if (text.indexOf('Quantity:') !== -1) {
                                quantity = text.replace('Quantity:', '').trim();
                            }
                            if (text.indexOf('Department:') !== -1) {
                                department = text.replace('Department:', '').trim();
                            }
                            if (text.indexOf('Hose Type:') !== -1) {
                                hoseType = text.replace('Hose Type:', '').trim();
                            }
                            if (text.indexOf('Selected Product:') !== -1) {
                                selectedProduct = text.replace('Selected Product:', '').trim();
                            }
                            if (text.indexOf('Hose End (Left):') !== -1) {
                                leftHoseEnd = text.replace('Hose End (Left):', '').trim();
                            }
                            if (text.indexOf('Hose End (Right):') !== -1) {
                                rightHoseEnd = text.replace('Hose End (Right):', '').trim();
                            }
                        });
                        
                        // Get product ID from the product name link
                        var productId = '';
                        var productLink = $row.find('.wc-block-components-product-name').attr('href');
                        if (productLink) {
                            var match = productLink.match(/\/product\/([^\/]+)/);
                            if (match) {
                                productId = match[1];
                            }
                        }
                        
                        assemblyData.push({
                            partNumber: partNumber,
                            setBOM: setBOM,
                            instructions: instructions,
                            length: lengthText,
                            quantity: quantity,
                            department: department,
                            hoseType: hoseType,
                            selectedProduct: selectedProduct,
                            leftHoseEnd: leftHoseEnd,
                            rightHoseEnd: rightHoseEnd,
                            productId: productId
                        });
                        
                        console.log('Collected complete data:', {
                            partNumber: partNumber,
                            setBOM: setBOM,
                            instructions: instructions,
                            length: lengthText,
                            quantity: quantity,
                            department: department,
                            hoseType: hoseType,
                            selectedProduct: selectedProduct,
                            leftHoseEnd: leftHoseEnd,
                            rightHoseEnd: rightHoseEnd
                        });
                    });
                    
                    if (assemblyData.length > 0) {
                        localStorage.setItem('hb_edit_assembly', JSON.stringify(assemblyData));
                        console.log('Complete assembly data stored in localStorage:', assemblyData);
                        window.location.href = '/viphoseandseals/build-a-hose/?edit=true';
                    } else {
                        console.log('No assembly specs found');
                        alert('No assembly data found to edit. Please add items to cart first.');
                    }
                });
                
                // ============================================
                // HANDLE DELETE BUTTON CLICK
                // ============================================
                
                $(document).on('click', '#hb-delete-all-btn', function(e) {
                    e.preventDefault();
                    console.log('Delete button clicked - clearing cart via AJAX');
                    
                    if (confirm('Are you sure you want to remove all items from your cart?')) {
                        var $btn = $(this);
                        $btn.prop('disabled', true).text('🗑️ Removing...');
                        
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'hb_clear_cart',
                                nonce: hbAjaxNonce
                            },
                            success: function(response) {
                                if (response.success) {
                                    console.log('Cart cleared successfully');
                                    window.location.reload();
                                } else {
                                    console.log('Error clearing cart:', response);
                                    $btn.prop('disabled', false).text('🗑️ Delete Items');
                                    alert('Failed to remove items. Please try again.');
                                }
                            },
                            error: function() {
                                $btn.prop('disabled', false).text('🗑️ Delete Items');
                                alert('Failed to remove items. Please try again.');
                            }
                        });
                    }
                });
                
                // ============================================
                // AUTO-EXPAND COUPON FORM
                // ============================================
                
                var couponPanel = $('.wc-block-components-totals-coupon');
                if (couponPanel.length && !couponPanel.hasClass('is-open')) {
                    couponPanel.addClass('is-open');
                }
                
                // Hide quantity selectors
                $('.wc-block-components-quantity-selector').hide();
                $('.wc-block-cart-items__header-quantity').hide();
                
                console.log('Cart modifications completed');
            }
            
            setTimeout(initCartModifications, 1000);
            $(document).on('updated_cart_totals', function() {
                setTimeout(initCartModifications, 800);
            });
            
        });
        </script>
        <?php
    }
}

// ============================================
// Modify cart item display
// ============================================

add_filter('woocommerce_cart_item_name', 'hb_custom_cart_item_name_with_hose_data', 10, 3);
function hb_custom_cart_item_name_with_hose_data($product_name, $cart_item, $cart_item_key) {
    $product = $cart_item['data'];
    $product_id = $product->get_id();
    
    // Check if this is the main hose product (has assembly data)
    $is_main_product = isset($cart_item['hb_assembly_data']) && !empty($cart_item['hb_assembly_data']);
    
    // Add product specs section (SKU and attributes)
    $product_specs = '<div class="hb-product-specs">';
    
    // SKU
    if ($product->get_sku()) {
        $product_specs .= '<span class="hb-spec-item"><strong>SKU:</strong> ' . esc_html($product->get_sku()) . '</span>';
    }
    
    // Product attributes
    $attributes = array(
        'id' => 'ID (in)',
        'od' => 'OD (in)',
        'working_pressure' => 'Working Pressure (psi)',
        'burst_pressure' => 'Burst Pressure (psi)',
        'hose_id' => 'Hose ID',
        'pipe_size' => 'Pipe Size',
        'thread_size' => 'Thread Size',
        'material' => 'Material',
        'manufactured_in' => 'Manufactured In'
    );
    
    foreach ($attributes as $meta_key => $label) {
        $value = hb_get_product_attr_value_cart($product_id, $meta_key, $label);
        if (!empty($value)) {
            $product_specs .= '<span class="hb-spec-item"><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</span>';
        }
    }
    
    $product_specs .= '</div>';
    
    // Add sale badge if product is on sale
    if ($product->is_on_sale()) {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $save_amount = $regular_price - $sale_price;
        
        $product_name .= '<div class="hb-sale-badge">';
        $product_name .= '🔥 Save $' . number_format($save_amount, 2);
        $product_name .= '</div>';
    }
    
    $product_name .= $product_specs;
    
    // ============================================
    // ASSEMBLY SPECS - ONLY ON MAIN PRODUCT
    // ============================================
    if ($is_main_product) {
        $assembly_data = $cart_item['hb_assembly_data'];
        
        $product_name .= '<div class="hb-assembly-specs-wrapper">';
        $product_name .= '<div class="hb-assembly-header">🔧 Assembly Specifications</div>';
        
        // Your Part Number
        if (isset($assembly_data['your_part_number']) && !empty($assembly_data['your_part_number'])) {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Your Part Number:</strong> ' . esc_html($assembly_data['your_part_number']);
            $product_name .= '</div>';
        } else {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Your Part Number:</strong> —</div>';
        }
        
        // Set BOM
        if (isset($assembly_data['set_bom']) && !empty($assembly_data['set_bom'])) {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Set BOM:</strong> ' . esc_html($assembly_data['set_bom']);
            $product_name .= '</div>';
        } else {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Set BOM:</strong> No</div>';
        }
        
        // Instructions/Comments
        if (isset($assembly_data['instructions']) && !empty($assembly_data['instructions'])) {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Instructions/Comments:</strong> ' . esc_html($assembly_data['instructions']);
            $product_name .= '</div>';
        } else {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Instructions/Comments:</strong> —</div>';
        }
        
        // End to End Length
        if (isset($assembly_data['length_value']) && isset($assembly_data['length_unit'])) {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>End to End Length:</strong> ' . esc_html($assembly_data['length_value']) . ' ' . esc_html($assembly_data['length_unit']);
            $product_name .= '</div>';
        } else {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>End to End Length:</strong> 5000 inches</div>';
        }
        
        // Quantity
        if (isset($assembly_data['quantity']) && $assembly_data['quantity'] > 0) {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Quantity:</strong> ' . esc_html($assembly_data['quantity']);
            $product_name .= '</div>';
        } else {
            $product_name .= '<div class="hb-assembly-item">';
            $product_name .= '<strong>Quantity:</strong> 1</div>';
        }
        
        $product_name .= '</div>';
    }
    
    // Add component badge for left/right ends
    if (isset($cart_item['hb_assembly_component'])) {
        if ($cart_item['hb_assembly_component'] === 'left_end') {
            $product_name .= '<div class="hb-component-badge hb-left-end-badge">';
            $product_name .= '⬅️ Left Hose End Component';
            $product_name .= '</div>';
        } elseif ($cart_item['hb_assembly_component'] === 'right_end') {
            $product_name .= '<div class="hb-component-badge hb-right-end-badge">';
            $product_name .= '➡️ Right Hose End Component';
            $product_name .= '</div>';
        }
    }
    
    return $product_name;
}

// ============================================
// Hide default page title
// ============================================

add_action('wp_head', 'hb_hide_default_cart_header');
function hb_hide_default_cart_header() {
    if (is_cart()) {
        echo '<style>
            .entry-title,
            .page-header h1,
            .woocommerce-cart .entry-header,
            .woocommerce .entry-title {
                display: none !important;
            }
        </style>';
    }
}