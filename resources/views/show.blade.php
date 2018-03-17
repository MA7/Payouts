@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">مشاهده جزییات درخواست با کد پیگیری {{$settlement->withdraw_ref_id}}</div>

            <div class="panel-body">
                توضیحات سیستمی <br><?= $settlement->paydescription; ?>
            </div>

            <div class="panel-body">
                توضیحات شما <br><?= $settlement->description; ?>
            </div>

        </div>
@endsection

