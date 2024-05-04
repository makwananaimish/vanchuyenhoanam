@if ($order->is_declared)
    <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#create-order-declaration"
        onclick="showDeclarationModal({{ $order->id }})">Đã kê khai</button>
@else
    <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#create-order-declaration"
        onclick="showDeclarationModal({{ $order->id }})">Chưa kê
        khai</button>
@endif
