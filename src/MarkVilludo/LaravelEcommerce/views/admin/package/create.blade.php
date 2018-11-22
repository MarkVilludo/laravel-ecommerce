@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Packages</h4>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="{{route('packages.index')}}">Packages</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <template>
                    <div class="row">
                       <div class="col-lg-6">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h4 class="header-title m-t-0">Create new package</h4>
                                        <p class="text-muted font-14 m-b-20">
                                            Legend  (*) required fields.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Package image</label>
                                        <input type="file" name="file" v-on:change="packageImage($event)" class="form-control">
                                        <!-- end row -->
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <label for="name">Name<span class="text-danger">*</span></label>
                                        <input type="text" name="name" parsley-trigger="change" required="required" placeholder="Package Name" class="form-control" id="name" v-model="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description<span class="text-danger">*</span></label>
                                        <textarea name="description" v-model="description" parsley-trigger="change" required="required" placeholder="Description" class="form-control" id="description" rows="5"> </textarea> 
                                    </div>
                                  
                                    <div class="form-group">
                                        <label for="warranty">Warranty</label>
                                        <input type="text" v-model="warranty" name="warranty" parsley-trigger="change" placeholder="Warranty" class="form-control" id="warranty">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Warranty Type</label>
                                        <input type="text" v-model="warranty_type" name="description" parsley-trigger="change" placeholder="Warranty Type" class="form-control" id="description">
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price<span class="text-danger">*</span></label>
                                        <input type="number" name="price" v-model="price" parsley-trigger="change" required="required" placeholder="Selling Price" class="form-control" id="price">
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status<span class="text-danger">*</span></label>
                                        <select name="status" v-model="status" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="1">Publish</option>
                                            <option value="0">Unpublish</option>
                                        </select>
                                    </div>

                                   

                                    <div class="form-group text-right" style="padding-top: 20px">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit"  v-on:click="storePackage()">
                                            Submit
                                        </button>
                                        <button type="reset" class="btn btn-secondary waves-effect m-l-5">
                                            Cancel
                                        </button>
                                    </div>
                            </div> <!-- end card-box -->
                        </div>
                        <div class="col-lg-6">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-lg-9 mt-4">
                                        <label for="status">Select product item <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-3 mb-2" style="padding-top: 30px">
                                       <a href="{{route('product.create')}}">
                                            <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add Product</button>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="category">Categories<span class="text-danger">*</span></label>
                                            <select name="child_sub_category_id" v-model="child_sub_category_id"  @change="onChangeCategory(child_sub_category_id)" class="form-control">
                                                <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-md-12 col-lg-12">
                                    <div class="col-md-12 col-lg-12 pl-0">
                                        <multiselect
                                            v-model="product_items" 
                                            :options="productOptions"
                                            :multiple="true"
                                            track-by="name"
                                            :custom-label="customLabel"
                                            class="form-control"
                                            >
                                          </multiselect>
                                    </div>
                                    <div v-if="product_items" v-for="product_item in product_items">
                                        <!-- <pre>@{{ product_item }}</pre> -->
                                        <label>Product : @{{ product_item.name }}</label>
                                        <div class="row">
                                            <div class="form-group col-md-6 col-lg-6">
                                                <label for="price">Select Variant<span class="text-danger">*</span></label>
                                                <select v-model="product_item.variant" class="form-control" required="required">
                                                  <option v-for="variant in product_item.variants" v-bind:value="variant.id" v-bind:style="{background: variant.color}">
                                                    <p>@{{ variant.color }}</p>
                                                    <label class="text-sm">(Stocks - @{{ variant.inventory }})</label>
                                                  </option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6 col-lg-6 pr-0">
                                                <label for="price">Quantity<span class="text-danger">*</span></label>
                                               <input type="text" name="quantity" v-model="product_item.quantity" class="form-control" required="required">
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
                <!-- end row -->
            </div>
    </div>
    <!-- end container -->
@endsection

@section('bottom_scripts')
@include('includes.vue-scripts')
<script src="{{url('assets/js/vue-infinite-loading.js')}}"></script>
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="{{url('assets/js/vue-multiselect.min.js')}}"></script>

<script>
    var vue = new Vue({
        el: '.content',
        mounted() {
         
        },
        components: {
            Multiselect: window.VueMultiselect.default
        },
        data: {
            productOptions: [],
            child_sub_category_id: 0,
            file : '',
            name : '',
            description : '',
            warranty : '',
            warranty_type : '',
            price : '',
            status : '',
            product_items : '',
            package_image : ''
        },
        methods: {
            packageImage: function(event){
                console.log(event.path[0].files)
                this.package_image = event.path[0].files[0];
            },
            filtered: function(status) {
                alert(status)
            },
            customLabel: function (option) {
              return `${option.name}`
            },
            onChangeCategory: function(child_sub_category_id) {
                this.productOptions = [];
                console.log(child_sub_category_id)  
                var subCategory = child_sub_category_id;
                axios.get("{{route('api.sub_category_details')}}/" + subCategory)
                .then((response) => {
                    console.log(response.data.data)
                    this.productOptions = response.data.data;
                });
            },
            storePackage: function() {
                getpage = this.getpage;
                axios.post("{{route('package.store')}}", {
                    file: this.package_image,
                    name: this.name,
                    description : this.description,
                    warranty: this.warranty,
                    warranty_type: this.warranty_type,
                    price: this.price,
                    status: this.status,
                    package_items: this.product_items,
                    child_sub_category_id : this.child_sub_category_id,
                    params: {
                        page : getpage
                    },
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    //reset all data in form
                   this.resetFormData();
                }).catch(error => {
                    console.log(error.response.data.status)
                    // console.log()
                    swal("Failed!",  JSON.stringify(error.response.data.errors), "info");
                });
            },
            resetFormData: function() {
                this.productOptions =  [];
                this.child_sub_category_id = 0;
                this.file =  '';
                this.name =  '';
                this.description =  '';
                this.warranty =  '';
                this.warranty_type =  '';
                this.price =  '';
                this.status =  '';
                this.product_items =  '';
                this.package_image =  ''; 

            }
        }
    });
</script>
<style type="text/css" ></style>
@endsection
