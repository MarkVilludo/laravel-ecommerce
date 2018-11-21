@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Orders</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Orders</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            @if (Session::has('message'))
               <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            <template>
                <!-- //Main content page. -->
            <!--     <div class="row">
                    <div class="row col-lg-12 mb-4"> -->
                        <!-- get selected order items -->
                        <!-- <div class="col-md-2 col-lg-2 mt-2">
                             Selected items : @{{ selectedItems.length }}
                        </div>
                        <div class="col-md-2 col-lg-2 mt-2">
                            <div class="btn-group dropdown">
                            <button type="button" class="btn btn-success waves-effect waves-light">
                                <i class="ion-gear-a"> </i>
                            </button>
                            <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false" ><i class="caret"></i>Actions</button>
                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(109px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <li class="menu-title">Change status </li>

                                    <li class="progtrckr-todo">
                                        <a @click='changeMultipleStatus({{config('setting.ProcessingOrderStatus')}})'>
                                            <i class="mdi mdi-package-variant"></i> Processing</label>
                                        </a>
                                    </li>
                                    <li class="progtrckr-todo">
                                        <a @click='changeMultipleStatus({{config('setting.ShippedStatus')}})'>
                                            <i class="ti-truck"></i> Shipped</label>
                                        </a>
                                    </li>
                                   <li class="progtrckr-todo">
                                        <a @click='changeMultipleStatus({{config('setting.DeliveredStatus')}})'>
                                             <i class="fa fa-handshake-o"></i> Delivered</label>
                                        </a>
                                    </li>
                                   <li class="progtrckr-todo">
                                        <a @click='changeMultipleStatus({{config('setting.CompletedStatus')}})'>
                                            <i class="ti-check-box"></i> Completed</label>
                                        </a>
                                    </li>
                                    <li class="progtrckr-todo">
                                        <a @click='changeMultipleStatus({{config('setting.CancelledReturnStatus')}})'>
                                            <i class="fa fa-times"></i> Cancelled</label>
                                        </a>
                                    </li>
                                    <li class="menu-title">Manage tag </li>
                                    <li class="progtrckr-todo">
                                        <a href="javascript:void(0);">
                                            <i class="fa fa-tags"></i> Add Tag</label>
                                        </a>
                                    </li>
                                    <li class="progtrckr-todo">
                                        <a href="javascript:void(0);">
                                             <i class="fa fa-times"></i> Remove Tag</label>
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row form-group inline">
                    <div class="col-md-8 col-lg-8 pl-0">
                       <div class="col-sm-12">
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ion-search"></i></span>
                                </div>
                                <input type="text" class="form-control" v-model="search" placeholder="Search order number.." id="datepicker"autocomplete="off" @change="filter()">
                            </div>
                            <!-- input-group -->
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 pr-4">
                        <select class="form-control" v-model="status" v-on:change="filter()">
                            <option selected value="">Status</option>
                            <option v-if="orderStatus.length > 0" v-for="order_status in orderStatus" v-bind:value="order_status.id">@{{order_status.name}}</option>
                        </select>
                   </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                       <thead>
                            <tr>
                                <th>Number</th>
                                <th>Place on</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Order By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="items.length > 0" v-for="item in items" >
                                <td>@{{item.number}}</td>
                                <td>@{{ item.placed_on }}</td>
                                <td align="left">
                                     <div v-if="item.order_items.length != 0" v-for="order_item in item.order_items">
                                        <p>
                                            <span>
                                                @{{order_item.item}} x @{{order_item.quantity}} = Php @{{order_item.total}}
                                            </span>
                                            <span v-bind:class="{'badge btn-primary':order_item.status.name =='Processing', 'badge btn-warning':order_item.status.name =='Shipping',  'badge btn-secondary':order_item.status.name =='Cancelled', 'badge btn-info':order_item.status.name =='Delivered' , 'badge badge-success':order_item.status.name =='Complete', 'badge btn-danger':order_item.status.name =='Return / Refund', 'badge badge-secondary':order_item.status.name =='Replacement'}">@{{order_item.status.name}}</span>
                                            <span v-if="order_item.is_replacement" class="badge btn-secondary">Replacement</span>
                                            <br>
                                            <small>
                                                <label style="text-decoration: line-through;"> Php @{{order_item.regular_price}} </label> @{{order_item.discount}} % Off
                                                <label>Php @{{order_item.selling_price}}</label>
                                            </small>
                                          
                                        </p>
                                    </div>
                                </td>
                                <td>
                                        <label style="text-decoration: line-through;">
                                           Php  @{{item.sub_total}} 
                                        </label>
                                        <label>Php @{{item.total_amount}}</label>
                                </td>
                                <td>
                                    <p>
                                        @{{item.user.first_name+ ' ' +item.user.last_name}}
                                    </p>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm"  v-on:click="viewDetails(item)">Manage</button>
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
<script src="{{url('assets/js/vue-infinite-loading.js')}}"></script>
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getOrders("{{route('api.orders')}}");
            this.getOrderStatus();
        },
        data: {
            search: '',
            status: '',
            items: [],
            pagination: '',
            pages: '',
            from: '',
            to: '',
            offset: '',
            firstPage: '',
            nextPage: '',
            prevPage: '',
            lastPage: '',
            orderStatus: [],
            checkItems: [],
            isCheckAll: false,
            noResultFound : false,
            selectedItems: [],
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}',
        },
        methods: {
            filter() {
                if (this.search && this.search.length >= 3) {
                    this.searchFunction();
                } else {
                    this.searchFunction();
                }
            }, 
            searchFunction() {
                axios.get('{{route('api.search.orders')}}'+'?status='+this.status+'&search='+this.search,
                    {
                        headers: {
                            'Authorization': this.header_authorization,
                            'Accept': this.header_accept
                        }
                    }).then((response) => {

                    console.log(response.data.data)
                    this.items = response.data.data;
                    if (this.items.length > 0) {
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
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.products = [];
                        this.getOrders(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.products = [];
                        this.getOrders(this.nextPage);
                    } else if(!step){
                        this.products = [];
                        this.getOrders(this.path+'?page='+page+"&status="+this.status+"&search="+this.search);
                    }
                }
            },
            getOrders(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    console.log(response.data.data)
                    this.items = response.data.data;
                    if (this.items.length > 0) {
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
            getOrderStatus() {
                 axios.get("{{route('api.order_status')}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    console.log(response.data.data)
                    this.orderStatus = response.data.data

                });
            },
            changeFilter() {
                this.items = [];
                this.orderStatus = [];

            },
            viewDetails(item) {
                console.log(item.user.id)
                window.location.href = "{{request()->root().'/customers' }}/"+item.user.id +"/orders/"+item.id;
                // window.location.href = '/customers/' + item.user.id + '/orders/' + item.id;
            },
            checkAll (){
                console.log(this.isCheckAll)

              this.isCheckAll = !this.isCheckAll;
              this.checkItems = [];
              if(this.isCheckAll == true){ // Check all
                for (var key in this.checkItems) {
                  this.checkItems.push(this.items[key]);
                }
              }
            },
            changeMultipleStatus(status) {
                console.log(this.selectedItems)
                console.log(status)
                var itemsArray = this.selectedItems;
                if(this.selectedItems.length) {
                    swal({
                        title: 'Fulfill Orders',
                        text: "Are you sure you want to update status this selected order(s)?",
                        type: 'info',
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        confirmButtonColor: '#3c8dbc',
                        cancelButtonColor: '#3c8dbc',
                        confirmButtonText: 'Yes, Please!',
                        cancelButtonText: 'No, cancel!',
                        confirmButtonClass: 'btn btn-info',
                        cancelButtonClass: 'btn btn-grey',
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.value) {
                            console.log(itemsArray)
                             axios({
                                method: 'post',
                                url: "{{route('update-multiple.orders')}}",
                                data: {orders: itemsArray, status: status},
                                config: { 
                                    headers: {'Authorization': this.header_authorization,
                                        'Accept': this.header_accept
                                    }
                                }
                            })
                            .then(function (response) {
                                //handle success
                                console.log(response.data.message);
                                swal("Updated!",response.data.message, "success");
                            })
                            .catch(function (response) {
                                //handle error
                                console.log(response);
                                 swal("Failed!", response.data.message, "error");
                            });

                        }
                    })
                } else {
                    alert('Please select item.');
                }
            }
        },
    });
</script>
@endsection

