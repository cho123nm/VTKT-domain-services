@extends('layouts.app')

@section('content')
<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
    <div class="app-container container-xxl d-flex flex-row flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <div class="card mb-5 mb-xl-10"></div>
                    <div class="card card-docs flex-row-fluid mb-2">
                        <div class="card-body fs-6 py-15 px-10 py-lg-15 px-lg-15 text-gray-700">
                            <div class="py-10">
                                <h1 class="anchor fw-bold mb-5" id="form-labels" data-kt-scroll-offset="50">
                                    <a href="#form-labels"></a> Quản Lý Tên Miền
                                </h1>
                                <div class="py-5">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên Miền</th>
                                                    <th>NS1</th>
                                                    <th>NS2</th>
                                                    <th>Trạng Thái</th>
                                                    <th>Mã Giao Dịch</th>
                                                    <th>Thời Gian</th>
                                                    <th>Thao Tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($domains as $index => $domain)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $domain->domain }}</td>
                                                    <td>{{ $domain->ns1 }}</td>
                                                    <td>{{ $domain->ns2 }}</td>
                                                    <td>
                                                        @if($domain->status == 0)
                                                            <span class="badge badge-warning">Chờ Xử Lý</span>
                                                        @elseif($domain->status == 1)
                                                            <span class="badge badge-success">Hoàn Thành</span>
                                                        @elseif($domain->status == 2)
                                                            <span class="badge badge-danger">Hủy</span>
                                                        @elseif($domain->status == 3)
                                                            <span class="badge badge-info">Chờ Duyệt DNS</span>
                                                        @elseif($domain->status == 4)
                                                            <span class="badge badge-secondary">Từ Chối</span>
                                                        @else
                                                            <span class="badge badge-secondary">Khác</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $domain->mgd }}</td>
                                                    <td>{{ $domain->time }}</td>
                                                    <td>
                                                        @if($domain->status == 1)
                                                            <a href="{{ route('domain.manage-dns', ['domain' => $domain->mgd]) }}" class="btn btn-sm btn-warning">
                                                                Quản Lý DNS
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Bạn chưa có tên miền nào</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

