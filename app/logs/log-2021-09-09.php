<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-09-09 11:15:02 --> Severity: Warning --> Undefined variable $error /Users/saleem/Sites/sma/themes/default/admin/views/sales/email.php 25
ERROR - 2021-09-09 11:18:19 --> Severity: Warning --> Attempt to read property "id" on bool /Users/saleem/Sites/sma/app/controllers/admin/Suppliers.php 186
ERROR - 2021-09-09 11:18:19 --> Severity: Warning --> Attempt to read property "company" on bool /Users/saleem/Sites/sma/app/controllers/admin/Suppliers.php 186
ERROR - 2021-09-09 11:42:10 --> Query error: Unknown column 'sma_warehouses_products.waewhouse_id' in 'where clause' - Invalid query: SELECT `sma_products`.*, `sma_warehouses_products`.`quantity` as `quantity`
FROM `sma_products`
LEFT JOIN `sma_warehouses_products` ON `sma_warehouses_products`.`product_id`=`sma_products`.`id`
WHERE `sma_warehouses_products`.`product_id` = '33'
AND `sma_warehouses_products`.`waewhouse_id` = '1'
