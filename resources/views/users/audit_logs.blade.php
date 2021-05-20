@extends('layouts.app')

@push('js')
    <script>

        $(function() {
            // server side - lazy loading
            $('#studies-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ route('bulk-mails-dt') }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'target', name: 'target'},
                    {data: 'subject', name: 'subject'},
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
                    searchPlaceholder: "Search E-Mails",
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
            // edit   product
            $(document).on('click', '.edit-study-btn', function() {
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
                            _SpoofInput.val('PUT');
                        },
                        success: function(data) {
                            console.log(data);
                            // populate the modal fields using data from the server
                            $('#name').val(data['study']);
                            $('#detail').val(data['study_detail']);
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
        <h1 class="mt-4">Bulk E-Mail</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Bulk E-Mail</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                All Bulk E-Mails
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <button class="btn btn-primary btn-sm add-btn">
                        <i class="fa fa-plus"></i> Send new E-Mail
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
                            <th>Target</th>
                            <th>Subject</th>
                            <th>Created By</th>
                            <th>Date Created</th>
{{--                            <th>Actions</th>--}}

                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Target</th>
                            <th>Subject</th>
                            <th>Created By</th>
                            <th>Date Created</th>
{{--                            <th>Actions</th>--}}
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
                    <h4 class="modal-title" id="myModalLabel"> Send Bulk E-Mail</h4>
                </div>
                <div class="modal-body" >
                    <form id="userform" action="{{ url('mails/bulk') }}" method="post" id="user-form" enctype="multipart/form-data">
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


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="subject">Subject</label>
                                    <input type="text" value="{{ old('subject') }}" class="form-control" id="subject" name="subject" required />
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="mail_content">Mail body content</label>
                                    <textarea name="mail_content" rows="10" id="mail_content" class="form-control" required></textarea>
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
