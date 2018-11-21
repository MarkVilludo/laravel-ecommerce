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
            <hr>
            @if (Session::has('message'))
               <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            <form action="{{url('products/'.$product->id.'/faqs/'. $faq->id)}}" novalidate="" method="post">         
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label>Title</label>
                   <input type="text" name="title" value="{{$faq->title}}" class="form-control">
                </div>
                 <div class="form-group">
                    <label>Description</label>
                   <textarea name="description" class="form-control" rows="6">{{$faq->description}}</textarea>
                </div>

                <input type="submit" class="btn  btn-success" value="Update">
            </form>
        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
@endsection
