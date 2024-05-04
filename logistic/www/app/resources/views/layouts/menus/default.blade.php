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
                <a href="javascript:;"
                    class="text-uppercase submenu-item {{ $routeName === 'chart.orders-bang-tuong' || $routeName === 'chart.customers' ? 'active' : '' }} ">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/storage(kho).png?v=1">

                        <span>Biểu đồ</span>
                    </span>

                    <div class="menu-arrow"></div>
                </a>

                <ul class="submenu-children {{ $routeName === 'chart.orders-bang-tuong' ||
                $routeName === 'chart.customers' ||
                $routeName === 'chart.revenue'
                    ? 'active d-block'
                    : '' }}"
                    data-level="1">
                    <li>
                        <a href="{{ route('chart.orders-bang-tuong') }}"
                            class="text-uppercase {{ $routeName === 'chart.orders-bang-tuong' ? 'active' : '' }}">
                            <span>----- Vận Đơn Bằng Tường/ Tổng</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chart.customers') }}"
                            class="text-uppercase {{ $routeName === 'chart.customers' ? 'active' : '' }}">
                            <span>----- Khách Hàng Kho Bằng Tường</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chart.revenue') }}"
                            class="text-uppercase {{ $routeName === 'chart.revenue' ? 'active' : '' }}">
                            <span>----- Doanh Thu</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('truck.index') }}"
                    class="submenu-item {{ request()->route()->getName() === 'truck.index'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/DanhSachXe.png">

                        <span>DANH SÁCH XE</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('truck.completed') }}"
                    class="submenu-item {{ request()->route()->getName() === 'truck.completed'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/DanhSachXe.png">

                        <span>XE ĐÃ HOÀN THÀNH</span>
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('customer.index') }}"
                    class="submenu-item {{ request()->route()->getName() === 'customer.index'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;"
                            src="/app-assets/img/icons/khachhang(customer).png">

                        <span>KHÁCH HÀNG</span>
                    </span>
                </a>
            </li>

            @foreach ($_inChinaLocations as $location)
                <li>
                    <a href="javascript:;"
                        class="text-uppercase submenu-item {{ expandLocationMenu($location) ? 'active' : '' }} ">
                        <span>
                            <img style="height: 24px;margin-right: 8px;"
                                src="/app-assets/img/icons/storage(kho).png?v=1">

                            <span>Kho {{ $location->name }}</span>
                        </span>

                        <div class="menu-arrow"></div>
                    </a>

                    <ul class="submenu-children {{ expandLocationMenu($location) ? 'active d-block' : '' }}"
                        data-level="1">
                        <li>
                            <a href="{{ route('order.location', ['location' => $location]) }}"
                                class="text-uppercase {{ $routeName === 'order.location' ? 'active' : '' }}">
                                <span>----- Tất cả</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('order.noname') }}"
                                class="text-uppercase {{ $routeName === 'order.noname' ? 'active' : '' }}">
                                <span>----- {{ \App\Customer::NONAME_CODE }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cost_china.index') }}"
                                class="text-uppercase {{ $routeName === 'cost_china.index' ? 'active' : '' }}">
                                <span>----- Chi phí TQ</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('order.express') }}"
                                class="text-uppercase {{ $routeName === 'order.express' ? 'active' : '' }}">
                                <span>----- Kí nhận CPN</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endforeach

            @foreach ($_inVietnamLocations as $location)
                <li>
                    <a href="javascript:;"
                        class="submenu-item 
                                            {{ expandVnInventoryMenu($location) ? 'active arrow-active' : '' }}
                                            ">
                        <span>
                            <img style="height: 24px;margin-right: 8px;"
                                src="/app-assets/img/icons/storage(kho).png?v=1">

                            <span class="text-uppercase">KHO {{ $location->name }}</span>
                        </span>

                        <div class="menu-arrow"></div>
                    </a>

                    <ul
                        class="submenu-children {{ expandVnInventoryMenu($location) || expandUnpaidMenu($location) ? 'active d-block' : '' }}  
                            data-level="1">
                        <li>
                            <a href="{{ route('order.vietnamese_inventory.location', ['location' => $location]) }}"
                                class="text-uppercase {{ expandVnInventoryMenu($location) ? 'active' : '' }}">
                                <span>----- Hàng Về {{ $location->name }}</span>
                            </a>
                        </li>

                        @if (!isSeller())
                            <li>
                                <a href="{{ route('order.unpaid') }}?location_id={{ $location->id }}"
                                    class="{{ expandUnpaidMenu($location) ? 'active' : '' }}">
                                    <span>----- CHƯA TÍNH TIỀN</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('order.address') }}"
                                class="text-uppercase {{ expandVnInventoryMenu($location) ? 'active' : '' }}">
                                <span>----- Địa chỉ trả hàng</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endforeach

            @if (!isSeller())
                <li>
                    <a href="{{ route('report.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'report.index'? 'active': '' }}">
                        <span>
                            <i class="iconly-Curved-Bag"></i>
                            <span>BÁO CÁO CHI TIẾT</span>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('top_revenue.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'top_revenue.index'? 'active': '' }}">
                        <span>
                            <i class="iconly-Curved-Bag"></i>
                            <span>TOP DOANH THU</span>
                        </span>
                    </a>
                </li>
            @endif

            @if (!auth()->guard('customer')->check())
                <li>
                    <a href="{{ route('seller.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'seller.index'? 'active': '' }}">
                        <span>
                            <i class="iconly-Broken-Calling"></i>
                            <span>BÁO CÁO SALE</span>
                        </span>
                    </a>
                </li>
            @endif

            <li>
                <a href="javascript:;"
                    class="submenu-item {{ request()->route()->getName() === 'transaction.index'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/thanhtoan(payment).png">

                        <span class="text-uppercase">THANH TOÁN</span>
                    </span>

                    <div class="menu-arrow"></div>
                </a>

                <ul class="submenu-children {{ request()->route()->getName() === 'transaction.index' ||request()->route()->getName() === 'transaction.autobank'? 'active d-block': '' }}"
                    data-level="1">
                    <li>
                        <a href="{{ route('transaction.index') }}"
                            class="text-uppercase {{ request()->route()->getName() === 'transaction.index'? 'active': '' }}">
                            <span>----- Danh sách</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('transaction.autobank') }}"
                            class="text-uppercase {{ request()->route()->getName() === 'transaction.autobank'? 'active': '' }}">
                            <span>----- Lịch sử Autobank</span>
                        </a>
                    </li>
                </ul>
            </li>

            @if (!isSeller())
                <li>
                    <a href="{{ route('user.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'user.index'? 'active': '' }}">
                        <span>
                            <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/quantrivien.png">

                            <span>QUẢN TRỊ VIÊN</span>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('log.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'log.index'? 'active': '' }}">
                        <span>
                            <i class="iconly-Curved-Activity"></i>
                            <span>LỊCH SỬ CHỈNH SỬA</span>
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('location.index') }}"
                        class="submenu-item {{ request()->route()->getName() === 'location.index'? 'active': '' }}">
                        <span>
                            <img style="height: 24px;margin-right: 8px;"
                                src="/app-assets/img/icons/setting(cauhinh).png">

                            <span>CẤU HÌNH</span>
                        </span>
                    </a>
                </li>
            @endif
        </ul>
    </li>

</ul>
