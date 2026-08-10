# Xinzhou WordPress Layer

The WordPress build uses native content objects, ACF-managed fields, and Elementor Theme Builder. Large HTML widgets are not used for page layouts.

## Content Model

- ACF Post Type: `Products` (`product`)
- ACF Taxonomy: `Product Categories` (`product_category`)
- ACF Field Group: `Product Details`
- ACF Field Group: `Product Category Details`
- ACF Field Group: `Article Details`
- Native WordPress Posts provide News, Exhibitions, Customer Exchange, Product Showcase, Company News, and Cases.
- Native WordPress Categories provide article and case archives.

The post type, taxonomy, and field groups are registered through ACF's admin UI. Deployment utilities may idempotently add a missing field to an existing ACF field group, but the must-use plugin does not hide or replace ACF objects.

## Elementor Templates

- `193`: Xinzhou Product Archive
- `195`: Xinzhou Product Single
- `197`: Xinzhou News Archive
- `199`: Xinzhou News Single
- `13`: Global Header - Xinzhou
- `32`: Global Footer - Xinzhou

Product archive and single templates use dedicated Xinzhou section widgets with native WordPress queries and pagination. Article templates use native and Xinzhou widgets for structured content, related content, article contents, and Fluent Forms.

## Xinzhou Widgets

The following widgets appear in the normal Elementor widget panel:

- Xinzhou Product Categories
- Xinzhou Product Category Content
- Xinzhou News Categories
- Xinzhou Breadcrumbs
- Xinzhou Product Gallery
- Xinzhou Product Summary Data
- Xinzhou Product Information
- Xinzhou Product Archive Grid
- Xinzhou Product Worldwide
- Xinzhou Product Detail Hero
- Xinzhou Product Information Tabs
- Xinzhou Related Products
- Xinzhou News Hero
- Xinzhou Featured News
- Xinzhou News Archive Grid
- Xinzhou Article Meta
- Xinzhou Article Contents

These widgets read WordPress and ACF data. Editors change content in the relevant Post, Product, or Product Category screen and change layout or labels in Elementor.

## Publishing

To publish a product:

1. Open Products > Add New.
2. Add the product name, excerpt, main description, featured image, and Product Category.
3. Complete the shared Product Details fields as required.
4. Publish. Product archives, category archives, related products, pagination, and the sitemap update automatically.

To publish an article or case:

1. Open Posts > Add New.
2. Add the title, excerpt, featured image, article body, and Category.
3. Use `h2` headings for article sections so the server-rendered Contents widget can link to them.
4. Complete Article Details if a location or cover caption is needed.
5. Publish. News archives, category archives, related news, pagination, and the sitemap update automatically.

## Deployment Tools

The scripts in `wordpress/tools` are idempotent deployment utilities for the current server documents. They are not routine content-entry tools.

- `apply-maintainable-templates.php` updates the four Theme Builder archive/single templates and ensures the small set of required ACF display fields exists once in the existing field groups.
- `apply-native-pages.php` updates Home, About, Services, Contact form widgets, and the global pre-footer.

After deploying plugin or Elementor document changes, run:

```bash
wp elementor flush-css
wp litespeed-purge all
wp cache flush
```
