-- "Bed linen images" admin tab: images attached to a "Категория комплектов"
-- category, each linked to specific leaf nodes (exact tree nodes, not names,
-- since the same leaf name repeats under many subcategories of a category).

CREATE TABLE jadmin_bed_linen_images (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    thumb_path VARCHAR(255) NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY category_id (category_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE jadmin_bed_linen_image_leafs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    image_id INT UNSIGNED NOT NULL,
    leaf_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY image_id (image_id),
    KEY leaf_id (leaf_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
