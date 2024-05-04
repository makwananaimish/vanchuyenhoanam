<ul>
    <li>
        <div class="menu-title">MAIN</div>
        <ul>
            <li>
                <a href="{{ route('home') }}"
                    class="submenu-item {{ request()->route()->getName() === 'home'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/Dashboard.png">

                        <span>DASHBOARD</span>
                    </span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <div class="menu-title">APPS</div>
        <ul>
            <li>
                <a href="javascript:;" class="submenu-item active">
                    <span>
                        <i class="iconly-Curved-Voice"></i>
                        <span>VẬN ĐƠN</span>
                    </span>

                    <div class="menu-arrow"></div>
                </a>

                <ul class="submenu-children active d-block" data-level="1">
                    <li>
                        <a href="{{ route('customer.orders', ['customer' => auth()->guard('customer')->id()]) }}"
                            class="text-uppercase">
                            <span>----- Tất cả</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.orders', ['customer' => auth()->guard('customer')->id()]) }}?customer_code={{ \App\Customer::NONAME_CODE }}"
                            class="text-uppercase">
                            <span>----- {{ \App\Customer::NONAME_CODE }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.orders', ['customer' => auth()->guard('customer')->id()]) }}?customer_code={{ \App\Customer::EXPRESS_CODE }}"
                            class="text-uppercase">
                            <span>----- {{ \App\Customer::EXPRESS_CODE }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customer.orders.completed', ['customer' => auth()->guard('customer')->id()]) }}"
                            class="text-uppercase">
                            <span>----- Đơn hoàn thành</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('payable.orders') }}"
                    class="submenu-item {{ request()->route()->getName() === 'payable.orders'? 'active': '' }}">
                    <span>
                        <i class="iconly-Curved-Calendar"></i>
                        <span>CÔNG NỢ</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('transaction.index') }}"
                    class="submenu-item {{ request()->route()->getName() === 'transaction.index'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/thanhtoan(payment).png">

                        <span>THANH TOÁN</span>
                    </span>
                </a>
            </li>
        </ul>
    </li>

</ul>
