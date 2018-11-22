@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- {{url('/vouchers/update',$item->id)}} -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Vouchers</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{route('vouchers.index')}}">Voucher</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <h4 class="header-title m-t-0">Edit new voucher</h4>
                        <p class="text-muted font-14 m-b-20">
                            Fields with (<span class="text-danger">*</span>) are required.
                        </p>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="code">Code <span class="text-danger">*</span></label>
                    <input type="text" id="code" name="code" v-model="voucher.code" parsley-trigger="change" required="required" placeholder="ex. 10%OFF" class="form-control" id="code">
                </div>
                <div class="form-group">
                    <label for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name"  v-model="voucher.name" parsley-trigger="change" required="required" placeholder="ex. 10% off" class="form-control" id="name">
                </div>
                <div class="form-group">
                    <label for="name">Description</label>
                    <textarea name="description" v-model="voucher.description" placeholder="ex. Holiday event" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 200px;">{{$item->description}} <br>{{$item->fixed }}</textarea>
                </div>

                <div class="form-group">
                    <label for="max_uses">Use limit </label>
                    <input type="number" v-model="voucher.max_uses" name="max_uses" parsley-trigger="change" placeholder="ex. 100" class="form-control" id="max_uses">
                </div>

                <div class="form-group">
                    <label for="max_uses_user">Max. Use per User </label>
                    <input type="number"  name="max_uses_user" v-model="voucher.max_uses_user" parsley-trigger="change" placeholder="ex. 4" class="form-control" id="max_uses_user">
                </div>

                <div class="form-group">
                    <label for="is_fixed">Discount Fixation <span class="text-danger">*</span></label>
                    <select name="is_fixed" v-model="voucher.is_fixed" class="form-control" required="required">
                        <option value=""></option>
                        <option {{$item->is_fixed ? 'selected' : ''}} value=1>Amount</option>
                        <option {{!$item->is_fixed ? 'selected' : ''}} value=0>Percentage</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="discount_amount">Discount Amount / Percentage <span class="text-danger">*</span></label>
                    <input type="number"  name="discount_amount" v-model="voucher.discount_amount" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="discount_amount">
                </div>
                <div class="form-group">
                    <label for="min_amt_availability">Minimum Amount Capacity <span class="text-danger">*</span></label>
                    <input type="number"  name="min_amt_availability" v-model="voucher.min_amt_availability" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="min_amt_availability">
                </div>
                <div class="form-group">
                    <label for="max_amt_cap">Maximum Amount Capacity <span class="text-danger">*</span></label>
                    <input type="number"  name="max_amt_cap" v-model="voucher.max_amt_cap" parsley-trigger="change" placeholder="ex. 10" class="form-control" id="max_amt_cap">
                </div>
                <div class="form-group">
                    <label for="start_date">Start date and time<span class="text-danger">*</span></label>
                    <input type="date" value="{{$item->start_date}}" id="start_date" name="start_date" parsley-trigger="change" required="required" class="form-control" id="start_date">
                    <input type="time" value="{{$item->start_time}}" id="start_time" name="start_time" parsley-trigger="change" required="required" class="form-control" id="start_time">
                </div>
                <div class="form-group mb-3">
                    <label for="expiry_date">Expiry date and time<span class="text-danger">*</span></label>
                    <input type="date" value="{{$item->expiry_date}}" id="expiry_date" name="expiry_date"  parsley-trigger="change" required="required" class="form-control" id="expiry_date">
                    <input type="time" value="{{$item->expiry_time}}" id="expiry_time" name="expiry_time" parsley-trigger="change" required="required" class="form-control" id="expiry_time">
                </div>
                <div class="form-group">
                    <label for="is_enabled">Enabled <span class="text-danger">*</span></label>
                    <select name="is_enabled" v-model="voucher.is_enabled" class="form-control">
                        <option {{$item->is_enabled ? 'selected' : ''}} value=1>Yes</option>
                        <option {{!$item->is_enabled ? 'selected' : ''}} value=0>No</option>
                    </select>
                </div>

                <button class="btn btn-info" @click="onUpdateVoucher()">Submit</button>
            </div>
        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection

@section('bottom_scripts')

  
@include('includes.vue-scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getVOucherDetails();
        },
        data: {
            voucher: '',
            complete_address: '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            getVOucherDetails() {
                axios.get("{{route('api.vouchers.details', $item->id)}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                  this.voucher = response.data.data;
                }).catch(error => {
                    console.log(error.response.data.status)
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });
            },
            onUpdateVoucher() {
                console.log(this.voucher)
                axios.post("{{route('vouchers.update', $item->id)}}", {
                    code: this.voucher.code,
                    name: this.voucher.name,
                    description: this.voucher.description,
                    discount_amount: this.voucher.discount_amount,
                    min_amt_availability: this.voucher.min_amt_availability,
                    is_enabled: this.voucher.is_enabled,
                    is_fixed: this.voucher.is_fixed,
                    max_amt_cap: this.voucher.max_amt_cap,
                    max_uses: this.voucher.max_uses,
                    max_uses_user: this.voucher.max_uses_user,
                    start_date: $('#start_date').val(),
                    start_time: $('#start_time').val(),
                    expiry_date: $('#expiry_date').val(),
                    expiry_time: $('#expiry_time').val()
                },{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data)
                    swal({ title: 'Success!', text: response.data.message, type: 'success',
                     confirmButtonText: 'Ok'
                   });

                }).catch(error => {
                    console.log(error.response.data.status)
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });
            }
        },
    });
</script>
@endsection