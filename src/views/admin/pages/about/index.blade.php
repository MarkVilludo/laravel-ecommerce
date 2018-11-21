
@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">About</h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="{{route('journals.index')}}">About</a></li>
                    <li class="breadcrumb-item active">Index</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
      </div>
    <template>
        <div class="row pb-2">
            <div class="col-lg-6 col-md-6">
                <form action="{{route('api.upload_image.global')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="savePath" value="/storage/about">
                    <input type="hidden" name="removePath" value="public/about/">
                    <input type="hidden" id="path" name="path" v-model="path">
                    <input type="hidden" id="file_name" name="file_name" v-model="file_name">
                    <div class="dz-message">
                        
                    </div> 
                    <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                        <span class="glyphicon glyphicon-hand-down"></span>
                    </h4>
                </form>
            </div>
            <div class="col-lg-6 col-md-6">
                <img v-if="file_name" :src="path" class="rounded-square img-thumbnail">
                <button v-if="file_name" class="btn btn-icon waves-effect waves-light btn-danger m-b-5 btn-block" @click="deleteCoverPhotoAbout()"> <i class="fa fa-remove"></i> </button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="col-lg-12">
                    <label>Mobile</label>
                    <div class="card-box">
                        <textarea v-model="content" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 335px;"></textarea> 
                        <br>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-12">
                    <label>Web</label>
                    <div class="card-box">
                        <quill-editor v-model="content_web"
                          ref="quillEditorA"
                          :options="editorOption"
                          @blur="onEditorBlur($event)"
                          @focus="onEditorFocus($event)"
                          @ready="onEditorReady($event)">
                        </quill-editor>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-success btn-block pt-2 pb-2" @click="updateAbout()"> Update </button>
    </template>

    </div>
</div>
<!-- end content -->
@endsection
@section('bottom_scripts')

@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<!-- Include stylesheet -->
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<!-- Include the Quill library -->
<script src="{{url('assets/js/quill.js')}}"></script>
<!-- Quill JS Vue -->
<script src="{{url('assets/js/vue-quill-editor.js')}}"></script>
<!-- Include stylesheet -->
<link href="https://cdn.quilljs.com/1.3.4/quill.core.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.4/quill.snow.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.4/quill.bubble.css" rel="stylesheet">
<script>
    
    Vue.use(VueQuillEditor)
    new Vue({
    el: '.content',
    data: {
        journal_category_id: '',
        content: '',
        content_web: '',
        editorOption: {
            theme: 'snow'
        },
        file_name: '',
        path: ''
    },
    methods: {
        onEditorBlur(quill) {
            console.log('editor blur!', quill)
        },
        onEditorFocus(quill) {
            console.log('editor focus!', quill)
        },
        onEditorReady(quill) {
        console.log('editor ready!', quill)
        },
        getAbout(url) {
            axios.get(url, {
                headers: {
                    'Authorization': this.header_authorization,
                    'Accept': this.header_accept
                }
            })
            .then((response) => {
                console.log(response.data.data)
                if (response.data.data) {
                    this.content = response.data.data[0].content;
                    this.content_web = response.data.data[0].content_web;
                    this.file_name = response.data.data[0].file_name;
                    this.path = response.data.data[0].path;
                }
               
            });
        }, updateAbout(){
            axios.post("{{route('pages.update_about',1)}}", { 
                    content : this.content,
                    content_web : this.content_web,
                    file_name : $("#file_name").val(),
                    path : $("#path").val(),
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
                //reset all data in form
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
        },
        deleteCoverPhotoAbout(){
            swal({
                title: 'Delete photo',
                text: "Are you sure you want to delete this about cover photo?",
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
                if (result.value){
                    axios.delete("{{url('api/v1/about/1/cover_photo')}}", {
                        headers: {
                            'Authorization': this.header_authorization,
                            'Accept': this.header_accept
                        }
                    })
                    .then((response) => {
                        swal("Success!",response.data.message, "success");
                        setTimeout(function(){
                           window.location.reload(1);
                        }, 3000);
                    })
                    .catch(function (response) {
                        //handle error
                        console.log(response);
                         swal("Failed!", response.data.message, "error");
                    });
                } else {
                    alert('test')
                }
            });
        }
    },
    computed: {
            editorA() {
                return this.$refs.quillEditorA.quill
            }
        },
        mounted() {
            this.getAbout("{{route('api.pages.about')}}");
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
    addRemoveLinks: false,
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
        $("#path").attr("value",response.path);
        $("#file_name").attr("value",response.file_name);
    }
}
</script>
<style type="text/css" >
    .quill-editor,
    .card-box {
      background-color: white;
    }
</style>
@endsection
