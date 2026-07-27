-- Bed linen images admin table redesign: each image now has its own name,
-- and is associated with a category's *direct child* subcategories
-- (e.g. "Под.145x210,пр.140x210,2 нав.") rather than deep leaf items.
-- jadmin_bed_linen_image_leafs.leaf_id now stores those subcategory node ids;
-- no data migration needed since that table was still empty.
ALTER TABLE jadmin_bed_linen_images ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT '' AFTER category_id;
