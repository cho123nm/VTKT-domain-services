@extends('layouts.admin')

@section('title', 'Quản Lý Gạch Cards')

@section('content')
<div class="col-span-12 mt-6">
    <div class="intro-y block sm:flex items-center h-10">
        <h2 class="text-lg font-medium truncate mr-5">Quản Lý Gạch Cards</h2>
        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
        </div>
    </div>
    <div class="intro-y box">
        <div class="p-5" id="head-options-table">
            <div class="preview">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">UID</th>
                                <th class="whitespace-nowrap">Mã Thẻ</th>
                                <th class="whitespace-nowrap">Serial</th>
                                <th class="whitespace-nowrap">Mệnh Giá</th>
                                <th class="whitespace-nowrap">Loại Thẻ</th>
                                <th class="whitespace-nowrap">Status</th>
                                <th class="whitespace-nowrap">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($cards->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">Chưa có thẻ cào nào</td>
                                </tr>
                            @else
                                @foreach($cards as $index => $card)
                                <tr>
                                    <td>#{{ $index + 1 }}</td>
                                    <td>{{ $card->uid }}</td>
                                    <td>{{ $card->pin }}</td>
                                    <td>{{ $card->serial }}</td>
                                    <td>{{ number_format($card->amount) }}đ</td>
                                    <td>{{ $card->type }}</td>
                                    <td>
                                        @if($card->status == 0)
                                            <button class="btn btn-primary">Đang Duyệt</button>
                                        @elseif($card->status == 1)
                                            <button class="btn btn-success">Thẻ Đúng</button>
                                        @elseif($card->status == 2)
                                            <button class="btn btn-danger">Thẻ Sai</button>
                                        @elseif($card->status == 3)
                                            <button class="btn btn-warning">Sai Mệnh Giá</button>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">{{ $card->time }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

