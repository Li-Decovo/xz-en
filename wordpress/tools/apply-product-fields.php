<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('acf_get_field_group') || !function_exists('acf_update_field')) {
    WP_CLI::error('ACF is not available.');
}

$group_id = 124;
$group = acf_get_field_group($group_id);
if (!$group) {
    WP_CLI::error('Product Details field group was not found.');
}

function xzpf_inner_html(DOMNode $node): string {
    $html = '';
    foreach ($node->childNodes as $child) {
        $html .= $node->ownerDocument->saveHTML($child);
    }
    return trim($html);
}

function xzpf_split_overview(string $html): array {
    if ($html === '' || !class_exists('DOMDocument')) {
        return [$html, 0, ''];
    }

    $document = new DOMDocument();
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?><div id="xz-overview-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);
    $grid = $xpath->query('//*[@id="xz-overview-root"]//*[contains(concat(" ", normalize-space(@class), " "), " product-overview-grid ")]')->item(0);
    $description = $xpath->query('//*[@id="xz-overview-root"]//*[contains(concat(" ", normalize-space(@class), " "), " product-overview-description ")]')->item(0);
    if (!$grid) {
        return [$html, 0, ''];
    }

    $copy = $xpath->query('./div[1]', $grid)->item(0);
    $image = $xpath->query('.//figure//img[1]', $grid)->item(0);
    $image_id = $image instanceof DOMElement ? attachment_url_to_postid($image->getAttribute('src')) : 0;
    return [
        $copy ? xzpf_inner_html($copy) : '',
        $image_id,
        $description ? xzpf_inner_html($description) : '',
    ];
}

$products = get_posts([
    'post_type' => 'product',
    'post_status' => 'any',
    'posts_per_page' => -1,
]);
$migrations = [];
foreach ($products as $product) {
    $specifications = get_field('product_specifications', $product->ID);
    $finished = get_field('product_finished_products', $product->ID);
    $overview = (string) get_field('product_overview', $product->ID);
    $migrations[$product->ID] = [
        'specifications' => $specifications,
        'finished' => $finished,
        'overview' => xzpf_split_overview($overview),
    ];
}

$base = [
    'parent' => $group_id,
    'instructions' => '',
    'required' => 0,
    'conditional_logic' => 0,
    'wrapper' => ['width' => '', 'class' => '', 'id' => ''],
];

function xzpf_update_field(array $field): int {
    $matches = get_posts([
        'post_type' => 'acf-field',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'name' => $field['key'],
        'orderby' => 'ID',
        'order' => 'ASC',
    ]);
    if ($matches) {
        $field['ID'] = (int) $matches[0]->ID;
    }
    acf_update_field($field);
    $field_id = (int) ($field['ID'] ?? 0);
    return $field_id;
}

function xzpf_delete_field_post(int $field_id): void {
    $children = get_posts([
        'post_type' => 'acf-field',
        'post_status' => 'any',
        'post_parent' => $field_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
    ]);
    foreach (array_map('intval', $children) as $child_id) {
        xzpf_delete_field_post($child_id);
    }
    wp_delete_post($field_id, true);
}

