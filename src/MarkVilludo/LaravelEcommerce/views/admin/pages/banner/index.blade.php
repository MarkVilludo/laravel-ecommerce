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
                            <h4 class="page-title">Banners</h4>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="#">Banners</a></li>
                                <li class="breadcrumb-item active">List</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- //Main content page. -->
                <div class="row col-lg-2 col-lg-offset10 pb-2">
                    <a class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right" href="{{route('pages.banner.create')}}"><i class="fa fa-plus"> </i> Add Banner
                    </a>
                </div>
                <div class="row mb-2 text-center pr-4">
                    <div class="col-lg-12">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="search" @change="filter()" placeholder="Search Banner File Name.." aria-label="Username" aria-describedby="basic-addon1">
                             <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <a href="#"> <i class="fa fa-search"></i> </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 30%">Image</th>
                                <th style="width: 20%">Title</th>
                                <th style="width: 30%">Description</th>
                                <th style="width: 20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="banners.length > 0" v-for="banner in banners">
                                <td> 
                                    <img v-if="banner.medium_path" :src="banner.medium_path" class="rounded-square img-thumbnail">
                                    <label v-if="!banner.medium_path">No image available</label>
                                </td>
                                <td>@{{banner.title}}</td>
                                <td>@{{banner.description}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(banner)">Edit</button>
                                    <button class="btn btn-danger w-sm" v-on:click="deleteBanner(banner)">Delete</button>
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
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getBanners("{{route('api.banners')}}");
        },
        data: {
            categories: [],
            variants: [],
            banners: [],
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
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getBanners(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.banners = response.data.data;
                    if (this.banners.length > 0) {
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
            viewDetails(product) {
                console.log(product.id)
                // console.log(window.location.href)
                window.location.href = "{{request()->root().'/pages/banners/edit' }}/"+product.id;

            },
            deleteBanner(banner){
                swal({
                        title: 'Delete banner',
                        text: "Are you sure you want to delete this banner?",
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
                             axios.delete("{{url('api/v1/banners')}}/"+banner.id, {
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
                        this.banners = [];
                        this.getBanners(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.banners = [];
                        this.getBanners(this.nextPage);
                    } else if(!step){
                        this.banners = [];
                        this.getBanners(this.path+'?page='+page);
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
                console.log(this.color)
                var color = this.color;
                axios.get('{{route('api.search.banners')}}'+"?title="+this.search,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.banners = response.data.data;
                    if (this.banners.length > 0) {
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
<style type="text/css">
    .color {
        height:40px;
    }
</style>
@endsection

