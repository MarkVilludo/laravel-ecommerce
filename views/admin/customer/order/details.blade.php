@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content" tabindex="5000">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Order Details</h4>
                        <ol class="breadcrumb float-right">
                            @if(url()->previous() == url('/orders'))
                                <li class="breadcrumb-item"><a href="{{route('orders.index')}}">Orders</a></li>
                                <li class="breadcrumb-item active">Details</li>
                            @else
                                <li class="breadcrumb-item active"><a href="{{ URL::previous() }}">Customer Details</a></li>
                                <li class="breadcrumb-item active">Order Details</li>
                            @endif
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template >
                <!-- //Main content page. -->
                <div class="col-lg-12 mygrid-wrapper-div zoom">
                    <div class="portlet">
                        <div class="portlet-heading portlet-default">
                            <p class="pb-0 pt-0">
                               Placed on: @{{ order.placed_on}}
                            </p>
                            <div class="row">
                                <div class="col-md-9 col-lg-9">
                                    <h4 class="portlet-title text-dark">
                                        Order  # @{{order.number}}
                                    </h4>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <label class="label pull-right">Total: Php @{{ order.total_amount}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet" v-if="order_items.length > 0" v-for="(item,index) in order_items">
                        <div class="col-md-12 col-lg-12">
                            <div>
                                <div id="bg-default" class="panel-collapse">
                                    <div class="portlet-heading">
                                        <p>
                                            Standard Shipping @{{item.standard_shipping_days}} 
                                            <a href="#" @click="editShippingDays(index, true)" alt="edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <div class="form-group row" v-bind:id="'shippingItemDays_'+index" data-file_name="'shippingItemDays_'+index" style="display: none">
                                                <label class="control-label col-sm-1">Edit Shipping Days</label>
                                                <div class="col-sm-2">
                                                    <div class="input-group">
                                                        <input type="date" v-bind:id="'shippingItemCalendar_'+index" class="form-control" placeholder="mm/dd/yyyy">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="ion-calendar"></i></span>
                                                        </div>
                                                    </div><!-- input-group -->
                                                    <button class="btn btn-sm btn-primary" @click="onUpdateShippingDays(item.id, index)">Save</button>
                                                    <button class="btn btn-sm btn-danger" @click="cancelEditShippingDays(index)">Cancel</button>
                                                </div>
                                            </div>
                                        </p>

                                        <h3 class="portlet-title text-dark"> 
                                            @{{item.item}} x @{{item.quantity}} = Php @{{item.total}}
                                            <p>
                                                <label style="text-decoration: line-through;"> Php @{{formatPrice(item.regular_price)}} </label> @{{item.discount}} % Off
                                                <label>Php @{{formatPrice(item.selling_price)}}</label>


                                                <span v-bind:class="{'badge btn-primary':item.status.name =='Processing', 'badge btn-warning':item.status.name =='Shipping',  'badge btn-secondary':item.status.name =='Cancelled', 'badge btn-info':item.status.name =='Delivered' , 'badge badge-success':item.status.name =='Complete', 'badge btn-danger':item.status.name =='Return / Refund', 'badge badge-secondary':item.status.name =='Replacement'}">@{{item.status.name}}</span>
                                                <span v-if="item.is_replacement" class="badge btn-secondary">Replacement</span>
                                            </p>
                                        </h3>
                                        <!-- v-if="item.status.name !=='Cancelled'" -->
                                        <div class="portlet-widgets">
                                            <div class="input-group-prepend">
                                               
                                                <div class="col-md-12 col-lg-12">
                                                    <span class="pull-right">
                                                       <div class="btn-group btn-dropdown pull-right">
                                                            <button type="button" class="btn btn-success waves-effect waves-light">
                                                                <h3 class="portlet-title text-dark mr-4">
                                                                    Change Status
                                                                </h3>
                                                            </button>
                                                            <button type="button" class="btn btn-success dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(109px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                <ul v-for="status in orderStatus">
                                                                    <li>
                                                                        <a href="#" @click="changeStatus(item.id, status.name,status.id)"> 
                                                                           @{{status.name}}</label>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row col-lg-12 ml-4">
                                            <ol class="progtrckr" data-progtrckr-steps="4">
                                                <li v-bind:class="{'progtrckr-done' : item.status.name =='Processing' || item.status.name =='Ready to ship' || item.status.name =='Shipping' || item.status.name =='Delivered' || item.status.name =='Complete', 'progtrckr-todo': item.status.name ==='Cancelled'}">
                                                    <h3>
                                                        <i class="mdi mdi-package-variant"></i>
                                                    </h3>
                                                    Processing
                                                    
                                                </li>
                                                <li v-bind:class="{'progtrckr-done' : item.status.name =='Shipping' || item.status.name =='Delivered' || item.status.name =='Complete', 'progtrckr-todo': item.status.name ==='Processing' || item.status.name ==='Cancelled'}">
                                                    <h3>
                                                        <i class="ti-truck"></i>
                                                    </h3>
                                                    Shipping
                                                </li>
                                                <li v-bind:class="{'progtrckr-done': item.status.name ==='Delivered' || item.status.name =='Complete', 'progtrckr-todo': item.status.name ==='Shipping' || item.status.name ==='Cancelled' || item.status.name ==='Processing'}">
                                                    <h3>
                                                        <i class="fa fa-handshake-o"></i>
                                                    </h3>
                                                    Delivered
                                                </li>
                                                    <li v-bind:class="{'progtrckr-done': item.status.name =='Complete', 'progtrckr-todo':  item.status.name ==='Processing' || item.status.name ==='Shipping' || item.status.name ==='Delivered' || item.status.name ==='Cancelled'}">
                                                    <h3>
                                                        <i class="ti-check-box"></i>
                                                    </h3>
                                                    Complete
                                                </li>
                                                <span v-show="item.status.name ==='Cancelled'">
                                                    </li>
                                                        <li v-bind:class="{'progtrckr-cancelled': item.status.name ==='Cancelled'}">
                                                        <h3>
                                                            <i class="fa fa-close"></i>
                                                        </h3>
                                                        Cancelled
                                                    </li>
                                                </span>
                                            </ol>
                                        </div>
                                    </div>
                                    <!-- //reason for cancellation -->
                    
                        <!--             <div v-if="cancelReason" v-bind:id="item.id">
                                        <div id="bg-default" class="panel-collapse">
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-6">
                                                        <h4 class="portlet-title text-dark">
                                                            Reason for cancellation
                                                        </h4>
                                                        <textarea v-model='reason' class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 158px; width: 900px;"></textarea>
                                                        <button class="btn btn-info"  @click="changeStatus('Cancelled',{{config('setting.CancelledReturnStatus')}})">Confirm</button>
                                                        <button class="btn btn-danger" @click="closeCancelOrder()">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- // order item notes-->
                                    <div class="row mb-2">
                                       <div class="col-md-10 col-lg-10">
                                            <h4 class="portlet-title text-dark">
                                                <table class="table">
                                                    <tbody>
                                                        <tr v-if="item.notes.length > 0" v-for="note in item.notes">
                                                            <td style="width: 30%">@{{ note.created_at }}</td>
                                                            <td style="width: 70%">@{{note.notes}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </h4>
                                        </div>
                                        <div class="col-lg-2 col-md-2 mt-4 mb-4">
                                            <button class="btn btn-sm btn-primary"  @click="createNote(item)"> <i class="fa fa-plus"></i> Add note</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                          
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="portlet">
                                        <div id="bg-default" class="panel-collapse">
                                            <div class="portlet-body">
                                                <h4 class="portlet-title text-dark">
                                                    Shipping address
                                                </h4>
                                                <div v-if="order.shipping_address">
                                                    <p><span>@{{order.shipping_address.name}}</span></p>
                                                    <p><span>@{{order.shipping_address.complete_address}}</span></p>
                                                    <p>
                                                        <span>
                                                            @{{order.shipping_address.city.name+' '+order.shipping_address.province.name+' '+order.shipping_address.country.name}}
                                                        </span>
                                                    </p>
                                                    <p><span>@{{order.shipping_address.mobile_number}}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="portlet">
                                        <div id="bg-default" class="panel-collapse">
                                            <div class="portlet-body">
                                                <h4 class="portlet-title text-dark">
                                                    Billing address
                                                </h4>
                                                <div v-if="order.billing_address">
                                                    <p><span>@{{order.billing_address.name}}</span></p>
                                                    <p><span>@{{order.billing_address.complete_address}}</span></p>
                                                    <p>
                                                        <span>
                                                            @{{order.billing_address.city.name+' '+order.billing_address.province.name+' '+order.billing_address.country.name}}
                                                        </span>
                                                    </p>
                                                    <p><span>@{{order.billing_address.mobile_number}}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="portlet">
                                        <div id="bg-default" class="panel-collapse">
                                            <div class="portlet-body">
                                                <h4 class="portlet-title text-dark">
                                                    Total Summary
                                                </h4>
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span>Subtotal</span></p>
                                                        <p><span>Shipping Fees</span></p>
                                                        <p><span>Promotions</span></p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span> </span>
                                                            Php @{{order.sub_total}}
                                                            <br>
                                                            </p>
                                                        <p><span> Php @{{order.shipping_fee}}</span></p>
                                                        <p><span> Php 0.00</span></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span>Discount</span></p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span> Php @{{formatPrice(order.discount)}}</span></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span>Total (VAT Applied where applicable)</span></p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-6">
                                                        <p><span> Php @{{order.total_amount}}</span></p>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <!-- End main content page -->
            <!-- end row -->
        <!-- end container -->
        <!-- end content -->
        </div>
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
            this.getOrdersDetails();
            this.getOrderStatus();
        },
        data: {
            status: '',
            order: '',
            order_status: '',
            order_items: [],
            notes: [],
            orderStatus: [],
            cancelReason : false,
            ShowCancelIcon : false,
            reason : '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}',
        },
        methods: {
            getOrdersDetails() {
                axios.get("{{route('api.order_details', $orderId)}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    // console.log(response.data.order);
                    this.order = response.data.order;
                    console.log(this.order)
                    // this.order_status = response.data.order.status ? response.data.order.status.name : '';

                    // if (this.order_status == 'Cancelled') {
                    //     this.ShowCancelIcon = true;
                    // } else {
                    //     this.ShowCancelIcon = false;
                    // }

                    this.order_items = response.data.order.order_items;
                    this.notes = response.data.order.notes;
                });
            },
            getOrderStatus() {
                 axios.get("{{route('api.order_status')}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    // console.log(response.data.data)
                    this.orderStatus = response.data.data

                });
            },
            formatPrice(value) {
                let val = (value/1).toFixed(2);
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
            cancelOrders() {
             this.cancelReason = true;
            }, closeCancelOrder() {
             this.cancelReason = false;
            },
            changeStatus(orderItemid, selectedStatus,status) {
                var orderId = this.order.id;
                var reason = this.reason;
                console.log(status)
                console.log(selectedStatus)
                this.cancelReason = false;

                swal({
                    title: selectedStatus,
                    text: "Are you sure you want to update this order item status?",
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

                        axios.post('{{request()->root()}}/api/v1/orders/' +orderId+ '/item/' +orderItemid+ '/status', {
                                status_id: status , reason: reason}   
                            ).then((response) => {
                            console.log(response.data.message);
                            swal("Success!",response.data.message, "success");
                            setTimeout(function(){
                               window.location.reload(1);
                            }, 2000);
                        }).catch(function (response) {
                            //handle error
                            // console.log(response);
                             swal("Failed!", response.message, "error");
                        });
                    }
                });
              
            },
            editShippingDays(itemIndex)
            {      
                $('#shippingItemDays_'+itemIndex).show();
            },
            cancelEditShippingDays(itemIndex)
            {
                $('#shippingItemDays_'+itemIndex).hide();
            },
            onUpdateShippingDays(itemId, itemIndex) {
                console.log($('#shippingItemCalendar_'+itemIndex).val());

                swal({
                    title: 'Edit Shipping Days',
                    text: "Are you sure you want to update this shipping days?",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3c8dbc',
                    // cancelButtonColor: '#3c8dbc',
                    confirmButtonText: 'Yes, Please!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-info',
                    cancelButtonClass: 'btn btn-grey',
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        axios.post('{{route('api.update_shipping_days')}}',
                        {
                            order_item_id: itemId,
                            date: $('#shippingItemCalendar_'+itemIndex).val(),
                        })
                        .then((response) => {
                        console.log(response.data.message);
                        swal("Success!",response.data.message, "success");
                        setTimeout(function(){
                           window.location.reload(1);
                        }, 2000);
                        }).catch(error => {
                            console.log(error.response.data.status)
                            // console.log()
                            swal("Failed!",  JSON.stringify(error.response.data.message), "info");
                        });
                    }
                });
            },
            createNote(item){
                window.location.href = '{{request()->root()}}/customers/' +{{$customerId}} + '/orders/' +{{$orderId}}+ '/item/' +item.id+ '/create-note' 
            }
        },
    });
