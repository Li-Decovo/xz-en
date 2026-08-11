<?php

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run this file with wp eval-file.\n");
    exit(1);
}

$header_id = 13;
$source = false;
$match = [];
foreach ([__DIR__ . '/apply-native-pages.php', '/tmp/xinzhou-apply-native-pages.php'] as $source_path) {
    if (is_readable($source_path)) {
        $candidate = file_get_contents($source_path);
        if ($candidate !== false && preg_match('/\/\* XZ HEADER GRID FIX START \*\/.*?\/\* XZ HEADER GRID FIX END \*\//s', $candidate, $candidate_match)) {
            $source = $candidate;
            $match = $candidate_match;
            break;
        }
    }
}

if ($source === false || empty($match[0])) {
    fwrite(STDERR, "Unable to find the global header CSS block.\n");
    exit(1);
}

$upload = wp_upload_dir();
$backup_dir = trailingslashit($upload['basedir']) . 'xinzhou-backups';
wp_mkdir_p($backup_dir);
$backup_path = trailingslashit($backup_dir) . 'elementor-header-' . gmdate('Ymd-His') . '.json';
$backup = [
    'post_id' => $header_id,
    'elementor_data' => get_post_meta($header_id, '_elementor_data', true),
    'elementor_page_settings' => get_post_meta($header_id, '_elementor_page_settings', true),
    'nav_menus' => array_map(static function (WP_Term $menu): array {
        return [
            'menu' => $menu,
            'items' => wp_get_nav_menu_items($menu->term_id, ['post_status' => 'any']),
        ];
    }, wp_get_nav_menus()),
];
file_put_contents($backup_path, wp_json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

foreach (wp_get_nav_menus() as $menu) {
    $items = wp_get_nav_menu_items($menu->term_id, ['post_status' => 'any']);
    $products_item = null;
    foreach ((array) $items as $item) {
        $classes = array_filter((array) $item->classes);
        if (in_array('xz-mega-menu', $classes, true)) {
            $products_item = $item;
            break;
        }
    }
    if (!$products_item) {
        continue;
    }

    $contact_items = array_values(array_filter((array) $items, static function ($item) use ($products_item): bool {
        if ((int) $item->menu_item_parent !== (int) $products_item->ID) {
            return false;
        }
        $classes = array_filter((array) $item->classes);
        return in_array('xz-mega-menu-contact', $classes, true)
            || strcasecmp(trim((string) $item->title), 'Discuss Your Project') === 0;
    }));

    $contact_item = null;
    foreach ($contact_items as $candidate) {
        $contact_item = $candidate;
        if (in_array('xz-mega-menu-contact', array_filter((array) $candidate->classes), true)) {
            break;
        }
    }

    $contact_id = wp_update_nav_menu_item($menu->term_id, $contact_item ? $contact_item->ID : 0, [
        'menu-item-title' => 'Discuss Your Project',
        'menu-item-description' => 'Share your product, output and factory requirements with Xinzhou.',
        'menu-item-url' => home_url('/contact/'),
        'menu-item-parent-id' => $products_item->ID,
        'menu-item-position' => 99,
        'menu-item-type' => 'custom',
        'menu-item-classes' => 'xz-mega-menu-contact',
        'menu-item-status' => 'publish',
    ]);
    if (!is_wp_error($contact_id)) {
        foreach ($contact_items as $duplicate) {
            if ((int) $duplicate->ID !== (int) $contact_id) {
                wp_delete_post($duplicate->ID, true);
            }
        }
    }
    break;
}

$settings = (array) get_post_meta($header_id, '_elementor_page_settings', true);
$custom_css = preg_replace(
    '/\/\* XZ HEADER GRID FIX START \*\/.*?\/\* XZ HEADER GRID FIX END \*\//s',
    '',
    (string) ($settings['custom_css'] ?? '')
);
$settings['custom_css'] = rtrim((string) $custom_css) . "\n\n" . $match[0];
update_post_meta($header_id, '_elementor_page_settings', $settings);

delete_post_meta($header_id, '_elementor_element_cache');
delete_post_meta($header_id, '_elementor_page_assets');
delete_post_meta($header_id, '_elementor_css');

if (class_exists('Elementor\\Plugin')) {
    Elementor\Plugin::$instance->files_manager->clear_cache();
}

echo "Updated global header responsive CSS.\n";
echo "Backup: {$backup_path}\n";
