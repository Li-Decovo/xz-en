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
        'css_classes' => 'xz-wp-product-archive',
    ], [
        xz_el_widget('xpa00002', 'xinzhou-product-categories', [
            'hide_empty' => '',
            'show_description' => '',
            'image_size' => 'large',
        ]),
        xz_el_widget('xpa00003', 'xinzhou-product-category-content', [
            'content_type' => 'short',
            'show_title' => 'yes',
            'all_products_link' => 'yes',
            'fallback_content' => '<p>Explore Xinzhou automated resistance welding machines and complete production lines for steel grating, reinforcing mesh, IBC tanks, lattice girders, cable trays, fence panels and custom welding applications.</p>',
        ]),
        xz_el_container('xpa00004', [
            'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '34', 'right' => '24', 'bottom' => '72', 'left' => '24', 'isLinked' => false],
            'flex_direction' => 'column',
            'gap' => ['unit' => 'px', 'size' => 26, 'sizes' => []],
            'css_classes' => 'xz-wp-archive-list',
        ], [
            xz_el_widget('xpa00005', 'heading', [
                'title' => 'Machines in This Category',
                'header_size' => 'h2',
            ]),
            xz_el_widget('xpa00006', 'archive-posts', [
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
                'archive_classic_read_more_text' => 'View Details',
                'pagination_type' => 'numbers_and_prev_next',
                'pagination_prev_label' => 'Previous',
                'pagination_next_label' => 'Next',
                '_css_classes' => 'xz-native-archive-posts',
            ]),
        ]),
        xz_el_container('xpa00007', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#F6F7F9',
            'css_classes' => 'xz-product-category-detail-section',
        ], [
            xz_el_widget('xpa00008', 'xinzhou-product-category-content', [
                'content_type' => 'detailed',
                'show_title' => '',
                'all_products_link' => '',
                'fallback_content' => '<p>Xinzhou combines welding engineering, automation, tooling and production-line integration to support both standard machine configurations and customized manufacturing projects. Each system can be planned around product specifications, target output, available factory space and the required automation level.</p>',
            ]),
        ]),
    ], false),
];