$fields = [
    ['key' => 'field_xz_product_tab_basic', 'label' => 'Basic Information', 'name' => '', 'type' => 'tab', 'menu_order' => 0, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_product_sort_order', 'label' => 'Sort Order', 'name' => 'product_sort_order', 'type' => 'number', 'menu_order' => 1, 'default_value' => 0, 'min' => '', 'max' => '', 'step' => 1],
    ['key' => 'field_xz_product_short_description', 'label' => 'Short Description', 'name' => 'product_short_description', 'type' => 'wysiwyg', 'menu_order' => 2, 'default_value' => '', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1, 'delay' => 0],
    ['key' => 'field_xz_product_gallery', 'label' => 'Product Gallery', 'name' => 'product_gallery', 'type' => 'gallery', 'menu_order' => 3, 'return_format' => 'id', 'preview_size' => 'medium', 'insert' => 'append', 'library' => 'all', 'min' => '', 'max' => '', 'min_width' => '', 'min_height' => '', 'min_size' => '', 'max_width' => '', 'max_height' => '', 'max_size' => '', 'mime_types' => ''],
    ['key' => 'field_xz_product_key_parameters', 'label' => 'Key Parameters', 'name' => 'product_key_parameters', 'type' => 'repeater', 'menu_order' => 4, 'layout' => 'table', 'button_label' => 'Add Parameter', 'min' => 0, 'max' => 0, 'collapsed' => ''],
    ['key' => 'field_xz_product_tab_overview', 'label' => 'Overview', 'name' => '', 'type' => 'tab', 'menu_order' => 5, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_product_overview_primary', 'label' => 'Overview Primary Content', 'name' => 'product_overview_primary', 'type' => 'wysiwyg', 'menu_order' => 6, 'default_value' => '', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1, 'delay' => 0],
    ['key' => 'field_xz_product_overview_image', 'label' => 'Overview Image', 'name' => 'product_overview_image', 'type' => 'image', 'menu_order' => 7, 'return_format' => 'id', 'preview_size' => 'medium', 'library' => 'all', 'min_width' => '', 'min_height' => '', 'min_size' => '', 'max_width' => '', 'max_height' => '', 'max_size' => '', 'mime_types' => ''],
    ['key' => 'field_xz_product_overview_video_url', 'label' => 'Overview Video URL', 'name' => 'product_overview_video_url', 'type' => 'url', 'menu_order' => 8, 'default_value' => '', 'placeholder' => ''],
    ['key' => 'field_xz_product_overview_secondary', 'label' => 'Overview Secondary Content', 'name' => 'product_overview_secondary', 'type' => 'wysiwyg', 'menu_order' => 9, 'default_value' => '', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1, 'delay' => 0],
    ['key' => 'field_xz_product_tab_specs', 'label' => 'Technical Specifications', 'name' => '', 'type' => 'tab', 'menu_order' => 10, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_product_specifications', 'label' => 'Technical Specifications', 'name' => 'product_specifications', 'type' => 'wysiwyg', 'menu_order' => 11, 'default_value' => '', 'tabs' => 'all', 'toolbar' => 'full', 'media_upload' => 1, 'delay' => 0],
    ['key' => 'field_xz_product_tab_finished', 'label' => 'Finished Products', 'name' => '', 'type' => 'tab', 'menu_order' => 12, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_product_finished_products', 'label' => 'Finished Products', 'name' => 'product_finished_products', 'type' => 'gallery', 'menu_order' => 13, 'return_format' => 'id', 'preview_size' => 'medium', 'insert' => 'append', 'library' => 'all', 'min' => '', 'max' => '', 'min_width' => '', 'min_height' => '', 'min_size' => '', 'max_width' => '', 'max_height' => '', 'max_size' => '', 'mime_types' => ''],
    ['key' => 'field_xz_product_tab_faq', 'label' => 'FAQ', 'name' => '', 'type' => 'tab', 'menu_order' => 14, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_product_faq', 'label' => 'Frequently Asked Questions', 'name' => 'product_faq', 'type' => 'repeater', 'menu_order' => 15, 'layout' => 'block', 'button_label' => 'Add FAQ', 'min' => 0, 'max' => 0, 'collapsed' => 'field_xz_faq_question'],
    ['key' => 'field_xz_product_tab_related', 'label' => 'Related Products', 'name' => '', 'type' => 'tab', 'menu_order' => 16, 'placement' => 'top', 'endpoint' => 0],
    ['key' => 'field_xz_related_products', 'label' => 'Related Products', 'name' => 'related_products', 'type' => 'relationship', 'menu_order' => 17, 'post_type' => ['product'], 'post_status' => ['publish'], 'taxonomy' => '', 'filters' => ['search', 'taxonomy'], 'return_format' => 'id', 'min' => '', 'max' => '', 'elements' => ''],
];

foreach ($fields as $field) {
    xzpf_update_field(array_merge($base, $field));
}

xzpf_update_field(array_merge($base, [
    'parent' => 'field_xz_product_key_parameters',
    'key' => 'field_xz_product_parameter_label',
    'label' => 'Label',
    'name' => 'product_parameter_label',
    'type' => 'text',
    'menu_order' => 0,
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => '',
    'append' => '',
]));
xzpf_update_field(array_merge($base, [
    'parent' => 'field_xz_product_key_parameters',
    'key' => 'field_xz_product_parameter_value',
    'label' => 'Value',
    'name' => 'product_parameter_value',
    'type' => 'text',
    'menu_order' => 1,
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => '',
    'append' => '',
]));
xzpf_update_field(array_merge($base, [
    'parent' => 'field_xz_product_faq',
    'key' => 'field_xz_faq_question',
    'label' => 'Question',
    'name' => 'faq_question',
    'type' => 'text',
    'menu_order' => 0,
    'default_value' => '',
    'maxlength' => '',
    'placeholder' => '',
    'prepend' => '',
    'append' => '',
]));
xzpf_update_field(array_merge($base, [
    'parent' => 'field_xz_product_faq',
    'key' => 'field_xz_faq_answer',
    'label' => 'Answer',
    'name' => 'faq_answer',
    'type' => 'wysiwyg',
    'menu_order' => 1,
    'default_value' => '',
    'tabs' => 'all',
    'toolbar' => 'full',
    'media_upload' => 1,
    'delay' => 0,
]));

foreach (['field_xz_product_card_label', 'field_xz_product_tab_workflow', 'field_xz_product_configuration_workflow', 'field_xz_workflow_step_title', 'field_xz_workflow_step_description', 'field_xz_product_overview', 'field_xz_specification_parameter', 'field_xz_specification_value', 'field_xz_finished_product_image', 'field_xz_finished_product_title'] as $key) {
    $obsolete_ids = get_posts([
        'post_type' => 'acf-field',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'name' => $key,
        'fields' => 'ids',
    ]);
    foreach (array_map('intval', $obsolete_ids) as $obsolete_id) {
        xzpf_delete_field_post($obsolete_id);
    }
}

global $wpdb;
$managed_keys = array_merge(array_column($fields, 'key'), [
    'field_xz_product_parameter_label',
    'field_xz_product_parameter_value',
    'field_xz_faq_question',
    'field_xz_faq_answer',
]);
foreach ($managed_keys as $managed_key) {
    $matching_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'acf-field' AND post_name = %s ORDER BY ID ASC",
        $managed_key
    ));
    foreach (array_slice(array_map('intval', $matching_ids), 1) as $duplicate_id) {
        xzpf_delete_field_post($duplicate_id);
    }
}

foreach ($migrations as $post_id => $migration) {
    if (get_post_meta($post_id, 'product_sort_order', true) === '') {
        update_field('field_xz_product_sort_order', 0, $post_id);
    }

    [$primary, $image_id, $secondary] = $migration['overview'];
    if ($primary && !get_field('product_overview_primary', $post_id)) {
        update_field('field_xz_product_overview_primary', $primary, $post_id);
    }
    if ($image_id && !get_field('product_overview_image', $post_id)) {
        update_field('field_xz_product_overview_image', $image_id, $post_id);
    }
    if ($secondary && !get_field('product_overview_secondary', $post_id)) {
        update_field('field_xz_product_overview_secondary', $secondary, $post_id);
    }

    if (is_array($migration['specifications'])) {
        $rows = '';
        foreach ($migration['specifications'] as $row) {
            $rows .= '<tr><th scope="row">' . esc_html((string) ($row['specification_parameter'] ?? '')) . '</th><td>' . wp_kses_post((string) ($row['specification_value'] ?? '')) . '</td></tr>';
        }
        update_field('field_xz_product_specifications', '<table><tbody>' . $rows . '</tbody></table>', $post_id);
    }

    if (is_array($migration['finished'])) {
        $ids = [];
        foreach ($migration['finished'] as $item) {
            $image_id = is_array($item)
                ? absint($item['finished_product_image']['ID'] ?? $item['finished_product_image'] ?? 0)
                : absint($item);
            if (!$image_id) {
                continue;
            }
            $ids[] = $image_id;
            $title = is_array($item) ? trim((string) ($item['finished_product_title'] ?? '')) : '';
            if ($title !== '') {
                wp_update_post(['ID' => $image_id, 'post_title' => $title]);
            }
        }
        update_field('field_xz_product_finished_products', array_values(array_unique($ids)), $post_id);
    }
}

WP_CLI::success('Product fields and existing product data were migrated.');
