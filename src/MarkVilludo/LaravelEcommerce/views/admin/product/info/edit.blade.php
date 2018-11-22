@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Edit Product info</h4>
                    <ol class="breadcrumb float-right">
                        <a href="{{route('product.edit', $product->id)}}">
                            <li class="breadcrumb-item"> Product : "{{$product->name}}" /</li>
                        </a>
                        <li class="breadcrumb-item">Product Info</li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <!-- //Main content page. -->
        <div class='col-lg-6 col-lg-offset-6'>
            <template>
                <hr>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label>Title</label>
                   <input type="text" id="title" name="title" value="{{$info->title}}" class="form-control">
                </div>
                 <div class="form-group">
                    <label>Description</label>
                   <textarea id="description" name="description" class="form-control" rows="6">{{$info->description}}</textarea>
                </div>

                <input type="button" class="btn  btn-success" @click="onUpdateInfo()" value="Update">
            </template>
        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getStoreDetails();
        },
        data: {
          
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            onUpdateInfo() {
                axios.post("{{url('products/'.$product->id.'/info/'. $info->id)}}", {
                    title: $('#title').val(),
                    description: $('#description').val()
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
