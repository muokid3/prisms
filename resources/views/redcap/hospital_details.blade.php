@extends('layouts.app')
@section('title', 'Group Details')
@push('js')
    <script>

        $(function() {
            // server side - lazy loading
            $('#users-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ url('ajax/redcap_hospitals/contacts/'.$rs->id) }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'contact_first_name', name: 'contact_first_name'},
                    {data: 'contact_full_name', name: 'contact_full_name'},
                    {data: 'contact_phone_no', name: 'contact_phone_no'},
                    {data: 'role', name: 'role'},
                    {data: 'actions', name: 'actions'},
                ],
                /*columnDefs: [
                    {searchable: false, targets: [5]},
                    {orderable: false, targets: [5]}
                ],*/
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Contacts",
                },
                "order": [[0, "desc"]]
            });

            // live search


            $(document).on('submit', '.del_contact_form', function() {
                if (confirm('Are you sure you want to delete this contact?')) {
                    return true;
                }
                return false;
            });


        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary card-header-icon">
                        <h4 class="card-title">Hospital Contacts - {{$rs->redcap_hospital_name}}</h4>
                    </div>
                    <div class="card-body">

                        @include('layouts.success')
                        @include('layouts.warnings')
                        @include('layouts.warning')

                        <div id="successView" class="alert alert-success" style="display:none;">
                            <button class="close" data-dismiss="alert">&times;</button>
                            <strong>Success!</strong><span id="successData"></span>
                        </div>
                        <div class="material-datatables">
                            <table id="users-dt" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>First Name</th>
                                        <th>Full Name</th>
                                        <th>Phone No.</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>First Name</th>
                                        <th>Full Name</th>
                                        <th>Phone No.</th>
                                        <th>Role</th>
                                        <th>Actions</th>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- end content-->
                </div>
                <!--  end card  -->
            </div>
            <!-- end col-md-8 -->

            <div class="col-md-4">

                <div class="card">
                    <div class="card-header card-header-tabs card-header-rose">
                        <div class="nav-tabs-navigation">
                            <div class="nav-tabs-wrapper">
                                <ul class="nav nav-tabs" data-tabs="tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#perms" data-toggle="tab">
                                            <i class="fa fa-tools"></i> Hospital Contacts
                                            <div class="ripple-container"></div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="perms">
                                <table class="table  ">
                                    <thead>
                                    <tr>
                                        <th>Upload contacts list</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>
                                            <div class="toolbar">
                                                <a href="{{url('assets/contacts.csv')}}" class="btn btn-primary btn-sm" >
                                                    <i class="fa fa-download"></i> Download sample file
                                                </a>
                                            </div>

                                            <form id="userform" action="{{ url('contacts/upload') }}" method="post" id="user-form" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>

                                                <div class="row">
                                                    <input type="hidden" name="hospital_id" value="{{$rs->id}}">
                                                    <div class="col-md-12">
                                                        <div class="form-group ">
                                                            <label class="control-label" for="user_role" style="line-height: 6px;">User Role</label>

                                                            <select class="dropdown form-control" data-style="select-with-transition" title="Choose User Role" tabindex="-98"
                                                                    name="user_role" id="user_role" required>
                                                                <option value="">Select role</option>

                                                                @foreach( \App\UserGroup::all() as $user_role)
                                                                    <option value="{{ $user_role->id  }}">{{ $user_role->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label" for="file">Select File</label>
                                                            <input type="file" name="file"  class="form-control" required />
                                                        </div>
                                                    </div>

                                                </div>



                                                <input type="hidden" name="id" id="id"/>
                                                <div class="form-group">
                                                    <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>
                                                </div>

                                            </form>

                                        </td>
                                    </tr>


                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>

@endsection
