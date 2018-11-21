@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Add Order Status</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{route('status.index')}}">Status</a></li>
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

            {{ Form::open(array('url' => 'status')) }}

            <div class="form-group">
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
            </div>

            {{ Form::submit('Save', array('class' => 'btn btn-success')) }}

            {{ Form::close() }}

        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection
