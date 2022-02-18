<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-08-19 10:19:55 --> Severity: Warning --> Undefined variable $data /Users/saleem/Sites/sma/app/models/admin/Pos_model.php 95
ERROR - 2021-08-19 10:19:55 --> Severity: Warning --> Trying to access array offset on value of type null /Users/saleem/Sites/sma/app/models/admin/Pos_model.php 95
ERROR - 2021-08-19 10:27:26 --> Query error: Table 'sma.sma_payment' doesn't exist - Invalid query: SELECT SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid
FROM `sma_sales`
LEFT JOIN `sma_payment` ON `sma_sales`.`id`=`sma_payments`.`sale_id`
WHERE `type` = 'received'
AND `sma_sales`.`date` >= '2021-08-19 00:00:00'
AND `sma_payments`.`date` <= '2021-08-19 23:59:59'
GROUP BY `sma_payments`.`sale_id`
ERROR - 2021-08-19 10:29:34 --> Query error: Table 'sma.sma_payment' doesn't exist - Invalid query: SELECT SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid
FROM `sma_sales`
LEFT JOIN `sma_payment` ON `sma_sales`.`id`=`sma_payments`.`sale_id`
WHERE `type` = 'received'
AND `sma_sales`.`date` >= '2021-08-19 00:00:00'
AND `sma_payments`.`date` <= '2021-08-19 23:59:59'
GROUP BY `sma_payments`.`sale_id`
ERROR - 2021-08-19 10:38:58 --> Severity: error --> Exception: Object of class CI_DB_mysqli_result could not be converted to string /Users/saleem/Sites/sma/app/models/admin/Pos_model.php 1134
ERROR - 2021-08-19 10:42:36 --> Query error: Unknown column 'grand_total' in 'field list' - Invalid query: SELECT COALESCE(grand_total, 0) AS total, SUM(COALESCE(amount, 0)) AS paid
FROM (SELECT COALESCE( grand_total, 0 ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid
FROM `sma_sales`
LEFT JOIN `sma_payments` ON `sma_sales`.`id`=`sma_payments`.`sale_id`
WHERE `type` = 'received'
AND `sma_sales`.`date` >= '2021-08-19 00:00:00'
AND `sma_payments`.`date` <= '2021-08-19 23:59:59'
GROUP BY `sma_sales`.`id`) sp
ERROR - 2021-08-19 10:43:41 --> Query error: Unknown column 'sp.total' in 'field list' - Invalid query: SELECT *
FROM (SELECT COALESCE( grand_total, 0 ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid, SUM(sp.total) as total, SUM(sp.paid) as paid
FROM `sma_sales`
LEFT JOIN `sma_payments` ON `sma_sales`.`id`=`sma_payments`.`sale_id`
WHERE `type` = 'received'
AND `sma_sales`.`date` >= '2021-08-19 00:00:00'
AND `sma_payments`.`date` <= '2021-08-19 23:59:59'
GROUP BY `sma_sales`.`id`) sp
ERROR - 2021-08-19 10:51:30 --> Severity: Warning --> Undefined variable $data /Users/saleem/Sites/sma/app/models/admin/Pos_model.php 95
ERROR - 2021-08-19 10:51:30 --> Severity: Warning --> Trying to access array offset on value of type null /Users/saleem/Sites/sma/app/models/admin/Pos_model.php 95
