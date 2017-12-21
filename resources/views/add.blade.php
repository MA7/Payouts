@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">ایجاد درخواست واریز جدید</div>

            <div class="panel-body">
                <div id="mobile-section">
                    <div class="form-group row">
                        <label for="mobile" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">شماره
                            موبایل</label>
                        <div class="col-sm-10">
                            <input type="text" style="direction: ltr" class="form-control form-control-lg"
                                   value="{{$mobile}}" id="mobile" placeholder="09120000000">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <button id="check-mobile" class="btn btn-primary">برسی شماره تلفن</button>
                        </div>
                    </div>
                </div>
                <div class="register_form" id="response_form"></div>
            </div>
        </div>
@endsection
@section('javascriptBlock')
    <script>
        $("#check-mobile").on("click", function (e) {
            e.preventDefault();
            let btn = $(this);
            let mobile = $("#mobile").val();
            btn.attr("disabled", true);
            btn.text("لطفا صبر کنید ...");
            $("#response_form").html("");
            $.ajax({
                type: "GET",
                url: "{{ route('getCheckMobile') }}",
                data: {
                    'mobile': mobile
                }
            }).done(function (data, status) {
                console.log(status);
                $("#response_form").html(data);
                $("#mobile-section").css('display', 'none');
                console.log(data);
            }).fail(function (data, status) {
                console.log(data.responseJSON);
                if (data.responseJSON.error == 'MobileIsNotValid') {
                    btn.attr("disabled", false);
                    btn.text("برسی شماره تلفن");
                    $("#response_form").text("شماره تلفن وارد شده معتبر نیست.");
                } else {
                    btn.removeClass("btn-primary");
                    btn.addClass("btn-danger");
                    btn.text("خطا در درخواست");
                }
            });
        });
    </script>
@endsection
