@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Order Status</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Status</a></li>
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
                <a href="{{route('status.create')}}">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Order Status</button>
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
                        @if ($orderStatus)
                            @foreach ($orderStatus as $status)
                                <tr>
                                    <td>{{$status->name}}</td>
                                    <td>
                                        <div class="row">
                                            <div>
                                                <a href="{{route('order_status.edit', $status->id)}}">
                                                    <button class="btn btn-primary w-sm">Edit</button>
                                                </a>
                                            </div>
                                            <div class="col-lg-2 col-md-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['order_status.destroy', $status->id] ]) !!}
                                                {!! Form::submit('Delete', ['class' => 'btn w-sm btn-danger']) !!}
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                       
                                        
                                       
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>

                </table>
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
