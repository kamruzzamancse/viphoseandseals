<?php
/**
 * Child theme styles
 */
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_style' );
function hello_elementor_child_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
}

/**
 * Shortcode to display ACF Product Details
 * Usage: [product_details]
 */
add_shortcode( 'product_details', 'product_details_shortcode' );

function product_details_shortcode() {
    global $post;
    
    // Check if we have a valid product
    if ( ! $post || $post->post_type !== 'product' ) {
        return '';
    }
    
    // Check if ACF is active
    if ( ! function_exists( 'get_field' ) ) {
        return '<p>ACF plugin is not active.</p>';
    }
    
    // Define fields to display (using your exact ACF field names)
    $fields = array(
        'weight'             => 'Weight (lbs)',
        'nom_id'             => 'Nom ID',
        'nom_od'             => 'Nom OD',
        'nom_height'         => 'Nom Height',
        'd_groove_width'     => 'D Groove Width',
        'rod'                => 'Rod',
        'g_housing_diameter' => 'G Housing Diameter',
        'f_throat_diameter'  => 'F Throat Diameter',
        'material'           => 'Material',
        'overall_height'     => 'Overall Height',
        'brand'              => 'Brand',
        'temperature_range'  => 'Temperature Range',
        'speed'              => 'Speed',
    );
    
    ob_start();
    ?>
    <div class="acf-product-details">
        <h3>Product Specifications</h3>
        <table class="product-specs-table">
            <?php foreach ( $fields as $field_name => $field_label ) : ?>
                <?php 
                $value = get_field( $field_name, $post->ID );
                if ( ! empty( $value ) ) : 
                ?>
                    <tr>
                        <th><?php echo esc_html( $field_label ); ?></th>
                        <td><?php echo esc_html( $value ); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
    
    return ob_get_clean();
}

/**
 * Add Specifications tab using the shortcode
 */
add_filter( 'woocommerce_product_tabs', 'add_specifications_tab_with_shortcode' );

function add_specifications_tab_with_shortcode( $tabs ) {
    $tabs['specifications'] = array(
        'title'    => __( 'Specifications', 'woocommerce' ),
        'priority' => 15,
        'callback' => 'specifications_tab_callback'
    );
    return $tabs;
}

function specifications_tab_callback() {
    echo do_shortcode( '[product_details]' );
}

/**
 * =============================================
 * SEALS BY SIZE FILTER - COMPLETE SYSTEM
 * =============================================
 */

/**
 * Shortcode 1: Display filter form
 * Usage: [seals_size_filter]
 */
add_shortcode( 'seals_size_filter', 'seals_size_filter_form' );

