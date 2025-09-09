<?php

$data = [
    'current_per_head_sale' => null,
    'current_sale' => null,
    'yesterday_sale' => null,
    'current_month_sale' => null,
    'last_month_sale' => null,
    'current_month_expense' => null,
    'last_month_expense' => null,
    'last_month_profit' => null,
    'avg_bucket_size_current_month' => null,
    'avg_bucket_size_till_today' => null,
    // 'branches_pie_chart' => null,
    'hourly_chart' => null,
    'daily_chart' => null,
    'monthly_chart' => null,
    'last_month_this_day_sale' => null,
    'last_month_till_this_day_sale' => null,
    'last_year_this_day_sale' => null,
    'yeasterday_till_this_time_sale' => null,
    'last_year_till_this_day_sale' => null,
];

$record_search = [
    'reporting_summery_date_search' => null,
    'reporting_product_date_search' => null,
    'reporting_item_date_search' => null,
    'reporting_hourly_date_search' => null,
    'order_date_search' => null,
    'delivery_date_search' => null,
    'other_sale_date_search' => null,
    'inventory_compare_date_search' => null,
    'event_date_search' => null,
    'expense_date_search' => null,
    'item_requisition_date_search' => null,
    'product_requisition_date_search' => null,
    'kitchen_delivery_date_search' => null,
    'transaction_date_search' => null,
];

$pos = [
    'create_order' => ['pos.create', 'pos.store', 'pos.print', 'pos.printed'],
    'print_order_before_bill_paid' => null,
    'update_completed_order' => null,
    'update_completed_order_foods' => null,
    'update_completed_order_payment_methods' => null,
    'delete_completed_order' => null,
];

$summary = [
    'sale_summary' => ['summary.overview'],
    'sale_product_summary' => ['summary.product'],
    'sale_item_summary' => ['summary.item'],
    'sale_hourly_summary' => ['summary.hourly'],
];

$report = [
	'sale' => ['report.sale'],
	'VAT' => ['report.vat'],
	'employee' => ['report.employee'],
	'requisition' => ['report.requisition'],
	'item_stock' => ['report.item_stock'],
	'item_inventory' => ['report.item_inventory'],
	'order_invoice' => ['report.order.invoice'],
	'export_sales' => ['export.sales'],
	'export_sale_products' => ['export.sale_products'],
	'export_sale_items' => ['export.sale_items'],
	'export_orders' => ['export.orders'],
	'export_expenses' => ['export.expenses'],
];

if (config('module.warehouse')) {
    $report['central_kitchen_delivery'] = ['report.kitchen_delivery'];
    $report['product_requisition'] = ['report.product_requisition'];
    $report['product_inventory'] = ['report.product_inventory'];
    $report['product_stock'] = ['report.product_stock'];
}

/**
 * OPERATION
 */
$order = [
    'view_order_list' => ['order.index', 'order.load'],
    'view_order_detail' => ['order.show'],
    'delete_order' => ['order.destroy'],
    'status_update' => ['order.status.update'],
];

$delivery = [
    'view_delivery_list' => ['delivery.index', 'delivery.load'],
    'update_delivery' => ['delivery.edit', 'delivery.update'],
    'view_delivery_detail' => ['delivery.show'],
    'status_update' => ['delivery.status.update'],
];

$live_kitchen = [
    'view_kitchen_log' => ['kitchen.index'],
    'change_kitchen_log' => ['kitchen.clock'],
    'view_production' => ['production.index'],
    'change_production_status' => ['production.view', 'production.status', 'production.delivered'],
];

$expense = [
    'view_expense_list' => ['expense.index', 'expense.load'],
    'create_new_expense' => ['expense.create', 'expense.store', 'expense.index'],
    'update_expense' => ['expense.edit', 'expense.update'],
    'view_expense_detail' => ['expense.show'],
    'delete_expense' => ['expense.destroy'],
    'status_update' => ['expense.status.update'],
];

$one_third = [
    'feature_on' => [''],
];

