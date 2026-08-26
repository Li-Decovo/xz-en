<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('update_field')) {
    WP_CLI::error('ACF is not available.');
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

function xz_xhj350_import_image(string $filename, string $title, string $alt): int {
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'meta_key' => '_xz_xhj350_asset',
        'meta_value' => $filename,
        'fields' => 'ids',
    ]);
    if ($existing) {
        $attachment_id = (int) $existing[0];
    } else {
        $source = __DIR__ . '/assets/xhj-350/' . $filename;
        if (!is_file($source)) {
            WP_CLI::error('Missing XHJ-350 asset: ' . $filename);
        }
        $tmp = wp_tempnam($filename);
        if (!$tmp || !copy($source, $tmp)) {
            WP_CLI::error('Unable to prepare XHJ-350 asset: ' . $filename);
        }
        $attachment_id = media_handle_sideload([
            'name' => $filename,
            'tmp_name' => $tmp,
        ], 0, $title);
        if (is_wp_error($attachment_id)) {
            @unlink($tmp);
            WP_CLI::error($attachment_id->get_error_message());
        }
        update_post_meta($attachment_id, '_xz_xhj350_asset', $filename);
    }

    wp_update_post(['ID' => $attachment_id, 'post_title' => $title]);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
    return $attachment_id;
}

$term = get_term_by('slug', 'lattice-girder', 'product_category');
if (!$term instanceof WP_Term) {
    WP_CLI::error('The Lattice Girder Welding Lines category was not found.');
}

$product = get_page_by_path('xhj-350-lattice-girder-welding-line', OBJECT, 'product');
if (!$product instanceof WP_Post) {
    $product_id = wp_insert_post([
        'post_type' => 'product',
        'post_status' => 'publish',
        'post_title' => 'XHJ-350 Lattice Girder Welding Line',
        'post_name' => 'xhj-350-lattice-girder-welding-line',
    ], true);
    if (is_wp_error($product_id)) {
        WP_CLI::error($product_id->get_error_message());
    }
} else {
    $product_id = (int) $product->ID;
}

$images = [
    'machine' => xz_xhj350_import_image('xhj-350-machine.png', 'XHJ-350 Lattice Girder Welding Line', 'XHJ-350 automated lattice girder welding line'),
    'card_finished' => xz_xhj350_import_image('xhj-350-finished-products.jpg', 'XHJ-350 Supported Lattice Girder Products', 'Lattice girder and reinforced concrete products made with the XHJ-350 line'),
    'girder_trusses' => xz_xhj350_import_image('lattice-girder-trusses.jpg', 'Lattice Girder Trusses', 'Lattice girder truss profiles'),
    'girder_profile' => xz_xhj350_import_image('lattice-girder-profile.jpg', 'Steel Bar Truss Girders', 'Steel bar truss girder profile'),
    'welded_reinforcement' => xz_xhj350_import_image('welded-lattice-reinforcement.jpg', 'Welded Lattice Reinforcement', 'Welded lattice girder reinforcement'),
    'precast_slab' => xz_xhj350_import_image('precast-slab-reinforcement.jpg', 'Precast Concrete Slab Reinforcement', 'Lattice girder reinforcement in precast concrete slabs'),
    'composite_deck' => xz_xhj350_import_image('composite-deck-slab-reinforcement.jpg', 'Composite Deck Slab Reinforcement', 'Composite deck slab reinforced with lattice girders'),
];

$short_description = '<p>An automated resistance welding line for producing lattice girders used in precast concrete slabs, floor systems and structural reinforcement.</p>';
$overview_primary = '<h3>Automated Lattice Girder Production</h3><p>The XHJ-350 connects wire feeding, forming, positioning and multi-point resistance welding in one coordinated production line. It is engineered for stable truss geometry, repeatable chord spacing and continuous production of lattice girders for precast construction.</p><p>Upper chord, lower chord and diagonal wire specifications can be configured around the required finished girder profile. Production settings are managed through the control system to support consistent output with reduced manual handling.</p>';
$overview_secondary = '<h3>Flexible Truss Specifications and Reliable Welding</h3><p>The line supports adjustable diagonal wire spacing from 190 to 210 mm and truss heights from 70 to 350 mm. Coordinated wire preparation and transformer welding help maintain dependable joint quality throughout long production runs.</p><p>Xinzhou can plan material feeding, finished product collection, factory layout, electrical configuration and auxiliary equipment according to the customer\'s drawing, target capacity and available workshop space.</p>';
$specifications = <<<'HTML'
<h3>XHJ-350 Technical Specifications</h3>
<table>
<tbody>
<tr><th scope="row">Upper Chord Diameter</th><td>8-12 mm</td></tr>
<tr><th scope="row">Lower Chord Diameter</th><td>6-12 mm</td></tr>
<tr><th scope="row">Diagonal Wire Diameter</th><td>3-8 mm</td></tr>
<tr><th scope="row">Diagonal Wire Spacing</th><td>190-210 mm (adjustable)</td></tr>
<tr><th scope="row">Truss Height</th><td>70-350 mm</td></tr>
<tr><th scope="row">Maximum Welding Speed</th><td>12 m/min</td></tr>
<tr><th scope="row">Welding Transformer Power</th><td>3 x 250 KVA</td></tr>
</tbody>
</table>
HTML;

