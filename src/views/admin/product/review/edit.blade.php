@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Edit Product Review</h4>
                    <ol class="breadcrumb float-right">
                        <a href="{{route('product.edit', $product->id)}}">
                            <li class="breadcrumb-item"> Product : "{{$product->name}}" /</li>
                        </a>
                        <li class="breadcrumb-item">Reviews</li>
                        <li class="breadcrumb-item active">Edit</li>
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
                    <label for="name">Rate<span class="text-danger">*</span></label>
                    <input type="number" v-model="rate" name="rate" max="5" min="1" parsley-trigger="change" required="required" placeholder="Rate" class="form-control" id="rate">
                </div>
                <div class="form-group">
                    <label for="name">Title<span class="text-danger">*</span></label>
                    <input type="text" v-model="title" name="title" parsley-trigger="change" required="required" placeholder="Title" class="form-control" id="title">
                </div>

                <div class="form-group">
                    <label for="description">Description<span class="text-danger">*</span></label>
                    <textarea v-model="description" name="description" parsley-trigger="change" required="required" placeholder="Description" class="form-control" id="description"> </textarea>
                </div>

                <div class="form-group text-right" style="padding-top: 20px">
                    <button class="btn btn-success waves-effect waves-light btn-block" type="buttom" @click="onUpdateProductReview()">
                                    Save
                    </button>
                </div>
            </div>
        </template>
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
        },
        data: {
            rate: "{{$review->rate}}",
            title: "{{$review->title}}",
            description: "{{$review->description}}",
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            onUpdateProductReview() {
                axios.post("{{request()->root().'/products/'}}"+{{$product->id}}+'/reviews/'+{{$review->id}}, {
                    rate: this.rate,
                    title: this.title,
                    description: this.description
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