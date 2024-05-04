<?php

use App\Transaction;
use App\User;

return [
    'updated'             => 'Cập nhật',
    'created'             => 'Tạo',
    'deleted'             => 'Xóa',

    'App\Truck'             => 'Xe',
    'App\Customer'             => 'Khách hàng',
    'App\Order'             => 'Vận đơn',

    'attributes' => [
        'name' => 'Tên',
        'departureLocation' => ['name' => 'Vị trí khởi hành'],
        'currentLocation' => ['name' => 'Vị trí hiện tại'],
        'departure_date' => 'Ngày khởi hành',
        'arrival_date' => 'Ngày về kho',

        'code' => 'Mã',
        'phone' => 'Số điện thoại',


        'bill' => 'Bill gốc',
        'product_name' => 'Tên hàng',
        'weight' => 'Cân nặng',

        'taxes' => 'Thuế',
        'taxes1' => 'Tiền Thuế NK/Thuế CBPG/ThuếBVMT',
        'taxes2' => 'Thuế VAT',

        'cost_china' => 'Chi phí Trung Quốc',
        'cost_china1' => 'Chi phí TQ - Ứng',
        'cost_china2' => 'Chi phí TQ - Kéo',

        'cost_vietnam' => 'Chi phí Việt Nam',
        'note' => 'Ghi chú',

        'truck' => ['name' => 'Tên xe'],
        'customer' => ['name' => 'Tên khách hàng'],
    ],

    'deposit' => 'Nạp tiền',
    'withdrawal' => 'Rút tiền',
    'payment' => 'Thanh toán công nợ',

    'transaction' => [
        'status' => [
            Transaction::STATUS_TEXT_PROCESSING => 'Đang xử lý',
            Transaction::STATUS_TEXT_COMPLETED => 'Đã hoàn thành',
            Transaction::STATUS_TEXT_CANCELLED => 'Đã hủy',
        ]
    ],

    'roles' => [
        User::ROLE_ADMIN => 'Admin',
        User::ROLE_SELLER => 'Sale',
        User::ROLE_VN_INVENTORY => 'Kho Việt Nam',
        User::ROLE_CN_INVENTORY => 'Kho Trung Quốc',
        User::ROLE_ACCOUNTANT => 'Kế toán',
    ]
];
