@extends('layouts.app')

@push('js')
    <script>

        $(function() {
            // server side - lazy loading
            $('#studies-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ route('bulk-sms-dt') }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'recipients', name: 'recipients'},
                    {data: 'message', name: 'message'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'created_at', name: 'created_at'},
                    // {data: 'actions', name: 'actions'},
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
                    searchPlaceholder: "Search SMS",
                },
                "order": [[0, "desc"]]
            });

            // live search

            var _ModalTitle = $('#user-modal-title'),
                _SpoofInput = $('#user-spoof-input'),
                _Form = $('#user-form');


            //add
            $(document).on('click', '.add-btn', function() {
                _ModalTitle.text('Add');
                _SpoofInput.val('POST');
                // $('#name').val('');
                // $('#detail').val('');
                // $('#id').val('');

                $('#user-modal').modal('show');

            });

            $(document).on('click', '.specify-btn', function() {
                _ModalTitle.text('Add');
                _SpoofInput.val('POST');
                // $('#name').val('');
                // $('#detail').val('');
                // $('#id').val('');

                $('#specify-modal').modal('show');

            });

            $(document).on('submit', '.del_study_form', function() {
                if (confirm('Are you sure you want to delete this study?')) {
                    return true;
                }
                return false;
            });



        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Bulk SMS</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bulk SMS</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Send Bulk SMS to a user group or specify phone numbers
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <button class="btn btn-primary btn-sm add-btn">
                        <i class="fas fa-users"></i> Send to user group
                    </button>

                    <button class="btn btn-primary btn-sm specify-btn">
                        <i class="fa fa-plus"></i> Specify Recipients
                    </button>
                </div>
                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')


                <div class="table table-responsive">
                    <table id="studies-dt" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipients</th>
                            <th>Message</th>
                            <th>Created By</th>
                            <th>Date Created</th>

                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Recipients</th>
                            <th>Message</th>
                            <th>Created By</th>
                            <th>Date Created</th>
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
                    <h4 class="modal-title" id="myModalLabel"> Send Bulk SMS to user group</h4>
                </div>
                <div class="modal-body" >
                    <form id="userform" action="{{ url('bulk/messaging/group') }}" method="post" id="user-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label class="control-label" for="user_group" style="line-height: 6px;">User Group</label>

                                    <select class="dropdown form-control" data-style="select-with-transition" title="Choose User Group" tabindex="-98"
                                            name="user_group" id="user_group" required>
                                        <option value="">Select group</option>

                                        @foreach( $userGroups as $userGroup)
                                            <option value="{{ $userGroup->id  }}">{{ $userGroup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="message">Message</label>
                                    <textarea name="message" rows="10" id="message" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>



                        <input type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Send</button>
                        </div>

                    </form>
                </div>

                <!--<div class="modal-footer">-->
                <!---->
                <!--</div>-->
            </div>
        </div>
    </div>


    <div class="modal fade" id="specify-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> Send Bulk SMS to specific recipients</h4>
                </div>
                <div class="modal-body" >
                    <form id="userform" action="{{ url('bulk/messaging/specify') }}" method="post" id="user-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="recipients">Recipient numbers (comma separated)</label>
                                    <input type="text" value="{{ old('recipients') }}" class="form-control" id="recipients" name="recipients" required />
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="message">Message</label>
                                    <textarea name="message" rows="10" id="message" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>



                        <input type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Send</button>
                        </div>

                    </form>
                </div>

                <!--<div class="modal-footer">-->
                <!---->
                <!--</div>-->
            </div>
        </div>
    </div>

@endsection
