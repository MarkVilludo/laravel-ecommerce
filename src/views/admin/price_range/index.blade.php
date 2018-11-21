@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Price Range</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Price Range</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- //Main content page. -->
            <div class="row col-lg-2 col-lg-offset-10">
                <a href="#">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right" @click="addPriceRange()"> <i class="fa fa-plus"> </i> Add Price Range</button>
                </a>
            </div>
            <template>
                <div class="form-group row" v-if="viewAddPriceRange">
                    <div class="col-lg-8 row">
                        <div class="col-lg-6 col-md-6">
                            <label for="name">From<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="priceRange.from" name="from">
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label for="name">To<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="priceRange.to" name="to">
                        </div>
                    </div>
                    <div class="col-lg-2 mt-2 pt-4">
                        <button class="btn btn-success btn-sm" @click="onSavePriceRange()"><i class="fa fa-save"> Save</i> </button>

                        <button class="btn btn-danger btn-sm" @click="viewAddPriceRange=false"><i class="fa fa-remove"> Cancel</i> </button>
                    </div>
                </div>
                <div class="form-group row" v-if="viewUpdatePriceRange">
                    <div class="col-lg-8 row">
                        <div class="col-lg-6 col-md-6">
                            <label for="name">From<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="updatePriceRange.from" name="from">
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label for="name">To<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" v-model="updatePriceRange.to" name="to">
                        </div>
                    </div>
                    <div class="col-lg-2 mt-2 pt-4">
                        <button class="btn btn-success btn-sm" @click="onUpdatePriceRange(updatePriceRange)"><i class="fa fa-save"> Update</i> </button>

                        <button class="btn btn-danger btn-sm" @click="viewUpdatePriceRange=false"><i class="fa fa-remove"> Cancel</i> </button>
                    </div>
                </div>  
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Range</th>
                                <th style="width: 20%">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="price_ranges.length > 0" v-for="price_range in price_ranges" >

                                <td>@{{price_range.from+' - '+price_range.to}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(price_range)"><i class="fa fa-pencil"></i> Edit</button>
                                    <button class="btn btn-danger w-sm" v-on:click="deletePriceRange(price_range)"><i class="fa fa-trash"></i> Delete</button>
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
            this.getPriceRange("{{route('api.price_range')}}");
        },
        data: {
            price_ranges: [],
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
            viewAddPriceRange: false,
            priceRange: {
                from: '',
                to: ''
            },
            updatePriceRange: {
                from: '',
                to: ''
            },
            viewUpdatePriceRange: false,
            category: '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getPriceRange(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.price_ranges = response.data.data;
                    if (this.price_ranges.length > 0) {
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
            addPriceRange() {
                this.priceRange = { from: '', to: '' };

                if (this.viewAddPriceRange == false) {
                    this.viewAddPriceRange = true;
                } else {
                    this.viewAddPriceRange = false;
                }
            },
            onSavePriceRange() {
                console.log(this.priceRange);
                axios.post("{{route('api.store.price_range')}}", { 
                        price_from : this.priceRange.from,
                        price_to : this.priceRange.to
                    }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    
                    this.getPriceRange("{{route('api.price_range')}}");
                    this.viewAddPriceRange = false;
                    //reset all data in form
                }).catch(error => {
                    if (error.response.data.status == 'success') {
                        swal("Failed!",  error.response.data.message, "info");
                    } else {
                       console.log(error.response.data.errors)
                        var errors = [];
                        $.each( error.response.data.errors, function( index, error ){
                            errors.push(error.message)
                        });
                        swal("Failed!",  JSON.stringify(errors.toString()), "info");
                    }
                });          

            },
            onUpdatePriceRange(pricerange) {
                console.log(pricerange)
                axios.post("{{url('api/v1/price_range')}}/"+pricerange.id, { 
                        price_from : pricerange.from,
                        price_to : pricerange.to
                    }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    
                    this.getPriceRange("{{route('api.price_range')}}");
                    this.viewUpdatePriceRange = false;
                    //reset all data in form
                }).catch(error => {
                    if (error.response.data.status == 'success') {
                        swal("Failed!",  error.response.data.message, "info");
                    } else {
                       console.log(error.response.data.errors)
                        var errors = [];
                        $.each( error.response.data.errors, function( index, error ){
                            errors.push(error.message)
                        });
                        swal("Failed!",  JSON.stringify(errors.toString()), "info");
                    }
                });        
            },
            viewDetails(price_range) {
                if (this.viewUpdatePriceRange == false) {
                    this.viewUpdatePriceRange = true;
                }
                this.updatePriceRange = price_range;
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
                        this.price_ranges = [];
                        this.getPriceRange(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.price_ranges = [];
                        this.getPriceRange(this.nextPage);
                    } else if(!step){
                        this.price_ranges = [];
                        this.getPriceRange(this.path+'?page='+page);
                    }
                }
            },
            deletePriceRange(price_range) {
                // console.log(price_range)
                swal({
                    title: 'Delete price range',
                    text: "Are you sure you want to delete this price range?",
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
                         axios.delete("{{url('api/v1/price_range')}}/"+price_range.id, {
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
                             swal("Failed!", response.message, "error");
                        });
                    }
                });
            }
        },
    });
</script>
@endsection


