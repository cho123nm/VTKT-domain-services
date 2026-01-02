@extends('layouts.admin')

@section('title', 'Chi Tiết Giao Dịch Thẻ')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Chi Tiết Giao Dịch Thẻ #{{ $card->id }}</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
            <a href="{{ route('admin.cards.index') }}" class="btn btn-primary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Quay Lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success show mb-2" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger show mb-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="intro-y box mt-5">
        <div class="p-5">
            <div class="grid grid-cols-12 gap-6">
                <!-- Thông tin thẻ -->
                <div class="col-span-12 lg:col-span-6">
                    <h3 class="text-lg font-medium mb-4">Thông Tin Thẻ Cào</h3>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="font-medium">ID Giao Dịch:</td>
                                    <td>#{{ $card->id }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">User ID:</td>
                                    <td>{{ $card->uid }}</td>
                                </tr>
                                @if($card->user)
                                <tr>
                                    <td class="font-medium">Tên Tài Khoản:</td>
                                    <td>{{ $card->user->taikhoan }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="font-medium">Loại Thẻ:</td>
                                    <td><span class="badge badge-primary">{{ $card->type }}</span></td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Mệnh Giá:</td>
                                    <td class="text-success font-medium">{{ number_format($card->amount) }}đ</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Serial:</td>
                                    <td><code>{{ $card->serial }}</code></td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Mã Thẻ (PIN):</td>
                                    <td><code>{{ $card->pin }}</code></td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Request ID:</td>
                                    <td>{{ $card->requestid ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Trạng Thái:</td>
                                    <td>
                                        @if($card->status == 0)
                                            <span class="badge badge-primary">Đang Duyệt</span>
                                        @elseif($card->status == 1)
                                            <span class="badge badge-success">Thẻ Đúng</span>
                                        @elseif($card->status == 2)
                                            <span class="badge badge-danger">Thẻ Sai</span>
                                        @elseif($card->status == 3)
                                            <span class="badge badge-warning">Sai Mệnh Giá</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-medium">Thời Gian Tạo:</td>
                                    <td>{{ $card->time }}</td>
                                </tr>
                                @if($card->time2)
                                <tr>
                                    <td class="font-medium">Time 2:</td>
                                    <td>{{ $card->time2 }}</td>
                                </tr>
                                @endif
                                @if($card->time3)
                                <tr>
                                    <td class="font-medium">Time 3:</td>
                                    <td>{{ $card->time3 }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form cập nhật trạng thái -->
                <div class="col-span-12 lg:col-span-6">
                    <h3 class="text-lg font-medium mb-4">Cập Nhật Trạng Thái</h3>
                    <form action="{{ route('admin.cards.update-status', $card->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng Thái Mới</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="0" {{ $card->status == 0 ? 'selected' : '' }}>Đang Duyệt</option>
                                <option value="1" {{ $card->status == 1 ? 'selected' : '' }}>Thẻ Đúng (Cộng tiền)</option>
                                <option value="2" {{ $card->status == 2 ? 'selected' : '' }}>Thẻ Sai</option>
                            </select>
                        </div>

                        <div class="alert alert-warning show mb-3" role="alert">
                            <strong>Lưu ý:</strong>
                            <ul class="mt-2 ml-4 list-disc">
                                <li>Nếu chuyển sang "Thẻ Đúng", hệ thống sẽ tự động cộng {{ number_format($card->amount) }}đ vào tài khoản user.</li>
                                <li>Nếu chuyển từ "Thẻ Đúng" sang trạng thái khác, hệ thống sẽ tự động trừ {{ number_format($card->amount) }}đ từ tài khoản user.</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <i data-lucide="save" class="w-4 h-4 mr-1"></i> Cập Nhật Trạng Thái
                        </button>
                    </form>

                    @if($card->user)
                    <div class="mt-5 p-4 bg-gray-100 rounded">
                        <h4 class="font-medium mb-2">Thông Tin User</h4>
                        <p><strong>Tài khoản:</strong> {{ $card->user->taikhoan }}</p>
                        <p><strong>Email:</strong> {{ $card->user->email ?? 'N/A' }}</p>
                        <p><strong>Số dư hiện tại:</strong> <span class="text-success font-medium">{{ number_format($card->user->tien) }}đ</span></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
