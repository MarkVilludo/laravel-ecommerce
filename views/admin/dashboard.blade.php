@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Welcome {{auth()->user()->first_name.' '.auth()->user()->last_name}}!</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">FS21</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="widget-simple-chart text-right card-box">
                        <div class="circliful-chart" data-dimension="90" data-text="35%" data-width="5" data-fontsize="14" data-percent="35" data-fgcolor="#5fbeaa" data-bgcolor="#505A66"></div>
                        <h3 class="text-success counter m-t-10">@{{ordersToday}}</h3>
                        <p class="text-muted text-nowrap m-b-10">Total Orders today</p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="widget-simple-chart text-right card-box">
                        <div class="circliful-chart" data-dimension="90" data-text="75%" data-width="5" data-fontsize="14" data-percent="75" data-fgcolor="#3bafda" data-bgcolor="#505A66"></div>
                        <h3 class="text-primary counter m-t-10">@{{activeCustomer}}</h3>
                        <p class="text-muted text-nowrap m-b-10">Active Customer</p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="widget-simple-chart text-right card-box">
                        <div class="circliful-chart" data-dimension="90" data-text="49%" data-width="5" data-fontsize="14" data-percent="49" data-fgcolor="#98a6ad" data-bgcolor="#505A66"></div>
                        <h3 class="text-inverse counter m-t-10">@{{pendingOrders}}</h3>
                        <p class="text-muted text-nowrap m-b-10">Pending Orders</p>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-8">
                    <div class="card-box">
                        <h4 class="header-title m-t-0">Latest Orders (@{{orders.length}})</h4>
                        <p class="text-muted m-b-25 font-13">
                            Proccessing order (s)
                        </p>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Item (s)</th>
                                    <th>Customer</th>
                                    <th>Placed Order</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="orders.length > 0" v-for="order in orders" >
                                    <td>@{{order.number}}</td>
                                    <td align="left">
                                        <div v-if="order.order_items.length != 0" v-for="order_item in order.order_items">
                                            <p>
                                                <span>
                                                    @{{order_item.item}}
                                                </span>
                                                <span v-bind:class="{'badge badge-warning':order_item.status.name =='Processing', 'badge badge-pink':order_item.status.name =='Cancelled', 'badge badge-info':order_item.status.name =='Delivered' , 'badge badge-success':order_item.status.name =='Complete'}">@{{order_item.status.name}}</span>
                                                <br>
                                                <small>
                                                    <label style="text-decoration: line-through;"> Php @{{order_item.regular_price}} </label>
                                                    <label>Php @{{order_item.selling_price}}</label><br>
                                                    <label>Quantity : @{{order_item.quantity}} </label>
                                                </small>
                                              
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <p>
                                            @{{order.user.first_name+ ' ' +order.user.last_name}}
                                        </p>
                                    </td>
                                    <td> @{{ order.created_at }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="row col-lg-12 text-right">
                                <ul class="pagination">
                                    <li v-show="prevPageOrders" v-if="prevPageOrders!==nextPageOrders" class="paginate_button page-item previous" id="responsive-datatable_previous" style="disabled: prevPageOrders ? 'true' : ''">
                                        <a href="#" aria-controls="responsive-datatable" data-dt-idx="0" tabindex="0" class="page-link"  @click="changePage(prevPageOrders)">Previous</a>
                                    </li>
                                    <li v-if="nextPageOrders" class="paginate_button page-item next" id="responsive-datatable_next">
                                        <a href="#" aria-controls="responsive-datatable" data-dt-idx="7" tabindex="0" class="page-link" @click="changePage(nextPageOrders)">Next</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -8 -->

                <div class="col-lg-4">
                    <div class="card-box">
                        <h4 class="header-title m-t-0">Products with minimal stocks (@{{products.length}})</h4>
                        <p class="text-muted m-b-25 font-13">
                            5 stocks remaining and below 
                        </p>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Variant / Stocks</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="products.length > 0" v-for="product in products" >
                                    <td>@{{product.name}}</td>
                                    <td>
                                        <div v-if="product.variants.length > 0" class="form-group row" v-for="variant in product.variants">
                                                <div class="col-lg-6">
                                                    <span v-if="variant.size"> size: @{{variant.size}}</span>
                                                    <span v-if="variant.color" v-bind:style="{background: variant.color}">@{{variant.color}}</span>
                                                </div>
                                            <div class="col-lg-6">
                                                <span> Stocks: @{{variant.inventory}}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="card-box">
                        <h4 class="header-title m-t-0">Latest Promos</h4>
                        <p class="text-muted m-b-25 font-13">
                            Available promos
                        </p>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Promo Name</th>
                                    <th>Validity Date From</th>
                                    <th>Validity Date To</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Minton Admin v1</td>
                                    <td>01/01/2017</td>
                                    <td>26/04/2017</td>
                                    <td><span class="badge badge-success">Published</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Minton Frontend v1</td>
                                    <td>01/01/2017</td>
                                    <td>26/04/2017</td>
                                    <td><span class="badge badge-success">Published</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Minton Admin v1.1</td>
                                    <td>01/05/2017</td>
                                    <td>10/05/2017</td>
                                    <td><span class="badge badge-pink">Unpublished</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Minton Frontend v1.1</td>
                                    <td>01/01/2017</td>
                                    <td>31/05/2017</td>
                                    <td><span class="badge badge-pink">Unpublished</span></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Minton Admin v1.3</td>
                                    <td>01/01/2017</td>
                                    <td>31/05/2017</td>
                                    <td><span class="badge badge-pink">Unpublished</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card-box">
                        <h4 class="header-title m-t-0">Activity Logs</h4>
                        <p class="text-muted m-b-25 font-13">
                            Recent Activities
                        </p>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Action</th>
                                    <th>Date / Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Minton Admin v1</td>
                                    <td>New user</td>
                                    <td>Registered new user</td>
                                    <td>2018-04-13 03:47:59</td>
                                </tr>
                                 <tr>
                                    <td>Minton Admin v1</td>
                                    <td>New user</td>
                                    <td>Registered new user</td>
                                    <td>2018-04-13 03:47:59</td>
                                </tr>
                                 <tr>
                                    <td>Minton Admin v1</td>
                                    <td>New user</td>
                                    <td>Registered new user</td>
                                    <td>2018-04-13 03:47:59</td>
                                </tr>
                                <tr>
                                    <td>Minton Admin v1</td>
                                    <td>New user</td>
                                    <td>Registered new user</td>
                                    <td>2018-04-13 03:47:59</td>
                                </tr>
                                <tr>
                                    <td>Minton Admin v1</td>
                                    <td>New user</td>
                                    <td>Registered new user</td>
                                    <td>2018-04-13 03:47:59</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
    </div>
    <!-- end container -->
         
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getDashBoardData('all',"{{route('api.dashboard')}}");
        },
        data: {
            ordersToday: 0,
            activeCustomer: 0,
            pendingOrders: 0,
            orders: [],
            products: [],
            promos: [],
            logs: [],
            //promo pagination
            firstPagePromo : '',
            nextPagePromo : '',
            prevPagePromo : '',
            lastPagePromo : '',
            //orders pagination
            firstPageOrders : '',
            nextPageOrders : '',
            prevPageOrders : '',
            lastPageOrders : '',
            //products pagination
            firstPageProducts : '',
            nextPageProducts : '',
            prevPageProducts : '',
            lastPageProducts : '',
            //logs paginations
            firstPageLogs : '',
            nextPageLogs : '',
            prevPageLogs : '',
            lastPageLogs : '',
            search: '',
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getDashBoardData(type,url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data)
                    this.orders = response.data.orders;
                    this.products = response.data.products;
                    //for promos
                    this.promos = response.data.promos.data;
                    if (this.promos.length > 0) {
                        this.noResultPromosFound = true;
                    } else {
                        this.noResultPromosFound = false;
                    }
                    this.firstPagePromo = response.data.promos.first_page_url;
                    this.nextPagePromo = response.data.promos.next_page_url;
                    this.prevPagePromo = response.data.promos.prev_page_url;
                    this.lastPagePromo = response.data.promos.last_page_url;


                    //for products
                    this.products = response.data.products.data;
                    if (this.products.length > 0) {
                        this.noResultProductsFound = true;
                    } else {
                        this.noResultProductsFound = false;
                    }
                    this.firstPageProducts = response.data.products.first_page_url;
                    this.nextPageProducts = response.data.products.next_page_url;
                    this.prevPageProducts = response.data.products.prev_page_url;
                    this.lastPageProducts = response.data.products.last_page_url;

                    //for orders
                    this.orders = response.data.orders.data;
                    if (this.orders.length > 0) {
                        this.noResultOrdersFound = true;
                    } else {
                        this.noResultOrdersFound = false;
                    }
                    this.firstPageOrders = response.data.orders.first_page_url;
                    this.nextPageOrders = response.data.orders.next_page_url;
                    this.prevPageOrders = response.data.orders.prev_page_url;
                    this.lastPageOrders = response.data.orders.last_page_url;


                    //for logs
                    this.logs = response.data.activity_logs.data;
                    if (this.logs.length > 0) {
                        this.noResultLogsFound = true;
                    } else {
                        this.noResultLogsFound = false;
                    }
                    this.firstPageLogs = response.data.activity_logs.first_page_url;
                    this.nextPageLogs = response.data.activity_logs.next_page_url;
                    this.prevPageLogs = response.data.activity_logs.prev_page_url;
                    this.lastPageLogs = response.data.activity_logs.last_page_url;

                    this.ordersToday = response.data.total_orders_today;
                    this.pendingOrders = response.data.total_proccessing_orders;
                    this.activeCustomer = response.data.active_customers;

                });
            },
            viewDetails(promo) {
                console.log(promo.id)
                window.location.href = '/promos/' + promo.id + '/edit';
            },
            changePage(type,url){
                if (url) {
                    this.getDashBoardData(type, url);
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
                axios.post("{{route('api.search.promo')}}", {'search' : this.search}, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.promos = response.data.data;
                    if (this.promos.length > 0) {
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
