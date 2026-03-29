jQuery(document).ready(function($) {
    
    // When Department changes, load Hose Types
    $('#department').on('change', function() {
        var department_id = $(this).val();
        var hose_type_select = $('#hose_type');
        var product_select = $('#selected_product');
        
        // Reset dropdowns
        hose_type_select.html('<option value="">Loading...</option>');
        product_select.html('<option value="">Select Product</option>');
        
        if (department_id) {
            $.ajax({
                url: hb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_hose_types',
                    department_id: department_id
                },
                success: function(response) {
                    if (response) {
                        hose_type_select.html(response);
                    } else {
                        hose_type_select.html('<option value="">No hose types found</option>');
                    }
                },
                error: function() {
                    hose_type_select.html('<option value="">Error loading data</option>');
                }
            });
        } else {
            hose_type_select.html('<option value="">Select Hose Type</option>');
        }
    });
    
    // When Hose Type changes, load Products
    $('#hose_type').on('change', function() {
        var hose_type_id = $(this).val();
        var product_select = $('#selected_product');
        
        // Reset product dropdown
        product_select.html('<option value="">Loading...</option>');
        
        if (hose_type_id) {
            $.ajax({
                url: hb_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_products_by_category',
                    category_id: hose_type_id
                },
                success: function(response) {
                    if (response) {
                        product_select.html(response);
                    } else {
                        product_select.html('<option value="">No products found</option>');
                    }
                },
                error: function() {
                    product_select.html('<option value="">Error loading products</option>');
                }
            });
        } else {
            product_select.html('<option value="">Select Product</option>');
        }
    });
    
});