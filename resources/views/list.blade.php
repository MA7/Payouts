@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">لیست درخواست های واریز</div>

            <div class="panel-body">
                <!-- will be used to show any messages -->

                <div class="col-sm-2" style="margin: 0 0 20px 5px">
                    <a href="/settlements?s=1" class="btn btn-primary">خاتمه یافته</a>
                </div>
                <div class="col-sm-2" style="margin: 0 0 20px 5px">
                    <a href="/settlements?s=0" class="btn btn-primary">در دست انجام</a>
                </div>
                <div class="col-sm-2" style="margin: 0 0 20px 5px">
                    <a href="/settlements" class="btn btn-primary">نمایش همه</a>
                </div>

                <div class="col-sm-2" style="margin: 0 0 20px 20px">
                    <a href="/settlements/inquiry" class="btn btn-danger">بروز رسانی وضعیت درخواست ها</a>
                </div>

                <div style="clear:both;"></div>
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif

                @if ($settlements->count() > 0)
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <td>کد پیگیری</td>
                            <td>ایحاد کننده </td>
                            <td>نام دریافت کننده</td>
                            <td>موبایل</td>
                            <td>مبلغ</td>
                            <td>وضعیت</td>
                            <td>تاریخ ایجاد درخواست</td>
                            <td>جزییات</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($settlements as $key => $value)
                            <tr>
                                <td>{{ $value->withdraw_ref_id }}</td>
                                <td>{{ $value->user->name }}</td>
                                <td>{{ $value->getFullName() }} <br> {{$value->zp}}</td>
                                <td>{{ $value->mobile }}</td>
                                <td>{{ number_format($value->amount) }}</td>
                                <td>
                                    @if($value->status==0)
                                        در دست انجام
                                    @else
واریز شده
                                    @endif
                                </td>
                                <td>{{ jDate::forge($value->created_at)->format('datetime') }}</td>
                                <td>
                                    <a href="/settlements/inquiry?withdraw_ref_id={{$value->withdraw_ref_id}}"class="">جزییات</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info"> درخواستی ثبت نشده است.</div>
                @endif
            </div>
        </div>
@endsection


@section('javascriptBlock')
    <script>
        $(".check-inquiry").on("click", function (e) {
            e.preventDefault();
            let btn = $(this);
            let zp_id = btn.attr('data-zp');
            let amount = btn.attr('data-amount');
            let transaction_public_id = btn.attr('data-transaction_public_id');
            btn.text("لطفا صبر کنید ...");
            $("#response_form").html("");
            $.ajax({
                type: "GET",
                url: "{{ route('postCheckInquiry') }}",
                data: {
                    'zp_id': zp_id,
                    'amount': amount,
                    'transaction_public_id': transaction_public_id,
                }
            }).done(function (data, status) {
                console.log(data);
                var json_obj = JSON.stringify(data);
                if(parseInt(data.status)==200){
                    btn.text("واریز شده");
                    alert(data.err);
                }else{
                    btn.text("در انتظار واریز");
                    alert(data.err);
                }
            }).fail(function (data, status) {
                btn.text("انجام نشده");
            });
        });
    </script>
@endsection
