<?php
/**
 * AJAX Handlers for Dynamic Dropdowns
 */

// Get Carbon Steel category ID function
function get_carbon_steel_category_id() {
    $carbon_steel = get_term_by('slug', 'carbon-steel', 'product_cat');
    if ($carbon_steel) {
        return $carbon_steel->term_id;
    }
    return 0;
}

// Get Hose Types (Subcategories of selected Department)
add_action('wp_ajax_get_hose_types', 'get_hose_types_callback');
add_action('wp_ajax_nopriv_get_hose_types', 'get_hose_types_callback');

function get_hose_types_callback() {
    $department_id = intval($_POST['department_id']);
    
    if ($department_id) {
        $child_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'parent' => $department_id,
            'hide_empty' => false,
        ));
        
        if (!empty($child_categories)) {
            echo '<option value="">Select Hose Type</option>';
            foreach ($child_categories as $category) {
                echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
            }
        } else {
            echo '<option value="">No hose types found</option>';
        }
    }
    
    wp_die();
}

// Get Products by Category
add_action('wp_ajax_get_products_by_category', 'get_products_by_category_callback');
add_action('wp_ajax_nopriv_get_products_by_category', 'get_products_by_category_callback');

function get_products_by_category_callback() {
    $category_id = intval($_POST['category_id']);
    
    if ($category_id) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id,
                ),
            ),
        );
        
        $products = new WP_Query($args);
        
        if ($products->have_posts()) {
            echo '<option value="">Select Product</option>';
            while ($products->have_posts()) {
                $products->the_post();
                $product_id = get_the_ID();
                $product_title = get_the_title();
                echo '<option value="product_' . $product_id . '">' . $product_title . '</option>';
            }
            wp_reset_postdata();
        } else {
            echo '<option value="">No products found</option>';
        }
    }
    
    wp_die();
}

// Get Category Hierarchy (for Carbon Steel) - WITH CORRECT LOGIC
add_action('wp_ajax_get_category_hierarchy', 'get_category_hierarchy_callback');
add_action('wp_ajax_nopriv_get_category_hierarchy', 'get_category_hierarchy_callback');

function get_category_hierarchy_callback() {
    $parent_id = intval($_POST['parent_id']);
    $level = intval($_POST['level']);
    
    if ($parent_id) {
        // Get child categories (sub-categories)
        $child_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'parent' => $parent_id,
            'hide_empty' => false,
        ));
        
        // Get products directly under this category (NOT under sub-categories)
        $products_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $parent_id,
                    'include_children' => false  // IMPORTANT: Only get products directly under this category
                ),
            ),
        );
        $products = new WP_Query($products_args);
        
        // Start output
        echo '<option value="">Select Option</option>';
        
        // CASE 1: Show sub-categories if they exist
        if (!empty($child_categories)) {
            foreach ($child_categories as $category) {
                echo '<option value="cat_' . $category->term_id . '">📁 ' . $category->name . '</option>';
            }
        }
        
        // CASE 2: Show products ONLY if there are no sub-categories
        // (Products that belong directly to this category, not to sub-categories)
        if (empty($child_categories) && $products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_id = get_the_ID();
                $product_title = get_the_title();
                echo '<option value="product_' . $product_id . '">📦 ' . $product_title . '</option>';
            }
            wp_reset_postdata();
        }
        
        // CASE 3: If nothing found
        if (empty($child_categories) && !$products->have_posts()) {
            echo '<option value="">No options available</option>';
        }
    }
    
    wp_die();
}