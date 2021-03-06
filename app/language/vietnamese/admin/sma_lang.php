<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Module: General Language File for common lang keys
 * Language: English
 *
 * Last edited:
 * 30th April 2015
 *
 * Package:
 * Stock Manage Advance v3.0
 *
 * You can translate this file to your language.
 * For instruction on new language setup, please visit the documentations.
 * You also can share your language files by emailing to saleem@tecdiary.com
 * Thank you
 */

/* --------------------- CUSTOM FIELDS ------------------------ */
/*
* Below are custome field labels
* Please only change the part after = and make sure you change the the words in between "";
* $lang['bcf1']                         = "Biller Custom Field 1";
* Don't change this                     = "You can change this part";
* For support email contact@tecdiary.com Thank you!
*/

$lang['bcf1'] = 'Biller Custom Field 1';
$lang['bcf2'] = 'Biller Custom Field 2';
$lang['bcf3'] = 'Biller Custom Field 3';
$lang['bcf4'] = 'Biller Custom Field 4';
$lang['bcf5'] = 'Biller Custom Field 5';
$lang['bcf6'] = 'Biller Custom Field 6';
$lang['pcf1'] = 'Product Custom Field 1';
$lang['pcf2'] = 'Product Custom Field 2';
$lang['pcf3'] = 'Product Custom Field 3';
$lang['pcf4'] = 'Product Custom Field 4';
$lang['pcf5'] = 'Product Custom Field 5';
$lang['pcf6'] = 'Product Custom Field 6';
$lang['ccf1'] = 'Customer Custom Field 1';
$lang['ccf2'] = 'Customer Custom Field 2';
$lang['ccf3'] = 'Customer Custom Field 3';
$lang['ccf4'] = 'Customer Custom Field 4';
$lang['ccf5'] = 'Customer Custom Field 5';
$lang['ccf6'] = 'Customer Custom Field 6';
$lang['scf1'] = 'Supplier Custom Field 1';
$lang['scf2'] = 'Supplier Custom Field 2';
$lang['scf3'] = 'Supplier Custom Field 3';
$lang['scf4'] = 'Supplier Custom Field 4';
$lang['scf5'] = 'Supplier Custom Field 5';
$lang['scf6'] = 'Supplier Custom Field 6';

/* ----------------- DATATABLES LANGUAGE ---------------------- */
/*
* Below are datatables language entries
* Please only change the part after = and make sure you change the the words in between "";
* 'sEmptyTable'                     => "No data available in table",
* Don't change this                 => "You can change this part but not the word between and ending with _ like _START_;
* For support email support@tecdiary.com Thank you!
*/

$lang['datatables_lang'] = [
    'sEmptyTable'     => 'Kh??ng c?? d??? li???u trong b???ng',
    'sInfo'           => 'Showing _START_ to _END_ of _TOTAL_ entries',
    'sInfoEmpty'      => 'Showing 0 to 0 of 0 entries',
    'sInfoFiltered'   => '(filtered from _MAX_ total entries)',
    'sInfoPostFix'    => '',
    'sInfoThousands'  => ',',
    'sLengthMenu'     => 'Hi???n _MENU_ ',
    'sLoadingRecords' => '??ang t???i...',
    'sProcessing'     => '??ang x??? l??...',
    'sSearch'         => 'T??m ki???m',
    'sZeroRecords'    => 'No matching records found',
    'oAria'           => [
        'sSortAscending'  => ': activate to sort column ascending',
        'sSortDescending' => ': activate to sort column descending',
    ],
    'oPaginate' => [
        'sFirst'    => '<< First',
        'sLast'     => 'Last >>',
        'sNext'     => 'Next >',
        'sPrevious' => '< Previous',
    ],
];

/* ----------------- Select2 LANGUAGE ---------------------- */
/*
* Below are select2 lib language entries
* Please only change the part after = and make sure you change the the words in between "";
* 's2_errorLoading'                 => "The results could not be loaded",
* Don't change this                 => "You can change this part but not the word between {} like {t};
* For support email support@tecdiary.com Thank you!
*/

