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
                <a href="{{ route('truck.index') }}"
                    class="submenu-item {{ request()->route()->getName() === 'truck.index'? 'active': '' }}">
                    <span>
                        <img style="height: 24px;margin-right: 8px;" src="/app-assets/img/icons/DanhSachXe.png">
                        <span>DANH SÁCH XE</span>
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

            <li>
                <a href="{{ route('log.index') }}"
                    class="submenu-item {{ request()->route()->getName() === 'log.index'? 'active': '' }}">
                    <span>
                        <i class="iconly-Curved-Activity"></i>
                        <span>LỊCH SỬ CHỈNH SỬA</span>
                    </span>
                </a>
            </li>
        </ul>
    </li>
</ul>
