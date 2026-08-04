# Xinzhou WordPress Layer

The server-side WordPress implementation keeps content registration in ACF PRO's database-managed UI:

- ACF Post Type: `Products` (`product`)
- ACF Taxonomy: `Product Categories` (`product_category`)
- ACF Field Groups: `Product Details`, `Product Category Details`, `Article Details`
- Elementor templates: `Xinzhou Product Archive`, `Xinzhou Product Single`, `Xinzhou News Archive`, `Xinzhou News Single`

The must-use plugin in this directory only renders ACF-managed product data and article navigation. It does not register the post type, taxonomy, or fields.

Content editors maintain products and categories in the WordPress admin. Template structure remains editable in Elementor Theme Builder, and menu links remain editable under Appearance > Menus.