$requisition = [
    'view_requisition_list' => ['requisition.index'],
    'create_new_requisition' => ['requisition.create', 'requisition.store'],
    'update_requisition' => ['requisition.edit', 'requisition.update'],
    'view_requisition_detail' => ['requisition.show'],
    'delete_requisition' => ['requisition.destroy'],
];

$product_requisition = [
    'view_product_requisition_list' => ['product_requisition.index'],
    'create_new_product_requisition' => ['product_requisition.create', 'product_requisition.store'],
    'update_product_requisition' => ['product_requisition.edit', 'product_requisition.update'],
    'view_product_requisition_detail' => ['product_requisition.show'],
    'delete_product_requisition' => ['product_requisition.destroy'],
];

$central_kitchen_delivery = [
    'view_kitchen_delivery_list' => ['kitchen_delivery.index'],
    'create_new_kitchen_delivery' => ['kitchen_delivery.create', 'kitchen_delivery.store'],
    'update_kitchen_delivery' => ['kitchen_delivery.edit', 'kitchen_delivery.update'],
    'view_kitchen_delivery_detail' => ['kitchen_delivery.show'],
    'delete_kitchen_delivery' => ['kitchen_delivery.destroy'],
];

$item_inventory = [
    'view_item_inventory_stock' => ['item_inventory_stock'],
    'view_item_inventory_out' => ['item_inventory_out'],
    'view_item_inventory_list' => ['item_inventory.index'],
    'view_item_inventory_in' => ['item_inventory.in', 'item_inventory.document', 'item_inventory.document_update'],
    'view_item_inventory_compare' => ['item_inventory.compare'],
    'view_item_inventory_activities' => ['item_inventory.activities'],
    'create_new_item_inventory' => ['item_inventory.create', 'item_inventory.store'],
    'update_item_inventory' => ['item_inventory.edit', 'item_inventory.update'],
    'document_item_inventory' => ['item_inventory.document', 'item_inventory.document_update'],
    'view_item_inventory_detail' => ['item_inventory.show'],
    'delete_item_inventory' => ['item_inventory.destroy'],
];

$product_inventory = [
    'view_product_inventory_stock' => ['product_inventory_stock'],
    'view_product_inventory_out' => ['product_inventory_out'],
    'view_product_inventory_list' => ['product_inventory.index'],
    'view_product_inventory_in' => ['product_inventory.in'],
    'view_product_inventory_detail' => ['product_inventory.show'],
    'delete_product_inventory' => ['product_inventory.destroy'],
];

$transaction = [
    'view_transaction_list' => ['transaction.index'],
    'create_new_transaction' => ['transaction.create', 'transaction.store'],
    'update_transaction' => ['transaction.edit', 'transaction.update'],
    'view_transaction_detail' => ['transaction.show'],
    'delete_transaction' => ['transaction.destroy'],
];

$document = [
    'delete_document' => ['document.destroy'],
];

$other_sale = [
    'view_other_sale_list' => ['other_sale.index'],
    'create_new_other_sale' => ['other_sale.create', 'other_sale.store'],
    'update_other_sale' => ['other_sale.edit', 'other_sale.update'],
    'view_other_sale_detail' => ['other_sale.show'],
    'delete_other_sale' => ['other_sale.destroy'],
];

/**
 * MANAGE
 */
$branch = [
    'view_branch_list' => ['branch.index'],
    'create_new_branch' => ['branch.create', 'branch.store'],
    'update_branch' => ['branch.edit', 'branch.update'],
    'view_branch_detail' => ['branch.show'],
    'status_update' => ['branch.status.update'],
    'delete_branch' => ['branch.destroy'],
    'setup_branch' => ['branch.access', 'branch.access.update'],
];

$central_kitchen = [
    'view_central_kitchen_list' => ['central.index'],
    'create_new_central_kitchen' => ['central.create', 'central.store'],
    'update_central_kitchen' => ['central.edit', 'central.update'],
    'view_central_kitchen_detail' => ['central.show'],
    'delete_central_kitchen' => ['central.destroy'],

    'setup_central_kitchen' => ['central.access', 'central.access_update'],
];

