-- Bed-linen tables get their own "v2_" prefix instead of "jadmin_", so the
-- new feature's tables are clearly distinguishable from the original app's
-- tables when referring back to prior-version facts/data.
RENAME TABLE jadmin_bed_linen_images TO v2_bed_linen_images;
RENAME TABLE jadmin_bed_linen_image_leafs TO v2_bed_linen_image_leafs;