</script>
@endsection


<style type="text/css">
.zoom {
  zoom: 67%;
}
.mygrid-wrapper-div {
    overflow-x: hidden;
    height: 950px;
}
.padding-left-50 {
    padding-left: 30px;
}

ol.progtrckr {
    margin: 0;
    padding: 0;
    list-style-type none;
}

ol.progtrckr li {
    display: inline-block;
    text-align: center;
    line-height: 3.5em;
}

ol.progtrckr[data-progtrckr-steps="2"] li { width:230px; }
ol.progtrckr[data-progtrckr-steps="3"] li { width:230px; }
ol.progtrckr[data-progtrckr-steps="4"] li { width:230px; }

ol.progtrckr li.progtrckr-cancelled {
    color: black;
    border-bottom: 4px solid darkred;
}
ol.progtrckr li.progtrckr-done {
    color: black;
    border-bottom: 4px solid yellowgreen;
}
ol.progtrckr li.progtrckr-todo {
    color: silver; 
    border-bottom: 4px solid silver;
}

ol.progtrckr li:after {
    content: "\00a0\00a0";
}
ol.progtrckr li:before {
    position: relative;
    bottom: -2.5em;
    float: left;
    left: 50%;
    line-height: 1em;
}
ol.progtrckr li.progtrckr-done:before {
    color: white;
    background-color: yellowgreen;
    line-height: 2.2em;
    border: none;
    border-radius: 2.2em;
}
ol.progtrckr li.progtrckr-cancelled:before {
    color: white;
    background-color: darkred;
    line-height: 2.2em;
    border: none;
    border-radius: 2.2em;
}
ol.progtrckr li.progtrckr-todo:before {
    color: silver;
    background-color: white;
    font-size: 2.2em;
    bottom: -1.2em;
}
@media only screen and (max-width: 1285px) {
    .zoom {
      padding-bottom: 10rem;
    }
}



</style>