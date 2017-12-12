@extends('layouts.app')

@section('content')
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">لیست درخواست های واریز</div>

            <div class="panel-body">
                <!-- will be used to show any messages -->
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif

                @if ($settlements->count() > 0)
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <td>zp</td>
                            <td>نام</td>
                            <td>موبایل</td>
                            <td>مبلغ</td>
                            <td>تاریخ</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($settlements as $key => $value)
                            <tr>
                                <td>{{ $value->zp }}</td>
                                <td>{{ $value->FullName }}</td>
                                <td>{{ $value->mobile }}</td>
                                <td>{{ $value->amount }}</td>
                                <td>{{ $value->createAt }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info"> درخواستی ثبت نشده است.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
