<div class="row justify-content-between">
    <div class="col-12 mt-16 fix-width scroll-inner">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col"><b>STT</b></th>
                    <th scope="col"><b>Loại sản phẩm</b></th>

                    <th scope="col"><b>Hình ảnh</b></th>

                    <th scope="col"><b>Tên sản phẩm, dùng để làm gì</b></th>
                    <th scope="col"><b>Kích thước sản phẩm</b></th>
                    <th scope="col"><b>Thương hiệu, kí hiệu trên sản phẩm</b></th>
                    <th scope="col"><b>Chất liệu</b></th>

                    <th scope="col"><b>Cân nặng 1 sản phẩm(g)</b></th>
                    <th scope="col"><b>Số sản phẩm trên thùng(bao tải)</b></th>
                    <th scope="col"><b>Số thùng(bao tải)</b></th>
                    <th scope="col"><b>Tổng số sản phẩm</b></th>
                    <th scope="col"><b>Điện áp ,Công Suất, Thông số</b></th>

                    <th scope="col"><b>Cân nặng 1 thùng(bao tải)</b></th>
                    <th scope="col"><b>Kích thước thùng(bao tải)</b></th>
                    <th scope="col"><b>Tổng số m³</b></th>
                    <th scope="col"><b>Tổng số kg</b></th>
                    <th scope="col"><b>HS CODE</b></th>

                    <th scope="col"><b>Hành động</b></th>
                </tr>
            </thead>

            <tbody>
                @foreach ($declarations as $index => $declaration)
                    <tr>
                        <td scope="row">{{ $index + 1 }}</td>
                        <td>{{ $declaration->type }}</td>

                        <td>
                            @foreach ($declaration->images as $image)
                                <img width="100px" src="{{ asset('/files/' . $image) }}" alt="" srcset="">
                            @endforeach
                        </td>

                        <td>{{ $declaration->name }}</td>
                        <td>{{ $declaration->size }}</td>
                        <td>{{ $declaration->brand }}</td>
                        <td>{{ $declaration->material }}</td>

                        <td>{{ $declaration->weight_per_product }}</td>
                        <td>{{ $declaration->quantity_per_pack }}</td>
                        <td>{{ $declaration->pack_quantity }}</td>
                        <td>{{ $declaration->quantity }}</td>
                        <td>{{ $declaration->voltage_power_parameters }}</td>

                        <td>{{ $declaration->weight_per_box }}</td>
                        <td>{{ $declaration->box_size }}</td>
                        <td>{{ $declaration->cubic_meters_format }}</td>
                        <td>{{ $declaration->weight_format }}</td>
                        <td>{{ $declaration->hs_code }}</td>

                        <td>
                            <div class="d-flex">
                                <form method="POST"
                                    action="{{ route('order_declaration.delete', ['declaration' => $declaration]) }}"
                                    id="frm-declaration-{{ $declaration->id }}">
                                    @csrf
                                </form>

                                <button class="btn btn-danger text-nowrap" onclick="_delete('declaration-{{ $declaration->id }}')">
                                    Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
