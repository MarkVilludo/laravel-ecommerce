@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Wishlist</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Wishlist</a></li>
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
            <template>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Products</th>
                                <th>Customer</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="wishlists.length > 0" v-for="wishlist in wishlists" >
                                <td>
                                    <p>
                                        @{{wishlist.product.name}}
                                    </p>
                                    <span>
                                        <small>Regular Price: @{{wishlist.product.regular_price}}</small>
                                        <small>Selling Price: @{{wishlist.product.selling_price}}</small>
                                    </span>
                                </td>
                                <td>@{{wishlist.customer.first_name + ' ' +wishlist.customer.last_name}}</td>
                                <td>
                                    @{{wishlist.created_at}}
                                </td>
                                <td>
                                    <button class="btn btn-danger w-sm" v-on:click="deleteWishlist(wishlist)"> <i class="fa fa-trash"></i> Delete</button>
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
            this.getWishlist("{{route('api.wishlist')}}");
        },
        data: {
            wishlists: [],
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
            getWishlist(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.wishlists = response.data.data;
                    if (this.wishlists.length > 0) {
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
            deleteWishlist(wishlist){
                swal({
                        title: 'Delete wishlist',
                        text: "Are you sure you want to delete this wishlist?",
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
                             axios.delete("{{url('/wishlists')}}/"+wishlist.id, {
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
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.wishlists = [];
                        this.getWishlist(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.wishlists = [];
                        this.getWishlist(this.nextPage);
                    } else if(!step){
                        this.wishlists = [];
                        this.getWishlist(this.path+'?page='+page);
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
                    this.wishlists = response.data.data;

                    //check if with data found
                    if (this.wishlists.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
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

