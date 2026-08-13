<?php

if (!defined('ABSPATH')) {
    exit;
}

function xz_el_container(string $id, array $settings, array $elements = [], bool $inner = true): array {
    return [
        'id' => $id,
        'elType' => 'container',
        'settings' => $settings,
        'elements' => $elements,
        'isInner' => $inner,
    ];
}

function xz_el_widget(string $id, string $type, array $settings = []): array {
    return [
        'id' => $id,
        'elType' => 'widget',
        'settings' => $settings,
        'elements' => [],
        'widgetType' => $type,
    ];
}

function xz_update_elementor_document(int $post_id, array $data, string $css, array $conditions): void {
    update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($data)));
    update_post_meta($post_id, '_elementor_page_settings', ['custom_css' => $css]);
    update_post_meta($post_id, '_elementor_conditions', $conditions);
    delete_post_meta($post_id, '_elementor_element_cache');
    delete_post_meta($post_id, '_elementor_page_assets');
    delete_post_meta($post_id, '_elementor_css');
}

$product_archive = [
    xz_el_container('xpa00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-products-page products-main',
    ], [
        xz_el_widget('xpa00002', 'xinzhou-product-categories', [
            'layout' => 'archive',
            'hide_empty' => '',
            'show_description' => '',
            'image_size' => 'large',
        ]),
        xz_el_widget('xpa00003', 'xinzhou-product-category-content', [
            'content_type' => 'short',
            'show_title' => 'yes',
            'all_products_link' => '',
            'fallback_content' => '<p>Explore Xinzhou automated resistance welding machines and complete production lines for steel grating, reinforcing mesh, IBC tanks, lattice girders, cable trays, fence panels and custom welding applications.</p>',
        ]),
        xz_el_widget('xpa00004', 'xinzhou-product-archive-grid', [
            'title' => 'Machines in This Category',
            'show_label' => 'yes',
        ]),
        xz_el_widget('xpa00005', 'xinzhou-product-category-content', [
            'content_type' => 'detailed',
            'show_title' => 'yes',
            'eyebrow' => 'Category Description',
            'all_products_link' => '',
            'fallback_content' => '<p>Xinzhou combines welding engineering, automation, tooling and production-line integration to support both standard machine configurations and customized manufacturing projects. Each system can be planned around product specifications, target output, available factory space and the required automation level.</p>',
        ]),
        xz_el_widget('xpa00006', 'xinzhou-product-worldwide', [
            'title' => 'Xinzhou Worldwide',
            'logos' => [
                ['image' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/xinzhou-home-assets/site-logo.webp'], 'alt' => 'Xinzhou Resistance Welder', 'link' => ['url' => '#']],
                ['image' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/xinzhou-home-assets/site-logo.webp'], 'alt' => 'Xinzhou Automated Welding Lines', 'link' => ['url' => '#']],
                ['image' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/xinzhou-home-assets/site-logo.webp'], 'alt' => 'Xinzhou Mesh Welding Systems', 'link' => ['url' => '#']],
                ['image' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/xinzhou-home-assets/site-logo.webp'], 'alt' => 'Xinzhou Global Service', 'link' => ['url' => '#']],
            ],
        ]),
    ], false),
];

$product_archive_css = '';

$product_single = [
    xz_el_container('xps00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-product-detail-page product-detail-main',
    ], [
        xz_el_widget('xps00002', 'xinzhou-product-detail-hero', ['parameter_limit' => 3, 'finished_limit' => 2, 'finished_title' => 'Finished Products', 'view_all_text' => 'View All', 'button_text' => 'Send an Inquiry']),
        xz_el_widget('xps00003', 'xinzhou-product-detail-information', ['eyebrow' => 'Product Information', 'title' => 'Engineering Details', 'overview_label' => 'Overview', 'specifications_label' => 'Technical Specifications', 'finished_label' => 'Finished Products', 'workflow_label' => 'Configuration & Workflow', 'faq_label' => 'FAQ']),
        xz_el_widget('xps00004', 'xinzhou-product-detail-related', ['eyebrow' => 'Related Products', 'title' => 'Complete Your Production Line', 'count' => 3]),
    ], false),
];

$product_single_css = '';

$news_archive = [
    xz_el_container('xna00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-news-page news-main',
    ], [
        xz_el_widget('xna00002', 'xinzhou-news-archive-hero', ['background' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/2026/07/news-hero-factory.webp'], 'title' => 'Updates from Xinzhou', 'description' => "Follow international exhibitions, customer exchanges and the latest developments in Xinzhou's automated welding equipment business."]),
        xz_el_widget('xna00003', 'xinzhou-news-archive-featured', ['eyebrow' => 'Latest Update', 'title' => 'Exhibitions and Industry Connections', 'description' => 'See how Xinzhou presents production line capabilities and discusses real manufacturing requirements with customers around the world.', 'featured_post' => 187, 'link_text' => 'Read Full Story']),
        xz_el_widget('xna00004', 'xinzhou-news-archive-grid', ['eyebrow' => 'News Archive', 'title' => 'More Xinzhou Updates', 'posts_per_page' => 9, 'link_text' => 'Read More', 'featured_post' => 187]),
    ], false),
];

$news_archive_css = '';

$news_single = [
    xz_el_container('xns00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-news-detail-page news-detail-main',
    ], [
        xz_el_widget('xns00002', 'xinzhou-news-detail-hero', ['background' => ['url' => 'https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/2026/08/news-detail-hero.webp'], 'news_label' => 'News']),
        xz_el_widget('xns00003', 'xinzhou-news-detail-body', ['toc_title' => 'Article Contents', 'back_text' => 'Back to All News', 'inquiry_label' => 'Equipment Inquiry', 'inquiry_title' => 'Planning an Automated Welding Project?', 'inquiry_copy' => 'Share your finished product, production target and factory conditions with the Xinzhou team.', 'button_text' => 'Send an Inquiry', 'email' => 'xinzhou@weldercn.com']),
        xz_el_widget('xns00004', 'xinzhou-news-detail-related', ['eyebrow' => 'More Updates', 'title' => 'Related News', 'view_all_text' => 'View All News', 'count' => 3]),
    ], false),
];

$news_single_css = '';

function xz_ensure_acf_field(int $group_id, array $field): void {
    if (!function_exists('acf_get_field_group') || !function_exists('acf_update_field')) {
        return;
    }

    foreach ((array) acf_get_fields($group_id) as $existing) {
        if (($existing['name'] ?? '') === ($field['name'] ?? '')) {
            return;
        }
    }

    $group = acf_get_field_group($group_id);
    if (!$group || empty($group['key'])) {
        return;
    }

    acf_update_field(array_merge([
        'type' => 'text',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
        'default_value' => '',
        'maxlength' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
    ], $field, ['parent' => $group_id]));
}

$managed_acf_fields = [
    'category_archive_title',
    'category_detailed_title',
];
foreach (get_posts([
    'post_type' => 'acf-field',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'post_parent' => 0,
]) as $orphan_field) {
    if (in_array($orphan_field->post_excerpt, $managed_acf_fields, true)) {
        wp_delete_post($orphan_field->ID, true);
    }
}

xz_ensure_acf_field(151, [
    'key' => 'field_xz_category_archive_title',
    'label' => 'Products Section Heading',
    'name' => 'category_archive_title',
    'instructions' => '',
    'menu_order' => 4,
]);
xz_ensure_acf_field(151, [
    'key' => 'field_xz_category_detailed_title',
    'label' => 'Detailed Description Heading',
    'name' => 'category_detailed_title',
    'instructions' => '',
    'menu_order' => 5,
]);

$steel_grating = get_term_by('slug', 'steel-grating', 'product_category');
if ($steel_grating instanceof WP_Term && function_exists('update_field')) {
    $term_context = 'product_category_' . $steel_grating->term_id;
    update_field('field_xz_category_archive_title', 'Steel Grating Production Lines', $term_context);
    update_field('field_xz_category_detailed_title', 'Intelligent Steel Grating Production Lines & Integrated Systems', $term_context);
    update_field('field_xz_category_long_description', implode("\n\n", [
        "The GGV Series is Xinzhou's heavy-duty engineering system for the high-volume production of industrial steel gratings. The production line integrates flat load bars with automatically fed twisted cross bars using transformer technology, microcomputer controls and a high-precision servo pulling system.",
        'HMI + PLC control supports visual operation and flexible cross bar pitch adjustment. Auxiliary equipment can be integrated for twisted bar forming, edge trimming, panel cutting, binding bar welding, side discharge and heavy-duty stacking.',
        'Standard and customized configurations are available according to grating specifications, output targets, factory layout and required automation level.',
    ]), $term_context);
}

$overview_image_url = wp_get_attachment_image_url(181, 'full');
if ($overview_image_url && get_post_type(158) === 'product' && function_exists('update_field')) {
    update_field('field_xz_product_overview_primary', '<h3>Fully Integrated Steel Grating Production</h3><p>The GGV Series is Xinzhou\'s heavy-duty engineering system for the high-volume production of industrial steel gratings. It integrates flat load bars with automatically fed twisted cross bars using transformer technology and microcomputer controls.</p><p>The line is managed through an HMI + PLC system and driven by high-precision servo pulling. Standard and customized configurations are available according to grating specifications, output targets, factory layout and required automation level.</p>', 158);
    update_field('field_xz_product_overview_image', 181, 158);
    update_field('field_xz_product_overview_secondary', '<h3>Performance, Feeding and Intelligent Control</h3><p>The water-cooled transformer delivers high welding current, efficient cooling and long service life. During production, a heavy-duty hydraulic correction mechanism realigns the steel grating panel to maintain a flat and uniform finished result.</p><p>Automatic servo cross bar feeding, dual-rod stocking and the load bar pre-feeding system reduce waiting time between welding cycles. HMI + PLC control provides clear visual operation, while servo pulling allows the cross bar pitch to be adjusted through the touch screen according to the required product specification.</p><p>Integrated IoT functions support remote monitoring, fault diagnosis and program updates, helping Xinzhou\'s technical team respond efficiently to overseas service requirements.</p>', 158);
}

xz_update_elementor_document(193, $product_archive, $product_archive_css, [
    'include/archive/product_archive',
    'include/archive/product_category',
]);
xz_update_elementor_document(195, $product_single, $product_single_css, [
    'include/singular/product',
]);
xz_update_elementor_document(197, $news_archive, $news_archive_css, [
    'include/archive/post_archive',
    'include/archive/category',
]);
xz_update_elementor_document(199, $news_single, $news_single_css, [
    'include/singular/post',
]);

// The posts page is rendered by the News archive template; keeping page-level HTML here is misleading.
update_post_meta(23, '_elementor_data', wp_slash('[]'));
delete_post_meta(23, '_elementor_element_cache');
delete_post_meta(23, '_elementor_page_assets');
delete_post_meta(23, '_elementor_css');

// Migrate scalar product copy into native WordPress fields used by Elementor widgets.
foreach (get_posts(['post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1]) as $product) {
    $updates = ['ID' => $product->ID];
    if (!$product->post_excerpt && function_exists('get_field')) {
        $updates['post_excerpt'] = (string) get_field('product_short_description', $product->ID);
    }
    if (!$product->post_content && function_exists('get_field')) {
        $updates['post_content'] = (string) get_field('product_overview_primary', $product->ID);
    }
    if (count($updates) > 1) {
        wp_update_post(wp_slash($updates));
    }
}

// Keep the native term description useful for SEO and core WordPress screens.
foreach (get_terms(['taxonomy' => 'product_category', 'hide_empty' => false]) as $term) {
    if (!$term->description && function_exists('get_field')) {
        $short = (string) get_field('category_short_description', 'product_category_' . $term->term_id);
        if ($short) {
            wp_update_term($term->term_id, 'product_category', ['description' => $short]);
        }
    }
}

wp_update_post([
    'ID' => 309,
    'post_status' => 'draft',
    'post_title' => 'Legacy Taxonomy Loop Item (Unused)',
]);

// The CPT archive owns /products/. Keep the old Page out of the editor workflow.
wp_update_post([
    'ID' => 22,
    'post_status' => 'draft',
    'post_title' => 'Products (Legacy Page - Not Used)',
    'post_name' => 'products-legacy-page',
]);

$conditions = get_option('elementor_pro_theme_builder_conditions', []);
$conditions['archive'][193] = ['include/archive/product_archive', 'include/archive/product_category'];
$conditions['archive'][197] = ['include/archive/post_archive', 'include/archive/category'];
$conditions['single'][195] = ['include/singular/product'];
$conditions['single'][199] = ['include/singular/post'];
update_option('elementor_pro_theme_builder_conditions', $conditions, false);

$sitemap_settings = (array) get_option('rank-math-options-sitemap', []);
$sitemap_settings['pt_product_sitemap'] = 'on';
$sitemap_settings['tax_product_category_sitemap'] = 'on';
update_option('rank-math-options-sitemap', $sitemap_settings, false);
if (class_exists('\\RankMath\\Sitemap\\Cache')) {
    \RankMath\Sitemap\Cache::invalidate_storage();
}

echo "Updated Elementor templates 193, 195, 197 and 199.\n";
