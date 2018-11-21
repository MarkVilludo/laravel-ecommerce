@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Shopping Bags </h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Shopping Bags </a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <!-- //Main content page. -->
                <!-- <div class="row form-group inline">
                    <div class="col-md-12 col-lg-12 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ion-search"></i></span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Search product name or customer name.." id="datepicker"autocomplete="off" @change="filter()">
                        </div>
                    </div>
                </div> -->
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Shopping bag</th>
                                <th>Quantity</th>
                                <th>Variant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="shopping_bags.length > 0" v-for="shoppingBag in shopping_bags" >
                                <td>@{{shoppingBag.user }}</td>
                                <td>@{{shoppingBag.product_name ? shoppingBag.product_name : shoppingBag.package_name }}</td>
                                <td>@{{shoppingBag.quantity}}</td>
                                <td>@{{shoppingBag.variant ? shoppingBag.variant.color : '---'}}</td>
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
                        <li v-if="nextPage" class="paginate_button page-item next" id="responsive-datatable_next">
                            <a href="#" aria-controls="responsive-datatable" data-dt-idx="7" tabindex="0" class="page-link" @click="changePage(nextPage)">Next</a>
                        </li>
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
            this.getShoppingBags("{{route('api.shopping_bags')}}");
        },
        data: {
            shopping_bags: [],
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
            getShoppingBags(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.shopping_bags = response.data.data;
                    if (this.shopping_bags.length > 0) {
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
            filter() {

                if (this.search && this.search.length >= 3) {
                    this.searchFunction();
                } else {
                    this.searchFunction();
                }
            },
            searchFunction() {
                axios.post("{{route('api.search.shopping_bags')}}", {
                    search : this.search
                    }, {
                        headers: {
                            'Authorization': this.header_authorization,
                            'Accept': this.header_accept
                        }
                    }).then((response) => {
                    console.log(response.data.data)
                    this.shopping_bags = response.data.data;
                    if (this.shopping_bags.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.firstPage = response.data.links.first
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.links.last;
                });
            },
            changePage(url){
                if (url) {
                    this.getShoppingBags(url);
                }
            }
        },
    });
</script>
@endsection
