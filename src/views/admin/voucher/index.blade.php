@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Voucher Codes</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Voucher Codes</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- //Main content page. -->
            <div class="row form-group inline">
                <div class="col-md-6 col-lg-6 pr-4 pt-4">
                    <div class="input-group pt-2">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="ion-search"></i></span>
                        </div>
                        <input type="text" class="form-control" v-model="search" placeholder="Search Voucher name.." id="datepicker"autocomplete="off" @change="filter()">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="input-group">
                        <div class="col-lg-12">
                            <label>Filter Date Range</label>
                            <span class="pull-right">Start Date</span>
                        </div>
                        <input type="date" name="start_date" v-model="start_date" @change="searchFunction()" class="form-control">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <div class="input-group">
                        <div class="col-lg-12">
                            <label>End Date</label><br>
                        </div>
                        <input type="date" name="end_date" v-model="end_date" @change="searchFunction()" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mb-3 row col-lg-2 col-lg-offset10">
                <a href="{{route('vouchers.create')}}">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Create Voucher</button>
                </a>
            </div>
            <div class="row col-lg-12">
            </div>
            <div class="row col-lg-12">
                <table class="table table-bordered table-striped" style="text-align: center">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Uses</th>
                            <th>Max. Uses</th>
                            <th>Max. use/user</th>
                            <th>Discount Amount</th>
                            <th>Starts At</th>
                            <th>Ends At</th>
                            <th>Max. Amt. Capacity</th>
                            <th>Min. Amt. Availability</th>
                            <th>Model</th>
                            <th>Enabled</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
              
                        <tr v-if="vouchers.length > 0" v-for="voucher in vouchers" >
                            <td> @{{voucher.status}}
                                <span v-bind:class="{ 'danger': voucher.status === 'expired', 'success': voucher.status === 'active'}">
                                </span>
                            </td>
                            <td>@{{voucher.code}}</td>
                            <td>@{{voucher.name}}</td>
                            <td>@{{voucher.description}}</td>
                            <td>@{{voucher.uses}}</td>
                            <td>@{{voucher.max_uses}}</td>
                            <td>@{{voucher.max_uses_user}}</td>
                            <td>@{{voucher.discount_amount}}</td>
                            <td>@{{voucher.starts_at}}</td>
                            <td>@{{voucher.expires_at}}</td>
                            <td>@{{voucher.max_amt_cap}}</td>
                            <td>@{{voucher.min_amt_availability}}</td>
                            <td>@{{voucher.model_name}}</td>
                            <td>@{{voucher.is_enabled ? 'Yes' : 'No'}}</td>
                            <td>
                                <button class="btn btn-primary w-sm" @click="viewDetails(voucher)">Edit</button>
                                <button class="btn btn-danger w-sm" @click="deleteVoucher(voucher)">Delete</button>
                            </td>
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
            this.getVouchers("{{route('api.vouchers')}}");
        },
        data: {
            vouchers: [],
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
            sortBy: '',
            sortOrder: '',
            start_date: '',
            end_date: '',
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getVouchers(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.vouchers = response.data.data;
                    if (this.vouchers.length > 0) {
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
            viewDetails(voucher) {
                console.log(voucher.id)
                window.location.href = "{{request()->root().'/vouchers/edit/' }}"+voucher.id;
            },
            deleteVoucher(voucher){
                swal({
                    title: 'Delete voucher',
                    text: "Are you sure you want to delete this voucher?",
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
                         axios.delete("{{url('/vouchers/delete')}}/"+voucher.id, {
                            headers: {
                                'Authorization': this.header_authorization,
                                'Accept': this.header_accept
                            }
                        })
                        .then((response) => {
                            swal("Success!",response.data.message, "success");
                            console.log(response.data)
                            setTimeout(function() {
                               window.location.reload(1);
                            }, 2000);
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
                    this.getVouchers(url);
                }
            },
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)

                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.vouchers = [];

                        this.getVouchers(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.vouchers = [];
                        this.getVouchers(this.nextPage);
                    } else if(!step){
                        this.vouchers = [];
                        this.getVouchers(this.path+'?page='+page+"&searchBy=name"+"&searchString="+this.search+"&start_date="+this.start_date+"&end_date="+this.end_date);
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
                axios.get("{{route('api.search.voucher')}}"+"?searchBy=name"+"&searchString="+this.search+"&start_date="+this.start_date+"&end_date="+this.end_date, 
                    {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.vouchers = response.data.data;
                    if (this.vouchers.length > 0) {
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
            }   
        },
    });
</script>
@endsection