$lang['select2_lang'] = [
    'formatMatches_s'         => 'One result is available, press enter to select it.',
    'formatMatches_p'         => 'results are available, use up and down arrow keys to navigate.',
    'formatNoMatches'         => 'No matches found',
    'formatInputTooShort'     => 'H??y g?? {n} ho???c nhi???u k?? t??? g???i ??',
    'formatInputTooLong_s'    => 'H??y x??a {n} k?? t???',
    'formatInputTooLong_p'    => 'Please delete {n} characters',
    'formatSelectionTooBig_s' => 'You can only select {n} item',
    'formatSelectionTooBig_p' => 'You can only select {n} items',
    'formatLoadMore'          => 'Loading more results...',
    'formatAjaxError'         => 'Ajax request failed',
    'formatSearching'         => '??ang t??m...',
];

/* ----------------- SMA GENERAL LANGUAGE KEYS -------------------- */

$lang['home']                                      = 'Trang ch???';
$lang['dashboard']                                 = 'B???ng ??i???u khi???n';
$lang['username']                                  = 'T??i kho???n';
$lang['password']                                  = 'M???t kh???u';
$lang['first_name']                                = 'H???';
$lang['last_name']                                 = 'T??n';
$lang['confirm_password']                          = 'X??c nh???n m???t kh???u';
$lang['email']                                     = 'Email';
$lang['phone']                                     = '??i???n tho???i';
$lang['company']                                   = 'C??ng ty';
$lang['product_code']                              = 'M?? s???n ph???m';
$lang['product_name']                              = 'T??n s???n ph???m';
$lang['cname']                                     = 'T??n kh??ch h??ng';
$lang['barcode_symbology']                         = 'K?? t??? m?? v???ch';
$lang['product_unit']                              = '????n v??? SP';
$lang['product_price']                             = 'Gi?? b??n';
$lang['contact_person']                            = 'Ng?????i li??n h???';
$lang['email_address']                             = '?????a ch??? Email';
$lang['address']                                   = '?????a ch???';
$lang['city']                                      = 'T???nh/TP';
$lang['today']                                     = 'H??m nay';
$lang['welcome']                                   = 'Ch??o m???ng';
$lang['profile']                                   = 'H??? s??';
$lang['change_password']                           = '?????i m???t kh???u';
$lang['logout']                                    = 'Tho??t';
$lang['notifications']                             = 'Th??ng b??o';
$lang['calendar']                                  = 'L???ch';
$lang['messages']                                  = 'Tin nh???n';
$lang['styles']                                    = 'Giao di???n';
$lang['language']                                  = 'Ng??n ng???';
$lang['alerts']                                    = 'C???nh b??o';
$lang['list_products']                             = 'DS s???n ph???m';
$lang['add_product']                               = 'Th??m s???n ph???m';
$lang['print_barcodes']                            = 'In M?? v???ch';
$lang['print_labels']                              = 'In Nh??n';
$lang['import_products']                           = 'Import s???n ph???m';
$lang['update_price']                              = 'C???p nh???t gi??';
$lang['damage_products']                           = 'S???n ph???m h?? h???ng';
$lang['sales']                                     = 'B??n h??ng';
$lang['list_sales']                                = 'DS ????n h??ng';
$lang['add_sale']                                  = 'Th??m ????n h??ng';
$lang['deliveries']                                = 'Giao h??ng';
$lang['gift_cards']                                = 'Gift Cards';
$lang['quotes']                                    = 'B??o gi??';
$lang['list_quotes']                               = 'DS b??o gi??';
$lang['add_quote']                                 = 'Th??m b??o gi??';
$lang['purchases']                                 = 'Nh???p h??ng';
$lang['list_purchases']                            = 'DS nh???p h??ng';
$lang['add_purchase']                              = 'Th??m nh???p h??ng';
$lang['add_purchase_by_csv']                       = 'Th??m b???ng CSV';
$lang['transfers']                                 = 'Chuy???n kho';
$lang['list_transfers']                            = 'DS chuy???n kho';
$lang['add_transfer']                              = 'Th??m chuy???n kho';
$lang['add_transfer_by_csv']                       = 'Th??m b???ng CSV';
$lang['people']                                    = 'Ng?????i d??ng';
$lang['list_users']                                = 'DS ng?????i d??ng';
$lang['new_user']                                  = 'Th??m ng?????i d??ng';
$lang['list_billers']                              = 'DS NV b??n h??ng';
$lang['add_biller']                                = 'Th??m NV b??n h??ng';
$lang['list_customers']                            = 'DS kh??ch h??ng';
$lang['add_customer']                              = 'Th??m kh??ch h??ng';
$lang['list_suppliers']                            = 'DS nh?? cung c???p';
$lang['add_supplier']                              = 'Th??m nh?? cung c???p';
$lang['settings']                                  = 'C??i ?????t';
$lang['system_settings']                           = 'C??i ?????t h??? th???ng';
$lang['change_logo']                               = '?????i Logo';
$lang['currencies']                                = 'Ti???n t???';
$lang['attributes']                                = 'Bi???n th??? s???n ph???m';
$lang['customer_groups']                           = 'Nh??m kh??ch h??ng';
$lang['categories']                                = 'Danh m???c SP';
$lang['subcategories']                             = 'Danh m???c con';
$lang['tax_rates']                                 = 'Thu??? su???t';
$lang['warehouses']                                = 'Kho h??ng';
$lang['email_templates']                           = 'M???u Email';
$lang['group_permissions']                         = 'Ph??n quy???n nh??m';
$lang['backup_database']                           = 'Sao l??u d??? li???u';
$lang['reports']                                   = 'B??o c??o';
$lang['overview_chart']                            = 'T???ng quan chung';
$lang['warehouse_stock']                           = 'Bi???u ????? t???n kho';
$lang['product_quantity_alerts']                   = 'C???nh b??o s??? l?????ng SP';
$lang['product_expiry_alerts']                     = 'C???nh b??o SP h???t h???n';
$lang['products_report']                           = 'B??o c??o s???n ph???m';
$lang['daily_sales']                               = 'Doanh s??? theo ng??y';
$lang['monthly_sales']                             = 'Doanh s??? theo th??ng';
$lang['sales_report']                              = 'B??o c??o doanh s???';
$lang['payments_report']                           = 'B??o c??o thanh to??n';
$lang['profit_and_loss']                           = 'L???i nhu???n v?? chi ph??';
$lang['purchases_report']                          = 'B??o c??o nh???p h??ng';
$lang['customers_report']                          = 'Th???ng k?? KH';
$lang['suppliers_report']                          = 'Th???ng k?? nh?? cung c???p';
$lang['staff_report']                              = 'Th???ng k?? NV';
$lang['your_ip']                                   = '?????a ch??? IP c???a b???n';
$lang['last_login_at']                             = '????ng nh???p g???n ????y';
$lang['notification_post_at']                      = 'Th??ng b??o ????ng t???i';
$lang['quick_links']                               = 'Li??n k???t nhanh';
$lang['date']                                      = 'Ng??y';
$lang['reference_no']                              = 'S??? tham chi???u';
$lang['products']                                  = 'S???n ph???m';
$lang['customers']                                 = 'Kh??ch h??ng';
$lang['suppliers']                                 = 'Nh?? cung c???p';
$lang['users']                                     = 'Ng?????i d??ng';
$lang['latest_five']                               = '5 d??? li???u m???i nh???t';
$lang['total']                                     = 'T???ng';
$lang['payment_status']                            = 'Tr???ng th??i thanh to??n';
$lang['paid']                                      = '???? thanh to??n';
$lang['customer']                                  = 'Kh??ch h??ng';
$lang['status']                                    = 'Tr???ng th??i';
$lang['amount']                                    = 'S??? l?????ng';
$lang['supplier']                                  = 'Nh?? cung c???p';
$lang['from']                                      = 'T???';
$lang['to']                                        = 'T???i';
$lang['name']                                      = 'T??n';
$lang['create_user']                               = 'Th??m ng?????i d??ng';
$lang['gender']                                    = 'Gi???i t??nh';
$lang['biller']                                    = 'Ng?????i b??n h??ng';
$lang['select']                                    = 'Ch???n';
$lang['warehouse']                                 = 'Kho h??ng';
$lang['active']                                    = 'K??ch ho???t';
$lang['inactive']                                  = 'H???y k??ch ho???t';
$lang['all']                                       = 'T???t c???';
$lang['list_results']                              = 'Vui l??ng s??? d???ng b???ng d?????i ????y ????? ??i???u h?????ng ho???c l???c c??c k???t qu???. B???n c?? th??? t???i v??? b???ng nh?? excel v?? pdf.';
$lang['actions']                                   = 'T??c v???';
$lang['pos']                                       = 'POS';
$lang['access_denied']                             = 'T??? ch???i truy c???p! B???n kh??ng c?? quy???n truy c???p v??o c??c trang y??u c???u. N???u b???n ngh?? r???ng, ???? l?? do nh???m l???n, xin vui l??ng li??n h??? qu???n tr???.';
$lang['add']                                       = 'Th??m';
$lang['edit']                                      = 'S???a';
$lang['delete']                                    = 'X??a';
$lang['view']                                      = 'Xem';
$lang['update']                                    = 'C???p nh???t';
$lang['save']                                      = 'Save';
$lang['login']                                     = '????ng nh???p';
$lang['submit']                                    = 'G???i';
$lang['no']                                        = 'Kh??ng';
$lang['yes']                                       = 'C??';
$lang['disable']                                   = 'T???t';
$lang['enable']                                    = 'M???';
$lang['enter_info']                                = 'Vui l??ng ??i???n v??o c??c th??ng tin d?????i ????y. C??c m???c ????nh d???u * l?? c??c m???c b???t bu???c ph???i nh???p v??o.';
$lang['update_info']                               = 'Vui l??ng c???p nh???t th??ng tin d?????i ????y. C??c m???c ????nh d???u * l?? c??c m???c b???t bu???c ph???i nh???p v??o.';
$lang['no_suggestions']                            = 'Kh??ng th??? t???i c??c d??? li???u cho c??c ????? xu???t, h??y ki???m tra ?????u v??o c???a b???n';
$lang['i_m_sure']                                  = 'V??ng t??i ch???c ch???n';
$lang['r_u_sure']                                  = 'B???n c?? ch???c kh??ng?';
$lang['export_to_excel']                           = 'Xu???t ra file Excel';
$lang['export_to_pdf']                             = 'Xu???t ra file PDF';
$lang['image']                                     = '???nh';
$lang['sale']                                      = 'B??n';
$lang['quote']                                     = 'B???ng b??o gi??';
$lang['purchase']                                  = 'Mua';
$lang['transfer']                                  = 'Chuy???n kho';
$lang['payment']                                   = 'Thanh to??n';
$lang['payments']                                  = 'C??c kho???n thanh to??n';
$lang['orders']                                    = '????n ?????t h??ng';
$lang['pdf']                                       = 'PDF';
$lang['vat_no']                                    = 'S??? VAT';
$lang['country']                                   = 'Qu???c gia';
$lang['add_user']                                  = 'Th??m ng?????i d??ng';
$lang['type']                                      = 'Lo???i';
$lang['person']                                    = 'Person';
$lang['state']                                     = 'Huy???n';
$lang['postal_code']                               = 'M?? b??u ch??nh';
$lang['id']                                        = 'ID';
$lang['close']                                     = '????ng';
$lang['male']                                      = 'Nam';
$lang['female']                                    = 'N???';
$lang['notify_user']                               = 'Th??ng b??o t???i th??nh vi??n';
$lang['notify_user_by_email']                      = 'Th??ng t???i t???i th??nh vi??n b???ng email';
$lang['billers']                                   = 'Ng?????i b??n';
$lang['all_warehouses']                            = 'T???t c??? kho h??ng';
$lang['category']                                  = 'Danh m???c';
$lang['product_cost']                              = 'Gi?? nh???p';
$lang['quantity']                                  = 'S??? l?????ng';
$lang['loading_data_from_server']                  = '??ang t???i d??? li???u t??? m??y ch???';
$lang['excel']                                     = 'Excel';
$lang['print']                                     = 'In';
$lang['ajax_error']                                = 'Ajax l???i x???y ra, h??y th??? l???i.';
$lang['product_tax']                               = 'Thu??? s???n ph???m';
$lang['order_tax']                                 = 'Thu??? mua h??ng';
$lang['upload_file']                               = 'Upload File';
$lang['download_sample_file']                      = 'T???i file m???u';
$lang['csv1']                                      = 'The first line in downloaded csv file should remain as it is. Please do not change the order of columns.';
$lang['csv2']                                      = 'The correct column order is';
$lang['csv3']                                      = '&amp; you must follow this. If you are using any other language then English, please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM)';
$lang['import']                                    = 'Nh???p kh???u';
$lang['note']                                      = 'Ghi ch??';
$lang['grand_total']                               = 'T???ng c???ng';
$lang['download_pdf']                              = 'T???i v??? d???ng PDF';
$lang['no_zero_required']                          = 'The %s field is required';
$lang['no_product_found']                          = 'Kh??ng c?? s???n ph???m';
$lang['pending']                                   = '??ang x??? l??';
$lang['sent']                                      = 'G???i';
$lang['completed']                                 = 'Ho??n th??nh';
$lang['shipping']                                  = 'Ph?? v???n chuy???n';
$lang['add_product_to_order']                      = 'H??y th??m s???n ph???m v??o danh s??ch ?????t h??ng';
$lang['order_items']                               = 'Order Items';
$lang['net_unit_cost']                             = '????n v??? gi?? nh???p';
$lang['net_unit_price']                            = '????n v??? gi?? b??n';
$lang['expiry_date']                               = 'Ng??y h???t h???n';
$lang['subtotal']                                  = 'Th??nh ti???n';
$lang['reset']                                     = 'L??m l???i';
$lang['items']                                     = 'M???c';
$lang['au_pr_name_tip']                            = 'H??y b???t ?????u g?? m??/t??n cho c??c ????? xu???t ho???c ch??? qu??t m?? v???ch';
$lang['no_match_found']                            = 'Kh??ng c?? k???t qu??? ph?? h???p ???????c t??m th???y! S???n ph???m c?? th??? kh??ng c?? trong kho.';
$lang['csv_file']                                  = 'CSV File';
$lang['document']                                  = 'T??i li???u ????nh k??m';
$lang['product']                                   = 'S???n ph???m';
$lang['user']                                      = 'Ng?????i d??ng';
$lang['created_by']                                = 'T???o b???i';
$lang['loading_data']                              = '??ang t???i d??? li???u b???ng t??? m??y ch???';
$lang['tel']                                       = '??i???n tho???i';
$lang['ref']                                       = 'Tham chi???u';
$lang['description']                               = 'M?? t???';
$lang['code']                                      = 'M??';
$lang['tax']                                       = 'Tax';
$lang['unit_price']                                = '????n gi??';
$lang['discount']                                  = 'Chi???t kh???u';
$lang['order_discount']                            = 'Chi???t kh???u ????n h??ng';
$lang['total_amount']                              = 'T???ng c???ng';
$lang['download_excel']                            = 'T???i v??? d???ng Excel';
$lang['subject']                                   = 'Ti??u ?????';
$lang['cc']                                        = 'CC';
$lang['bcc']                                       = 'BCC';
$lang['message']                                   = 'Tin nh???n';
$lang['show_bcc']                                  = 'Hi???n/???n BCC';
$lang['price']                                     = 'Gi??';
$lang['add_product_manually']                      = 'Th??m s???n ph???m th??? c??ng';
$lang['currency']                                  = 'Ti???n t???';
$lang['product_discount']                          = 'S???n ph???m gi???m gi??';
$lang['email_sent']                                = 'G???i email th??nh c??ng';
$lang['add_event']                                 = 'Th??m s??? ki???n';
$lang['add_modify_event']                          = 'Th??m/S???a ?????i c??c t??? ch???c s??? ki???n';
$lang['adding']                                    = '??ang th??m...';
$lang['delete']                                    = 'X??a';
$lang['deleting']                                  = '??ang x??a...';
$lang['calendar_line']                             = 'Vui l??ng click ng??y ????? th??m/s???a ?????i s??? ki???n.';
$lang['discount_label']                            = 'Chi???t kh???u (5/5%)';
$lang['product_expiry']                            = 'S???n ph???m h???t h???n';
$lang['unit']                                      = '????n v???';
$lang['cost']                                      = 'Chi ph??';
$lang['tax_method']                                = 'Tax Method';
$lang['inclusive']                                 = 'Bao g???m';
$lang['exclusive']                                 = 'Kh??ng bao g???m';
$lang['expiry']                                    = 'H???n s??? d???ng';
$lang['customer_group']                            = 'Nh??m kh??ch h??ng';
$lang['is_required']                               = 'l?? b???t bu???c';
$lang['form_action']                               = 'M???u thao t??c';
$lang['return_sales']                              = '????n h??ng ho??n';
$lang['list_return_sales']                         = 'DS ????n h??ng ho??n';
$lang['no_data_available']                         = 'Kh??ng c?? d??? li???u';
$lang['disabled_in_demo']                          = 'Ch??ng t??i r???t xin l???i nh??ng t??nh n??ng n??y b??? v?? hi???u h??a trong demo.';
$lang['payment_reference_no']                      = 'S??? tham chi???u thanh to??n';
$lang['gift_card_no']                              = 'M?? s??? th??? gi???m gi??';
$lang['paying_by']                                 = 'Thanh to??n b???ng';
$lang['cash']                                      = 'Ti???n m???t';
$lang['gift_card']                                 = 'Th??? gi???m gi??';
$lang['CC']                                        = 'Th??? t??n d???ng';
$lang['cheque']                                    = 'S??c';
$lang['cc_no']                                     = 'S??? th??? t??n d???ng';
$lang['cc_holder']                                 = 'T??n ch??? th???';
$lang['card_type']                                 = 'Lo???i th???';
$lang['Visa']                                      = 'Visa';
$lang['MasterCard']                                = 'MasterCard';
$lang['Amex']                                      = 'Amex';
$lang['Discover']                                  = 'Discover';
$lang['month']                                     = 'Th??ng';
$lang['year']                                      = 'N??m';
$lang['cvv2']                                      = 'CVV2';
$lang['cheque_no']                                 = 'S??? s??c';
$lang['Visa']                                      = 'Visa';
$lang['MasterCard']                                = 'MasterCard';
$lang['Amex']                                      = 'Amex';
$lang['Discover']                                  = 'Discover';
$lang['send_email']                                = 'G???i Email';
$lang['order_by']                                  = '?????t h??ng b???i';
$lang['updated_by']                                = 'C???p nh???t b???i';
$lang['update_at']                                 = 'C???p nh???t l??c';
$lang['error_404']                                 = '404 Page Not Found ';
$lang['default_customer_group']                    = 'Nh??m kh??ch h??ng m???c ?????nh';
$lang['pos_settings']                              = 'POS Settings';
$lang['pos_sales']                                 = 'POS Sales';
$lang['seller']                                    = 'Ng?????i b??n';
$lang['ip:']                                       = 'IP:';
$lang['sp_tax']                                    = 'Thu??? b??n h??ng';
$lang['pp_tax']                                    = 'Thu??? mua s???n ph???m';
$lang['overview_chart_heading']                    = 'Bi???u ????? t???ng quan kho h??ng t??nh bao g???m doanh s??? b??n h??ng h??ng th??ng ???? c?? thu???, thu??? b??n h??ng (c???t), nh???p h??ng (d??ng) v?? gi?? tr??? hi???n t???i c???a kho h??ng theo gi?? nh???p v?? gi?? b??n (h??nh tr??n). B???n c?? th??? l??u c??c bi???u ????? d???ng jpg, png v?? pdf.';
$lang['stock_value']                               = 'Gi?? tr??? t???n kho';
$lang['stock_value_by_price']                      = 'Gi?? tr??? t??nh theo gi?? b??n';
$lang['stock_value_by_cost']                       = 'Gi?? tr??? t??nh theo gi?? nh???p';
$lang['sold']                                      = '???? b??n';
$lang['purchased']                                 = 'Nh???p h??ng';
$lang['chart_lable_toggle']                        = 'B???n c?? th??? thay ?????i c??c bi???u ????? b???ng c??ch nh???p chu???t v?? c??c ghi ch??. Nh???p chu???t v??o m???t ghi ch?? b???t k??? ????? hi???n/???n n?? tr??n bi???u ?????.';
$lang['register_report']                           = 'B??o c??o ????ng k??';
$lang['sEmptyTable']                               = 'Kh??ng c?? d??? li???u trong b???ng';
$lang['upcoming_events']                           = 'S??? ki???n s???p t???i';
$lang['clear_ls']                                  = 'X??a d??? li???u ???? l??u';
$lang['clear']                                     = 'X??a';
$lang['edit_order_discount']                       = 'S???a chi???t kh???u ????n h??ng';
$lang['product_variant']                           = 'Bi???n th??? s???n ph???m';
$lang['product_variants']                          = 'C??c bi???n th??? s???n ph???m';
$lang['prduct_not_found']                          = 'Kh??ng c?? s???n ph???m';
$lang['list_open_registers']                       = 'List Open Registers';
$lang['delivery']                                  = 'Giao h??ng';
$lang['serial_no']                                 = 'S??? Srial';
$lang['logo']                                      = 'Logo';
$lang['attachment']                                = '????nh k??m';
$lang['balance']                                   = 'D?? n???';
$lang['nothing_found']                             = 'Kh??ng t??m th???y b???n ghi ph?? h???p';
$lang['db_restored']                               = 'Kh??i ph???c d??? li???u th??nh c??ng.';
$lang['backups']                                   = 'Backups';
$lang['best_seller']                               = 'B??n ch???y nh???t';
$lang['chart']                                     = 'Bi???u ?????';
$lang['received']                                  = '???? nh???n';
$lang['returned']                                  = 'Tr??? l???i';
$lang['award_points']                              = '??i???m th?????ng';
$lang['expenses']                                  = 'Chi ph??';
$lang['add_expense']                               = 'Th??m chi ph??';
$lang['other']                                     = 'Kh??ch';
$lang['none']                                      = 'Kh??ng c??';
$lang['calculator']                                = 'M??y t??nh';
$lang['updates']                                   = 'C???p nh???t';
$lang['update_available']                          = 'B???n c???p nh???t m???i c?? s???n, c???p nh???t ngay b??y gi???.';
$lang['please_select_customer_warehouse']          = 'Vui l??ng ch???n kh??ch h??ng/kho h??ng';
$lang['variants']                                  = 'Bi???n th???';
$lang['add_sale_by_csv']                           = 'Th??m b???ng CSV';
$lang['categories_report']                         = 'B??o c??o danh m???c';
$lang['adjust_quantity']                           = '??i???u ch???nh s??? l?????ng';
$lang['quantity_adjustments']                      = '??i???u ch???nh s??? l?????ng';
$lang['partial']                                   = 'T???ng ph???n';
$lang['unexpected_value']                          = 'Unexpected value provided!';
$lang['select_above']                              = 'Vui l??ng ch???n ??? tr??n ?????u';
$lang['no_user_selected']                          = 'Kh??ng c?? ng?????i d??ng ???????c ch???n, xin vui l??ng ch???n m???t ng?????i d??ng';
$lang['due']                                       = 'N???';
$lang['ordered']                                   = 'Ordered';
$lang['profit']                                    = 'L???i nhu???n';
$lang['unit_and_net_tip']                          = 'Calculated on unit (with tax) and net (without tax) i.e <strong>unit(net)</strong> for all sales';
$lang['expiry_alerts']                             = 'C???nh b??o h???n s??? d???ng';
$lang['quantity_alerts']                           = 'S??? l?????ng c???nh b??o';
$lang['products_sale']                             = 'Doanh thu s???n ph???m';
$lang['products_cost']                             = 'Chi ph?? s???n ph???m';
$lang['day_profit']                                = 'L???i nhu???n/Chi ph?? h??m nay';
$lang['get_day_profit']                            = 'B???n c?? th??? click v??o ng??y ????? xem l???i nhu???n/chi ph?? c???a ng??y h??m nay.';
$lang['please_select_these_before_adding_product'] = 'Vui l??ng ch???n c??c m???c n??y tr?????c khi th??m s???n ph???m b???t k???';
$lang['combine_to_pdf']                            = 'Combine to pdf';
$lang['print_barcode_label']                       = 'In m?? v???ch/nh??n';
$lang['list_gift_cards']                           = 'DS th??? gi???m gi??';
$lang['today_profit']                              = 'L???i nhu???n h??m nay';
$lang['adjustments']                               = '??i???u ch???nh';