function seals_size_filter_form() {
    ob_start();
    ?>
    <div class="seals-filter">
        <div class="seals-filter-title">SEALS BY SIZE</div>
        
        <div class="seals-filter-row">
            <!-- Measurement Dropdown -->
            <div class="seals-filter-field">
                <label>MEASUREMENT:</label>
                <select id="filter-measurement" class="seals-select">
                    <option value="inch">Inch</option>
                    <option value="metric">Metric</option>
                </select>
            </div>
            
            <!-- Type Dropdown -->
            <div class="seals-filter-field">
                <label>TYPE:</label>
                <select id="filter-type" class="seals-select">
                    <option value="all">All</option>
                    <?php
                    $seals_category = get_term_by('slug', 'seals', 'product_cat');
                    
                    if( $seals_category ) {
                        $subcategories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => false,
                            'parent' => $seals_category->term_id,
                            'orderby' => 'term_id',
                            'order' => 'DESC'
                        ));
                        
                        foreach($subcategories as $subcat) {
                            echo '<option value="' . esc_attr($subcat->slug) . '">' . esc_html($subcat->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            
            <!-- ID Field - TEXT input -->
            <div class="seals-filter-field">
                <label>ID</label>
                <div class="seals-combo">
                    <div class="input-wrapper">
                        <input type="text" id="filter-id" class="seals-input" placeholder="Enter ID">
                    </div>
                </div>
                <div class="dropdown-wrapper">
                    <label>Tol</label>
                    <select id="id-tolerance" class="seals-tol-select">
                        <option value="0">0%</option>
                        <option value="2">2%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                        <option value="25">25%</option>
                    </select>
                </div>
            </div>
            
            <!-- OD Field - TEXT input -->
            <div class="seals-filter-field">
                <label>OD</label>
                <div class="seals-combo">
                    <div class="input-wrapper">
                        <input type="text" id="filter-od" class="seals-input" placeholder="Enter OD">
                    </div>
                </div>
                <div class="dropdown-wrapper">
                    <label>Tol</label>
                    <select id="od-tolerance" class="seals-tol-select">
                        <option value="0">0%</option>
                        <option value="2">2%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                        <option value="25">25%</option>
                    </select>
                </div>
            </div>
            
            <!-- HT Field - TEXT input -->
            <div class="seals-filter-field">
                <label>HT</label>
                <div class="seals-combo">
                    <div class="input-wrapper">
                        <input type="text" id="filter-height" class="seals-input" placeholder="Enter Height">
                    </div>
                </div>
                <div class="dropdown-wrapper">
                    <label>Tol</label>
                    <select id="height-tolerance" class="seals-tol-select">
                        <option value="0">0%</option>
                        <option value="2">2%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                        <option value="15">15%</option>
                        <option value="20">20%</option>
                        <option value="25">25%</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode 2: Display products only from Seals category and its sub-categories
 * Usage: [seals_products]
 */
add_shortcode( 'seals_products', 'seals_products_display' );

function seals_products_display() {
    ob_start();
    
    // Get Seals category ID
    $seals_category = get_term_by('slug', 'seals', 'product_cat');
    
    if( ! $seals_category ) {
        echo '<p>Seals category not found. Please check the category slug.</p>';
        return ob_get_clean();
    }
    
    // Get all category IDs under Seals
    $category_ids = array($seals_category->term_id);
    
    $subcategories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => $seals_category->term_id,
    ));
    
    if( ! is_wp_error($subcategories) ) {
        foreach($subcategories as $subcat) {
            $category_ids[] = $subcat->term_id;
        }
    }
    
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_ids,
                'include_children' => true,
            )
        )
    );
    
    $query = new WP_Query($args);
    
    if( $query->have_posts() ) {
        woocommerce_product_loop_start();
        while( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();
        
        // Pagination
        if( $query->max_num_pages > 1 ) {
            echo '<div class="woocommerce-pagination">';
            echo paginate_links(array(
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
            ));
            echo '</div>';
        }
    } else {
        echo '<p>No products found in Seals category.</p>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}

function filter_seals_products_callback() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $seals_category = get_term_by('slug', 'seals', 'product_cat');

    if ( ! $seals_category ) {
        echo '<div class="woocommerce-info">Seals category not found.</div>';
        wp_die();
    }

    $category_ids = array($seals_category->term_id);

    $subcategories = get_terms(array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => $seals_category->term_id,
    ));

    if ( ! is_wp_error($subcategories) ) {
        foreach($subcategories as $subcat) {
            $category_ids[] = $subcat->term_id;
        }
    }

    // Meta query setup
    $meta_query = array('relation' => 'AND');

    // Tax query setup
    $tax_query = array(
        array(
            'taxonomy'         => 'product_cat',
            'field'            => 'term_id',
            'terms'            => $category_ids,
            'include_children' => true,
        )
    );

    // TYPE FILTER
    if ( ! empty($_POST['product_type']) && $_POST['product_type'] !== 'all' ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['product_type']),
        );
    }

    // ID FILTER
    if ( ! empty($_POST['id']) ) {
        $value = sanitize_text_field($_POST['id']);
        $tol   = floatval($_POST['id_tolerance']);

        if ( is_numeric($value) && $tol > 0 ) {
            $min = $value - ($value * $tol / 100);
            $max = $value + ($value * $tol / 100);
            $meta_query[] = array(
                'key'     => 'nom_id',
                'value'   => array($min, $max),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        } else {
            $meta_query[] = array(
                'key'     => 'nom_id',
                'value'   => $value,
                'compare' => 'LIKE',
            );
        }
    }

    // OD FILTER
    if ( ! empty($_POST['od']) ) {
        $value = sanitize_text_field($_POST['od']);
        $tol   = floatval($_POST['od_tolerance']);

        if ( is_numeric($value) && $tol > 0 ) {
            $min = $value - ($value * $tol / 100);
            $max = $value + ($value * $tol / 100);
            $meta_query[] = array(
                'key'     => 'nom_od',
                'value'   => array($min, $max),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        } else {
            $meta_query[] = array(
                'key'     => 'nom_od',
                'value'   => $value,
                'compare' => 'LIKE',
            );
        }
    }

    // HEIGHT FILTER
    if ( ! empty($_POST['height']) ) {
        $value = sanitize_text_field($_POST['height']);
        $tol   = floatval($_POST['height_tolerance']);

        if ( is_numeric($value) && $tol > 0 ) {
            $min = $value - ($value * $tol / 100);
            $max = $value + ($value * $tol / 100);
            $meta_query[] = array(
                'key'     => 'nom_height',
                'value'   => array($min, $max),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        } else {
            $meta_query[] = array(
                'key'     => 'nom_height',
                'value'   => $value,
                'compare' => 'LIKE',
            );
        }
    }

    // WP Query arguments
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'tax_query'      => $tax_query,
    );

    if ( count($meta_query) > 1 ) {
        $args['meta_query'] = $meta_query;
    }

    $query = new WP_Query($args);

    ob_start();

    if ( $query->have_posts() ) {
        // Use same wrapper as main WooCommerce loop
        echo '<ul class="products columns-4">';

        while ( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' );
        }

        echo '</ul>';

        // Optional: pagination
        if ( $query->max_num_pages > 1 ) {
            echo '<div class="woocommerce-pagination">';
            echo paginate_links(array(
                'total'   => $query->max_num_pages,
                'current' => max(1, $paged),
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
            ));
            echo '</div>';
        }
    } else {
        echo '<div class="woocommerce-info">No products found matching your criteria.</div>';
    }

    wp_reset_postdata();

    echo ob_get_clean();
    wp_die();
}

add_action( 'wp_ajax_filter_seals_products', 'filter_seals_products_callback' );
add_action( 'wp_ajax_nopriv_filter_seals_products', 'filter_seals_products_callback' );

/**
 * Enqueue JavaScript
 */
add_action( 'wp_enqueue_scripts', 'seals_filter_scripts' );

function seals_filter_scripts() {
    // Get current post safely
    $current_post = get_post();
    $has_shortcode = false;
    
    // Check if post exists and has content
    if ( $current_post && isset( $current_post->post_content ) ) {
        $has_shortcode = has_shortcode( $current_post->post_content, 'seals_size_filter' );
    }
    
    // Enqueue scripts on appropriate pages
    if( is_page( 'seals-shop' ) || is_shop() || $has_shortcode ) {
        wp_enqueue_script( 'seals-filter-js', get_stylesheet_directory_uri() . '/js/seals-filter.js', array('jquery'), '1.0', true );
        wp_localize_script( 'seals-filter-js', 'seals_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' )
        ));
    }
}

