<?php
/**
 * Hose Builder Form Template
 * Dynamic Dropdowns with Carbon Steel/Open Options
 * Additional Hose Options section after product selection
 */
?>

<div id="hose-builder-wrapper">
    <form method="post" action="" id="hose-builder-form">
        <div class="hb-container">
            
            <!-- Main Title -->
            <h1 class="hb-main-title">Build Your Own Hose Assembly</h1>
            
            <!-- Three Cards Grid -->
            <div class="hb-cards-grid">
                
                <!-- Card 1: Hose End (Left) -->
                <div class="hb-card" id="card-hose-end-left">
                    <div class="hb-card-header">
                        <h2 class="hb-card-title">Hose End</h2>
                        <div class="hb-card-header-image">
                            <img src="<?php echo HB_URL; ?>assets/images/hose-left.png" alt="Hose Left End" class="hb-end-image">
                        </div>
                    </div>
                    <div class="hb-card-body">
                        <p class="hb-warning-text" id="hose-end-1-warning">⚠️ Please select a hose first</p>
                        <div class="hb-dropdown-wrapper" id="hose-end-1-wrapper" style="display: none;">
                            <select name="hose_end_1_type" id="hose_end_1_type" class="hb-select">
                                <option value="">Select Hose End Type</option>
                                <option value="carbon_steel">Carbon Steel</option>
                                <option value="open">Open</option>
                            </select>
                            <div id="hose-end-1-dynamic" style="display: none;"></div>
                            <div id="hose-end-1-open-message" class="hb-info-message" style="display: none;">
                                <p>ℹ️ No Hose End selected - Open configuration</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2: Select Configuration -->
                <div class="hb-card" id="card-configuration">
                    <div class="hb-card-header">
                        <h2 class="hb-card-title">Hose</h2>
                        <div class="hb-card-header-image">
                            <img src="<?php echo HB_URL; ?>assets/images/hose.png" alt="Hose Body" class="hb-body-image">
                        </div>
                    </div>
                    <div class="hb-card-body">
                        <label class="hb-label">Departments</label>
                        <select name="department" id="department" class="hb-select" required>
                            <option value="">Select Department</option>
                            <?php
                            $hose_category = get_term_by('slug', 'hose', 'product_cat');
                            if ($hose_category) {
                                $subcategories = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'parent' => $hose_category->term_id,
                                    'hide_empty' => false,
                                ));
                                foreach ($subcategories as $subcat) {
                                    echo '<option value="' . $subcat->term_id . '">' . $subcat->name . '</option>';
                                }
                            }
                            ?>
                        </select>
                        
                        <label class="hb-label">Hose Type</label>
                        <select name="hose_type" id="hose_type" class="hb-select" required>
                            <option value="">Select Hose Type</option>
                        </select>
                        
                        <label class="hb-label">Select Product</label>
                        <select name="selected_product" id="selected_product" class="hb-select" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                </div>
                
                <!-- Card 3: HOSE END (Right) -->
                <div class="hb-card" id="card-hose-end-right">
                    <div class="hb-card-header">
                        <h2 class="hb-card-title">Hose End</h2>
                        <div class="hb-card-header-image">
                            <img src="<?php echo HB_URL; ?>assets/images/hose-right.png" alt="Hose Right End" class="hb-end-image">
                        </div>
                    </div>
                    <div class="hb-card-body">
                        <p class="hb-warning-text" id="hose-end-2-warning">⚠️ Please select a hose first</p>
                        <div class="hb-dropdown-wrapper" id="hose-end-2-wrapper" style="display: none;">
                            <select name="hose_end_2_type" id="hose_end_2_type" class="hb-select">
                                <option value="">Select Hose End Type</option>
                                <option value="carbon_steel">Carbon Steel</option>
                                <option value="open">Open</option>
                            </select>
                            <div id="hose-end-2-dynamic" style="display: none;"></div>
                            <div id="hose-end-2-open-message" class="hb-info-message" style="display: none;">
                                <p>ℹ️ No Hose End selected - Open configuration</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- NEW SECTION: Hose Options (Shows after product selection) -->
            <div id="hose-options-section" class="hose-options-section" style="display: none;">
                <div class="section-header">
                    <h2 class="section-title">Hose Options</h2>
                </div>
                
                <div class="options-grid">
                    <!-- Part Number Section -->
                    <div class="option-card">
                        <label class="option-label">Your Part Number:</label>
                        <input type="text" name="part_number" id="part_number" class="hb-input" placeholder="Enter part number">
                        <a href="#" class="more-info-link">(More Info)</a>
                    </div>
                    
                    <!-- BOM Section -->
                    <div class="option-card checkbox-card">
                        <label class="checkbox-label">
                            <input type="checkbox" name="set_bom" id="set_bom" value="yes">
                            <span>Set BOM (Set Info)</span>
                        </label>
                    </div>
                </div>
                
                <!-- Instructions/Comments Section -->
                <div class="instructions-section">
                    <label class="option-label">Instructions/Comments:</label>
                    <textarea name="instructions" id="instructions" rows="3" class="hb-textarea" placeholder="Use the box below to enter any instructions or comments that need to be followed for the construction of this hose assembly"></textarea>
                    <div class="info-link">
                        <a href="#">(Hose End Orientation)</a>
                    </div>
                </div>
                
                <!-- End to End Length Section with Range Slider -->
                <div class="length-section">
                    <label class="option-label">End to End Length:</label>
                    <div class="length-inputs">
                        <div class="length-value">
                            <input type="range" name="length_range" id="length_range" class="hb-range" min="0" max="10000" step="1" value="5000">
                            <div class="length-input-group">
                                <input type="number" name="length_value" id="length_value" class="hb-input" value="5000" step="1">
                                <select name="length_unit" id="length_unit" class="hb-select">
                                    <option value="inches">inches</option>
                                    <option value="mm">mm</option>
                                    <option value="feet">feet</option>
                                </select>
                            </div>
                        </div>
                        <div class="length-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="use_millimeters" id="use_millimeters" value="mm">
                                <span>Use millimeters</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="use_feet" id="use_feet" value="feet">
                                <span>Use feet</span>
                            </label>
                            <a href="#" class="more-info-link">(More Info)</a>
                        </div>
                    </div>
                </div>
                
                <!-- Quantity Section -->
                <div class="quantity-section">
                    <label class="option-label">Qty to add to cart:</label>
                    <input type="number" name="quantity" id="quantity" class="hb-input quantity-input" value="1" min="1" step="1">
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" name="hb_add_to_cart" class="hb-add-to-cart-btn">Add to Cart</button>
                    <button type="button" id="build-new-hose" class="hb-secondary-btn">Build New Hose</button>
                </div>
            </div>
            
        </div>
    </form>
