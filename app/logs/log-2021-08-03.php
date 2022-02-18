<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-08-03 16:59:27 --> Severity: Warning --> Undefined property: stdClass::$category_slug /Users/saleem/Sites/sma/themes/default/shop/views/blog/show.php 19
ERROR - 2021-08-03 16:59:27 --> Severity: Warning --> Undefined property: stdClass::$category_name /Users/saleem/Sites/sma/themes/default/shop/views/blog/show.php 19
ERROR - 2021-08-03 16:59:27 --> Severity: Warning --> Undefined property: stdClass::$subcategory_slug /Users/saleem/Sites/sma/themes/default/shop/views/blog/show.php 20
ERROR - 2021-08-03 17:00:56 --> Query error: Column 'slug' in where clause is ambiguous - Invalid query: SELECT sma_blog.*, c.slug as category_slug, sc.slug as subcategory_slug, c.name as category_name, sc.name as subcategory_name
FROM `sma_blog`
LEFT JOIN `sma_blog_categories` `c` ON `sma_blog`.`blog_category_id`=`c`.`id`
LEFT JOIN `sma_blog_categories` `sc` ON `sma_blog`.`blog_subcategory_id`=`sc`.`id`
WHERE `slug` = 'test-blog-1'
