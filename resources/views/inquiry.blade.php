@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">استعلام</div>

            <div class="panel-body">
                <div id="inquiry-section">
                    <div class="form-group row">
                        <label for="zp_id" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">آدرس کیف پول</label>
                        <div class="col-sm-10">
                            <input type="text" style="direction: ltr" class="form-control form-control-lg"
                                   value="{{$zp_id}}" id="zp_id" placeholder="zp.32.333">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="amount" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">مبلغ</label>
                        <div class="col-sm-10">
                            <input type="text" style="direction: ltr" class="form-control form-control-lg"
                                   value="{{$amount}}" id="amount" placeholder="20000">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="transaction_public_id" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">شماره تراکنش</label>
                        <div class="col-sm-10">
                            <input type="text" style="direction: ltr" class="form-control form-control-lg"
                                   value="{{$transaction_public_id}}" id="transaction_public_id" placeholder="1313313131313131313">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <button id="check-inquiry" class="btn btn-primary">استعلام</button>
                        </div>
                    </div>
                </div>
                <div class="register_form" id="response_form"></div>
            </div>
        </div>
@endsection
@section('javascriptBlock')
    <script>
        $("#check-inquiry").on("click", function (e) {
            e.preventDefault();
            let btn = $(this);
            let zp_id = $("#zp_id").val();
            let amount = $("#amount").val();
            let transaction_public_id = $("#transaction_public_id").val();
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
                console.log(status);
                //console.log(data);
                $("#response_form").html(data);
//                $("#inquiry-section").css('display', 'none');
//                console.log(data);
                btn.text("استعلام");
            }).fail(function (data, status) {
                $("#response_form").html('transaction یافت نشد.');
                btn.text("استعلام");
            });
        });
    </script>
@endsection
