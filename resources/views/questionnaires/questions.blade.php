@extends('layouts.app')

@push('js')
    <script>
        $(function() {
            // server side - lazy loading
            $('#questions-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ route('ajax-questions') }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'question', name: 'question'},
                    {data: 'type', name: 'type'},
                    {data: 'answer_count', name: 'answer_count'},
                    {data: 'answers', name: 'answers'},
                    {data: 'responses', name: 'responses'},
                    {data: 'actions', name: 'actions'}
                ],
                // columnDefs: [
                //     { searchable: false, targets: [5] },
                //     { orderable: false, targets: [5] }
                // ],
                "pagingType": "full_numbers",
                "lensgthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Questions",
                },
                order: [[0, 'desc']]
            });//end datatable

            var _ModalTitle = $('#questionnaire-modal-title'),
                _SpoofInput = $('#questionnaire-spoof-input'),
                _Form = $('#question-form');

            // add
            $('#add-question-btn').on('click', function() {
                _ModalTitle.text('Add');
                _ModalTitle.text('Add');
                _SpoofInput.val('POST');

                $('#question').val('');
                $('#answer_count').val('').change();
                $('#type').val('').change();

                $('#questionnaire-modal').modal('show');
            });
            // add answer
            $(document).on('click', '.add-answer-btn', function() {
                var seasonBtn = $(this);
                var question_id = seasonBtn.attr('acs-id');
                var question = seasonBtn.attr('que-id');


                if (question_id !== '') {

                    $('#question_id').val(question_id);
                    $('#question_text').val(question);

                    // open the modal
                    $('#answer-modal').modal('show');
                }
            });

            // edit   question
            $(document).on('click', '.edit-question-btn', function() {
                var _Btn = $(this);
                var _id = _Btn.attr('acs-id'),
                    _Form = $('#question-form');

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

                            $('#question').val(data['question']);
                            $('#answer_count').val(data['answer_count']).change();
                            $('#type').val(data['type']).change();
                            $('#id').val(data['id']);

                            // set the update url
                            var action =  _Form .attr('action');
                            // action = action + '/' + season_id;
                            console.log(action);
                            _Form .attr('action', action);

                            // open the modal
                            $('#questionnaire-modal').modal('show');
                        }
                    });
                }
            });

            $(document).on('submit', '.del_question_form', function() {
                if (confirm('Are you sure you want to delete the question?')) {
                    return true;
                }
                return false;
            });
        });
    </script>
@endpush


@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Survey Questions</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Questionnaire</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
               Survey Questions
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <button class="btn btn-primary btn-sm" id="add-question-btn">
                        <i class="fa fa-plus"></i> Add Question
                    </button>
                </div>


                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')


                <div class="material-datatables">
                    <div class="table table-responsive">
                        <table id="questions-dt"
                               class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Answer Count</th>
                                <th>Answers</th>
                                <th>Responses</th>
                                <th class="disabled-sorting text-right">Actions</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Answer Count</th>
                                <th>Answers</th>
                                <th>Responses</th>
                                <th class="disabled-sorting text-right">Actions</th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>

                </div>

            </div>
        </div>
    </div>

    {{--modal--}}
    <div class="modal fade" id="questionnaire-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><span id="questionnaire-modal-title">Add </span> Question</h4>
                </div>
                <div class="modal-body" >
                    <form action="{{ url('questions') }}" method="post" id="question-form"  enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" id="questionnaire-spoof-input" value="PUT" disabled/>

                        <div class="form-group">
                            <label for="type"></label>
                            <select name="type" class="form-control form-control-sm" required id="type">
                                <option value="">Select Type...</option>
                                <option value="OPEN">OPEN-ENDED</option>
                                <option value="CHOICE">MULTIPLE CHOICE</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="answer_count"></label>
                            <select name="answer_count" class="form-control form-control-sm" required id="answer_count">
                                <option value="">Select Answer Count...</option>
                                <option value="SINGLE">SINGLE ANSWER</option>
                                <option value="MULTIPLE">MULTIPLE ANSWERS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="question">Question</label>
                            <input type="text" class="form-control" id="question" name="question"required/>
                        </div>

                        <input type="hidden" name="id" id="id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </form>
                </div>

                <!--<div class="modal-footer">-->
                <!---->
                <!--</div>-->
            </div>
        </div>
    </div>

    <div class="modal fade" id="answer-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><span id="questionnaire-modal-title">Add </span> Answer</h4>
                </div>
                <div class="modal-body" >
                    <form action="{{ url('answer') }}" method="post" id="answer-form"  enctype="multipart/form-data">
                        {{ csrf_field() }}


                        <div class="form-group">
                            <label class="control-label">Question</label>
                            <input type="text" class="form-control" id="question_text" readonly/>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="answer">Possible Answer</label>
                            <input type="text" class="form-control" id="answer" name="answer" required/>
                        </div>

                        <input type="hidden" name="question_id" id="question_id"/>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>
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
