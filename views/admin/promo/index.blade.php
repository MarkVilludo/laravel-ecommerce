@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Promos</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('promos.index')}}">Promos</a></li>
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
            <div class="row form-group inline">
                <div class="col-md-12 col-lg-12 pr-4">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="ion-search"></i></span>
                        </div>
                        <input type="text" class="form-control" v-model="search" placeholder="Search Promo name.." id="datepicker"autocomplete="off" @change="filter()">
                    </div>
                </div>
            </div>
            <div class="row col-lg-12">
                <a href="{{route('promos.create')}}" class="pull-right">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Promo</button>
                </a>
            </div>
            @if (Session::has('message'))
               <div class="alert alert-info">
                {{ Session::get('message') }}
               </div>
            @endif
            <div class="row col-lg-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Promo</th>
                            <th>Descriptions</th>
                            <th>Validity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="promos.length > 0" v-for="promo in promos" >
                            <td>
                                <img v-if="promo.original_path" :src="promo.original_path" style="width:120px">
                                <label v-if="!promo.original_path">No image available</label>
                            </td>

                            <td>@{{promo.name}}</td>
                            <td>@{{promo.description}}</td>
                            <td>@{{promo.date}}</td>
                            <td>
                                <button class="btn btn-primary w-sm" @click="viewDetails(promo)"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="btn btn-danger w-sm" @click="deletePromo(promo)"><i class="fa fa-trash"></i> Delete</button>
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
            this.getPromos("{{route('api.promos')}}");
        },
        data: {
            promos: [],
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
            getPromos(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.promos = response.data.data;
                    if (this.promos.length > 0) {
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
            viewDetails(promo) {
                console.log(promo.id)
                window.location.href = "{{request()->root().'/promos/' }}"+promo.id+ "/edit";
            },
            deletePromo(promo){
                swal({
                        title: 'Delete promo',
                        text: "Are you sure you want to delete this promo?",
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
                             axios.delete("{{url('/promos')}}/"+promo.id, {
                                headers: {
                                    'Authorization': this.header_authorization,
                                    'Accept': this.header_accept
                                }
                            })
                            .then((response) => {
                                swal("Success!",response.data.message, "success");
                                console.log(response.data)
                                setTimeout(function(){
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
          isCurrentPage(page) {
                return this.pagination.current_page === page;
            },
            changePage(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                if (page) {
                    this.pagination.current_page = page;
                    if (step == 'previous' && this.pagination.current_page >= 1) {
                        this.promos = [];
                        this.getPromos(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.promos = [];
                        this.getPromos(this.nextPage);
                    } else if(!step){
                        this.promos = [];
                        this.getPromos(this.path+'?page='+page);
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
                axios.get("{{route('api.search.promo')}}"+"?search="+this.search)
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
                    this.lastPage = response.data.links.last_page;
                });
            }   
        },
    });
</script>
@endsection
