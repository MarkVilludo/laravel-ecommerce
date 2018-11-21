@extends('layouts.app')
@section('content')

<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title"></i>Add Sub Category</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{route('category.edit',$category->id)}}">Sub  Category</a></li>
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
            <form action="{{url('categories/sub_categories/'.$category->id.'/store')}}" novalidate="" method="post">         
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label>Title</label>
                       <input type="text" name="title" class="form-control">
                    </div>

                 <div class="form-group">
                  <input type="hidden" name="category_id" value="{{$category->id}}">
                </div>
                 <div class="form-group">
                        <label>Dscriptions</label>
                       <textarea name="description" class="form-control"></textarea>
                    </div>
                <input type="submit" class="btn btn-primary" name="submit">
            </form>
        </div>
         <!-- End main content page -->
        <!-- end row -->
<!-- end container -->
<!-- end content -->
@endsection
