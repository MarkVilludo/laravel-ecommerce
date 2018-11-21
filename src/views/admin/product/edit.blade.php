@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <form action="{{url('products', $productId)}}" novalidate="" method="post" enctype="multipart/form-data">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Products</h4>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="{{route('products.index')}}">Products</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                @if (Session::has('message'))
                   <div class="alert alert-success">{{ Session::get('message') }}</div>
                @endif
                <template>
                    <div class="row">
                       <div class="col-lg-6">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h4 class="header-title m-t-0">Edit product "@{{product.name}}"</h4>
                                        <p class="text-muted font-14 m-b-20">
                                            Legend  (*) required fields.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <img v-if="product.default_image" :src="product.default_image" class="rounded-square img-thumbnail">
                                        <label v-if="!product.default_image">No image available</label>

                                        <label>Change Product image</label>
                                        <input type="file" name="file" class="form-control">
                                        <!-- end row -->
                                    </div>
                                </div>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group">
                                        <label for="name">Name<span class="text-danger">*</span></label>
                                        <input type="text" name="name" v-model="product.name" parsley-trigger="change" required="required" placeholder="Product Name" class="form-control" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="short_description">Short descriptions</label>
                                        <input type="text" name="short_description" v-model="product.short_description" parsley-trigger="change" required="required" placeholder="Short Description" class="form-control" id="short_description">
                                    </div>
                                      <div class="form-group">
                                        <label for="description">Description<span class="text-danger">*</span></label>
                                        <textarea name="description" v-model="product.description" parsley-trigger="change" required="required" placeholder="Description" class="form-control" id="description" rows="5"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Categories<span class="text-danger">*</span></label>
                                        <select v-model="productCategory" name="child_sub_category_id" class="form-control">
                                            <option value="">Select Category</option>
                                            <option v-if="categories.length > 0" v-for="category in categories" v-bind:value="category.id">@{{category.title}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="regular_price">Regular Price<span class="text-danger">*</span></label>
                                        <input type="number" name="regular_price" v-model="product.regular_price" parsley-trigger="change" required="required" placeholder="Regular Price" class="form-control" id="regular_price">
                                    </div>
                                    <div class="form-group">
                                        <label for="selling_price">Selling Price<span class="text-danger">*</span></label>
                                        <input type="number" name="selling_price" v-model="product.selling_price" parsley-trigger="change" required="required" placeholder="Selling Price" class="form-control" id="selling_price">
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status<span class="text-danger">*</span></label>
                                        <select v-model="product.status" name="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option v-if="statuses.length > 0" v-for="status in statuses" v-bind:value="status.id">@{{status.title}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="featured">Featured<span class="text-danger">*</span></label>
                                        <select v-model="product.featured" name="featured" class="form-control">
                                            <option value="">Select Status</option>
                                            <option v-if="featuredOption.length > 0" v-for="feature in featuredOption" v-bind:value="feature.id">@{{feature.title}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="is_new_arrival">Is New Arrival <span class="text-danger">*</span></label>
                                        <select v-model="product.is_new_arrival" name="is_new_arrival" class="form-control">
                                            <option v-if="arrivalOption.length > 0" v-for="newArrival in arrivalOption" v-bind:value="newArrival.id">@{{newArrival.title}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="fbt">Frequently Bought Together </label>
                                        <select v-model="product.fbt_id" name="fbt" class="form-control">
                                            <option value="">Select</option>
                                            <option v-if="setFbts.length > 0" v-for="setFbt in setFbts" v-bind:value="setFbt.id">@{{setFbt.name}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group text-right" style="padding-top: 20px">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                            Update
                                        </button>
                                    </div>

                            </div> <!-- end card-box -->
                        </div>
                        <div class="col-lg-6">
                           <div class="portlet">
                            <div class="portlet-heading bg-inverse">
                                <h3 class="portlet-title">
                                    PRODUCT VARIANTS
                                </h3>
                                <div class="portlet-widgets">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#bg-inverse"><i class="ion-minus-round"></i></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div id="bg-inverse" class="panel-collapse collapse show">
                                <div class="portlet-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-3">
                                            </div>
                                            <div class="col-lg-9 mb-2">
                                                <a  class="btn btn-sm btn-success waves-effect waves-light pull-right" href="{{route('product.variant.create', $productId)}}"> <i class="fa fa-plus"></i> Add
                                                </a>
                                            </div>
                                        </div>
                                            <div class="row table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Colors</th>
                                                        <th>Inventory</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="product.variants" v-for="variant in product.variants">
                                                        <td>
                                                            <span v-if="variant.colors" v-for="color in variant.colors" v-bind:style="{background: color}">
                                                                &emsp;@{{color}}&emsp;
                                                            </span>
                                                        </td>
                                                        <td>
                                                           @{{variant.inventory ? variant.inventory : 'Unavailable'}}
                                                        </td>
                                                        <td>
                                                            <a v-on:click="viewVariantDetails(variant)" href="#" class="btn btn-sm btn-primary">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                            <a v-on:click="deleteVariant(variant)" href="#" class="btn btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="portlet">
                                <div class="portlet-heading bg-inverse">
                                    <h3 class="portlet-title">
                                        PRODUCT INFO
                                    </h3>
                                    <div class="portlet-widgets">
                                        <a data-toggle="collapse" data-parent="#accordion2" href="#bg-product-info"><i class="ion-minus-round"></i></a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div id="bg-product-info" class="panel-collapse collapse show">
                                    <div class="portlet-body">
                                       <div class="row">
                                            <div class="col-lg-6">
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 25%">Title</th>
                                                        <th style="width: 40%">Descriptions</th>
                                                        <th style="width: 25%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="product.informations" v-for="info in product.informations">
                                                        <td>@{{info.title}}</td>
                                                        <td>@{{info.description}}</td>
                                                        <td>
                                                              <a v-on:click="viewProductInfoDetails(info)" href="#" class="btn btn-sm btn-primary">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet">
                                <div class="portlet-heading bg-inverse">
                                    <h3 class="portlet-title">
                                        HOW TO USE
                                    </h3>
                                    <div class="portlet-widgets">
                                        <span class="divider"></span>
                                        <a data-toggle="collapse" data-parent="#accordion3" href="#how-to-use"><i class="ion-minus-round"></i></a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div id="how-to-use" class="panel-collapse collapse show">
                                    <div class="portlet-body">
                                       <textarea class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 157px;" name="manual" v-model="product.manual"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet">
                                <div class="portlet-heading bg-inverse">
                                    <h3 class="portlet-title">
                                        REVIEWS
                                    </h3>
                                    <div class="portlet-widgets">
                                        <span class="divider"></span>
                                        <a data-toggle="collapse" data-parent="#accordion3" href="#reviews"><i class="ion-minus-round"></i></a>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div id="reviews" class="panel-collapse collapse show">
                                    <div class="portlet-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                            </div>
                                            <div class="col-lg-6 mb-2">
                                                <a  class="btn btn-sm btn-success waves-effect waves-light pull-right" href="{{route('product.review.create', $productId)}}">  <i class="fa fa-plus"></i> Add
                                                </a>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="width:20%">Rate (starts 1-5)</th>
                                                        <th style="width:30%">Review</th>
                                                        <th style="width:20%">Created By</th>
                                                        <th style="width:30%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="reviews" v-for="review in reviews">
                                                        <td>
                                                            @{{review.rate}}
                                                           <!--  <span class="fa fa-star"></span>
                                                            <span class="fa fa-star @{{review.rate >= 2 ? 'checked' : ''}}"></span>
                                                            <span class="fa fa-star @{{review.rate >= 3 ? 'checked' : ''}}"></span>
                                                            <span class="fa fa-star @{{review.rate >= 4 ? 'checked' : ''}}"></span>
                                                            <span class="fa fa-star @{{review.rate >= 5 ? 'checked' : ''}}"></span> -->
                                                        </td>
                                                        <td>
                                                            <p>
                                                               <span>
                                                                    <strong>
                                                                        @{{review.title}}
                                                                   </strong>
                                                                </span>
                                                                <br>
                                                               <span>@{{review.description}}</span>
                                                                <br>

                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p v-if="review.user">
                                                                @{{review.user.first_name+' '+review.user.last_name}}
                                                                <small>@{{review.date}}</small>
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <a v-on:click="viewRatingDetails(review)" href="#" class="btn btn-sm btn-primary">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                            <a v-on:click="deleteRating(review)" href="#" class="btn btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12">
                                                <ul class="pagination">
                                                    <li class="paginate_button page-item previous" id="datatable-editable_previous"><a href="#" aria-controls="datatable-editable" data-dt-idx="0" tabindex="0" class="page-link" :disabled="paginationReviews.current_page == paginationReviews.from" @click.prevent="changePageReview(paginationReviews.current_page - 1,'previous')">Previous</a></li>
                                                    <li v-for="page in pagesReviews" class="paginate_button page-item active"><a href="#" :class="isCurrentPageReviews(page) ? 'is-current' : ''" @click.prevent="changePageReview(page)" aria-controls="datatable-editable" data-dt-idx="1" tabindex="0" class="page-link">@{{page}}</a></li>
                                                    <li class="paginate_button page-item next" id="datatable-editable_next"><a href="#" aria-controls="datatable-editable" data-dt-idx="7" tabindex="0" class="page-link" :disabled="paginationReviews.current_page >= paginationReviews.last_page" @click.prevent="changePageReview(paginationReviews.current_page + 1,'next')">Next</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        <!-- End main content page -->
        </form>
    </div>
    <!-- end container -->
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<script src="{{url('assets/js/vue-multiselect.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getProductDetails();
            this.getCategories("{{route('api.categories')}}");
            this.getFBT("{{route('api.fbt')}}");
            this.getReviews("{{route('api.product.reviews', $productId)}}");
        },
        data: {
            categories: [],
            setFbts: [],
            product: '',
            reviews: [],
            productCategory: '',
            statuses: [
                {'id': 0, 'title': 'Unpublish'},
                {'id': 1, 'title': 'Publish'},
            ],
            featuredOption: [
                {'id': 0, 'title': 'No'},
                {'id': 1, 'title': 'Yes'},
            ],
            arrivalOption: [
                {'id': 0, 'title': 'No'},
                {'id': 1, 'title': 'Yes'},
            ],
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}',
            paginationReviews: '', //for product reviews
            pagesReviews: '',
            pathReviews: '',
            fromReviews: '',
            toReviews: '',
            offsetReviews: '',
            firstPageReviews: '',
            nextPageReviews: '',
            prevPageReviews: '',
            lastPageReviews: '',
        },
        methods: {
            getProductDetails() {
                axios.get("{{route('api.products.details', $productId)}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.store)

                  this.product = response.data.product;
                  this.productCategory = response.data.product.category.id;

                }).catch(error => {
                    console.log(error.response.data.status)
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });
            },
            getCategories(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.categories = response.data.data;

                });
            },

            getFBT(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.setFbts = response.data.data;

                });
            },
            getReviews(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.reviews = response.data.data;

                    this.paginationReviews = response.data.meta;
                    this.pathReviews = response.data.meta.path;
                    //get last page item
                    this.pagesReviews = response.data.meta.last_page;

                    this.firstPageReviews = response.data.links.first;
                    this.nextPageReviews = response.data.links.next;
                    this.prevPageReviews = response.data.links.prev;
                    this.lastPageReviews = response.data.meta.last_page;

                });
            },
            isCurrentPageReviews(page) {
                return this.paginationReviews.current_page === page;
            },
            changePageReview(page, step) {
                // console.log(this.lastPage)
                // console.log(page)
                if (page) {
                    this.paginationReviews.current_page = page;
                    if (step == 'previous' && this.paginationReviews.current_page >= 1) {
                        this.reviews = [];
                        this.getReviews(this.prevPage);
                    } else if(step == 'next' && this.paginationReviews.current_page <= this.paginationReviews.last_page){
                        this.reviews = [];
                        this.getReviews(this.nextPageReviews);
                    } else if(!step){
                        this.reviews = [];
                        this.getReviews(this.pathReviews+'?page='+page);
                    }
                }
            },
            viewVariantDetails(variant) {
                window.location.href = "{{request()->root().'/products/'}}"+variant.product_id+'/variants/'+variant.id;
            },
            deleteVariant(variant) {
                window.location.href = "{{request()->root().'/products/'}}"+variant.product_id+'/variants/'+variant.id+'/delete';
            },
            viewProductInfoDetails(info) {
                window.location.href = "{{request()->root().'/products/'}}"+info.product_id+'/info/'+info.id;
            },
            deleteProductInfo(info) {
                window.location.href = "{{request()->root().'/products/'}}"+info.product_id+'/info/'+info.id+'/delete';
            },
            viewRatingDetails(review) {
                window.location.href = "{{request()->root().'/products/'}}"+review.product_id+'/reviews/'+review.id;
            },
            deleteRating(review) {
                window.location.href = "{{request()->root().'/products/'}}"+review.product_id+'/reviews/'+review.id+'/delete';
            }
        },
    });
</script>
<style>
.checked {
    color: orange;
}
</style>
@endsection
