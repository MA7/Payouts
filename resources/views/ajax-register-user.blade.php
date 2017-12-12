<h4>
    ثبت نام کاربر جدید
</h4>
<br>

<form action="{{route('register.user')}}" method="post">
    {{ csrf_field() }}
    <div class="form-group row">
        <label for="firs-tname" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">نام </label>
        <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm" id="first-name" name="first-name">
        </div>
    </div>
    <div class="form-group row">
        <label for="last-name" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">نام
            خانوادگی </label>
        <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm" id="last-name" name="last-name">
        </div>
    </div>
    <div class="form-group row">
        <label for="mobile-number" style="text-align: left;"
               class="col-sm-2 col-form-label col-form-label-lg">موبایل </label>
        <div class="col-sm-10">
            <input type="text" style="direction: ltr" class="form-control form-control-sm" id="mobile-number" max="12"
                   name="mobile-number" value="{{ $mobile }}" placeholder="09351123334">
        </div>
    </div>
    <div class="form-group row">
        <label for="ssn" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg"> کد ملی </label>
        <div class="col-sm-10">
            <input type="text" style="direction: ltr" class="form-control form-control-sm" id="ssn" name="ssn" max="10"
                   placeholder="0079770221">
        </div>
    </div>
    <div class="form-group row">
        <label for="birth-date" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">تاریخ
            تولد </label>
        <div class="col-sm-10">
            <input type="text" style="direction: ltr" class="form-control form-control-sm" id="birth-date"
                   name="birth-date" placeholder="1990-01-01">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2">
            <button id="check-mobile" class="btn btn-primary">ثبت نام کاربر</button>
        </div>
    </div>
</form>
