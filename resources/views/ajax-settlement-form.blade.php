<?php
/** @var string $zp Zp id of zarinpal users. */
/** @var string $name First name of user. */
/** @var string $family $family name of user. */
/** @var string $mobile User mobile to register. */
?>
<h4>
    تکمیل فرم درخواست واریز برای {{$name}} {{$family}} ({{$mobile}})
</h4>

<br>
<form action="{{route('settlement.create')}}" method="post">
    {{ csrf_field() }}
    <div class="form-group row">
        <label for="purse" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">کیف پول
            مبدا</label>
        <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm" id="purse">
        </div>
    </div>
    <div class="form-group row">
        <label for="amount" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">مبلغ</label>
        <div class="col-sm-10">
            <input type="number" style="direction: ltr" class="form-control form-control-sm" id="amount" name="amount">
        </div>
    </div>
    <div class="form-group row">
        <label for="iban" style="text-align: left;" class="col-sm-2 col-form-label col-form-label-lg">شماره شبا</label>
        <div class="col-sm-10">
            <input type="text" style="direction: ltr" class="form-control form-control-sm" id="iban" name="iban">
        </div>
    </div>
    <div class="form-group row">
        <label for="description" style="text-align: left;"
               class="col-sm-2 col-form-label col-form-label-lg">توضیحات</label>
        <div class="col-sm-10">
            <textarea class="form-control form-control-sm" id="description" name="description" rows="3"></textarea>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-2">
            <button id="check-mobile" class="btn btn-primary">ثبت درخواست</button>
        </div>
    </div>
</form>