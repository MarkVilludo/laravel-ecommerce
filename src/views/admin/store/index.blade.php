@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Stores</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Stores</a></li>
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
            <div class="row col-lg-12">
                <a href="{{route('store.create')}}" class="pull-right">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Store</button>
                </a>
            </div>
            <template>
                <div class="row form-group inline">
                    <div class="col-md-12 col-lg-12 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ion-search"></i></span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Search name.." id="datepicker"autocomplete="off" @change="filter()">
                        </div>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Store</th>
                                <th style="width: 60%">Address</th>
                                <th style="width: 20%">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="stores.length > 0" v-for="store in stores" >

                                <td>@{{store.name}}</td>
                                <td>@{{store.complete_address}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(store)"><i class="fa fa-pencil"></i> Edit</button>
                                    <button class="btn btn-danger w-sm" v-on:click="deleteStore(store)"><i class="fa fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                            <tr v-if="!noResultFound">
                                <td colspan="7"> No data found.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <ul class="pagination">
                                <li class="paginate_button page-item previous" id="datatable-editable_previous"><a href="#" aria-controls="datatable-editable" data-dt-idx="0" tabindex="0" class="page-link" :disabled="pagination.current_page == pagination.from" @click.prevent="changePage(pagination.current_page - 1,'previous')">Previous</a></li>
                                <li v-for="page in pages" class="paginate_button page-item active"><a href="#" :class="isCurrentPage(page) ? 'is-current' : ''" @click.prevent="changePage(page)" aria-controls="datatable-editable" data-dt-idx="1" tabindex="0" class="page-link">@{{page}}</a></li>
                                <li class="paginate_button page-item next" id="datatable-editable_next"><a href="#" aria-controls="datatable-editable" data-dt-idx="7" tabindex="0" class="page-link" :disabled="pagination.current_page >= pagination.last_page" @click.prevent="changePage(pagination.current_page + 1,'next')">Next</a></li>
                            </ul>
                        </div>
                    </div>
            </template>
            <!-- End main content page -->
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
            this.getStores("{{route('api.stores')}}");
        },
        data: {
            stores: [],
            path: [],
            pagination: '',
            pages: '',
            from: '',
            to: '',
            offset: '',
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
            getStores(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.stores = response.data.data;
                    if (this.stores.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.pagination = response.data.meta;  
                    //get last page item
                    this.pages = response.data.meta.last_page;
                    this.path = response.data.meta.path;

                    this.firstPage = response.data.links.first;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.meta.last_page;
                });
            },
            viewDetails(store) {
                console.log(store.id)
                window.location.href = "{{request()->root().'/stores/' }}"+store.id+"/edit";
            },
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.stores = [];
                        this.getStores(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.stores = [];
                        this.getStores(this.nextPage);
                    } else if(!step){
                        this.stores = [];
                        this.getStores(this.path+'?page='+page);
                    }
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
                axios.get('{{route('api.search.store')}}'+"?name="+this.search,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.stores = response.data.data;

                    //check if with data found
                    if (this.stores.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.firstPage = response.data.links.first;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.meta.last_page;
                });
            },
            deleteStore(store) {
                // console.log(store)
                swal({
                    title: 'Delete store',
                    text: "Are you sure you want to delete this store?",
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
                         axios.delete("{{url('api/v1/stores')}}/"+store.id, {
                            headers: {
                                'Authorization': this.header_authorization,
                                'Accept': this.header_accept
                            }
                        })
                        .then((response) => {
                            swal("Success!",response.data.message, "success");
                            setTimeout(function(){
                               window.location.reload(1);
                            }, 2000);
                        })
                        .catch(function (response) {
                            //handle error
                            console.log(response);
                             swal("Failed!", response.message, "error");
                        });
                    }
                });
            }
        },
    });
</script>
@endsection

