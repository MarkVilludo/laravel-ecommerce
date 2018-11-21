@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Customer's Detail</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Customers</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <!-- div for scroll -->
                <div class="zoom div-scroll">
                    <!-- //Main content page. -->
                    <div class="row mb-2">
                        <div class="col-lg-4">
                            <div class="portlet">
                                <div class="portlet-heading portlet-default mb-4">
                                    <h2 class="portlet-title text-dark">
                                        Personal Information
                                    </h2>
                                </div>
                                <div id="bg-default" class="panel-collapse collapse show">
                                    <div class="portlet-body">
                                        <p>
                                            <span> {{$customer->first_name .' '.$customer->last_name}}</span>
                                        </p>
                                        <p>
                                            <span>{{$customer->email}}</span>
                                        </p>
                                        <p> <label>Newsletter Subscription: </label>
                                            <span>{{$customer->subscribe ? 'ON' : 'OFF'}}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="portlet">
                                <div class="portlet-heading portlet-default">
                                    <h2 class="portlet-title text-dark">
                                        Addresses
                                    </h2>
                                </div>
                                <div id="bg-default" class="panel-collapse collapse show">
                                    <div class="portlet-body">
                                        @if ($customer->addresses)
                                            @foreach ($customer->addresses as $address)
                                                <p><span>{{$address->name}}</span></p>
                                                <p><span>{{$address->complete_address}}</span></p>
                                                <p>
                                                    <span>
                                                        {{$address->city->name.' '.$address->province->name.' '.$address->country->name}}
                                                    </span>
                                                </p>
                                                <p><span>{{$address->mobile_number}}</span></p>
                                                <hr>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet">
                        <div class="portlet-heading portlet-default">
                            <h3 class="portlet-title text-dark">
                                Recent Orders
                            </h3>
                            <div class="clearfix"></div>
                        </div>
                        <div id="bg-default" class="panel-collapse collapse show">
                            <div class="portlet-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Number</th>
                                        <th>Place on</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="orders.length > 0" v-for="order in orders" >
                                        <td>@{{order.number}}</td>
                                        <td>@{{ order.placed_on }}</td>
                                        <td align="left">
                                             <div v-if="order.order_items.length != 0" v-for="order_item in order.order_items">
                                                <p>
                                                    <span>
                                                        @{{order_item.item}} x @{{order_item.quantity}} = Php @{{order_item.total}}
                                                    </span>
                                                    <span v-bind:class="{'badge btn-primary':order_item.status.name =='Processing', 'badge btn-warning':order_item.status.name =='Shipping',  'badge btn-secondary':order_item.status.name =='Cancelled', 'badge btn-info':order_item.status.name =='Delivered' , 'badge badge-success':order_item.status.name =='Complete', 'badge btn-danger':order_item.status.name =='Return / Refund', 'badge badge-secondary':order_item.status.name =='For Replacement / Replacement'}">@{{order_item.status.name}}</span>
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
                                                   Php  @{{order.sub_total}} 
                                                </label>
                                                <label>Php @{{order.total_amount}}</label>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-sm"  v-on:click="viewDetails(order)">Manage</button>
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
                        </div>
                    </div>
                    <!-- End main content page -->
                </div>
                <!-- end div for scroll -->
            </template>
           
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
            this.getCustomerDetails("{{route('api.customer_orders_cms', $customer->id)}}");
        },
        data: {
            pagination: '',
            pages: '',
            from: '',
            to: '',
            offset: '',
            firstPage: '',
            nextPage: '',
            prevPage: '',
            lastPage: '',
            orders: [],
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            getCustomerDetails(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)

                this.orders = response.data.data;
                if (this.orders.length > 0) {
                    this.noResultFound = true;
                } else {
                    this.noResultFound = false;
                } 
                console.log(this.orders)

                this.pagination = response.data.meta;  
                //get last page item
                this.pages = response.data.meta.last_page;
                this.path = response.data.meta.path;

                this.firstPage = response.data.links.first;
                this.nextPage = response.data.links.next;
                this.prevPage = response.data.links.prev;
                this.lastPage = response.data.meta.last_page;
             

                }).catch(error => {
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });
            },
            isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                this.pagination.current_page = page;
                if (page) {
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.products = [];
                        this.getCustomerDetails(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.products = [];
                        this.getCustomerDetails(this.nextPage);
                    } else if(!step){
                        this.products = [];
                        this.getCustomerDetails(this.path+'?page='+page);
                    }
                }
            },
            viewDetails(order) {
                console.log(order.user.id)
                window.location.href = "{{request()->root().'/customers' }}/"+order.user.id +"/orders/"+order.id;
                // window.location.href = '/customers/' + order.user.id + '/orders/' + order.id;
            }
        },
    });
</script>
@endsection


<style type="text/css">
.zoom {
  zoom: 67%;
}
.div-scroll {
    overflow-x: hidden;
    height: 950px;
}

@media only screen and (max-width: 1285px) {
    .zoom {
      padding-bottom: 10rem;
    }
}

</style>