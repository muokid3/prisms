@extends('layouts.app')
@section('title', 'RedCap Hospitals')
@push('js')
    <script>
        $(function() {
            $('#roles-dt').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
        });



        var _ModalTitle = $('#group-modal-title'),
            _SpoofInput = $('#spoof-input'),
            _Form = $('#group-form');

        //add
        $(document).on('click', '.add-btn', function() {
            _ModalTitle.text('Add');
            _SpoofInput.val('POST');
            $('#name').val('');
            $('#id').val('');

            $('#user-role-modal').modal('show');

        });

        // edit   product
        $(document).on('click', '.edit', function() {
            var seasonBtn = $(this);
            var season_id = seasonBtn.attr('acs-id'),
                seasonForm = $('#group-form');

            if (season_id !== '') {
                $.ajax({
                    url: 'redcap_hospitals/'+season_id,
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
                        $('#redcap_hospital_id').val(data['redcap_hospital_id']);
                        $('#redcap_hospital_name').val(data['redcap_hospital_name']);
                        $('#id').val(data['id']);

                        var action =  _Form .attr('action');
                        // action = action + '/' + season_id;
                        console.log(action);
                        _Form .attr('action', action);

                        // open the modal
                        $('#user-role-modal').modal('show');
                    }
                });
            }
        });




        // delete javascript
        $('.delete-model-form').on('submit', function() {
            if (confirm('Are you sure you want to delete the hospital ?')) {
                return true;
            }
            return false;
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-users-cog"></i>
                        </div>
                        <h4 class="card-title">Manage RedCap Hospitals</h4>
                    </div>
                    <div class="card-body">
                        <div class="toolbar">
                            <button class="btn btn-primary btn-sm add-btn" >
                                <i class="fa fa-plus"></i> Add Hospital
                            </button>
                        </div>
                        @include('layouts.success')
                        @include('layouts.warnings')
                        @include('layouts.warning')

                        <div class="material-datatables">
                            <table id="datatables" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RedCap Hosp. ID</th>
                                    <th>Hosp. Name</th>
                                    <th class="disabled-sorting text-right">Actions</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>RedCap Hosp. ID</th>
                                    <th>Hosp. Name</th>
                                    <th class="disabled-sorting text-right">Actions</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                @foreach($hospitals as $hospital)
                                    <tr>
                                        <td>{{$hospital->id}}</td>
                                        <td>{{$hospital->redcap_hospital_id}}</td>
                                        <td>{{$hospital->redcap_hospital_name}}</td>
                                        <td class="text-right">
                                            <button acs-id="{{$hospital->id}}" class="btn btn-sm btn-link btn-warning btn-just-icon edit"><i class="fa fa-edit"></i> Edit</button>
                                            <form action="{{ url('redcap_hospitals/delete') }}" method="post" style="display: inline;" class="delete-model-form">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="id" value="{{$hospital->id}}">
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                                            </form>
                                            <a href="{{ url('redcap_hospitals/details/'.$hospital->id) }}" class="btn btn-sm btn-link bg-success">
                                                <i class="fa fa-check" style="color: white"></i><span style="color: white"> Manage Hospital</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- end content-->
                </div>
                <!--  end card  -->
            </div>
            <!-- end col-md-12 -->
        </div>
        <!-- end row -->
    </div>

    {{--modal--}}
    <div class="modal fade" id="user-role-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"> <span id="group-modal-title">Add </span> User Group</h4>
{{--                    <h4 class="modal-title" id="myModalLabel"> {{ $edit ?'Edit' : 'Add' }} Role</h4>--}}
                </div>
                <div class="modal-body" >

                    <form action="{{ url('redcap_hospitals') }}"  method="post" id="group-form">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" id="spoof-input" value="PUT" disabled/>


                        <div class="form-group mb-4">
                            <label class="control-label" for="redcap_hospital_id">Hospital ID</label>
                            <input type="number" min="1" value="{{ old('redcap_hospital_id') }}" class="form-control" id="redcap_hospital_id" name="redcap_hospital_id" required/>
                        </div>


                        <div class="form-group mb-4">
                            <label class="control-label" for="redcap_hospital_name">Hospital Name</label>
                            <input type="text" value="{{ old('redcap_hospital_name') }}" class="form-control" id="redcap_hospital_name" name="redcap_hospital_name" required/>
                        </div>


                        <input type="hidden" id="id" name="id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save">save</i> Save</button>
                            {{--<!-- {!! $actionsRepo->formButtons() !!} -->--}}
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