$parameters = [
    ['product_parameter_label' => 'Upper Chord', 'product_parameter_value' => '8-12 mm'],
    ['product_parameter_label' => 'Lower Chord', 'product_parameter_value' => '6-12 mm'],
    ['product_parameter_label' => 'Diagonal Wire', 'product_parameter_value' => '3-8 mm'],
    ['product_parameter_label' => 'Diagonal Spacing', 'product_parameter_value' => '190-210 mm'],
    ['product_parameter_label' => 'Truss Height', 'product_parameter_value' => '70-350 mm'],
    ['product_parameter_label' => 'Welding Speed', 'product_parameter_value' => '12 m/min'],
    ['product_parameter_label' => 'Transformer Power', 'product_parameter_value' => '3 x 250 KVA'],
];

$faq = [
    ['faq_question' => 'Which lattice girder sizes can the XHJ-350 produce?', 'faq_answer' => '<p>The line supports upper chord diameters of 8-12 mm, lower chord diameters of 6-12 mm, diagonal wire diameters of 3-8 mm and truss heights from 70 to 350 mm.</p>'],
    ['faq_question' => 'Can diagonal wire spacing be adjusted?', 'faq_answer' => '<p>Yes. The standard adjustable range shown for this model is 190-210 mm. Final settings are confirmed from the customer\'s lattice girder drawing.</p>'],
    ['faq_question' => 'Can the production line layout be customized?', 'faq_answer' => '<p>Yes. Xinzhou can plan feeding direction, line length, product collection, electrical configuration and auxiliary equipment around the factory layout and target output.</p>'],
    ['faq_question' => 'Do you provide installation and training?', 'faq_answer' => '<p>Xinzhou can provide installation guidance, commissioning, production testing, operator training and long-term technical support.</p>'],
];

wp_update_post(wp_slash([
    'ID' => $product_id,
    'post_status' => 'publish',
    'post_title' => 'XHJ-350 Lattice Girder Welding Line',
    'post_excerpt' => wp_strip_all_tags($short_description),
    'post_content' => $overview_primary,
]));

wp_set_object_terms($product_id, [(int) $term->term_id], 'product_category', false);
set_post_thumbnail($product_id, $images['machine']);
update_field('field_xz_product_sort_order', 95, $product_id);
update_field('field_xz_product_short_description', $short_description, $product_id);
update_field('field_xz_product_gallery', [$images['machine'], $images['card_finished']], $product_id);
update_field('field_xz_product_card_finished_image', $images['card_finished'], $product_id);
update_field('field_xz_product_key_parameters', $parameters, $product_id);
update_field('field_xz_product_overview_primary', $overview_primary, $product_id);
update_field('field_xz_product_overview_image', $images['machine'], $product_id);
update_field('field_xz_product_overview_secondary', $overview_secondary, $product_id);
update_field('field_xz_product_specifications', $specifications, $product_id);
update_field('field_xz_product_finished_products', [
    $images['girder_trusses'],
    $images['girder_profile'],
    $images['welded_reinforcement'],
    $images['precast_slab'],
    $images['composite_deck'],
], $product_id);
update_field('field_xz_product_faq', $faq, $product_id);

$related = get_posts([
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 3,
    'post__not_in' => [$product_id],
    'tax_query' => [[
        'taxonomy' => 'product_category',
        'field' => 'term_id',
        'terms' => [(int) $term->term_id],
    ]],
    'fields' => 'ids',
]);
update_field('field_xz_related_products', array_map('intval', $related), $product_id);

update_post_meta($product_id, 'rank_math_primary_product_category', (int) $term->term_id);
update_post_meta($product_id, 'rank_math_title', 'XHJ-350 Lattice Girder Welding Line | Xinzhou');
update_post_meta($product_id, 'rank_math_description', 'XHJ-350 automated lattice girder welding line for precast concrete and structural reinforcement, with adjustable truss height and wire spacing.');
update_post_meta($product_id, 'rank_math_focus_keyword', 'lattice girder welding line');

clean_post_cache($product_id);
WP_CLI::success('XHJ-350 product published with ID ' . $product_id . '.');
