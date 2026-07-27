-- Price for bed-linen patterns is now edited per (image, subcategory) in the
-- Bed linen images admin page (jadmin_bed_linen_image_leafs.price), so the
-- old per-leaf info_items.price is no longer the source of truth for these
-- nodes. Zeroed out (not deleted) so the "clicks" counter and row itself
-- survive - this only touches leaves under the 3 curated categories, not
-- any other product category on the site.
UPDATE jadmin_info_items
SET price = 0
WHERE pid IN (SELECT id FROM jadmin_nav_tree WHERE node_type = 'template_category');
