@extends('layouts.app')

@section('title')
    Danh Sách Quản Trị Viên
@endsection

@section('content')
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Home</a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('user.index') }}">Quản Trị Viên</a>
                </li>

                <li class="breadcrumb-item active">Danh Sách</li>
            </ol>
        </nav>
    </div>

    <div class="col-12 col-md-6">
        <div class="hp-page-title">
            <h1 class="mb-8 text-uppercase">DANH SÁCH QUẢN TRỊ VIÊN</h1>
        </div>
    </div>

    <div class="col-12 d-none col-md-6 d-md-block">
        <div class="hp-page-title-logo d-flex justify-content-end">
            <img src="{{ asset('app-assets/img/logo/logo2@2x.png') }}">
        </div>
    </div>

    <div class="col-12">
        @if (session('message'))
            <div class="alert {{ session('alert-class') }}">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-12 mt-16">
                        <form action="" method="get" class="filter-trucks">
                            <div class="row g-16 mb-16">
                                @if (auth()->user()->is_admin)
                                    <div class="col hp-flex-none w-auto">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#create-user">
                                            Thêm Quản Trị Viên
                                        </button>
                                    </div>
                                @endif

                                <div class="col-12 col-md-3 col-lg-2 hp-flex-none">
                                    <select class="form-control" name="role" onchange="filterTrucks()">
                                        <option value="">Chọn vai trò</option>
                                        <option value="{{ \App\User::ROLE_ADMIN }}"
                                            {{ request('role') === \App\User::ROLE_ADMIN ? 'selected' : '' }}>
                                            {{ __('app.roles.' . \App\User::ROLE_ADMIN) }}</option>
                                        <option value="{{ \App\User::ROLE_SELLER }}"
                                            {{ request('role') === \App\User::ROLE_SELLER ? 'selected' : '' }}>
                                            {{ __('app.roles.' . \App\User::ROLE_SELLER) }}</option>
                                        <option value="{{ \App\User::ROLE_VN_INVENTORY }}"
                                            {{ request('role') === \App\User::ROLE_VN_INVENTORY ? 'selected' : '' }}>
                                            {{ __('app.roles.' . \App\User::ROLE_VN_INVENTORY) }}</option>
                                        <option value="{{ \App\User::ROLE_CN_INVENTORY }}"
                                            {{ request('role') === \App\User::ROLE_CN_INVENTORY ? 'selected' : '' }}>
                                            {{ __('app.roles.' . \App\User::ROLE_CN_INVENTORY) }}</option>
                                        <option value="{{ \App\User::ROLE_ACCOUNTANT }}"
                                            {{ request('role') === \App\User::ROLE_ACCOUNTANT ? 'selected' : '' }}>
                                            {{ __('app.roles.' . \App\User::ROLE_ACCOUNTANT) }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 mt-16 fix-width scroll-inner">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Tên</th>
                                    <th scope="col">Chức vụ</th>
                                    <th scope="col">Email</th>

                                    @if (request('role') === 'SELLER')
                                        <th scope="col">Công nợ</th>
                                    @endif

                                    @if (auth()->user()->is_admin)
                                        <th scope="col"></th>
                                    @endif
                                </tr>
                            </thead>

                            @php
                                $_user = $user ?? null;
                            @endphp

                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td scope="row">{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ __('app.roles.' . $user->role) }}</td>
                                        <td>{{ $user->email }}</td>

                                        @if (request('role') === 'SELLER')
                                            <td>
                                                {{ number_format($user->customers->sum('debt'), 0, '', '.') }}
                                            </td>
                                        @endif


                                        @if (auth()->user()->is_admin)
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('user.show', ['user' => $user]) }}#update-user"
                                                        class="btn btn-primary text-nowrap me-6"
                                                        @if (optional($_user)->id === $user->id) data-bs-toggle="modal"
                                                data-bs-target="#update-user" @endif>
                                                        Cập Nhật
                                                    </a>

                                                    @if (auth()->id() !== $user->id)
                                                        <form method="POST"
                                                            action="{{ route('user.delete', ['user' => $user]) }}"
                                                            id="frm-{{ $user->id }}">
                                                            @csrf
                                                        </form>

                                                        <button class="btn btn-danger"
                                                            onclick="deleteUser({{ $user->id }})">
                                                            Xóa
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $users->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>

        @include('components.modals.create-user')

        @if ($_user)
            @include('components.modals.update-user', ['_user' => $_user])
        @endif
    </div>
@endsection
