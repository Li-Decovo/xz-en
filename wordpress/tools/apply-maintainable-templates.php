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
        'css_classes' => 'xz-wp-news-archive',
    ], [
        xz_el_container('xna00002', [
            'content_width' => 'full',
            'min_height' => ['unit' => 'px', 'size' => 330, 'sizes' => []],
            'flex_direction' => 'column',
            'flex_justify_content' => 'center',
            'flex_align_items' => 'center',
            'css_classes' => 'xz-wp-news-hero',
        ], [
            xz_el_widget('xna00003', 'theme-archive-title', [
                'header_size' => 'h1',
                'align' => 'center',
                'title_color' => '#FFFFFF',
            ]),
        ]),
        xz_el_widget('xna00004', 'xinzhou-news-categories'),
        xz_el_container('xna00005', [
            'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '42', 'right' => '24', 'bottom' => '86', 'left' => '24', 'isLinked' => false],
            'css_classes' => 'xz-wp-archive-list',
        ], [
            xz_el_widget('xna00006', 'archive-posts', [
                '_skin' => 'archive_classic',
                'archive_classic_columns' => 3,
                'archive_classic_columns_tablet' => 2,
                'archive_classic_columns_mobile' => 1,
                'archive_classic_show_image' => 'yes',
                'archive_classic_image_size' => 'large',
                'archive_classic_show_title' => 'yes',
                'archive_classic_show_excerpt' => 'yes',
                'archive_classic_excerpt_length' => 24,
                'archive_classic_show_read_more' => 'yes',
                'archive_classic_read_more_text' => 'Read More',
                'pagination_type' => 'numbers_and_prev_next',
                'pagination_prev_label' => 'Previous',
                'pagination_next_label' => 'Next',
                '_css_classes' => 'xz-native-archive-posts',
            ]),
        ]),
    ], false),
];

