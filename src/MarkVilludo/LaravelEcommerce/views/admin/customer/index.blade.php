@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Customers</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Customers</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <!-- //Main content page. -->
                <div class="row form-group inline">
                    <div class="col-md-4 col-lg-4 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ion-search"></i></span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Search name.." id="datepicker"autocomplete="off" @change="filter()">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Sort By:</span>
                            </div>
                            <select v-model="sortBy" name="sortBy" class="form-control" @change="searchFunction()">
                                <option value="first_name">Name</option>
                                <option value="email">Email</option>
                                <option value="created_at">Date/Time Added</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Order By:</span>
                            </div>
                            <select v-model="orderBy" name="orderBy" class="form-control" @change="searchFunction()">
                                <option value="asc">Accending</option>
                                <option value="desc">Descending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped" style="text-align: center;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date/Time Added</th>
                                <th>Orders Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           <tr v-if="customers.length > 0" v-for="customer in customers">

                                <td>@{{customer.first_name +' '+customer.last_name}}</td>
                                <td>@{{customer.email}}</td>
                                <td>@{{customer.created_at }}</td>
                                <td>@{{customer.orders_count }}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" @click="viewDetails(customer)">View</button>
                                    <button v-if="!customer.status" class="btn btn-success w-sm" @click="deactivateCustomer(customer)">Activate</button>
                                    <button v-if="customer.status" class="btn btn-danger w-sm" @click="deactivateCustomer(customer)">Deactivate</button>
                                </td>
                            </tr>
                            <tr v-if="!noResultFound">
                                <td colspan="7"> No data found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
            this.getCustomers("{{route('api.customers')}}");
        },
        data: {
            customers: [],
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
            orderBy: 'asc',
            sortBy: '',
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            getCustomers(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.customers = response.data.data;
                    if (this.customers.length > 0) {
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
            viewDetails(customer) {
                // console.log(customer.id)
                window.location.href = "{{request()->root().'/customers/details' }}/"+customer.id;
            },
            deactivateCustomer(user){
                swal({
                        title: 'Deactivate account',
                        text: "Are you sure you want to deactivate account this customer?",
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
                             axios.post("{{url('/api/v1/deactivate/users')}}/"+user.id, {status: user.status}, {
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
            filter() {

                if (this.search && this.search.length >= 3) {
                    this.searchFunction();
                } else {
                    this.searchFunction();
                }
            },
            searchFunction() {
                axios.get("{{route('api.search.customer')}}"+"?name="+this.search+"&orderBy="+this.orderBy+"&sortBy="+this.sortBy
                    ,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.customers = response.data.data;
                    if (this.customers.length > 0) {
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
                    this.lastPage = response.data.links.last_page;
                });
            },
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.customers = [];
                        this.getCustomers(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.customers = [];
                        this.getCustomers(this.nextPage);
                    } else if(!step){
                        this.customers = [];
                        this.getCustomers(this.path+'?page='+page+"&sortBy="+this.sortBy+"&orderBy="+this.orderBy);
                    }
                }
            }
        }
      
    });
</script>
<style>
    .pagination {
        margin-top: 40px;
    }
</style>
@endsection
