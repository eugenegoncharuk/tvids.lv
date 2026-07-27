-- Renames every remaining "jadmin_" table to "v2_", so the whole schema uses
-- one consistent prefix. Paired with system/application/config/database.php
-- changing $db['default']['dbprefix'] to "v2_" (and removing the separate
-- $db['v2'] connection group that used to exist only for the bed-linen
-- tables, since it's now redundant), plus every hardcoded "jadmin_" table
-- name in raw SQL across the app being updated to "v2_" in the same commit.
RENAME TABLE jadmin_admin_users TO v2_admin_users;
RENAME TABLE jadmin_ci_sessions TO v2_ci_sessions;
RENAME TABLE jadmin_content TO v2_content;
RENAME TABLE jadmin_info_items TO v2_info_items;
RENAME TABLE jadmin_info_users TO v2_info_users;
RENAME TABLE jadmin_nav_hier TO v2_nav_hier;
RENAME TABLE jadmin_nav_tree TO v2_nav_tree;
RENAME TABLE jadmin_requests TO v2_requests;
RENAME TABLE jadmin_requests_items TO v2_requests_items;
RENAME TABLE jadmin_users TO v2_users;