$product_archive_css = <<<'CSS'
selector .xz-wp-product-archive{padding-top:28px;}
selector .xz-wp-archive-list{margin:0 auto;}
selector .xz-wp-archive-list>.elementor-widget-heading .elementor-heading-title{font-family:Inter,Arial,sans-serif;font-size:32px;font-weight:700;color:#111827;}
selector .xz-native-archive-posts .elementor-posts-container{column-gap:22px;row-gap:32px;}
selector .xz-native-archive-posts .elementor-post{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-archive-posts .elementor-post__thumbnail__link{margin-bottom:0;}
selector .xz-native-archive-posts .elementor-post__thumbnail{padding-bottom:75%!important;}
selector .xz-native-archive-posts .elementor-post__thumbnail img{width:100%;height:100%;object-fit:cover;}
selector .xz-native-archive-posts .elementor-post__text{padding:20px 22px 24px;text-align:center;}
selector .xz-native-archive-posts .elementor-post__title{font-family:Inter,Arial,sans-serif;font-size:18px;line-height:1.45;}
selector .xz-native-archive-posts .elementor-post__title a{color:#111827;}
selector .xz-native-archive-posts .elementor-post__excerpt{color:#64748b;line-height:1.65;}
selector .xz-native-archive-posts .elementor-post__read-more{color:#d84120;font-weight:700;text-transform:uppercase;}
selector .elementor-pagination{margin-top:38px;}
@media(max-width:767px){selector .xz-wp-product-archive{padding-top:18px;}selector .xz-wp-archive-list{padding-left:16px!important;padding-right:16px!important;}}
CSS;

$product_single = [
    xz_el_container('xps00001', [
        'content_width' => 'full',
        'flex_direction' => 'column',
        'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
        'css_classes' => 'xz-wp-product-single',
    ], [
        xz_el_container('xps00002', [
            'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
            'flex_direction' => 'row',
            'gap' => ['unit' => 'px', 'size' => 0, 'sizes' => []],
            'css_classes' => 'xz-wp-product-single-hero',
        ], [
            xz_el_container('xps00003', [
                'content_width' => 'full',
                'width' => ['unit' => '%', 'size' => 54, 'sizes' => []],
                'css_classes' => 'xz-wp-product-gallery-column',
            ], [
                xz_el_widget('xps00004', 'xinzhou-product-gallery'),
            ]),
            xz_el_container('xps00005', [
                'content_width' => 'full',
                'width' => ['unit' => '%', 'size' => 46, 'sizes' => []],
                'flex_direction' => 'column',
                'gap' => ['unit' => 'px', 'size' => 18, 'sizes' => []],
                'css_classes' => 'xz-wp-product-summary',
            ], [
                xz_el_widget('xps00006', 'xinzhou-breadcrumbs'),
                xz_el_widget('xps00007', 'theme-post-title', [
                    'header_size' => 'h1',
                ]),
                xz_el_widget('xps00008', 'theme-post-excerpt'),
                xz_el_widget('xps00009', 'xinzhou-product-summary-data', [
                    'parameter_limit' => 4,
                    'finished_limit' => 2,
                ]),
                xz_el_widget('xps00010', 'button', [
                    'text' => 'Send an Inquiry',
                    'link' => ['url' => '/contact/#inquiry', 'is_external' => '', 'nofollow' => '', 'custom_attributes' => 'data-inquiry-open|true'],
                    '_css_classes' => 'xz-product-inquiry-widget',
                ]),
            ]),
        ]),
        xz_el_container('xps00011', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#F6F7F9',
            'padding' => ['unit' => 'px', 'top' => '66', 'right' => '24', 'bottom' => '76', 'left' => '24', 'isLinked' => false],
            'css_classes' => 'xz-wp-product-information-section',
        ], [
            xz_el_widget('xps00012', 'xinzhou-product-information'),
        ]),
        xz_el_container('xps00013', [
            'width' => ['unit' => 'px', 'size' => 1850, 'sizes' => []],
            'padding' => ['unit' => 'px', 'top' => '70', 'right' => '24', 'bottom' => '86', 'left' => '24', 'isLinked' => false],
            'flex_direction' => 'column',
            'gap' => ['unit' => 'px', 'size' => 28, 'sizes' => []],
            'css_classes' => 'xz-wp-related-products',
        ], [
            xz_el_widget('xps00014', 'heading', [
                'title' => 'Related Products',
                'header_size' => 'h2',
            ]),
            xz_el_widget('xps00015', 'posts', [
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
                'read_more_text' => 'View Details',
                'pagination_type' => '',
                'posts_post_type' => 'product',
                'posts_query_id' => 'xinzhou_related_products',
                '_css_classes' => 'xz-native-related-posts',
            ]),
        ]),
    ], false),
];

$product_single_css = <<<'CSS'
selector .xz-wp-product-single{font-family:Inter,Arial,sans-serif;color:#111827;}
selector .xz-wp-product-single-hero{margin:0 auto;align-items:stretch;}
selector .xz-wp-product-gallery-column{padding-right:24px;padding-bottom:42px;}
selector .xz-wp-product-summary{padding:54px clamp(34px,4vw,72px);justify-content:center;}
selector .xz-wp-product-summary .elementor-widget-theme-post-title .elementor-heading-title{max-width:760px;margin:0;font-size:clamp(30px,2.5vw,42px);line-height:1.14;color:#111827;}
selector .xz-wp-product-summary .elementor-widget-theme-post-excerpt{color:#64748b;font-size:16px;line-height:1.75;}
selector .xz-product-inquiry-widget .elementor-button{min-height:48px;padding:15px 24px;border-radius:0;background:#d84120;font-weight:700;text-transform:uppercase;}
selector .xz-wp-product-information-section>.e-con-inner{width:min(100%,1850px);margin:0 auto;}
selector .xz-wp-related-products{margin:0 auto;}
selector .xz-wp-related-products>.elementor-widget-heading .elementor-heading-title{font-size:32px;color:#111827;}
selector .xz-native-related-posts .elementor-post{overflow:hidden;border:1px solid #e2e8f0;background:#fff;}
selector .xz-native-related-posts .elementor-post__thumbnail__link{margin-bottom:0;}
selector .xz-native-related-posts .elementor-post__thumbnail{padding-bottom:75%!important;}
selector .xz-native-related-posts .elementor-post__thumbnail img{width:100%;height:100%;object-fit:cover;}
selector .xz-native-related-posts .elementor-post__text{padding:18px 20px 22px;text-align:center;}
selector .xz-native-related-posts .elementor-post__title{font-size:18px;line-height:1.45;}
selector .xz-native-related-posts .elementor-post__title a{color:#111827;}
selector .xz-native-related-posts .elementor-post__read-more{color:#d84120;font-weight:700;text-transform:uppercase;}
@media(max-width:900px){selector .xz-wp-product-single-hero{flex-direction:column!important;}selector .xz-wp-product-gallery-column,selector .xz-wp-product-summary{width:100%!important;}selector .xz-wp-product-gallery-column{padding-right:0;}selector .xz-wp-product-summary{padding:34px 24px 48px;}}
@media(max-width:640px){selector .xz-wp-product-summary{padding-left:16px;padding-right:16px;}selector .xz-wp-product-information-section,selector .xz-wp-related-products{padding-left:16px!important;padding-right:16px!important;}}
CSS;

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