</div>

<!-- Hidden Inputs for Storing Selected Values -->
<input type="hidden" name="hose_end_1_selected" id="hose_end_1_selected" value="">
<input type="hidden" name="hose_end_2_selected" id="hose_end_2_selected" value="">

<script type="text/javascript">
jQuery(document).ready(function($) {
    
    // Function to load category hierarchy recursively
    function loadCategoryHierarchy(containerId, parentId, level, selectedValue, targetInputId) {
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'get_category_hierarchy',
                parent_id: parentId,
                level: level
            },
            success: function(response) {
                if (response && response !== '') {
                    var dropdownId = 'dynamic-dropdown-' + containerId + '-' + level;
                    var dropdownHtml = '<div class="hb-dynamic-level" id="level-' + containerId + '-' + level + '">';
                    dropdownHtml += '<label class="hb-label">Departments / Products</label>';
                    dropdownHtml += '<select id="' + dropdownId + '" class="hb-select dynamic-select" data-level="' + level + '" data-container="' + containerId + '">';
                    dropdownHtml += response;
                    dropdownHtml += '</select>';
                    dropdownHtml += '<div id="sub-level-' + containerId + '-' + level + '" class="hb-sub-level"></div>';
                    dropdownHtml += '</div>';
                    
                    $('#' + containerId + ' .hb-dynamic-level').each(function() {
                        var currentLevel = parseInt($(this).find('.dynamic-select').data('level'));
                        if (currentLevel >= level) {
                            $(this).remove();
                        }
                    });
                    
                    $('#' + containerId).append(dropdownHtml);
                    
                    if (selectedValue && selectedValue.length > level) {
                        $('#' + dropdownId).val(selectedValue[level]).trigger('change');
                    }
                    
                    $('#' + dropdownId).off('change').on('change', function() {
                        var newValue = $(this).val();
                        var currentLevel = $(this).data('level');
                        var currentContainer = $(this).data('container');
                        var fullPath = [];
                        
                        $('#' + currentContainer + ' .dynamic-select').each(function() {
                            var val = $(this).val();
                            if (val) {
                                fullPath.push(val);
                            }
                        });
                        
                        var isProduct = newValue && newValue.toString().startsWith('product_');
                        
                        if (isProduct) {
                            var productId = newValue.toString().replace('product_', '');
                            if (currentContainer === 'hose-end-1-dynamic') {
                                $('#hose_end_1_selected').val(productId);
                            } else {
                                $('#hose_end_2_selected').val(productId);
                            }
                            $('#' + currentContainer + ' .hb-dynamic-level').each(function() {
                                var thisLevel = parseInt($(this).find('.dynamic-select').data('level'));
                                if (thisLevel > currentLevel) {
                                    $(this).remove();
                                }
                            });
                        } else if (newValue && newValue !== '') {
                            var storedValue = JSON.stringify(fullPath);
                            if (currentContainer === 'hose-end-1-dynamic') {
                                $('#hose_end_1_selected').val(storedValue);
                            } else {
                                $('#hose_end_2_selected').val(storedValue);
                            }
                            loadCategoryHierarchy(currentContainer, newValue, currentLevel + 1, fullPath, targetInputId);
                        } else {
                            $('#' + currentContainer + ' .hb-dynamic-level').each(function() {
                                var thisLevel = parseInt($(this).find('.dynamic-select').data('level'));
                                if (thisLevel > currentLevel) {
                                    $(this).remove();
                                }
                            });
                            if (currentContainer === 'hose-end-1-dynamic') {
                                $('#hose_end_1_selected').val('');
                            } else {
                                $('#hose_end_2_selected').val('');
                            }
                        }
                    });
                }
            }
        });
    }
    
    function updateWarningMessages() {
        if ($('#hose-end-1-wrapper').is(':visible')) {
            $('#hose-end-1-warning').hide();
        } else {
            $('#hose-end-1-warning').show();
        }
        if ($('#hose-end-2-wrapper').is(':visible')) {
            $('#hose-end-2-warning').hide();
        } else {
            $('#hose-end-2-warning').show();
        }
    }
    
    // When Product is selected, show the hose end dropdowns and hose options section
    $('#selected_product').on('change', function() {
        var product_id = $(this).val();
        
        if (product_id && product_id !== '') {
            $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideDown(400, function() {
                updateWarningMessages();
            });
            $('#hose-options-section').slideDown(500);
        } else {
            $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideUp(300, function() {
                updateWarningMessages();
                $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
                $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
                $('#hose_end_1_selected, #hose_end_2_selected').val('');
                $('#hose_end_1_type, #hose_end_2_type').val('');
            });
            $('#hose-options-section').slideUp(300);
        }
    });
    
    // Handle Carbon Steel / Open selection for Hose End 1
    $('#hose_end_1_type').on('change', function() {
        var selected = $(this).val();
        if (selected === 'carbon_steel') {
            $('#hose-end-1-open-message').hide();
            $('#hose-end-1-dynamic').show().empty();
            $('#hose_end_1_selected').val('');
            var carbonSteelId = <?php echo get_carbon_steel_category_id(); ?>;
            if (carbonSteelId) {
                loadCategoryHierarchy('hose-end-1-dynamic', carbonSteelId, 0, [], 'hose_end_1_selected');
            } else {
                $('#hose-end-1-dynamic').html('<div class="hb-info-message warning"><p>⚠️ Carbon Steel category not found.</p></div>');
            }
        } else if (selected === 'open') {
            $('#hose-end-1-dynamic').empty().hide();
            $('#hose-end-1-open-message').show();
            $('#hose_end_1_selected').val('open');
        } else {
            $('#hose-end-1-dynamic').empty().hide();
            $('#hose-end-1-open-message').hide();
            $('#hose_end_1_selected').val('');
        }
    });
    
    // Handle Carbon Steel / Open selection for Hose End 2
    $('#hose_end_2_type').on('change', function() {
        var selected = $(this).val();
        if (selected === 'carbon_steel') {
            $('#hose-end-2-open-message').hide();
            $('#hose-end-2-dynamic').show().empty();
            $('#hose_end_2_selected').val('');
            var carbonSteelId = <?php echo get_carbon_steel_category_id(); ?>;
            if (carbonSteelId) {
                loadCategoryHierarchy('hose-end-2-dynamic', carbonSteelId, 0, [], 'hose_end_2_selected');
            } else {
                $('#hose-end-2-dynamic').html('<div class="hb-info-message warning"><p>⚠️ Carbon Steel category not found.</p></div>');
            }
        } else if (selected === 'open') {
            $('#hose-end-2-dynamic').empty().hide();
            $('#hose-end-2-open-message').show();
            $('#hose_end_2_selected').val('open');
        } else {
            $('#hose-end-2-dynamic').empty().hide();
            $('#hose-end-2-open-message').hide();
            $('#hose_end_2_selected').val('');
        }
    });
    
    // Range Slider and Number Input Synchronization
    $('#length_range').on('input', function() {
        var value = $(this).val();
        $('#length_value').val(value);
    });
    
    $('#length_value').on('input', function() {
        var value = $(this).val();
        $('#length_range').val(value);
    });
    
    // Unit checkboxes synchronization
    $('#use_millimeters').on('change', function() {
        if ($(this).is(':checked')) {
            $('#use_feet').prop('checked', false);
            $('#length_unit').val('mm');
        }
    });
    
    $('#use_feet').on('change', function() {
        if ($(this).is(':checked')) {
            $('#use_millimeters').prop('checked', false);
            $('#length_unit').val('feet');
        }
    });
    
    $('#length_unit').on('change', function() {
        var unit = $(this).val();
        if (unit === 'mm') {
            $('#use_millimeters').prop('checked', true);
            $('#use_feet').prop('checked', false);
        } else if (unit === 'feet') {
            $('#use_millimeters').prop('checked', false);
            $('#use_feet').prop('checked', true);
        } else {
            $('#use_millimeters').prop('checked', false);
            $('#use_feet').prop('checked', false);
        }
    });
    
    // ============================================
    // FORM SUBMIT VALIDATION
    // ============================================
    $('#hose-builder-form').on('submit', function(e) {
        var hasError = false;
        var errorMessages = [];
        
        // Check if product is selected
        var selectedProduct = $('#selected_product').val();
        if (!selectedProduct || selectedProduct === '') {
            errorMessages.push('Please select a product before adding to cart.');
            hasError = true;
        } else {
            // Only check Hose Ends if product is selected
            // Check Left Hose End
            var leftHoseEndType = $('#hose_end_1_type').val();
            var leftHoseEndSelected = $('#hose_end_1_selected').val();
            var leftValid = false;
            
            if (leftHoseEndType === 'carbon_steel') {
                if (leftHoseEndSelected && leftHoseEndSelected !== '' && leftHoseEndSelected !== 'open') {
                    leftValid = true;
                }
            } else if (leftHoseEndType === 'open') {
                leftValid = true;
            }
            
            // Check Right Hose End
            var rightHoseEndType = $('#hose_end_2_type').val();
            var rightHoseEndSelected = $('#hose_end_2_selected').val();
            var rightValid = false;
            
            if (rightHoseEndType === 'carbon_steel') {
                if (rightHoseEndSelected && rightHoseEndSelected !== '' && rightHoseEndSelected !== 'open') {
                    rightValid = true;
                }
            } else if (rightHoseEndType === 'open') {
                rightValid = true;
            }
            
            // Validation for Hose Ends
            if (!leftValid && !rightValid) {
                errorMessages.push('Please select a left-side hose end for this assembly.');
                errorMessages.push('Please select a right-side hose end for this assembly.');
                hasError = true;
            } else if (!leftValid) {
                errorMessages.push('Please select a left-side hose end for this assembly.');
                hasError = true;
            } else if (!rightValid) {
                errorMessages.push('Please select a right-side hose end for this assembly.');
                hasError = true;
            }
        }
        
        // If there are errors, show them and prevent form submission
        if (hasError) {
            e.preventDefault();
            
            // Remove any existing notices
            $('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
            
            // Create error notice HTML with proper structure
            var errorHtml = '<ul class="woocommerce-error" role="alert" style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 12px 20px; margin-bottom: 20px; list-style: none; border-radius: 8px;">';
            errorHtml += '<strong style="display: block; margin-bottom: 8px; font-size: 14px;">Please correct the following error(s):</strong>';
            for (var i = 0; i < errorMessages.length; i++) {
                errorHtml += '<li style="margin: 0; padding: 0; font-size: 14px; line-height: 1.5;">• ' + errorMessages[i] + '</li>';
            }
            errorHtml += '</ul>';
            
            // Insert error notice at the top of the form
            $('#hose-builder-form').prepend(errorHtml);
            
            // Scroll to error notice
            $('html, body').animate({
                scrollTop: $('#hose-builder-form .woocommerce-error').offset().top - 100
            }, 500);
            
            return false;
        }
        
        return true;
    });
    
    // Build New Hose button - reset form with range slider reset
    $('#build-new-hose').on('click', function() {
        $('#selected_product').val('').trigger('change');
        $('#department').val('');
        $('#hose_type').html('<option value="">Select Hose Type</option>');
        $('#part_number').val('');
        $('#set_bom').prop('checked', false);
        $('#instructions').val('');
        // Reset length section
        $('#length_range').val('5000');
        $('#length_value').val('5000');
        $('#length_unit').val('inches');
        $('#use_millimeters, #use_feet').prop('checked', false);
        $('#quantity').val('1');
        $('#hose_end_1_type, #hose_end_2_type').val('');
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    // Department change handler
    $('#department').on('change', function() {
        var department_id = $(this).val();
        var hose_type_select = $('#hose_type');
        var product_select = $('#selected_product');
        
        hose_type_select.html('<option value="">Select Hose Type</option>');
        product_select.html('<option value="">Select Product</option>');
        
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideUp(300, function() {
            updateWarningMessages();
        });
        $('#hose-options-section').slideUp(300);
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        $('#hose_end_1_type, #hose_end_2_type').val('');
        
        if (department_id) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_hose_types',
                    department_id: department_id
                },
                success: function(response) {
                    if (response) {
                        hose_type_select.html(response);
                    }
                }
            });
        }
    });
    
    // Hose Type change handler
    $('#hose_type').on('change', function() {
        var hose_type_id = $(this).val();
        var product_select = $('#selected_product');
        
        product_select.html('<option value="">Select Product</option>');
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideUp(300, function() {
            updateWarningMessages();
        });
        $('#hose-options-section').slideUp(300);
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        $('#hose_end_1_type, #hose_end_2_type').val('');
        
        if (hose_type_id) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_products_by_category',
                    category_id: hose_type_id
                },
                success: function(response) {
                    if (response) {
                        product_select.html(response);
                    }
                }
            });
        }
    });
    
    // Initial check
    if ($('#selected_product').val() && $('#selected_product').val() !== '') {
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').show();
        $('#hose-options-section').show();
        updateWarningMessages();
    } else {
        updateWarningMessages();
    }
    
});
</script>