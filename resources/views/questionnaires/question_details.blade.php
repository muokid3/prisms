@extends('layouts.app')

@push('js')
    <script>
        $(function() {
            // server side - lazy loading
            $('#answers-dt').DataTable({
                processing: true, // loading icon
                serverSide: true, // this means the datatable is no longer client side
                ajax: '{{ route('ajax-answers',$question->id) }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'answer', name: 'answer'},
                    {data: 'followup', name: 'followup'},
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
                    searchPlaceholder: "Search Answers",
                },
                order: [[0, 'desc']]
            });//end datatable

            var seasonModalTitle = $('#questionnaire-modal-title'),
                seasonSpoofInput = $('#questionnaire-spoof-input'),
                seasonForm = $('#questionnaire-form');

            // // add  season
            // $('#add-followup-btn').on('click', function() {
            //     seasonModalTitle.text('Add');
            //     seasonSpoofInput.attr('disabled', 'disabled');
            //     // productForm.find('input.form-control, select').val('');
            //     seasonForm.attr('action', seasonForm.attr('source'));
            // });
            // edit   product
            $(document).on('click', '.add-followup-btn', function() {
                var seasonBtn = $(this);
                var answer_id = seasonBtn.attr('acs-id');
                var answer = seasonBtn.attr('ans-id');


                if (answer_id !== '') {

                    $('#answer_id').val(answer_id);
                    $('#answer_text').val(answer);

                    // open the modal
                    $('#followup-modal').modal('show');

                    // $.ajax({
                    //     url: seasonBtn.attr('source'),
                    //     type: 'get',
                    //     dataType: 'json',
                    //     beforeSend: function() {
                    //         seasonModalTitle.text('Edit');
                    //         seasonSpoofInput.removeAttr('disabled');
                    //     },
                    //     success: function(data) {
                    //         console.log(data);
                    //         // populate the modal fields using data from the server
                    //         $('#name').val(data['name']);
                    //         $('#introduction').val(data['introduction']);
                    //         $('#id').val(data['id']);
                    //
                    //         // set the update url
                    //         var action =  seasonForm .attr('action');
                    //         action = action + '/' + season_id;
                    //         console.log(action);
                    //         seasonForm .attr('action', action);
                    //
                    //         // open the modal
                    //         $('#questionnaire-modal').modal('show');
                    //     }
                    // });
                }
            });

            $(document).on('submit', '.del_answer_form', function() {
                if (confirm('Are you sure you want to delete this answer?')) {
                    return true;
                }
                return false;
            });
        });
    </script>
@endpush


@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Question</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{url('/')}}">Questions</a></li>
            <li class="breadcrumb-item active">Question Details</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                {{$question->question}}
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <button class="btn btn-primary btn-sm" id="add-season-btn" data-toggle="modal" data-target="#answer-modal">
                        <i class="fa fa-plus"></i> Add Answer
                    </button>
                </div>


                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')


            <!--tabbed content-->
                <div class="row">
                    <div class="col-md-10 ml-auto mr-auto">
                        <div class="page-categories">
                            <h3 class="title text-center">{{ $question->question  }}</h3>
                            <br />

                            <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" tab-id="information" href="#information" role="tablist">
                                        <i class="fa fa-info"></i> Question
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" tab-id="sections" href="#sections" role="tablist">
                                        <i class="fa fa-check-circle"></i> Answers
                                    </a>
                                </li>

                            </ul>


                            <div class="tab-content tab-space tab-subcategories">

                                <div class="tab-pane active" id="information">
                                    <div class="card text-left">
                                        <div class="card-header">
                                            <h4 class="card-title">Question</h4>
                                            <p class="card-category">
                                                Question Details
                                            </p>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    Type: {!! $question->type !!}
                                                </div>

                                                <div class="col-md-3">
                                                    Answer Count: {!! $question->answer_count !!}
                                                </div>

                                                <div class="col-md-3">
                                                    Total Answers: {!! $question->answers->count() !!}
                                                </div>

                                                <div class="col-md-3">
                                                    Responses: {!! $question->responses->count() !!}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="sections">
                                    <div class="card text-left">
                                        <div class="card-header">
                                            <h4 class="card-title">Answers</h4>
                                            <p class="card-category">
                                                Available Answers
                                            </p>
                                        </div>
                                        <div class="card-body">
                                            <div class="loader" style="display: none;">Loading...</div>
                                            <div class="material-datatables">
                                                <table id="answers-dt"
                                                       class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Answer</th>
                                                        <th>Follow up question</th>
                                                        <th>Action</th>

                                                    </tr>
                                                    </thead>
                                                    <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Answer</th>
                                                        <th>Follow up question</th>
                                                        <th>Action</th>

                                                    </tr>
                                                    </tfoot>
                                                </table>
                                                <!-- end content-->
                                            </div>
                                            <!--  end card  -->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--end tabbed content-->









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
                            <input type="text" class="form-control" id="question_text" value="{{$question->question }}" readonly/>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="answer">Possible Answer</label>
                            <input type="text" class="form-control" id="answer" name="answer" required/>
                        </div>

                        <input type="hidden" name="question_id" value="{{$question->id }}" id="question_id"/>
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

    <div class="modal fade" id="followup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><span id="questionnaire-modal-title">Add </span> follow up question</h4>
                </div>
                <div class="modal-body" >
                    <form action="{{ url('answer/followup') }}" method="post" id="answer-form"  enctype="multipart/form-data">
                        {{ csrf_field() }}


                        <div class="form-group">
                            <label class="control-label">Answer</label>
                            <input type="text" class="form-control" id="answer_text" readonly/>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="followup">Follow up question</label>
                            <input type="text" class="form-control" id="followup" name="followup" required/>
                        </div>

                        <input type="hidden" name="answer_id"  id="answer_id"/>
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
