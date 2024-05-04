<div class="modal fade" id="address-notification" tabindex="-1" aria-labelledby="address-notification" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">Thông báo địa chỉ</h5>
                <button type="button" class="btn-close hp-bg-none d-flex align-items-center justify-content-center"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line hp-text-color-dark-0 lh-1" style="font-size: 24px;"></i>
                </button>
            </div>


            <div class="modal-body">
                <div class="overflow-auto">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col"><b>STT</b></th>
                                <th scope="col"><b>Mã khách hàng</b></th>

                                <th scope="col"><b>Trạng thái</b></th>

                                <th scope="col"><b>Ngày tạo</b></th>

                                <th scope="col"><b>Mã vận đơn</b></th>

                                <th scope="col"><b>Số kg</b></th>
                                <th scope="col"><b>Số m3</b></th>

                                <th scope="col"><b>Ngày khai báo</b></th>
                                <th scope="col"><b>Địa chỉ</b></th>
                                <th scope="col"><b>Số điện thoại</b></th>
                                <th scope="col"><b>Tên người nhận</b></th>
                                <th scope="col"><b>Ghi chú</b></th>

                                <th scope="col"><b>Số dư</b></th>
                                <th scope="col"><b>Tổng số nợ</b></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($notifyAddresses as $index => $address)
                                <tr>
                                    <td scope="row">{{ $index + 1 }}</td>
                                    <td>
                                        <a
                                            href="{{ route('customer.show', ['customer' => $address->order->customer]) }}">
                                            {{ $address->order->customer->code }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ optional($address->order)->status_text }}
                                    </td>

                                    <td>{{ $address->created_at }}</td>

                                    <td>
                                        <a href="{{ route('order.show', ['order' => optional($address->order)->id]) }}">
                                            {{ optional($address->order)->code }}
                                        </a>
                                    </td>

                                    <td>{{ optional($address->order)->weight }}</td>
                                    <td>{{ optional($address->order)->cubic_meters }}</td>

                                    <td>{{ $address->date }}</td>
                                    <td>{{ $address->address }}</td>
                                    <td>{{ $address->phone }}</td>
                                    <td>{{ $address->name }}</td>
                                    <td>{{ $address->note }}</td>

                                    <td>{{ optional(optional($address->order))->customer->balance_formatted }}</td>
                                    <td>
                                        {{ number_format(optional($address->order)->customer->debt, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
