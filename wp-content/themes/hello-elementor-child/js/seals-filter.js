/**
 * Seals Filter JavaScript - Debug Version
 */

jQuery(document).ready(function($) {
    
    console.log('Seals Filter JS loaded');
    
    var debounceTimer;
    function debounce(callback, delay) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(callback, delay);
    }
    
    function applyFilters() {
        var filterData = {
            'action': 'filter_seals_products',
            'product_type': $('#filter-type').val(),
            'id': $('#filter-id').val(),
            'id_tolerance': $('#id-tolerance').val(),
            'od': $('#filter-od').val(),
            'od_tolerance': $('#od-tolerance').val(),
            'height': $('#filter-height').val(),
            'height_tolerance': $('#height-tolerance').val(),
            'paged': 1
        };
        
        console.log('Sending filter data:', filterData);
        
        $.ajax({
            url: seals_ajax.ajax_url,
            type: 'POST',
            data: filterData,
            beforeSend: function() {
                $('.seals-products-wrapper').html('<div class="filter-loader" style="text-align:center;padding:50px;">Loading products...</div>');
            },
            success: function(response) {
                console.log('Response received');
                $('.seals-products-wrapper').html(response);
                
                // Trigger WooCommerce events
                $(document.body).trigger('updated_wc_div');
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                console.log('Response Text:', xhr.responseText);
                $('.seals-products-wrapper').html('<div class="woocommerce-info">Error loading products. Please try again. Error: ' + error + '</div>');
            }
        });
    }
    
    // Trigger filter on input keyup
    $('.seals-input').on('keyup', function(e) {
        console.log('Input changed:', $(this).val());
        debounce(function() {
            applyFilters();
        }, 500);
    });
    
    // Trigger filter on dropdown change
    $('.seals-select, .seals-tol-select').on('change', function() {
        console.log('Dropdown changed:', $(this).val());
        applyFilters();
    });
});