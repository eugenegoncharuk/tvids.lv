-- Each checked subcategory for a bed-linen image now carries its own price
-- (different sizes of the same pattern legitimately cost different amounts),
-- editable directly in the Bed linen images admin page instead of only via
-- the separate per-item price form.
ALTER TABLE jadmin_bed_linen_image_leafs
    ADD COLUMN price DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00 AFTER leaf_id;

-- Backfill existing rows from the price the front-end was already showing
-- (info_items, keyed by the actual leaf tree node for that image's pattern
-- name under that specific subcategory), so nothing changes visually until
-- an admin edits a price going forward.
UPDATE jadmin_bed_linen_image_leafs bll
JOIN jadmin_bed_linen_images bli ON bli.id = bll.image_id
JOIN jadmin_nav_tree leaf ON (leaf.pid = bll.leaf_id AND leaf.name = bli.name)
JOIN jadmin_info_items ii ON ii.pid = leaf.id
SET bll.price = ii.price;
