@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">FBT</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">FBT</a></li>
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
            <div class="row pb-2">
                    <div class="col-lg-2 pr-0">
                        <a class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-left" href="{{route('fbt.create')}}"><i class="fa fa-plus"> </i> Add FBT</a>
                    </div>
                    <div v-show="loading" class="col-lg-10 text-right">
                        <h4>
                            <i class="fa fa-spinner fa-spin"></i>
                            Loading data ..
                        </h4>
                    </div>
                </div>
            <template>
                <div class="row form-group inline">
                    <div class="col-md-12 col-lg-12 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ion-search"></i></span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Search set.." id="datepicker"autocomplete="off" @change="filter()">
                        </div>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>FBT</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="fbts.length > 0" v-for="fbt in fbts" >

                                <td>
                                    <p v-if="fbt.name">
                                        @{{fbt.name}}
                                    </p>
                                </td>
                                <td>
                                    <div v-if="fbt.fbtProducts.length > 0" v-for="product in fbt.fbtProducts">
                                        <ul> 
                                            <li v-if="product.product">@{{product.product.name}}</li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(fbt)">Edit</button>
                                    <button class="btn btn-danger w-sm" @click="deleteFBT(fbt)">Delete</button>
                                </td>
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
            this.getFBT("{{route('api.fbt')}}");
        },
        data: {
            loading: false,
            fbts: [],
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
            getFBT(url) {
                this.loading = true;
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    this.loading = false;
                    console.log(response.data.data)
                    this.fbts = response.data.data;
                    if (this.fbts.length > 0) {
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
            viewDetails(fbt) {
                console.log(fbt.id)
                window.location.href = "{{request()->root().'/fbt/' }}"+fbt.id+"/edit";
            },
            changePage(url){
                if (url) {
                    this.getFBT(url);
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
                this.fbts = [];
                axios.get('{{route('api.search.fbt')}}'+"?name="+this.search,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.fbts = response.data.data;

                    //check if with data found
                    if (this.fbts.length > 0) {
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
            deleteFBT(fbt){
                swal({
                        title: 'Delete',
                        text: "Are you sure you want to delete this set of FBT?",
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
                             axios.delete("{{request()->root().'/fbt/' }}"+fbt.id, {
                                headers: {
                                    'Authorization': this.header_authorization,
                                    'Accept': this.header_accept
                                }
                            })
                            .then((response) => {

                                swal("Success!",response.data.message, "success");
                                console.log(response.data)
                                location.reload();
                            })
                            .catch(function (response) {
                                //handle error
                                console.log(response);
                                 swal("Failed!", response.data.message, "error");
                            });
                        }
                    });
            },
        },
    });
</script>
@endsection

