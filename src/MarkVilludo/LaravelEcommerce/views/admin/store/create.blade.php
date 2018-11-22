@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Add Store</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{route('stores.index')}}">Store</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <template>
            <!-- //Main content page. -->
            <div class='col-lg-6 col-lg-offset-6'>
                <hr>
                <div class="form-group">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" v-model="name" name="name" parsley-trigger="change" required="required" placeholder="Name" class="form-control" id="name">
                </div>

                <div class="form-group">
                    <label for="complete_address">Complete Address<span class="text-danger">*</span></label>
                    <textarea v-model="complete_address" name="complete_address" parsley-trigger="change" required="required" placeholder="Complete Address" class="form-control" id="complete_address"> </textarea>
                </div>

                <div class="form-group text-right" style="padding-top: 20px">
                    <button class="btn btn-success waves-effect waves-light btn-block" type="buttom" @click="onSaveStores()">
                                    Save
                    </button>
                </div>
            </div>
        </template>
        <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
        },
        data: {
            name: '',
            complete_address: '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            onSaveStores() {
                axios.post("{{route('api.stores.save')}}", {
                    name: this.name,
                    complete_address: this.complete_address
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

                  this.name = '';
                  this.complete_address = '';

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
