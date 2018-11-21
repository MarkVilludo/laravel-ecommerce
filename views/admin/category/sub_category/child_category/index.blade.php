@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Categories</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Categories</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- //Main content page. -->
            <div class="row col-lg-2 col-lg-offset-10">
                <a href="{{route('category.create')}}">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Category</button>
                </a>
            </div>
            <div class="row col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date/Time Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($categories as $category)
                            <tr>

                                <td>{{$category->title}}</td>
                                <td>{{$category->description}}</td>
                                <td>{{$category->status}}</td>
                                <td>{{ $category->created_at->format('F d, Y h:ia') }}</td>
                                <td>

                                    <div class="btn-group dropdown">
                                        <button type="button" class="btn btn-success waves-effect waves-light">
                                            <i class="ion-gear-a"> </i>
                                        </button>
                                        <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(109px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="{{route('category.edit', $category->child_sub_category_id)}}">
                                                <button class="btn btn-primary w-sm">Edit</button>
                                            </a>
                                            <a class="dropdown-item" href="#"> 
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $category->id] ]) !!}
                                                {!! Form::submit('Delete', ['class' => 'btn w-sm btn-danger']) !!}
                                                {!! Form::close() !!}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
                {{ $categories->links() }}
            </div>
            <!-- End main content page -->
            <!-- end row -->
            <!-- end container -->
        </div>
    <!-- end content -->
    </div>

@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
