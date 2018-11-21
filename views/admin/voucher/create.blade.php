@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <form action="{{route('vouchers.store')}}" novalidate="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"></i>Vouchers</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('vouchers.index')}}">Voucher</a></li>
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
                <div class="card-box">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4 class="header-title m-t-0">Create new voucher</h4>
                            <p class="text-muted font-14 m-b-20">
                                Fields with (<span class="text-danger">*</span>) are required.
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="code">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" parsley-trigger="change" required="required" placeholder="ex. 10%OFF" class="form-control" id="code">
                    </div>
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" parsley-trigger="change" required="required" placeholder="ex. 10% off" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="name">Description</label>
                        <textarea name="description" placeholder="ex. Holiday event" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 200px;"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="max_uses">Use limit </label>
                        <input type="number" name="max_uses" parsley-trigger="change" placeholder="ex. 100" class="form-control" id="max_uses">
                    </div>

                    <div class="form-group">
                        <label for="max_uses_user">Max. Use per User </label>
                        <input type="number" name="max_uses_user" parsley-trigger="change" placeholder="ex. 4" class="form-control" id="max_uses_user">
                    </div>

                    <div class="form-group">
                        <label for="is_fixed">Discount Fixation <span class="text-danger">*</span></label>
                        <select name="is_fixed" class="form-control" required="required">
                            <option value=""></option>
                            <option value=1>Amount</option>
                            <option value=0>Percentage</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="discount_amount">Discount Amount / Percentage <span class="text-danger">*</span></label>
                        <input type="text" name="discount_amount" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="discount_amount">
                    </div>

                    <div class="form-group">
                        <label for="min_amt_availability">Minimum Amount Capacity<span class="text-danger">*</span></label>
                        <input type="number"  name="min_amt_availability" v-model="voucher.min_amt_availability" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="min_amt_availability">
                    </div>
                    <div class="form-group">
                        <label for="max_amt_cap">Maximum Amount Capacity  <span class="text-danger">*</span></label>
                        <input type="number"  name="max_amt_cap" v-model="voucher.max_amt_cap" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="max_amt_cap">
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start date and time<span class="text-danger">*</span></label>
                        <input type="date" name="start_date" parsley-trigger="change" required="required" class="form-control" id="start_date">
                        <input type="time" name="start_time" parsley-trigger="change" required="required" class="form-control" id="start_time">
                    </div>
                    <div class="form-group mb-3">
                        <label for="expiry_date">Expiry date and time<span class="text-danger">*</span></label>
                        <input type="date" name="expiry_date" parsley-trigger="change" required="required" class="form-control" id="expiry_date">
                        <input type="time" name="expiry_time" parsley-trigger="change" required="required" class="form-control" id="expiry_time">
                    </div>
                    <div class="form-group">
                        <label for="is_enabled">Enabled <span class="text-danger">*</span></label>
                        <select name="is_enabled" class="form-control">
                            <option selected="selected" value=1>Yes</option>
                            <option value=0>No</option>
                        </select>
                    </div>

                    <input type="submit" class="btn btn-info" value="Submit">
                </div>
            </div>
        </form>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection

@section('bottom_scripts')

  
    @include('includes.vue-scripts')
    @include('includes.form-api')

@endsection