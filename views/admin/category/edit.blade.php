@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row" table-responsive>
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"></i>Edit Category</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Category</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-sm-12 mb-">
                    <a href="{{route('sub_category.create', $category->id)}}">
                        <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5"> <i class="fa fa-plus"> </i> Add Sub Category</button>
                    </a>
                </div>
                  <!-- //Main content page. -->
                <div class='col-lg-4 col-lg-offset-4'>
                    <h3>Details</h3>
                    {{-- @include ('errors.list') --}}
                    {{ Form::model($category, array('route' => array('category.update', $category->id), 'method' => 'POST')) }}

                    <div class="form-group">
                        {{ Form::label('title', 'Name') }}
                        {{ Form::text('title', null, array('class' => 'form-control')) }}
                    </div>

                    <div class="form-group mb-2">
                        {{ Form::label('description', 'Description') }}
                        {{ Form::textarea('description', null, array('class' => 'form-control')) }}
                    </div>
                    {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}

                    {{ Form::close() }}    
                </div>
                <div class='col-lg-8 col-lg-offset-8'>
                    <h3>Sub Categories</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Title</td>
                                <td>Descriptions</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($category->subCategories)
                                @foreach ($category->subCategories as $sub_category)
                                    <tr>
                                        <td>{{$sub_category->title}}</td>
                                        <td>{{$sub_category->description}}</td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-success waves-effect waves-light">
                                                    <i class="ion-gear-a"> </i>
                                                </button>
                                                <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(109px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="{{route('sub_category.edit', $sub_category->id)}}">
                                                        <button class="btn btn-primary w-sm">Edit</button>
                                                    </a>
                                                    <a class="dropdown-item" href="#"> 
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['sub_category.delete', $sub_category->id] ]) !!}
                                                        {!! Form::submit('Delete', ['class' => 'btn w-sm btn-danger']) !!}
                                                        {!! Form::close() !!}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
                <!-- End main content page -->
        </div>
            <!-- end container -->
    </div>
     
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
