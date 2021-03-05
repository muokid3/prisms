@extends('layouts.app')
@section('title', 'Group Details')
@push('js')
    <script>

        $(function() {
            // server side - lazy loading
            $('#users-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ url('ajax/users/groups/details/'.$group->id) }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'email', name: 'email'},
                    {data: 'role', name: 'role'},
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



        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary card-header-icon">
                        <h4 class="card-title">User Group - {{$group->name}}</h4>
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
                                        <th>Last Name</th>
                                        <th>E-Mail</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Id</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>E-Mail</th>
                                        <th>Role</th>
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
                                            <i class="fa fa-cog"></i> Permissions
                                            <div class="ripple-container"></div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#add_perms" data-toggle="tab">
                                            <i class="fa fa-tools"></i> Add Permissions
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
                                        <th>Permissions</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($user_permissions as $user_permission)
                                        <tr>
                                            <td>{{$user_permission->get_permission->name}}</td>
                                            <td style="text-align: right">
                                                <a href="{{url('/users/groups/permissions/delete/'.$user_permission->id)}}">
                                                                <span class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span>
                                                                &nbsp;Delete</span></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <div class="tab-pane" id="add_perms">

                                <form class="form-horizontal" role="form" method="POST" action="{{ url('/users/groups/permissions/add') }}">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="group_id" value="{{$group->id}}">

                                    <div class="row clearfix">
                                        <div class="col-md-12 {{ $errors->has('permission') ? ' has-error' : '' }}" style="margin-bottom: 0px">
                                            <div class="input-group input-group-lg">
                                                <div class="form-group bmd-form-group">
                                                    <select id="permission" name="permission[]" multiple class="dropdown form-control" data-style="select-with-transition">
                                                        @foreach(\App\Permission::all() as $perm)
                                                            @if(!$group->has_perm([$perm->id],$group->id))
                                                                <option value="{{$perm->id}}">{{$perm->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="input-group input-group-lg">
                                                <div class="form-group bmd-form-group">
                                                    <button type="submit" class="btn btn-success waves-effect">ADD</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>

@endsection
