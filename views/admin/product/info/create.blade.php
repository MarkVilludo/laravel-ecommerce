@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Add Product info</h4>
                    <ol class="breadcrumb float-right">
                        <a href="{{route('product.edit', $product->id)}}">
                            <li class="breadcrumb-item"> Product : "{{$product->name}}" /</li>
                        </a>
                        <li class="breadcrumb-item">Product info</li>
                        <li class="breadcrumb-item active">Create</li>
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
           
            {{-- @include ('errors.list') --}}
            {{ Form::open(array('url' => 'products/'.$product->id.'/info')) }}

            <div class="form-group">
                {{ Form::label('title', 'Title') }}
                {{ Form::text('title', null, array('class' => 'form-control')) }}
            </div>
            <div class="form-group">
                {{ Form::label('description', 'Description') }}
                {{ Form::textarea('description', null, array('class' => 'form-control')) }}
            </div>

            {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}

            {{ Form::close() }}

        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
@endsection
