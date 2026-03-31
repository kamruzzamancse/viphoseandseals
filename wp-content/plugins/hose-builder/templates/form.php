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

            <!-- Hidden Inputs for Storing Selected Values -->
            <input type="hidden" name="hose_end_1_selected" id="hose_end_1_selected" value="">
            <input type="hidden" name="hose_end_2_selected" id="hose_end_2_selected" value="">
            
        </div>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    
    // ============================================
    // SAFETY CHECK - Only run on hose builder page
    // ============================================
    if (!$('#hose-builder-wrapper').length) {
        return;
    }
    
    // ============================================
    // GLOBAL VARIABLES
    // ============================================
    window.hbLeftProductId = '';
    window.hbRightProductId = '';
    
    // ============================================
    // EDIT MODE DATA
    // ============================================
    var urlParams = new URLSearchParams(window.location.search);
    var editMode = urlParams.get('edit');
    var editData = null;
    
    // Load saved data if in edit mode
    if (editMode === 'true') {
        console.log('🔄 Edit mode detected - loading saved data...');
        var savedData = localStorage.getItem('hb_edit_assembly');
        if (savedData) {
            try {
                var assemblyData = JSON.parse(savedData);
                if (assemblyData.length > 0) {
                    editData = assemblyData[0];
                    console.log('📦 Edit data loaded:', editData);
                }
            } catch(e) {
                console.log('Error parsing saved data:', e);
            }
            localStorage.removeItem('hb_edit_assembly');
        }
    }
    
    // ============================================
    // Function to fill form after all AJAX is complete
    // ============================================
    function fillFormWithEditData() {
        if (!editData) return;
        
        console.log('Filling form with edit data...');
        
        // Fill Hose Options fields
        if (editData.partNumber && editData.partNumber !== '' && editData.partNumber !== '—') {
            $('#part_number').val(editData.partNumber);
            console.log('✓ Part Number filled:', editData.partNumber);
        }
        
        if (editData.setBOM === 'Yes') {
            $('#set_bom').prop('checked', true);
            console.log('✓ Set BOM filled: Yes');
        }
        
        if (editData.instructions && editData.instructions !== '') {
            $('#instructions').val(editData.instructions);
            console.log('✓ Instructions filled:', editData.instructions);
        }
        
        if (editData.length && editData.length !== '') {
            var lengthMatch = editData.length.match(/(\d+)\s*(\w+)/);
            if (lengthMatch) {
                $('#length_value').val(lengthMatch[1]);
                $('#length_range').val(lengthMatch[1]);
                $('#length_unit').val(lengthMatch[2]);
                console.log('✓ Length filled:', lengthMatch[1], lengthMatch[2]);
            }
        }
        
        if (editData.quantity && editData.quantity !== '') {
            $('#quantity').val(editData.quantity);
            console.log('✓ Quantity filled:', editData.quantity);
        }
        
        // Fill Department
        if (editData.department && editData.department !== '') {
            var deptFound = false;
            $('#department option').each(function() {
                if ($(this).text() === editData.department) {
                    $(this).prop('selected', true);
                    deptFound = true;
                    console.log('✓ Department selected:', editData.department);
                    return false;
                }
            });
            if (deptFound) {
                $('#department').trigger('change');
            }
        }
        
        // Wait for Hose Type dropdown to populate then select
        if (editData.hoseType && editData.hoseType !== '') {
            var hoseTypeInterval = setInterval(function() {
                if ($('#hose_type option').length > 1) {
                    var hoseFound = false;
                    $('#hose_type option').each(function() {
                        if ($(this).text() === editData.hoseType) {
                            $(this).prop('selected', true);
                            hoseFound = true;
                            console.log('✓ Hose Type selected:', editData.hoseType);
                            $('#hose_type').trigger('change');
                            clearInterval(hoseTypeInterval);
                            return false;
                        }
                    });
                    if (!hoseFound) {
                        clearInterval(hoseTypeInterval);
                    }
                }
            }, 500);
        }
        
        // Wait for Product dropdown to populate then select
        if (editData.selectedProduct && editData.selectedProduct !== '') {
            var productInterval = setInterval(function() {
                if ($('#selected_product option').length > 1) {
                    var productFound = false;
                    $('#selected_product option').each(function() {
                        if ($(this).text() === editData.selectedProduct) {
                            $(this).prop('selected', true);
                            productFound = true;
                            console.log('✓ Selected Product selected:', editData.selectedProduct);
                            $('#selected_product').trigger('change');
                            clearInterval(productInterval);
                            return false;
                        }
                    });
                    if (!productFound) {
                        clearInterval(productInterval);
                    }
                }
            }, 500);
        }
        
        // Handle Hose Ends (Left and Right)
        if (editData.leftHoseEnd && editData.leftHoseEnd !== '') {
            // Wait for product selection to complete
            setTimeout(function() {
                if (editData.leftHoseEnd.includes('Carbon Steel')) {
                    $('#hose_end_1_type').val('carbon_steel').trigger('change');
                    console.log('✓ Left Hose End type set to: carbon_steel');
                    
                    // Wait for carbon steel dropdown to populate
                    setTimeout(function() {
                        var leftEndName = editData.leftHoseEnd.replace('Carbon Steel → ', '').trim();
                        if (leftEndName) {
                            var leftInterval = setInterval(function() {
                                var $leftDropdown = $('#hose-end-1-dynamic .dynamic-select:last');
                                if ($leftDropdown.length && $leftDropdown.find('option').length > 1) {
                                    $leftDropdown.find('option').each(function() {
                                        if ($(this).text() === leftEndName || $(this).text().includes(leftEndName)) {
                                            $(this).prop('selected', true);
                                            $leftDropdown.trigger('change');
                                            console.log('✓ Left Hose End selected:', leftEndName);
                                            clearInterval(leftInterval);
                                            return false;
                                        }
                                    });
                                    clearInterval(leftInterval);
                                }
                            }, 500);
                        }
                    }, 800);
                } else if (editData.leftHoseEnd === 'Open (No End)') {
                    $('#hose_end_1_type').val('open').trigger('change');
                    console.log('✓ Left Hose End set to: open');
                }
            }, 1000);
        }
        
        // Handle Right Hose End
        if (editData.rightHoseEnd && editData.rightHoseEnd !== '') {
            setTimeout(function() {
                if (editData.rightHoseEnd.includes('Carbon Steel')) {
                    $('#hose_end_2_type').val('carbon_steel').trigger('change');
                    console.log('✓ Right Hose End type set to: carbon_steel');
                    
                    setTimeout(function() {
                        var rightEndName = editData.rightHoseEnd.replace('Carbon Steel → ', '').trim();
                        if (rightEndName) {
                            var rightInterval = setInterval(function() {
                                var $rightDropdown = $('#hose-end-2-dynamic .dynamic-select:last');
                                if ($rightDropdown.length && $rightDropdown.find('option').length > 1) {
                                    $rightDropdown.find('option').each(function() {
                                        if ($(this).text() === rightEndName || $(this).text().includes(rightEndName)) {
                                            $(this).prop('selected', true);
                                            $rightDropdown.trigger('change');
                                            console.log('✓ Right Hose End selected:', rightEndName);
                                            clearInterval(rightInterval);
                                            return false;
                                        }
                                    });
                                    clearInterval(rightInterval);
                                }
                            }, 500);
                        }
                    }, 800);
                } else if (editData.rightHoseEnd === 'Open (No End)') {
                    $('#hose_end_2_type').val('open').trigger('change');
                    console.log('✓ Right Hose End set to: open');
                }
            }, 1000);
        }
        
        // Show edit notice
        if ($('.hb-edit-notice').length === 0) {
            var noticeHtml = '<div class="hb-edit-notice" style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 20px; margin-bottom: 20px; border-radius: 8px; color: #92400e; font-size: 14px;">';
            noticeHtml += '✏️ <strong>Edit Mode:</strong> You are editing an existing assembly. Make your changes and click "Add to Cart" to update.';
            noticeHtml += '</div>';
            $('.hb-container').prepend(noticeHtml);
        }
    }
    
    // ============================================
    // Function to load category hierarchy recursively
    // ============================================
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
                    dropdownHtml += '<label class="hb-label">Select Option</label>';
                    dropdownHtml += '<select id="' + dropdownId + '" class="hb-select dynamic-select" data-level="' + level + '" data-container="' + containerId + '">';
                    dropdownHtml += response;
                    dropdownHtml += '</select>';
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
                            if (val && val !== '') {
                                fullPath.push(val);
                            }
                        });
                        
                        var isProduct = newValue && newValue.toString().startsWith('product_');
                        var isCategory = newValue && newValue.toString().startsWith('cat_');
                        
                        if (isProduct) {
                            var productId = newValue.toString().replace('product_', '');
                            
                            if (currentContainer === 'hose-end-1-dynamic') {
                                $('#hose_end_1_selected').val(productId);
                                window.hbLeftProductId = productId;
                            } else {
                                $('#hose_end_2_selected').val(productId);
                                window.hbRightProductId = productId;
                            }
                            
                            $('#' + currentContainer + ' .hb-dynamic-level').each(function() {
                                var thisLevel = parseInt($(this).find('.dynamic-select').data('level'));
                                if (thisLevel > currentLevel) {
                                    $(this).remove();
                                }
                            });
                        } else if (isCategory) {
                            var categoryId = newValue.toString().replace('cat_', '');
                            var storedValue = JSON.stringify(fullPath);
                            
                            if (currentContainer === 'hose-end-1-dynamic') {
                                $('#hose_end_1_selected').val(storedValue);
                            } else {
                                $('#hose_end_2_selected').val(storedValue);
                            }
                            loadCategoryHierarchy(currentContainer, categoryId, currentLevel + 1, fullPath, targetInputId);
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
                                window.hbLeftProductId = '';
                            } else {
                                $('#hose_end_2_selected').val('');
                                window.hbRightProductId = '';
                            }
                        }
                    });
                }
            }
        });
    }
    
    // ============================================
    // Update warning messages
    // ============================================
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
    
    // ============================================
    // When Product is selected, show hose end dropdowns
    // ============================================
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
                window.hbLeftProductId = '';
                window.hbRightProductId = '';
                $('#hose_end_1_type, #hose_end_2_type').val('');
            });
            $('#hose-options-section').slideUp(300);
        }
    });
    
    // ============================================
    // Handle Carbon Steel / Open selection for Hose End 1
    // ============================================
    $('#hose_end_1_type').on('change', function() {
        var selected = $(this).val();
        
        if (selected === 'carbon_steel') {
            $('#hose-end-1-open-message').hide();
            $('#hose-end-1-dynamic').show().empty();
            $('#hose_end_1_selected').val('');
            window.hbLeftProductId = '';
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
            window.hbLeftProductId = 'open';
        } else {
            $('#hose-end-1-dynamic').empty().hide();
            $('#hose-end-1-open-message').hide();
            $('#hose_end_1_selected').val('');
            window.hbLeftProductId = '';
        }
    });
    
    // ============================================
    // Handle Carbon Steel / Open selection for Hose End 2
    // ============================================
    $('#hose_end_2_type').on('change', function() {
        var selected = $(this).val();
        
        if (selected === 'carbon_steel') {
            $('#hose-end-2-open-message').hide();
            $('#hose-end-2-dynamic').show().empty();
            $('#hose_end_2_selected').val('');
            window.hbRightProductId = '';
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
            window.hbRightProductId = 'open';
        } else {
            $('#hose-end-2-dynamic').empty().hide();
            $('#hose-end-2-open-message').hide();
            $('#hose_end_2_selected').val('');
            window.hbRightProductId = '';
        }
    });
    
    // ============================================
    // Range Slider Synchronization
    // ============================================
    $('#length_range').on('input', function() {
        $('#length_value').val($(this).val());
    });
    $('#length_value').on('input', function() {
        $('#length_range').val($(this).val());
    });
    
    // ============================================
    // Unit checkboxes synchronization
    // ============================================
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
        if (window.hbLeftProductId && window.hbLeftProductId !== '') {
            $('#hose_end_1_selected').val(window.hbLeftProductId);
        }
        if (window.hbRightProductId && window.hbRightProductId !== '') {
            $('#hose_end_2_selected').val(window.hbRightProductId);
        }
        
        if (!window.hbLeftProductId || window.hbLeftProductId === '') {
            var leftDynamicValue = $('#hose-end-1-dynamic .dynamic-select:last').val();
            if (leftDynamicValue && leftDynamicValue.toString().startsWith('product_')) {
                $('#hose_end_1_selected').val(leftDynamicValue.toString().replace('product_', ''));
            }
        }
        
        if (!window.hbRightProductId || window.hbRightProductId === '') {
            var rightDynamicValue = $('#hose-end-2-dynamic .dynamic-select:last').val();
            if (rightDynamicValue && rightDynamicValue.toString().startsWith('product_')) {
                $('#hose_end_2_selected').val(rightDynamicValue.toString().replace('product_', ''));
            }
        }
        
        var hasError = false;
        var errorMessages = [];
        var selectedProduct = $('#selected_product').val();
        
        if (!selectedProduct || selectedProduct === '') {
            errorMessages.push('Please select a product before adding to cart.');
            hasError = true;
        } else {
            var leftValid = false, rightValid = false;
            var leftType = $('#hose_end_1_type').val();
            var leftSelected = $('#hose_end_1_selected').val();
            var rightType = $('#hose_end_2_type').val();
            var rightSelected = $('#hose_end_2_selected').val();
            
            if (leftType === 'carbon_steel') {
                if (leftSelected && leftSelected !== '' && leftSelected !== 'open' && leftSelected !== '[]') leftValid = true;
            } else if (leftType === 'open') leftValid = true;
            
            if (rightType === 'carbon_steel') {
                if (rightSelected && rightSelected !== '' && rightSelected !== 'open' && rightSelected !== '[]') rightValid = true;
            } else if (rightType === 'open') rightValid = true;
            
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
        
        if (hasError) {
            e.preventDefault();
            $('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
            var errorHtml = '<ul class="woocommerce-error" role="alert" style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 12px 20px; margin-bottom: 20px; list-style: none;">';
            errorHtml += '<strong style="display: block; margin-bottom: 8px; font-size: 14px;">Please correct the following error(s):</strong>';
            for (var i = 0; i < errorMessages.length; i++) {
                errorHtml += '<li style="margin: 0; padding: 0; font-size: 14px; line-height: 1.5;">• ' + errorMessages[i] + '</li>';
            }
            errorHtml += '</ul>';
            $('#hose-builder-form').prepend(errorHtml);
            $('html, body').animate({ scrollTop: $('#hose-builder-form .woocommerce-error').offset().top - 100 }, 500);
            return false;
        }
        
        if (urlParams.has('edit')) {
            window.history.replaceState({}, document.title, window.location.pathname);
            localStorage.removeItem('hb_edit_assembly');
        }
        
        return true;
    });
    
    // ============================================
    // Build New Hose button
    // ============================================
    $('#build-new-hose').on('click', function() {
        $('#selected_product').val('').trigger('change');
        $('#department').val('');
        $('#hose_type').html('<option value="">Select Hose Type</option>');
        $('#part_number').val('');
        $('#set_bom').prop('checked', false);
        $('#instructions').val('');
        $('#length_range').val('5000');
        $('#length_value').val('5000');
        $('#length_unit').val('inches');
        $('#use_millimeters, #use_feet').prop('checked', false);
        $('#quantity').val('1');
        $('#hose_end_1_type, #hose_end_2_type').val('');
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        window.hbLeftProductId = '';
        window.hbRightProductId = '';
        localStorage.removeItem('hb_edit_assembly');
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    // ============================================
    // Department change handler
    // ============================================
    $('#department').on('change', function() {
        var department_id = $(this).val();
        var hose_type_select = $('#hose_type');
        var product_select = $('#selected_product');
        
        hose_type_select.html('<option value="">Select Hose Type</option>');
        product_select.html('<option value="">Select Product</option>');
        
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideUp(300, function() { updateWarningMessages(); });
        $('#hose-options-section').slideUp(300);
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        window.hbLeftProductId = '';
        window.hbRightProductId = '';
        $('#hose_end_1_type, #hose_end_2_type').val('');
        
        if (department_id) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: { action: 'get_hose_types', department_id: department_id },
                success: function(response) { if (response) hose_type_select.html(response); }
            });
        }
    });
    
    // ============================================
    // Hose Type change handler
    // ============================================
    $('#hose_type').on('change', function() {
        var hose_type_id = $(this).val();
        var product_select = $('#selected_product');
        
        product_select.html('<option value="">Select Product</option>');
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').slideUp(300, function() { updateWarningMessages(); });
        $('#hose-options-section').slideUp(300);
        $('#hose-end-1-dynamic, #hose-end-2-dynamic').empty().hide();
        $('#hose-end-1-open-message, #hose-end-2-open-message').hide();
        $('#hose_end_1_selected, #hose_end_2_selected').val('');
        window.hbLeftProductId = '';
        window.hbRightProductId = '';
        $('#hose_end_1_type, #hose_end_2_type').val('');
        
        if (hose_type_id) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: { action: 'get_products_by_category', category_id: hose_type_id },
                success: function(response) { if (response) product_select.html(response); }
            });
        }
    });
    
    // ============================================
    // Initial check and Edit Mode form fill
    // ============================================
    if ($('#selected_product').val() && $('#selected_product').val() !== '') {
        $('#hose-end-1-wrapper, #hose-end-2-wrapper').show();
        $('#hose-options-section').show();
        updateWarningMessages();
    } else {
        updateWarningMessages();
    }
    
    // If in edit mode, fill the form after a delay to allow AJAX to complete
    if (editData) {
        setTimeout(fillFormWithEditData, 1500);
    }
    
});
</script>

<style>
.hb-edit-notice {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 12px 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    color: #92400e;
    font-size: 14px;
}
.hb-edit-notice strong {
    font-weight: 700;
}
</style>