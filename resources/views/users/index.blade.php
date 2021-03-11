@extends('layouts.app')

@push('js')
    <script>

        $(function() {
            // server side - lazy loading
            $('#users-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ route('users-dt') }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'role', name: 'role'},
                    {data: 'phone_no', name: 'phone_no'},
                    {data: 'email', name: 'email'},
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
                    searchPlaceholder: "Search Users",
                },
                "order": [[0, "desc"]]
            });

            // live search

            var _ModalTitle = $('#user-modal-title'),
                _SpoofInput = $('#user-spoof-input'),
                _Form = $('#user-form');

            // edit   product
            $(document).on('click', '.edit-user-btn', function() {
                var _Btn = $(this);
                var _id = _Btn.attr('acs-id'),
                    _Form = $('#user-form');

                if (_id !== '') {
                    $.ajax({
                        url: _Btn.attr('source'),
                        type: 'get',
                        dataType: 'json',
                        beforeSend: function() {
                            _ModalTitle.text('Edit');
                            _SpoofInput.removeAttr('disabled');
                        },
                        success: function(data) {
                            console.log(data);
                            // populate the modal fields using data from the server
                            $('#title').val(data['title']);
                            $('#first_name').val(data['first_name']);
                            $('#last_name').val(data['last_name']);
                            $('#email').val(data['email']);
                            $('#phone_no').val(data['phone_no']);
                            $("#user_group").val(data['user_group']).change();
                            $("#site").val(data['site_id']).change();
                            $('#id').val(data['id']);

                            // set the update url
                            var action =  _Form .attr('action');
                            // action = action + '/' + season_id;
                            console.log(action);
                            _Form .attr('action', action);

                            // open the modal
                            $('#user-modal').modal('show');
                        }
                    });
                }
            });

            $(document).on('submit', '.del_user_form', function() {
                if (confirm('Are you sure you want to delete this user?')) {
                    return true;
                }
                return false;
            });



        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Users</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                Create, update and delete users from this page
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                All Users
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#user-modal">
                        <i class="fa fa-plus"></i> Add New User
                    </button>
                </div>
                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')



                <div class="table table-responsive">
                    <table id="users-dt" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Phone No.</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Phone No.</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--modal--}}
    <div class="modal fade" id="user-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> <span id="user-modal-title">Add </span> User</h4>
                </div>
                <div class="modal-body" >
                    <form id="userform" action="{{ url('enroll') }}" method="post" id="user-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{--spoofing--}}
                        <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="title">Title</label>
                                    <input type="text" value="{{  old('title') }}" class="form-control" id="title" name="title" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first_name">First Name</label>
                                    <input type="text" value="{{  old('first_name') }}" class="form-control" id="first_name" name="first_name" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first_name">Last Name</label>
                                    <input type="text" value="{{ old('last_name') }}" class="form-control" id="last_name" name="last_name" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="phone_no">Phone Number</label>
                                    <input type="number" value="{{ old('phone_no') }}" class="form-control pb-0 mt-2" name="phone_no" id="phone_no" required/>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="control-label" for="user_group" style="line-height: 6px;">User Role</label>

                                        <select class="dropdown form-control" data-style="select-with-transition" title="Choose User Role" tabindex="-98"
                                                name="user_group" id="user_group" required>
                                            <option value="">Select role</option>

                                        @foreach( $user_roles as $user_role)
                                                <option value="{{ $user_role->id  }}">{{ $user_role->name }}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="control-label" for="site" style="line-height: 6px;">Site</label>

                                        <select class="dropdown form-control" data-style="select-with-transition" title="Choose Site" tabindex="-98"
                                                name="site" id="site" required>
                                            <option value="">Select site</option>
                                            @foreach( \App\Site::all() as $site)
                                                <option value="{{ $site->id  }}">{{ $site->site_name }}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="email">Email</label>
                                    <input type="email" value="{{ old('email') }}" class="form-control pb-0 mt-2" name="email" id="email"/>
                                </div>
                            </div>

                        </div>





                        <input type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>
                        </div>

                    </form>
                    {{--hidden fields--}}

                </div>

                <!--<div class="modal-footer">-->
                <!---->
                <!--</div>-->
            </div>
        </div>
    </div>

@endsection
