@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Products</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('products.index')}}">Products</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <form action="#" enctype="multipart/form-data">
                    <div class="row">
                       <div class="col-lg-6">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <h4 class="header-title m-t-0">Create new product</h4>
                                        <p class="text-muted font-14 m-b-20">
                                            Legend  (*) required fields.
                                        </p>
                                         
                                        <form action="{{route('api.product.upload_image')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="dz-message">
                                                
                                            </div> 
                                            <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                                <span class="glyphicon glyphicon-hand-down"></span>
                                            </h4>
                                        </form>
                                    </div>
                                    <div class="col-lg-4">
                                        <img id="imagePreview" src="" class="rounded-square img-thumbnail">
                                        <input type="hidden" id="imageProduct" name="imageProduct" value="" >
                                        <input type="hidden" id="imageProductFilename" value="">
                                    </div>
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <label for="name">Name<span class="text-danger">*</span></label>
                                    <input type="text" v-model="name" name="name" parsley-trigger="change" required="required" placeholder="Product Name" class="form-control" id="name">
                                </div>
                                <div class="form-group">
                                    <label for="short_description">Short descriptions</label>
                                    <input type="text" v-model="short_description" name="short_description" parsley-trigger="change" required="required" placeholder="Short Description" class="form-control" id="short_description">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description<span class="text-danger">*</span></label>
                                    <textarea name="description" v-model="description" parsley-trigger="change" required="required" placeholder="Description" class="form-control" id="description" rows="5"> </textarea> 
                                </div>
                                <div class="form-group">
                                    <label for="category">Categories<span class="text-danger">*</span></label>
                                    <select v-model="child_sub_category_id" name="child_sub_category_id" class="form-control" required="required">
                                        <option value="">Select Category</option>
                                        <option v-if="categories.length > 0" v-for="category in categories" v-bind:value="category.id">@{{category.title}}</option>
                                    </select>
                                </div>
                               <!--  <div class="form-group">
                                    <label for="warranty">Warranty</label>
                                    <input type="text" v-model="warranty" name="warranty" parsley-trigger="change" placeholder="Warranty" class="form-control" id="warranty">
                                </div>
                                <div class="form-group">
                                    <label for="warranty_type">Warranty Type</label>
                                    <input type="text" v-model="warranty_type" name="warranty_type" parsley-trigger="change" placeholder="Warranty Type" class="form-control" id="warranty_type">
                                </div>
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input type="text" v-model="model" name="model" parsley-trigger="change" placeholder="Model" class="form-control" id="model">
                                </div> -->
                                <div class="form-group">
                                    <label for="regular_price">Regular Price<span class="text-danger">*</span></label>
                                    <input type="number" v-model="regular_price" name="regular_price" parsley-trigger="change" required="required" placeholder="Regular Price" class="form-control" id="regular_price">
                                </div>
                                <div class="form-group">
                                    <label for="selling_price">Selling Price<span class="text-danger">*</span></label>
                                    <input type="number" v-model="selling_price" name="selling_price" parsley-trigger="change" required="required" placeholder="Selling Price" class="form-control" id="selling_price">
                                </div>
                                <div class="form-group">
                                    <label for="status">Status<span class="text-danger">*</span></label>
                                    <select v-model="status" name="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1">Publish</option>
                                        <option value="0">Unpublish</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="featured">Featured </label>
                                    <select v-model="featured" name="featured" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>    
                                <div class="form-group">
                                    <label for="is_new_arrival">Is New Arrival? </label>
                                    <select v-model="is_new_arrival" name="is_new_arrival" class="form-control">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>           
                                <div class="form-group">
                                    <label for="fbt">Frequently Bought Together </label>
                                    <select v-model="fbt" name="fbt" class="form-control">
                                        <option value="">Select</option>
                                        <option v-if="setFbts.length > 0" v-for="setFbt in setFbts" v-bind:value="setFbt.id">@{{setFbt.name}}</option>
                                    </select>
                                </div>     
                            </div> <!-- end card-box -->
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
                                       <textarea class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 157px;" name="manual" v-model="manual" ></textarea>
                                    </div>
                                </div>
                            </div>   
                                <div class="form-group text-right" style="padding-top: 20px">
                                    <button type="submit" @click="storeNewProduct()" class="btn btn-success waves-effect waves-light"> Save Product</button>
                                </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card-box">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <label for="status">Variants<span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <button class="btn btn-sm btn-primary waves-effect waves-light btn-block pt-2 pb-2" type="button"  @click="addVariant()">
                                                <i class="fa fa-plus"></i> Add Variant
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row col-lg-12 pb-4 pt-2" v-for="(variant,index) in variants">
                                        <div class="col-lg-5">
                                            <form action="{{route('api.product.upload_image')}}" accept-charset="UTF-8" id="real-dropzone-variant" enctype="multipart/form-data" class="dropzone dz-clickable">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="variantKey" v-bind:value="index">
                                                <div class="dz-message">
                                                    
                                                </div> 
                                                <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                                    <span class="glyphicon glyphicon-hand-down"></span>
                                                </h4>
                                            </form>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="col-lg-12">
                                                <!-- // preview -->
                                                <img v-bind:id="'imagePreviewVariant_'+index"  src="" style="width:200px">
                                                <!-- //multiple images collect per variant -->
                                                <!-- <textarea v-bind:id="'collectionImages_'+index" type="text"> </textarea>  -->
                                                <br>
                                                <label for="colors">Color<span class="text-danger">*</span></label>
                                                <input v-bind:id="'variantImagePath_'+index" name="image_path" type="hidden" value="">
                                                <input v-bind:id="'variantFileName_'+index" data-file_name="'variantFileName_'+index" name="file_name" type="hidden" value="">
                                                <input type="color" v-bind:id="'color_'+index" name="colors[]" v-model="variant.color" parsley-trigger="change" required="required" placeholder="Color" class="form-control color" @change="changeCollectColors(index,'color_'+index, variant.color)">

                                                <multiselect
                                                    v-bind:id="'colors_'+index" 
                                                    v-model="variant.colors" 
                                                    :options="variant.colors"
                                                    :multiple="true"
                                                    :custom-label="customLabel"
                                                    class=""
                                                    >
                                                </multiselect>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="stocks">Stocks<span class="text-danger">*</span></label>
                                                <input type="number" v-bind:id="'stocks_'+index" v-model="variant.stock" name="stocks[]" parsley-trigger="change" required="required" placeholder="Stocks" class="form-control">
                                            </div>
                                           
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="remove-variant">
                                                <a class="btn btn-danger pt-4 pb-4 pr-4 pl-4" href="#" @click="removeVariant(index)">
                                                   X
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                           
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <!-- /// -->
                                    <div class="portlet">
                                        <div class="portlet-heading bg-inverse">
                                            <h3 class="portlet-title">
                                                PRODUCT INFO
                                            </h3>
                                            <div class="portlet-widgets">
                                                <span class="divider"></span>
                                                <a data-toggle="collapse" data-parent="#accordion2" href="#product-info"><i class="ion-minus-round"></i></a>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div id="product-info" class="panel-collapse collapse show">
                                            <div class="portlet-body">
                                                <div class="row pb-4 pt-2" v-for="(info,index) in infos">
                                                    <div class="col-lg-12">
                                                        <!-- //Question -->
                                                        <input type="text" v-bind:id="'infoAnswer'+index" name="title[]" parsley-trigger="change" placeholder="Title" class="form-control" v-model="info.title">
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <!-- Answer -->
                                                        <textarea v-bind:id="'infoQuestion'+index" name="description[]" parsley-trigger="change" placeholder="Description" class="form-control" v-model="info.description" style="margin-top: 0px; margin-bottom: 0px; height: 171px;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>   
                                </div>
                                <!-- // -->
                            </div>
                        </div>
                    </div>
                </form>
                <!-- End main content page -->
                <!-- end row -->
            </template>
        </div>
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
<!-- Parsleyjs -->
<script type="text/javascript" src="../plugins/parsleyjs/dist/parsley.min.js"></script>

<script src="assets/js/jquery.core.js"></script>
<script src="assets/js/jquery.app.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('form').parsley();
    });
</script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getCategories("{{route('api.categories')}}");
            this.getFBT("{{route('api.fbt')}}");
        },
        components: {
            Multiselect: window.VueMultiselect.default
        },
        data: {
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}',
            image: '',
            categories: [],
            setFbts: [],
            token : "{{ csrf_token() }}",
            variants: [ 
                {'image_path':'','file_name':'', 'color':'#000000', 'colors': [],'images': [], 'stock': ''},
                {'image_path':'','file_name':'', 'color':'#000000', 'colors': [],'images': [], 'stock': ''},
                {'image_path':'','file_name':'', 'color':'#000000', 'colors': [],'images': [], 'stock': ''}
            ],
            infos: [ 
                {'title':'Details','description': ''},
                {'title':'Content + Care','description': ''}
            ],
            colors: [],
            sizes: [],
            stocks: [],
            name: '',
            description: '',
            short_description: '',
            child_sub_category_id: '',
            warranty: '',
            warranty_type: '',
            model: '',
            regular_price: '',
            selling_price: '',
            status: '',
            featured: '',
            is_new_arrival: 0,
            fbt: '',
            file_path: '',
            manual: '',
            uploaded_image: ''
        },
        events: {
            'vdropzone-success': function (file) {
                console.log('A file was successfully uploaded' + file);
            }
        },
        methods: {
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
            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.image = e.target.result;
                };
                reader.readAsDataURL(file);
                console.log(vm.image)
                this.upload(this.image)
            },
            customLabel: function (option) {
              return `${option}`
            },
            changeCollectColors(variantKey,variantColorFieldId, color) {
                console.log(variantKey)
                console.log(variantColorFieldId)
                console.log(color)
                $.each( this.variants, function( index, variant ){
                    if (index === variantKey) {
                        variant.colors.push(color)
                    }
                });
            },
            addVariant() {
                this.variants.push({'image_path':'','file_name':'', 'colors': [], 'images': [], 'stock': ''});
            },
            removeVariant(key){
                this.variants.splice(key, 1);
            },
            addFaq() {
                this.infos.push({'title':'','description': ''});
            },
            removeFaq(key){
                this.infos.splice(key, 1);
            },
            storeNewProduct(){

                //get variants get file path via id 
                var variantsData = [];
                var variantsImages = [];

                
                $.each( this.variants, function( index, variant ) {

                    console.log($("#collectionImages_"+index).val())
                    variantsData.push({'image_path' : $("#variantImagePath_"+index).val(), 'file_name' : $("#variantFileName_"+index).val(), colors : variant.colors,'images' : $("#collectionImages_"+index).val(),stock : variant.stock,'index' : index})


                });
                console.log(variantsData)
                axios.post("{{route('api.store_product')}}",
                {
                    name: this.name, 
                    description: this.description,
                    short_description: this.short_description,
                    child_sub_category_id: this.child_sub_category_id,
                    warranty: this.warranty,
                    warranty_type: this.warranty_type,
                    model: this.model,
                    regular_price: this.regular_price,
                    selling_price: this.selling_price,
                    status: this.status,
                    featured: this.featured,
                    is_new_arrival: this.is_new_arrival,
                    fbt: this.fbt,
                    product_image: $("#imageProduct").val(),
                    uploaded_image_file_name: $("#imageProductFilename").val(),
                    variants: variantsData,
                    infos: this.infos,
                    manual: this.manual,
                },
                {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.status)
                    if(response.data.status){
                        swal({ title: 'Success!', text: response.data.message, type: 'success', confirmButtonText: 'Ok' });
                    } else {
                        swal({ title: 'Failed!', text: response.data.message, type: 'info', confirmButtonText: 'Ok' });
                    }
                   
                    // setTimeout(function(){
                    //    window.location.reload(1);
                    // }, 2000);
                }).catch(error => {
                    console.log(error)
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });

                    var errorMsg = errors ? JSON.stringify(errors.toString()).replace(/[0-9]/g, " ") : error.response.data.message;
                    swal("Failed!",  errorMsg, "info");
                });
              
            }
        },
    });

//for product variants
var collectionImages = [];
Dropzone.options.realDropzoneVariant = {
    acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
    uploadMultiple: false,
    parallelUploads: 10,
    maxFilesize: 2,
    // maxFiles: 1,
    // previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 8MB',
    createImageThumbnails: true,

    // The setting up of the dropzone
    init:function() {
        this.on('success', function(file){
           this.removeFile(file);
        })
        this.on('error', function(file){
           swal({
             title: 'Error!',
             text: 'Uploading "' + file.name + '" encountered a problem, Please select valid image file type from : (jpeg, png, jpg)',
             type: 'error',
             confirmButtonText: 'Ok'
           })
        })
    },
    error: function(file, response) {
        this.removeFile(file);
        if ($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
         else
            var message = response.message;
         file.previewElement.classList.add("dz-error");
         _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
         _results = [];
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i];
             _results.push(node.textContent = message);
         }
         return _results;
    },
    success: function(file,response) {
        console.log(file);
        console.log(response.path);
        console.log(response.variant_key);
        
        // collectImages(response.path,response.file_name,response.variant_key);

        $("#imagePreviewVariant_"+response.variant_key).attr("src",response.url_path);
        $("#variantImagePath_"+response.variant_key).attr("value",response.path);
        $("#variantFileName_"+response.variant_key).attr("value",response.file_name);

        // //multiple images
        collectionImages.push({'path': response.path, 'file_name': response.file_name});
        console.log(collectionImages)
        
        $("#collectionImages_"+response.variant_key).text(collectionImages);
    }
}
//for product
Dropzone.options.realDropzone = {

    acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
    uploadMultiple: false,
    parallelUploads: 10,
    maxFilesize: 2,
    maxFiles: 1,
    // previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 8MB',
    createImageThumbnails: true,

    // The setting up of the dropzone
    init:function() {

        this.on('success', function(file){
           this.removeFile(file);
        })
        this.on('error', function(file){
            swal({
             title: 'Error!',
             text: 'Uploading "' + file.name + '" encountered a problem, Please select valid image file type from : (jpeg, png, jpg)',
             type: 'error',
             confirmButtonText: 'Ok'
           })
        })
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
              this.removeFile(this.files[0]);
            }
       });
    },
    error: function(file, response) {
        this.removeFile(file);
        if ($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
        else
            var message = response.message;
         file.previewElement.classList.add("dz-error");
         _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
         _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i];
             _results.push(node.textContent = message);
        }
        return _results;
    },
    success: function(file,response) {
        console.log(file);
        console.log(response.path);
        console.log(response.url_path);
        $("#imagePreview").attr("src",response.url_path);
        $("#imageProduct").attr("value",response.path);
        $("#imageProductFilename").attr("value",response.file_name);
    }
}
</script>

<style type="text/css">
    .color {
        height:40px;
    }
    img{
        max-height: 200px;
    }
    .remove-variant {
        padding-top:30px;
    }

    .dropzone {
        width: 200px;
    } 
</style>
@endsection