// $topping = [
//     'view_topping_list' => ['topping.index'],
//     'create_new_topping' => ['topping.create', 'topping.store'],
//     'update_topping' => ['topping.edit', 'topping.update'],
//     'view_topping_detail' => ['topping.show'],
//     'delete_topping' => ['topping.destroy'],
// ];

$customer = [
    'view_customer_list' => ['customer.index'],
    'create_new_customer' => ['customer.create', 'customer.store'],
    'update_customer' => ['customer.edit', 'customer.update'],
    'view_customer_detail' => ['customer.show'],
    'delete_customer' => ['customer.destroy'],
];

$product = [
    'view_product_list' => ['product.index'],
    'create_new_product' => ['product.create', 'product.store'],
    'update_product' => ['product.edit', 'product.update'],
    'view_product_detail' => ['product.show'],
    'delete_product' => ['product.destroy'],
];

$item = [
    'view_item_list' => ['item.index'],
    'create_new_item' => ['item.create', 'item.store'],
    'update_item' => ['item.edit', 'item.update'],
    'view_item_detail' => ['item.show'],
    'delete_item' => ['item.destroy'],
];

$employee = [
    'view_employee_list' => ['employee.index'],
    'create_new_employee' => ['employee.create', 'employee.store'],
    'update_employee' => ['employee.edit', 'employee.update'],
    'add_employee_user' => ['employee.user'],
    'view_employee_detail' => ['employee.show'],
    'delete_employee' => ['employee.destroy'],
];

$account = [
    'view_account_list' => ['account.index'],
    'create_new_account' => ['account.create', 'account.store'],
    'update_account' => ['account.edit', 'account.update'],
    'view_account_detail' => ['account.show'],
    'delete_account' => ['account.destroy'],
];

$user = [
    'view_user_list' => ['user.index'],
    'create_new_user' => ['user.create', 'user.store'],
    'update_user' => ['user.edit', 'user.update'],
    'update_email_digest' => ['user.digest', 'user.digest.update'],
    'status_update' => ['user.status.update'],
    'view_user_detail' => ['user.show'],
    'delete_user' => ['user.destroy'],
];

/**
 * SYSTEM
 */
$other_sale_category = [
    'view_other_sale_category_list' => ['other_sale_category.index'],
    'create_new_other_sale_category' => ['other_sale_category.create', 'other_sale_category.store'],
    'update_other_sale_category' => ['other_sale_category.edit', 'other_sale_category.update'],
    'view_other_sale_category_detail' => ['other_sale_category.show'],
    'delete_other_sale_category' => ['other_sale_category.destroy'],
];

$expense_category = [
    'view_expense_category_list' => ['expense_category.index'],
    'create_new_expense_category' => ['expense_category.create', 'expense_category.store'],
    'update_expense_category' => ['expense_category.edit', 'expense_category.update'],
    'view_expense_category_detail' => ['expense_category.show'],
    'delete_expense_category' => ['expense_category.destroy'],
];

$product_category = [
    'view_product_category_list' => ['product_category.index'],
    'create_new_product_category' => ['product_category.create', 'product_category.store'],
    'update_product_category' => ['product_category.edit', 'product_category.update'],
    'specification_product_category' => ['product_category.specification', 'product_category.specification_update'],
    'view_product_category_detail' => ['product_category.show'],
    'delete_product_category' => ['product_category.destroy'],
];

$online_product_category = [
    'view_online_product_category_list' => ['online_product_category.index', 'online_product_category.positioning'],
    'create_new_online_product_category' => ['online_product_category.create', 'online_product_category.store'],
    'update_online_product_category' => ['online_product_category.edit', 'online_product_category.update'],
    'order_online_product_category' => ['online_product_category.order', 'online_product_category.order_update'],
    'view_online_product_category_detail' => ['online_product_category.show'],
    'delete_online_product_category' => ['online_product_category.destroy'],
];

$method = [
    'view_method_list' => ['method.index'],
    'create_new_method' => ['method.create', 'method.store'],
    'update_method' => ['method.edit', 'method.update'],
    'view_method_detail' => ['method.show'],
    'delete_method' => ['method.destroy'],
];

