@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Journal Categories</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">Journal Categories</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- //Main content page. -->
            <div class="row col-lg-2 col-lg-offset-10">
                <a href="#">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right" @click="addCategory()"> <i class="fa fa-plus"> </i> Add Journal Category</button>
                </a>
            </div>
            <template>
                <div class="form-group row" v-if="viewAddCategory">
                    <div class="col-lg-8">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="journal_category" name="journal_category">
                    </div>
                    <div class="col-lg-2 mt-2 pt-4">
                        <button class="btn btn-success btn-sm" @click="onSaveJournalCategory()"><i class="fa fa-save"> Save</i> </button>

                        <button class="btn btn-danger btn-sm" @click="viewAddCategory=false"><i class="fa fa-remove"> Cancel</i> </button>
                    </div>
                </div>
                <div class="form-group row" v-if="viewUpdateCategory">
                    <div class="col-lg-8">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="category.new_name" name="category_name">
                    </div>
                    <div class="col-lg-2 mt-2 pt-4">
                        <button class="btn btn-success btn-sm" @click="onUpdateJournalCategory(category)"><i class="fa fa-save"> Update</i> </button>

                        <button class="btn btn-danger btn-sm" @click="viewUpdateCategory=false;category=''"><i class="fa fa-remove"> Cancel</i> </button>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Name</th>
                                <th style="width: 20%">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-if="journal_categories.length > 0" v-for="category in journal_categories" >

                                <td>@{{category.name}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" v-on:click="viewDetails(category)"><i class="fa fa-pencil"></i> Edit</button>
                                    <button class="btn btn-danger w-sm" v-on:click="deleteJournalCategory(category)"><i class="fa fa-trash"></i> Delete</button>
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
            this.getJournalCategories("{{route('api.journal.categories')}}");
        },
        data: {
            journal_categories: [],
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
            viewAddCategory: false,
            journal_category: '',
            viewUpdateCategory: false,
            category: '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getJournalCategories(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.journal_categories = response.data.data;
                    if (this.journal_categories.length > 0) {
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
            addCategory() {
                this.journal_category = '';
                if (this.viewAddCategory == false) {
                    this.viewAddCategory = true;
                } else {
                    this.viewAddCategory = false;
                }
            },
            onSaveJournalCategory() {
                axios.post("{{route('api.store.journal_categories')}}", { 
                        name : this.journal_category,
                    }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    
                    this.getJournalCategories("{{route('api.journal.categories')}}");
                    this.viewAddCategory = false;
                    //reset all data in form
                }).catch(error => {
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });

            },
            onUpdateJournalCategory(category) {
                axios.post("{{url('api/v1/journal_categories')}}/"+category.id, { 
                        name : category.new_name,   
                    }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    
                    this.getJournalCategories("{{route('api.journal.categories')}}");
                    this.viewUpdateCategory = false;
                    //reset all data in form
                }).catch(error => {
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });             
            },
            viewDetails(category) {
                if (this.viewUpdateCategory == false) {
                    this.viewUpdateCategory = true;
                }
                this.category = category;
                this.category.new_name = category.name;
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
                        this.journal_categories = [];
                        this.getJournalCategories(this.prevPage);
                    } else if(step == 'next' && this.pagination.current_page <= this.pagination.last_page){
                        this.journal_categories = [];
                        this.getJournalCategories(this.nextPage);
                    } else if(!step){
                        this.journal_categories = [];
                        this.getJournalCategories(this.path+'?page='+page);
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
                axios.get('{{route('api.search.store')}}'+"?name="+this.search,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.journal_categories = response.data.data;

                    //check if with data found
                    if (this.journal_categories.length > 0) {
                        this.noResultFound = true;
                    } else {
                        this.noResultFound = false;
                    }
                    this.firstPage = response.data.links.first;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.meta.last_page;
                });
            },
            deleteJournalCategory(category) {
                // console.log(category)
                swal({
                    title: 'Delete Journal category',
                    text: "Are you sure you want to delete this journal category?",
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
                         axios.delete("{{url('api/v1/journal_categories')}}/"+category.id, {
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


