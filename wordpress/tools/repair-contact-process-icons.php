<?php

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run this file with wp eval-file.\n");
    exit(1);
}

$post_id = 24;
$data = json_decode((string) get_post_meta($post_id, '_elementor_data', true), true);
if (!is_array($data)) {
    fwrite(STDERR, "Contact page Elementor data is invalid.\n");
    exit(1);
}

$icons = [
    ['value' => 'far fa-file-alt', 'library' => 'fa-regular'],
    ['value' => 'fas fa-sitemap', 'library' => 'fa-solid'],
    ['value' => 'far fa-comments', 'library' => 'fa-regular'],
];
$updated = 0;

$repair = static function (array $elements) use (&$repair, &$updated, $icons): array {
    foreach ($elements as &$element) {
        if (($element['widgetType'] ?? '') === 'xinzhou-contact-process') {
            $steps = (array) ($element['settings']['steps'] ?? []);
            foreach ($steps as $index => &$step) {
                if (isset($icons[$index])) {
                    $step['icon'] = $icons[$index];
                }
            }
            unset($step);
            $element['settings']['steps'] = $steps;
            $updated++;
        }
        if (!empty($element['elements']) && is_array($element['elements'])) {
            $element['elements'] = $repair($element['elements']);
        }
    }
    unset($element);
    return $elements;
};

$data = $repair($data);
if (!$updated) {
    fwrite(STDERR, "Contact process widget was not found.\n");
    exit(1);
}

$upload = wp_upload_dir();
$backup_dir = trailingslashit($upload['basedir']) . 'xinzhou-backups';
wp_mkdir_p($backup_dir);
$backup_path = trailingslashit($backup_dir) . 'contact-process-icons-' . gmdate('Ymd-His') . '.json';
file_put_contents($backup_path, (string) get_post_meta($post_id, '_elementor_data', true));

update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($data)));
delete_post_meta($post_id, '_elementor_element_cache');
delete_post_meta($post_id, '_elementor_page_assets');
delete_post_meta($post_id, '_elementor_css');

echo "Updated {$updated} contact process widget.\n";
echo "Backup: {$backup_path}\n";