$table = [
    'view_table_list' => ['stuff.index'],
    'create_new_table' => ['stuff.create', 'stuff.store'],
    'update_table' => ['stuff.edit', 'stuff.update'],
    'view_table_detail' => ['stuff.show'],
    'delete_table' => ['stuff.destroy'],
];

// $unit = [
//     'view_unit_list' => ['unit.index'],
//     'create_new_unit' => ['unit.create', 'unit.store'],
//     'update_unit' => ['unit.edit', 'unit.update'],
//     'view_unit_detail' => ['unit.show'],
//     'delete_unit' => ['unit.destroy'],
// ];

$item_category = [
    'view_item_category_list' => ['item_category.index'],
    'create_new_item_category' => ['item_category.create', 'item_category.store'],
    'update_item_category' => ['item_category.edit', 'item_category.update'],
    'view_item_category_detail' => ['item_category.show'],
    'delete_item_category' => ['item_category.destroy'],
];

$designation = [
    'view_designation_list' => ['designation.index'],
    'create_new_designation' => ['designation.create', 'designation.store'],
    'update_designation' => ['designation.edit', 'designation.update'],
    'add_designation_role' => ['designation.role'],
    'view_designation_detail' => ['designation.show'],
    'delete_designation' => ['designation.destroy'],
];

$location = [
    'view_location_list' => ['location.index'],
    'create_new_location' => ['location.create', 'location.store'],
    'update_location' => ['location.edit', 'location.update'],
    'add_location_role' => ['location.role'],
    'view_location_detail' => ['location.show'],
    'delete_location' => ['location.destroy'],
];

$event = [
    'view_event_list' => ['event.index'],
    'view_event_calendar' => ['event.calendar'],
    'create_new_event' => ['event.create', 'event.store'],
    'update_event' => ['event.edit', 'event.update'],
    'view_event_detail' => ['event.show'],
    'delete_event' => ['event.destroy'],
];

$role = [
    'view_role_list' => ['role.index'],
    'create_new_role' => ['role.create', 'role.store'],
    'update_role' => ['role.edit', 'role.update'],
    'permission_update' => ['role.permission.edit', 'role.permission.update'],
    'view_role_detail' => ['role.show'],
    'delete_role' => ['role.destroy'],
];

$setting = [
    'update' => ['setting.edit', 'setting.update'],
];

/*
|--------------------------------------------------------------------------
| Permission
|--------------------------------------------------------------------------
|
| permission menu, key as permission key and value as route name
|
*/

$modules = [];

$modules['data'] = $data;
$modules['record_search'] = $record_search;
$modules['report'] = $report;
$modules['summary'] = $summary;
$modules['POS'] = $pos;
$modules['bill_/_order'] = $order;
$modules['requisition'] = $requisition;
$modules['event'] = $event;
$modules['other_sale'] = $other_sale;
$modules['expense'] = $expense;
$modules['item_inventory'] = $item_inventory;

if (config('module.delivery')) {
    $modules['order_delivery'] = $delivery;
    $modules['location'] = $location;
    $modules['online_product_category'] = $online_product_category;
}

if (config('module.production')) {
    $modules['live_kitchen'] = $live_kitchen;
}

if (config('module.warehouse')) {
    $modules['central_kitchen'] = $central_kitchen;
    $modules['central_kitchen_delivery'] = $central_kitchen_delivery;
    $modules['product_inventory'] = $product_inventory;
    $modules['product_requisition'] = $product_requisition;
}

$modules['branch'] = $branch;
$modules['transaction'] = $transaction;
$modules['document'] = $document;
$modules['account'] = $account;
$modules['product'] = $product;
$modules['item'] = $item;
$modules['user'] = $user;
$modules['customer'] = $customer;
$modules['employee'] = $employee;
$modules['other_sale_category'] = $other_sale_category;
$modules['expense_category'] = $expense_category;
$modules['product_category'] = $product_category;
$modules['method'] = $method;
$modules['table'] = $table;
$modules['category_(Item)'] = $item_category;
$modules['designation'] = $designation;
$modules['role_(User)'] = $role;
$modules['setting'] = $setting;
$modules['one_third'] = $one_third;


// $modules['topping'] 		= $topping;
// $modules['unit']			= $unit;
return $modules;
