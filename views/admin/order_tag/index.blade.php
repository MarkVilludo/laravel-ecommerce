@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Order tags</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Tags</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            @if (Session::has('message'))
               <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            <!-- //Main content page. -->
            <div class="row col-lg-2 col-lg-offset-10">
                <a href="{{route('tags.create')}}">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Order Tag</button>
                </a>
            </div>
            <div class="row col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($orderTags)
                            @foreach ($orderTags as $tags)
                                <tr>
                                    <td>{{$tags->name}}</td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-success waves-effect waves-light">
                                                <i class="ion-gear-a"> </i>
                                            </button>
                                            <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(109px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a class="dropdown-item" href="{{route('tags.edit', $tags->id)}}">
                                                    <button class="btn btn-primary w-sm">Edit</button>
                                                </a>
                                                <a class="dropdown-item" href="#"> 
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['tags.destroy', $tags->id] ]) !!}
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
                {{$orderTags->links()}}
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
