@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <template>
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Products</h4>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="#">Products</a></li>
                                <li class="breadcrumb-item active">List</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- //Main content page. -->
                <div class="row pb-2">
                    <div class="col-lg-2 pr-0">
                        <a class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-left" href="{{url('/products/create')}}"><i class="fa fa-plus"> </i> Add Product</a>
                    </div>
                    <div v-show="loading" class="col-lg-10 text-right">
                        <h4>
                            <i class="fa fa-spinner fa-spin"></i>
                            Loading data ..
                        </h4>
                    </div>
                </div>
                <div class="row col-lg-2 col-lg-offset8 pb-2">
                </div>
                 <div class="row mb-2 text-center pr-4">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="search" @change="filter()" placeholder="Search Product Name.." aria-label="Username" aria-describedby="basic-addon1">
                             <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <a href="#" @click="filter()"> <i class="fa fa-search"></i> </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2 text-center pr-4">
                    <div class="col-lg-2 mb-1 pt-2">
                        <label>Filter: </label>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <select class="form-control" v-model="category" v-on:change="searchFunction(category,color,price)">
                            <option selected value="">Category</option>
                            <option v-if="categories.length > 0" v-for="category in categories" v-bind:value="category.id">@{{category.title}}</option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <select class="form-control" v-model="color" v-on:change="searchFunction(category,color,price)" v-bind:style="{background: color}">
                            <option selected value="">Color</option>
                            <option v-if="variants.length > 0" v-for="variant in variants" v-bind:value="variant.color" v-bind:style="{background: variant.color}"></option>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-2">
                        <select class="form-control" v-model="price" v-on:change="searchFunction()">
                            <option selected value="">Price</option>
                            <option value="1-500">Under Php 500</option>
                            <option value="500-1500">Php 500 - Php 1,500</option>
                            <option value="1501-5000">Php 1,500 - above</option>
                        </select>
                    <!--     <button class="btn btn-primary btn-sm btn-block">
                            <i class="fa fa-plus"></i> Add pricing options</button> -->
                    </div>
                    <div class="col-lg-2 mb-1">
                        <select class="form-control" v-model="sortBy" v-on:change="searchFunction()">
                            <option selected value="">SortBy</option>
                            <option v-if="fields.length > 0" v-for="field in fields" v-bind:value="field.value">@{{field.title}}</option>
                        </select>
                    </div>
                     <div class="col-lg-2 mb-1">
                        <select class="form-control" v-model="orderBy" v-on:change="searchFunction()">
                            <option v-if="orders.length > 0" v-for="order in orders" v-bind:value="order.value">@{{order.title}}</option>
                        </select>
                    </div>
               </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Image</th>
                                <th>Product</th>
                                <th>Variants</th>
                                <th>Inventory</th>
                                <th>Price</th>
                                <th>Most Picks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="products.length" v-for="product in products">
                                <td> 
                                    <img v-if="product.default_image" :src="product.default_image" class="rounded-square img-thumbnail">
                                    <label v-if="!product.default_image">No image available</label>
                                </td>
                                <td>
                                    @{{product.name}}
                                    <p v-if="product.category">
                                        <small>(@{{product.category.title}})</small>
                                    </p>
                                </td> 
                                <td>
                                    <div class="form-group" v-for="variant in product.variants">
                                        <span v-if="variant.colors" v-for="color in variant.colors" v-bind:style="{background: color}" class="pr-2 pl-2">
                                           @{{color}}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group" v-for="variant in product.variants">
                                        @{{variant.inventory ? variant.inventory : 'Unavailable'}}
                                    </div>
                                </td>
                                <td>
                                    <strike>Php @{{product.regular_price}}</strike> <br>
                                    Php @{{product.selling_price}}
                                </td>
                                <td>@{{product.featured ? 'YES' : 'NO'}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(product)"><i class="fa fa-pencil"></i> Edit</button>
                                    <button class="btn btn-danger w-sm" v-on:click="deleteProduct(product)"><i class="fa fa-trash"></i> Delete</button>
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
                </div>
                <!-- End main content page -->
                <!-- end row -->
                <!-- end container -->
            </div>
        </template>
    <!-- end content -->
    </div>
@endsection

@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<!-- template for the modal component -->
<script type="x/template" id="modal-template">
  <div class="modal-mask" v-show="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container">

        <div class="modal-header">
          <slot name="header">
            default header
          </slot>
        </div>
        
        <div class="modal-body">
          <slot name="body">
            default body
          </slot>
        </div>

        <div class="modal-footer">
          <slot name="footer">
            default footer
            <button class="modal-default-button"
              @click="show = false">
              OK
            </button>
          </slot>
        </div>
      </div>
    </div>
  </div>
</script>
<script>
    

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getProducts("{{route('api.products')}}");
            this.getCategories();
        },
        data: {
            loading: false,
            categories: [],
            variants: [],
            products: [],
            fields: [
                    {'value': 'is_new_arrival', 'title': 'New Arrival'},
                    {'value': 'ratings', 'title': 'Ratings'},
                    {'value': 'price', 'title': 'Price'}
            ],
            orders: [
                        {'value': 'desc', 'title': 'Descending'},
                        {'value': 'asc', 'title': 'Ascending'}
            ],
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
            searchString : '',
            search: '',
            noResultFound : false,
            color: '',
            price: '',
            category : '',
            sortBy: 'ratings',
            orderBy: 'asc',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getProducts(url) {
                this.loading = true;
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    
                    this.loading = false;
                    this.products = response.data.data;
                    if (this.products.length > 0) {
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
            getCategories() {
                axios.get("{{route('api.categories')}}")
                .then((response) => {
                    console.log(response.data.data)
                    this.categories = response.data.data;
                });
            },
            getVariants(categoryId) {
                this.variants = [];
                axios.get("{{request()->root().'/api/v1/categories/' }}"+categoryId+'/available_colors')
                .then((response) => {
                    console.log(response.data.data)
                    this.variants = response.data.data;
                });
            },
            viewDetails(product) {
                console.log(product.id)
                // console.log(window.location.href)
                window.location.href = "{{request()->root().'/products/edit' }}/"+product.id;

            },
            deleteProduct(product){
                swal({
                        title: 'Delete product',
                        text: "Are you sure you want to delete this product?",
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
                             axios.delete("{{url('api/v1/products')}}/"+product.id, {
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
                            .catch(error => {
                                console.log(error.response.data.errors)
                                swal("Failed!",  JSON.stringify(error.response.data.message), "info");
                            });
                        }
                    });
            },
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                console.log(this.sortBy)
                console.log(this.orderBy)
                var category = this.category;
                var color = this.color;
                var newStrColor = color.replace('#', '');
                var price = this.price;

                if(category) {
                    this.getVariants(category);
                }

                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.products = [];

                        this.getProducts(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.products = [];
                        this.getProducts(this.nextPage);
                    } else if(!step){
                        this.products = [];
                        this.getProducts(this.path+'?page='+page+"&category="+category+"&color="+newStrColor+"&price="+price+"&sortBy="+this.sortBy+"&orderBy="+this.orderBy);
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
                var category = this.category;
                var color = this.color;
                var newStrColor = color.replace('#', '');
                var price = this.price;

                if(category) {
                    this.getVariants(category);
                }
                axios.get('{{route('api.search.products')}}'+"?name="+this.search+"&category="+category+"&color="+newStrColor+"&price="+price+"&sortBy="+this.sortBy+"&orderBy="+this.orderBy,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.products = response.data.data;
                    if (this.products.length > 0) {
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
            }
        },
    });
</script>
<style type="text/css">
    .color {
        height:40px;
    }
    .pagination {
        margin-top: 40px;
    }
</style>
@endsection