$news_archive_css = <<<'CSS'
selector .xz-wp-news-hero{position:relative;background:linear-gradient(rgba(5,10,18,.62),rgba(5,10,18,.62)),url('https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/2026/07/news-hero-factory.webp') center/cover no-repeat;}
selector .xz-wp-news-hero .elementor-heading-title{max-width:1100px;font-family:Inter,Arial,sans-serif;font-size:clamp(38px,4vw,64px);line-height:1.08;text-align:center;color:#fff;}
selector .xz-wp-archive-list{margin:0 auto;}
selector .xz-native-archive-posts .elementor-posts-container{column-gap:24px;row-gap:34px;}
selector .xz-native-archive-posts .elementor-post{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-archive-posts .elementor-post__thumbnail__link{margin-bottom:0;}
selector .xz-native-archive-posts .elementor-post__thumbnail{padding-bottom:75%!important;}
selector .xz-native-archive-posts .elementor-post__thumbnail img{width:100%;height:100%;object-fit:cover;}
selector .xz-native-archive-posts .elementor-post__text{padding:22px 22px 26px;}
selector .xz-native-archive-posts .elementor-post__title{font-family:Inter,Arial,sans-serif;font-size:20px;line-height:1.4;}
selector .xz-native-archive-posts .elementor-post__title a{color:#111827;}
selector .xz-native-archive-posts .elementor-post__excerpt{display:-webkit-box;overflow:hidden;color:#64748b;line-height:1.7;-webkit-box-orient:vertical;-webkit-line-clamp:3;}
selector .xz-native-archive-posts .elementor-post__read-more{color:#d84120;font-weight:700;text-transform:uppercase;}
@media(max-width:640px){selector .xz-wp-archive-list{padding-left:16px!important;padding-right:16px!important;}}
CSS;

$news_single = [
    xz_el_container('xns00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-wp-news-single',
    ], [
        xz_el_container('xns00002', [
            'content_width' => 'full',
            'min_height' => ['unit' => 'px', 'size' => 410, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '60', 'right' => '24', 'bottom' => '60', 'left' => '24', 'isLinked' => false],
            'flex_direction' => 'column',
            'flex_justify_content' => 'center',
            'flex_align_items' => 'center',
            'gap' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
            'css_classes' => 'xz-wp-news-single-hero',
        ], [
            xz_el_widget('xns00003', 'xinzhou-breadcrumbs'),
            xz_el_widget('xns00004', 'theme-post-title', [
                'header_size' => 'h1',
                'align' => 'center',
                'title_color' => '#FFFFFF',
            ]),
            xz_el_widget('xns00005', 'xinzhou-article-meta'),
        ]),
        xz_el_container('xns00006', [
            'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
            'flex_direction' => 'row',
            'gap' => ['unit' => 'px', 'size' => 48, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '68', 'right' => '24', 'bottom' => '92', 'left' => '24', 'isLinked' => false],
            'css_classes' => 'xz-wp-news-single-body',
        ], [
            xz_el_container('xns00007', [
                'content_width' => 'full',
                'width' => ['unit' => '%', 'size' => 70, 'sizes' => []],
                'flex_direction' => 'column',
                'gap' => ['unit' => 'px', 'size' => 30, 'sizes' => []],
                'css_classes' => 'xz-wp-news-single-content',
            ], [
                xz_el_widget('xns00008', 'theme-post-featured-image', ['image_size' => 'full']),
                xz_el_widget('xns00009', 'theme-post-content'),
            ]),
            xz_el_container('xns00010', [
                'content_width' => 'full',
                'width' => ['unit' => '%', 'size' => 30, 'sizes' => []],
                'flex_direction' => 'column',
                'gap' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
                'css_classes' => 'xz-wp-news-single-aside',
            ], [
                xz_el_widget('xns00011', 'xinzhou-article-toc', ['title' => 'Contents']),
                xz_el_container('xns00012', [
                    'content_width' => 'full',
                    'padding' => ['unit' => 'px', 'top' => '26', 'right' => '26', 'bottom' => '26', 'left' => '26', 'isLinked' => true],
                    'background_background' => 'classic',
                    'background_color' => '#111827',
                    'flex_direction' => 'column',
                    'gap' => ['unit' => 'px', 'size' => 12, 'sizes' => []],
                    'css_classes' => 'xz-wp-news-inquiry',
                ], [
                    xz_el_widget('xns00013', 'heading', [
                        'title' => 'Discuss Your Project',
                        'header_size' => 'h2',
                        'title_color' => '#FFFFFF',
                    ]),
                    xz_el_widget('xns00014', 'text-editor', [
                        'editor' => '<p>Share your product specifications, target output and factory requirements with Xinzhou.</p>',
                    ]),
                    xz_el_widget('xns00015', 'fluent-form-widget', [
                        'form_list' => '1',
                        'theme_style' => '',
                        '_css_classes' => 'xz-news-inquiry-form',
                    ]),
                ]),
            ]),
        ]),
        xz_el_container('xns00016', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#F6F7F9',
            'css_classes' => 'xz-wp-related-news-section',
        ], [
            xz_el_container('xns00017', [
                'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
                'padding' => ['unit' => 'px', 'top' => '68', 'right' => '24', 'bottom' => '84', 'left' => '24', 'isLinked' => false],
                'flex_direction' => 'column',
                'gap' => ['unit' => 'px', 'size' => 28, 'sizes' => []],
            ], [
                xz_el_widget('xns00018', 'heading', ['title' => 'Related News', 'header_size' => 'h2']),
                xz_el_widget('xns00019', 'posts', [
                    '_skin' => 'classic',
                    'classic_columns' => 3,
                    'classic_columns_tablet' => 2,
                    'classic_columns_mobile' => 1,
                    'posts_per_page' => 3,
                    'show_image' => 'yes',
                    'image_size' => 'large',
                    'show_title' => 'yes',
                    'show_excerpt' => 'yes',
                    'excerpt_length' => 18,
                    'show_read_more' => 'yes',
                    'read_more_text' => 'Read More',
                    'pagination_type' => '',
                    'posts_post_type' => 'post',
                    'posts_query_id' => 'xinzhou_related_news',
                    '_css_classes' => 'xz-native-related-posts',
                ]),
            ]),
        ]),
    ], false),
];

$news_single_css = <<<'CSS'
selector .xz-wp-news-single-hero{background:linear-gradient(rgba(5,10,18,.68),rgba(5,10,18,.68)),url('https://darkturquoise-camel-554606.hostingersite.com/wp-content/uploads/2026/07/news-hero-factory.webp') center/cover no-repeat;color:#fff;}
selector .xz-wp-news-single-hero .xz-breadcrumbs,selector .xz-wp-news-single-hero .xz-article-meta{justify-content:center;color:rgba(255,255,255,.82);}
selector .xz-wp-news-single-hero .elementor-heading-title{max-width:1180px;margin:0 auto;font-family:Inter,Arial,sans-serif;font-size:clamp(34px,3.7vw,58px);line-height:1.12;text-align:center;color:#fff;}
selector .xz-wp-news-single-body{margin:0 auto;align-items:flex-start;}
selector .xz-wp-news-single-content .elementor-widget-theme-post-featured-image img{width:100%;aspect-ratio:4/3;object-fit:cover;}
selector .xz-wp-news-single-content .elementor-widget-theme-post-content{color:#374151;font-family:Inter,Arial,sans-serif;font-size:17px;line-height:1.85;}
selector .xz-wp-news-single-content h2{margin:44px 0 18px;color:#111827;font-size:30px;line-height:1.25;scroll-margin-top:150px;}
selector .xz-wp-news-single-content blockquote{margin:30px 0;padding:22px 26px;border-left:4px solid #d84120;background:#f6f7f9;}
selector .xz-wp-news-inquiry .elementor-heading-title{font-size:22px;color:#fff;}
selector .xz-wp-news-inquiry .elementor-widget-text-editor{color:rgba(255,255,255,.72);line-height:1.65;}
selector .xz-news-inquiry-form label{color:#fff;}
selector .xz-news-inquiry-form input,selector .xz-news-inquiry-form textarea{border-radius:0!important;}
selector .xz-news-inquiry-form .ff-btn-submit{width:100%;border-radius:0!important;background:#d84120!important;color:#fff!important;font-weight:700;text-transform:uppercase;}
selector .xz-wp-related-news-section>.e-con-inner{width:100%;}
selector .xz-wp-related-news-section .e-con-boxed{margin:0 auto;}
selector .xz-native-related-posts .elementor-post{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-related-posts .elementor-post__thumbnail__link{margin-bottom:0;}
selector .xz-native-related-posts .elementor-post__thumbnail{padding-bottom:75%!important;}
selector .xz-native-related-posts .elementor-post__thumbnail img{width:100%;height:100%;object-fit:cover;}
selector .xz-native-related-posts .elementor-post__text{padding:18px 20px 22px;}
selector .xz-native-related-posts .elementor-post__title{font-size:18px;line-height:1.45;}
selector .xz-native-related-posts .elementor-post__title a{color:#111827;}
selector .xz-native-related-posts .elementor-post__read-more{color:#d84120;font-weight:700;text-transform:uppercase;}
@media(max-width:900px){selector .xz-wp-news-single-body{flex-direction:column!important;padding-left:16px!important;padding-right:16px!important;}selector .xz-wp-news-single-content,selector .xz-wp-news-single-aside{width:100%!important;}selector .xz-wp-news-single-aside{position:static;margin-top:20px;}}
CSS;

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
    'product_card_label',
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
    'label' => 'Archive Display Title',
    'name' => 'category_archive_title',
    'instructions' => 'Optional heading shown above the products. Falls back to the category name.',
    'menu_order' => 4,
]);
xz_ensure_acf_field(151, [
    'key' => 'field_xz_category_detailed_title',
    'label' => 'Detailed Description Title',
    'name' => 'category_detailed_title',
    'instructions' => 'Optional heading shown with the detailed category description.',
    'menu_order' => 5,
]);
xz_ensure_acf_field(124, [
    'key' => 'field_xz_product_card_label',
    'label' => 'Product Card Label',
    'name' => 'product_card_label',
    'instructions' => 'Short label displayed over the product image in archive and related-product cards.',
    'menu_order' => 16,
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

$product_card_labels = [
    158 => 'Steel Grating Lines',
    160 => 'Steel Grating Lines',
    162 => 'Auxiliary Equipment',
    164 => 'Finishing Equipment',
    166 => 'Handling Systems',
    168 => 'Cutting Equipment',
    170 => 'Finishing Equipment',
    172 => 'Material Preparation',
    174 => 'Material Preparation',
    176 => 'Feeding Systems',
    178 => 'Press-Lock Grating',
    180 => 'Turnkey Systems',
];
if (function_exists('update_field')) {
    foreach ($product_card_labels as $product_id => $label) {
        if (get_post_type($product_id) === 'product') {
            update_field('field_xz_product_card_label', $label, $product_id);
        }
    }
}

$overview_image_url = wp_get_attachment_image_url(181, 'full');
if ($overview_image_url && get_post_type(158) === 'product' && function_exists('update_field')) {
    $overview_html = '<div class="product-overview-grid"><div><h3>Fully Integrated Steel Grating Production</h3><p>The GGV Series is Xinzhou\'s heavy-duty engineering system for the high-volume production of industrial steel gratings. It integrates flat load bars with automatically fed twisted cross bars using transformer technology and microcomputer controls.</p><p>The line is managed through an HMI + PLC system and driven by high-precision servo pulling. Standard and customized configurations are available according to grating specifications, output targets, factory layout and required automation level.</p></div><figure><img src="' . esc_url($overview_image_url) . '" alt="Complete GGV steel grating line installed in a manufacturing workshop"></figure></div><div class="product-overview-description"><h3>Performance, Feeding and Intelligent Control</h3><p>The water-cooled transformer delivers high welding current, efficient cooling and long service life. During production, a heavy-duty hydraulic correction mechanism realigns the steel grating panel to maintain a flat and uniform finished result.</p><p>Automatic servo cross bar feeding, dual-rod stocking and the load bar pre-feeding system reduce waiting time between welding cycles. HMI + PLC control provides clear visual operation, while servo pulling allows the cross bar pitch to be adjusted through the touch screen according to the required product specification.</p><p>Integrated IoT functions support remote monitoring, fault diagnosis and program updates, helping Xinzhou\'s technical team respond efficiently to overseas service requirements.</p></div>';
    update_field('field_xz_product_overview', $overview_html, 158);
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
        $updates['post_content'] = (string) get_field('product_overview', $product->ID);
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
