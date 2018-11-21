@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Packages</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Packages</a></li>
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
            <div class="row form-group inline">
                <div class="col-md-12 col-lg-12 pr-4">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="ion-search"></i></span>
                        </div>
                        <input type="text" class="form-control" v-model="search" placeholder="Search Package name.." id="datepicker"autocomplete="off" @change="filter()">
                    </div>
                </div>
            </div>
            <div class="row col-lg-2 col-lg-offset10">
                <a href="{{route('package.create')}}">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Package</button>
                </a>
            </div>
            <div class="row col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Date/Time Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                           <tr v-if="packages.length > 0" v-for="package in packages">
                                <td>
                                    <img v-if="package.path" :src="package.path" style="width:120px">
                                    <label v-if="!package.path">No image available</label>
                                </td>

                                <td>@{{package.name}}</td>
                                <td>
                                    <span v-if="package.items.length > 0" v-for="item in package.items">
                                        @{{item.product.name}}, 
                                    </span>
                                </td>
                                <td>@{{package.category}}</td>
                                <td>Php @{{package.price}}</td>
                                <td>@{{package.created_at}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" @click="viewDetails(package)">Edit</button>
                                    <button class="btn btn-danger w-sm" @click="deletePackage(package)">Delete</button>
                                </td>
                           </tr>
                            <tr v-if="!noResultFound">
                                <td colspan="7"> No data found.</td>
                            </tr>
                    </tbody>
                </table>
                <div class="row col-lg-12 text-right">
                    <ul class="pagination">
                        <li v-show="prevPage" v-if="prevPage!==nextPage" class="paginate_button page-item previous" id="responsive-datatable_previous" style="disabled: prevPage ? 'true' : ''">
                            <a href="#" aria-controls="responsive-datatable" data-dt-idx="0" tabindex="0" class="page-link"  @click="changePage(prevPage)">Previous</a>
                        </li>
                        <li v-if="lastPage!==nextPage" class="paginate_button page-item next" id="responsive-datatable_next">
                            <a href="#" aria-controls="responsive-datatable" data-dt-idx="7" tabindex="0" class="page-link" @click="changePage(nextPage)">Next</a>
                        </li>
                    </ul>
                </div>
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
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getPackages("{{route('api.packages')}}");
        },
        data: {
            packages: [],
            package_items: [],
            firstPage: '',
            nextPage: '',
            prevPage: '',
            lastPage: '',
            search: '',
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getPackages(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.packages = response.data.data;
                    this.package_items = response.data.data.items;
                    if (this.packages.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.firstPage = response.data.links.first;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.links.last;
                });
            },
            viewDetails(package) {
                console.log(package.id)
                // console.log(window.location.href)
                window.location.href = "{{request()->root().'/packages/edit' }}/"+package.id;
            },
            deletePackage(package){
                swal({
                        title: 'Delete package',
                        text: "Are you sure you want to delete this package?",
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3c8dbc',
                        cancelButtonColor: '#3c8dbc',
                        confirmButtonText: 'Yes, Please!',
                        cancelButtonText: 'No, cancel!',
                        confirmButtonClass: 'btn btn-info',
                        cancelButtonClass: 'btn btn-grey',
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.value) {
                             axios.delete("{{url('/packages')}}/"+package.id, {
                                headers: {
                                    'Authorization': this.header_authorization,
                                    'Accept': this.header_accept
                                }
                            })
                            .then((response) => {
                                swal("Success!",response.data.message, "success");
                                console.log(response.data)
                            })
                            .catch(function (response) {
                                //handle error
                                console.log(response);
                                 swal("Failed!", response.data.message, "error");
                            });
                        }
                    });
            },
            changePage(url){
                if (url) {
                    this.getPackages(url);
                }
            },
            filter() {

                if (this.search && this.search.length >= 3) {
                    this.searchFunction();
                } else {
                    this.searchFunction();
                }
            },
            searchFunction() {
                axios.post("{{route('api.search.package')}}", {'search' : this.search}, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.packages = response.data.data;
                    if (this.packages.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.firstPage = response.data.links.first;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.links.last;
                });
            }   
        },
    });
</script>
@endsection

