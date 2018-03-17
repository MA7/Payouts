@extends('layouts.app')

@section('content')
        <div class="panel panel-default">
            <div class="panel-heading">مشاهده جزییات درخواست با کد پیگیری {{$settlement->withdraw_ref_id}}</div>

            <div class="panel-body">
                توضیحات سیستمی :  <br><?php
                $a1=array("confirmed","pendingExit");
                $a2=array("واریز شده","در دست واریز");
                echo  str_replace($a1,$a2,$settlement->paydescription); ?>
            </div>

            <div class="panel-body">
                توضیحات درخواست شما :  <br><?= $settlement->description; ?>
            </div>

        </div>
@endsection