/**
 * =============================================
 * FORCE HIDE ALL OTHER CATEGORIES ON SEALS PAGE
 * =============================================
 */

/**
 * Remove category listings from products loop
 */
add_action( 'wp', 'force_remove_categories_from_seals_page' );

function force_remove_categories_from_seals_page() {
    if( is_page( 'seals-shop' ) ) {
        // Remove WooCommerce category actions
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_product_categories', 30 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_product_subcategories', 20 );
        remove_action( 'woocommerce_after_shop_loop', 'woocommerce_product_subcategories', 20 );
        
        // Remove from product loop
        remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
    }
}

/**
 * Filter products query to only show products (not categories)
 */
add_filter( 'woocommerce_product_subcategories_hide_empty', '__return_true' );

/**
 * Force hide categories via CSS
 */
add_action( 'wp_head', 'force_hide_categories_css' );

function force_hide_categories_css() {
    if( is_page( 'seals-shop' ) ) {
        ?>
        <style>
            /* Hide ALL category items in product loop */
            ul.products li.product-category,
            .products li.product-category,
            .woocommerce-loop-category__title,
            li.product-category,
            .product-category {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                position: absolute !important;
                overflow: hidden !important;
            }
            
            /* Make sure normal products are visible */
            ul.products li.product:not(.product-category),
            .products li.product:not(.product-category) {
                display: block !important;
                visibility: visible !important;
                height: auto !important;
                width: auto !important;
                position: relative !important;
            }
            
            /* Reset grid for products */
            ul.products {
                display: flex !important;
                flex-wrap: wrap !important;
            }
        </style>
        <?php
    }
}

/**
 * Override the main query to only get products (not categories)
 */
add_action( 'woocommerce_product_query', 'seals_only_products' );

function seals_only_products( $query ) {
    if( is_page( 'seals-shop' ) ) {
        // Force product post type only
        $query->set( 'post_type', 'product' );
        
        // Only get products from Seals category
        $seals_category = get_term_by('slug', 'seals', 'product_cat');
        
        if( $seals_category ) {
            $tax_query = $query->get('tax_query');
            if( ! is_array($tax_query) ) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => 'seals',
                'operator' => 'IN'
            );
            
            $query->set('tax_query', $tax_query);
        }
    }
}

/**
 * Alternative: Use pre_get_posts to filter
 */
add_action( 'pre_get_posts', 'pre_get_posts_seals_only' );

function pre_get_posts_seals_only( $query ) {
    if( ! is_admin() && $query->is_main_query() && is_page( 'seals-shop' ) ) {
        $query->set( 'post_type', 'product' );
        $query->set( 'tax_query', array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => 'seals',
                'operator' => 'IN'
            )
        ));
    }
}