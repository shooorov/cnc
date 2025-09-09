<?php

return [
    [
        'items' => [
            [
                'name' => 'Dashboard',
                'route_name' => 'dashboard',
                'valid' => 'dashboard',
            ],
            [
                'name' => 'Reporting',
                'route_name' => 'summary.*',
                'items' => [
                    [
                        'name' => 'Summery',
                        'route_name' => 'summary.overview',
                        'valid' => 'summary.overview',
                    ],
                    [
                        'name' => 'Product',
                        'route_name' => 'summary.product',
                        'valid' => 'summary.product',
                    ],
                    [
                        'name' => 'Item',
                        'route_name' => 'summary.item',
                        'valid' => 'summary.item',
                    ],
                    [
                        'name' => 'Hourly',
                        'route_name' => 'summary.hourly',
                        'valid' => 'summary.hourly',
                    ],
                ],
            ],
            [
                'name' => 'POS',
                'route_name' => 'pos.create',
                'valid' => 'pos.*',
                'use_specific' => true,
            ],
            [
                'name' => 'Kitchen',
                'route_name' => 'production.index',
                'valid' => 'kitchen.*',
                'use_specific' => true,
            ],
            // [
            //     'name'          => 'product_inventory',
            //     'route_name'    => 'product_inventory_stock',
            //     'items' => [
            //         [
            //             'name'          => 'Stock',
            //             'route_name'    => 'product_inventory_stock',
            //             'valid'         => 'product_inventory_stock',
            //         ],
            //         [
            //             'name'          => 'In',
            //             'route_name'    => 'product_inventory.in',
            //             'valid'         => 'product_inventory.in',
            //         ],
            //         [
            //             'name'          => 'Out',
            //             'route_name'    => 'product_inventory_out',
            //             'valid'         => 'product_inventory_out',
            //         ],
            //     ],
            // ],
        ],
    ],

    [
        'items' => [
            [
                'name' => 'Order Bill',
                'route_name' => 'order.index',
                'valid' => 'order.*',
            ],
            [
                'name' => 'Delivery',
                'route_name' => 'delivery.index',
                'valid' => 'delivery.*',
            ],
            // [
            //     'name'          => 'product_inventory',
            //     'route_name'    => 'product_inventory.in',
            //     'valid'         => 'product_inventory.in',
            // ],
            [
                'name' => 'item_inventory',
                'route_name' => 'item_inventory.in',
                'valid' => 'item_inventory.in',
            ],
        ],
    ],

    [
        'items' => [
            [
                'name' => 'Event',
                'route_name' => 'event.calendar',
                'valid' => 'event.*',
            ],
            // [
            //     'name'          => 'product_inventory',
            //     'route_name'    => 'product_inventory_stock',
            //     'items' => [
            //         [
            //             'name'          => 'Stock',
            //             'route_name'    => 'product_inventory_stock',
            //             'valid'         => 'product_inventory_stock',
            //         ],
            //         [
            //             'name'          => 'In',
            //             'route_name'    => 'product_inventory.in',
            //             'valid'         => 'product_inventory.in',
            //         ],
            //         [
            //             'name'          => 'Out',
            //             'route_name'    => 'product_inventory_out',
            //             'valid'         => 'product_inventory_out',
            //         ],
            //     ],
            // ],
            // [
            //     'name' => 'item_inventory',
            //     'route_name' => 'item_inventory_stock',
            //     'valid' => 'item_inventory.*',
            //     'items' => [
            //         [
            //             'name' => 'Stock',
            //             'route_name' => 'item_inventory_stock',
            //             'valid' => 'item_inventory_stock',
            //         ],
            //         [
            //             'name' => 'In',
            //             'route_name' => 'item_inventory.in',
            //             'valid' => 'item_inventory.*',
            //         ],
            //         [
            //             'name' => 'Out',
            //             'route_name' => 'item_inventory_out',
            //             'valid' => 'item_inventory_out',
            //         ],
            //     ],
            // ],
            [
                'name' => 'Other Sale',
                'route_name' => 'other_sale.index',
                'valid' => 'other_sale.*',
            ],
            [
                'name' => 'Expense',
                'route_name' => 'expense.index',
                'valid' => 'expense.*',
            ],
            [
                'name' => 'Requisition',
                'route_name' => 'requisition.index',
                'valid' => 'requisition.*',
            ],
            [
                'name' => 'Product Requisition',
                'route_name' => 'product_requisition.index',
                'valid' => 'product_requisition.*',
            ],
            [
                'name' => 'Kitchen Delivery',
                'route_name' => 'kitchen_delivery.index',
                'valid' => 'kitchen_delivery.*',
            ],
        ],
    ],

    [
        'items' => [
            [
                'name' => 'Manage',
                'route_name' => '#',
                'items' => [
                    [
                        'name' => 'Account',
                        'route_name' => 'account.index',
                        'valid' => 'account.*',
                    ],
                    [
                        'name' => 'Branch',
                        'route_name' => 'branch.index',
                        'valid' => 'branch.*',
                    ],
                    [
                        'name' => 'Central Kitchen',
                        'route_name' => 'central.index',
                        'valid' => 'central.*',
                    ],
                    [
                        'name' => 'Product',
                        'route_name' => 'product.index',
                        'valid' => 'product.*',
                    ],
                    [
                        'name' => 'Item',
                        'route_name' => 'item.index',
                        'valid' => 'item.*',
                    ],
                    [
                        'name' => 'Customer',
                        'route_name' => 'customer.index',
                        'valid' => 'customer.*',
                    ],
                    [
                        'name' => 'Employee',
                        'route_name' => 'employee.index',
                        'valid' => 'employee.*',
                    ],
                    [
                        'name' => 'User',
                        'route_name' => 'user.index',
                        'valid' => 'user.*',
                    ],
                ],
            ],

            [
                'name' => 'System',
                'route_name' => '#',
                'items' => [
                    [
                        'name' => 'Role (User)',
                        'route_name' => 'role.index',
                        'valid' => 'role.*',
                    ],
                    [
                        'name' => 'Category (Other Sale)',
                        'route_name' => 'other_sale_category.index',
                        'valid' => 'other_sale_category.*',
                    ],
                    [
                        'name' => 'Category (Expense)',
                        'route_name' => 'expense_category.index',
                        'valid' => 'expense_category.*',
                    ],
                    [
                        'name' => 'Category (Product)',
                        'route_name' => 'product_category.index',
                        'valid' => 'product_category.*',
                    ],
                    [
                        'name' => 'Online Category (Product)',
                        'route_name' => 'online_product_category.index',
                        'valid' => 'online_product_category.*',
                    ],
                    [
                        'name' => 'Category (Item)',
                        'route_name' => 'item_category.index',
                        'valid' => 'item_category.*',
                    ],
                    [
                        'name' => 'Designation',
                        'route_name' => 'designation.index',
                        'valid' => 'designation.*',
                    ],
                    [
                        'name' => 'Location',
                        'route_name' => 'location.index',
                        'valid' => 'location.*',
                    ],
                    [
                        'name' => 'Payment Method (Order)',
                        'route_name' => 'method.index',
                        'valid' => 'method.*',
                    ],
                    [
                        'name' => 'Table',
                        'route_name' => 'stuff.index',
                        'valid' => 'stuff.*',
                    ],
                    // [
                    //     'name'          => 'Unit',
                    //     'route_name'    => 'unit.index',
                    //     'valid'         => 'unit.*',
                    // ],
                    [
                        'name' => 'Setting',
                        'route_name' => 'setting.edit',
                        'valid' => 'setting.*',
                    ],
                ],
            ],

        ],
    ],
];
