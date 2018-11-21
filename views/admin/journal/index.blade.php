@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Journals</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('journals.index')}}">Journals</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="row col-lg-12">
                <a href="{{route('journal.create')}}" class="pull-right">
                    <button class="btn btn-success btn-custom waves-effect w-md waves-light m-b-5 pull-right"> <i class="fa fa-plus"> </i> Add journal</button>
                </a>
            </div>
            <template>
                <div class="row form-group inline">
                    <div class="col-md-8 col-lg-8 pr-4">
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ion-search"></i></span>
                            </div>
                            <input type="text" class="form-control" v-model="search" placeholder="Search Journal Title.." id="datepicker"autocomplete="off" @change="filter()">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 pr-4">
                        <select class="form-control" v-model="category" v-on:change="searchFunction(category)">
                            <option selected value="">Category</option>
                            <option v-if="journalCategories.length > 0" v-for="category in journalCategories" v-bind:value="category.id">@{{category.name}}</option>
                        </select>
                    </div>
                </div>
                <div class="row col-lg-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Featured Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Created at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="journals.length > 0" v-for="journal in journals" >
                                <td> 
                                    <img v-if="journal.featured_image" :src="journal.featured_image" class="rounded-square img-thumbnail">
                                    <label v-if="!journal.featured_image">No image available</label></td>
                                <td>@{{journal.title}}</td>
                                <td>@{{journal.category}}</td>
                                <td>@{{journal.created_at}}</td>
                                <td>
                                    <button class="btn btn-primary w-sm" @click="viewDetails(journal)">Edit</button>
                                    <button class="btn btn-danger w-sm" @click="removeJournal(journal)">Delete</button>
                                </td>
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
            this.getJournals("{{route('api.journals')}}");
            this.getJournalCategories("{{route('api.journal.categories')}}");
        },
        data: {
            journals: [],
            journalCategories: [],
            category: '',
            nextPage: '',
            prevPage: '',
            lastPage: '',
            search: '',
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getJournals(url) {
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.journals = response.data.data;
                    this.nextPage = response.data.links.next;
                    this.prevPage = response.data.links.prev;
                    this.lastPage = response.data.links.last;
                });
            }, getJournalCategories(url){
                axios.get(url, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.journalCategories = response.data.data;
                });
            }, filter() {

                if (this.search && this.search.length >= 3) {
                    this.searchFunction();
                } else {
                    this.searchFunction();
                }
            },
            searchFunction() {
                axios.get('{{route('api.search.journal')}}'+"?title="+this.search+'&category='+this.category,{
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response)
                    this.journals = response.data.data;

                    //check if with data found
                    if (this.journals.length > 0) {
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
            viewDetails(journal) {
                console.log(journal.id)
                // window.location.href = '/journals/' + journal.id + '/details';
                 window.location.href = "{{request()->root().'/journals/' }}"+journal.id+"/details";
            },
            removeJournal(journal) {
                swal({
                    title: 'Delete journal',
                    text: "Are you sure you want to delete this journal?",
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
                         axios.delete("{{url('api/v1/journals')}}/"+journal.id, {
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
            },
            changePage(url){
                if (url) {
                    this.getJournals(url);
                }
            }
        },
    });
</script>
@endsection