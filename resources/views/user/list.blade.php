@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">لیست کاربران</div>

            <div class="panel-body">
                <!-- will be used to show any messages -->

                <div class="col-sm-2" style="margin: 0 0 20px 20px">
                    <a href="/user/new" class="btn btn-primary">ثبت کاربر جدید</a>
                </div>


                <div style="clear:both;"></div>
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif

                @if ($users->count() > 0)
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <td>نام و نام خانوادگی</td>
                            <td>تغییر رمز عبور</td>
                            <td>ایجاد</td>
                            <td>ویرایش</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $key => $value)
                            <tr>
                                <td>{{ $value->name }}</td>
                                <td>
                                    <a href="/user/changepassword/{{$value->id}}" class="btn btn-primary">تغییر رمز</a>
                                </td>
                                <td>{{ $value->created_at }}</td>
                                <td>{{ $value->updated_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info"> کاربری نشده است.</div>
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
