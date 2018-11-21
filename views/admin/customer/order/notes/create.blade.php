@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Add note</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item">
                            Order:
                            <a href="{{url('customers/'.$customer_id.'/orders/'.$order->id.'')}}">#{{$order->number}}</a>
                        </li>
                        <li class="breadcrumb-item active">Note</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        @if (Session::has('message'))
           <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        <!-- //Main content page. -->
        <div class='col-lg-6 col-lg-offset-6'>
            <hr>
            <form method="post" action="{{url('customers/'.$customer_id.'/orders/'.$order->id.'/store')}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="user_id" value="{{ $customer_id }}">
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="order_item_id" value="{{ $order_item_id }}">
                <div class="form-group">
                    <label for="note">Note<span class="text-danger">*</span></label>
                    <textarea name="note" parsley-trigger="change" required="required" placeholder="Note" class="form-control" id="note" rows="7"> </textarea> 
                </div>
                <input type="submit" class="btn btn-success" name="submit" value="Save">
            </form>
        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection
