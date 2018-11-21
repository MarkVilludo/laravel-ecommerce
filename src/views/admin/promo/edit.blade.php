@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
              <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"></i>Edit Promo</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('promos.index')}}">Promo</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <!-- //Main content page. -->
                <div class='col-lg-6 col-lg-offset-6'>
                    <div class="row">
                        <div class="col-lg-6">
                            <label>Upload New Image
                            </label>
                            <br>
                            <small>
                                   <span class="text-danger">*</span>
                                    min size of (565x376) and max size of (1024x683)
                                </small>
                            <form action="{{route('api.upload_image_validate.global')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="savePath" value="/storage/promos">
                                <input type="hidden" id="path" name="path" v-model="path">
                                <input type="hidden" id="file_name" name="file_name" v-model="file_name">
                                <div class="dz-message">
                                </div> 
                                    <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                        <span class="glyphicon glyphicon-hand-down"></span>
                                    </h4>
                            </form>    
                            <!-- end row -->
                        </div>
                        <div class="col-lg-6">
                            @if($promo->file_name) 
                                <label>Category</label>
                                <img src="{{url($promo->path.'/'.$promo->file_name)}}" class="rounded-square img-thumbnail">
                            @else
                                <label>No image available</label>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" value="{{$promo->name}}" parsley-trigger="change" required="required" placeholder="Name" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="name">Description<span class="text-danger">*</span></label>
                        <textarea name="description" id="description" placeholder="Description" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 200px;">{{$promo->description}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start date<span class="text-danger">*</span></label>
                        <input type="date" id="start_date" name="start_date" value="{{$promo->start_date}}" parsley-trigger="change" required="required" placeholder="Start date" class="form-control" id="start_date">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End date<span class="text-danger">*</span></label>
                        <input type="date" id="end_date" name="end_date" value="{{$promo->end_date}}" parsley-trigger="change" required="required" placeholder="End date" class="form-control" id="end_date">
                    </div>
                    <div class="form-group">
                        <label for="status">Status<span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-control" value="{{$promo->status}}">
                            <option value="">Select Status</option>
                            <option value="1" {{$promo->status==1 ? 'selected':''}}>Publish</option>
                            <option value="0" {{!$promo->status ? 'selected':''}}>Unpublish</option>
                        </select>
                    </div>
                    <input type="button" @click="updatePromos()" class="btn btn-info" value="Submit">
                </div>
            </template>

                <!-- End main content page -->
        </div>
            <!-- end container -->
    </div>
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<!-- Include stylesheet -->
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<script>
 
    new Vue({
    el: '.content',
    data: {
        journal_category_id: '',
        content: '',
        file_name: '',
        path: ''
    },
    methods: {
        updatePromos() {
            axios.post("{{route('api.promos.update', $promo->id)}}", { 
                    image : $("#file_name").val(),
                    path : $("#path").val(),
                    name : $("#name").val(),
                    start_date : $("#start_date").val(),
                    end_date : $("#end_date").val(),
                    description : $("#description").val(),
                    status : $("#status").val(),
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
                //reset all data in form
            }).catch(error => {
                console.log(error.response.data.status)
                console.log(error.response.data.errors)
                var errors = [];
                $.each( error.response.data.errors, function( index, error ){
                    errors.push(error.message)
                });
                swal("Failed!",  JSON.stringify(errors.toString()), "info");
            });
        }
    },
    computed: {
        },
        mounted() {
        }
    })

//for about
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
           // this.removeFile(file);
        })
        this.on('error', function(file){
            swal({
                title: 'Error!',
                text: 'Uploading "' + file.name + '" encountered a problem, please select valid image file type from : (jpeg, png, jpg) and with min image size of (565x376) and max image size of (1024x683).',
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
        $("#path").attr("value",response.path);
        $("#file_name").attr("value",response.file_name);
    }
}
</script>
@endsection

