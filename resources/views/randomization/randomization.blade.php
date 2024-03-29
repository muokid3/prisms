
@extends('layouts.app')

@push('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script>

        $(function() {
            // server side - lazy loading
            $('#random-dt').DataTable({
                processing: true, // loading icon
                serverSide: false, // this means the datatable is no longer client side
                ajax: '{{ route('randomization-dt') }}', // the route to be called via ajax
                columns: [ // datatable columns
                    {data: 'id', name: 'id'},
                    {data: 'sequence', name: 'sequence'},
                    {data: 'study', name: 'study'},
                    {data: 'allocation', name: 'allocation'},
                    {data: 'stratum', name: 'stratum'},
                    {data: 'date_randomised', name: 'date_randomised'},
                    {data: 'participant_id', name: 'participant_id'},
                    {data: 'staff', name: 'staff'},
                    {data: 'ipno', name: 'ipno'},
                    {data: 'site', name: 'site'},
                ],
                dom: 'Blfrtip',
                buttons:[
                    {"extend": "copy", "text": "Copy Data", "className": 'btn btn-info btn-xs'},
                    {"extend": "excel", "text": "Export to Excel", "className": 'btn btn-success btn-sm'},
                    {"extend": "pdf", "text": "Export to PDF", "className": 'btn btn-default btn-xs'},
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
                    searchPlaceholder: "Search Logs",
                },
                "order": [[5, "desc"]]
            });

            $('#study_id').on('change', function() {
                $('#stratum_id').empty();


                $.ajax({
                    url: '/study/strata/'+this.value,
                    dataType: 'JSON',
                    type: 'GET',
                    success: function(response) {
                        //console.log(response);

                        var len = response.length;

                        //console.log("Length"+len);

                        $('#stratum_id').append( '<option value="">Select Stratum</option>' );

                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var stratum = response[i]['stratum'];

                            // console.log(i);
                            // console.log(data[i].id);
                            console.log(id);
                            console.log(name);

                            console.log("<option value='"+id+"'>"+stratum+"</option>");


                            $('#stratum_id').append( '<option value="'+id+'">'+stratum+'</option>' );


                        }

                    }
                })
            });


        });



        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawAllocationChart);

        function drawAllocationChart() {
            var items = [
                    ['Treatment', 'Allocation'],

                        @foreach ($allocations as $allocation)
                            [ '{{ $allocation->allocation }}', {{ $allocation->total }}],
                        @endforeach

                ];

           // console.log(items);

            var data = google.visualization.arrayToDataTable(items);

            var options = {'title':'Allocation rates', 'width':550, 'height':300};

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }


        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawTimeRandomizationChart);

        function drawTimeRandomizationChart() {

            var items = [
                ['Date', 'Participants'],
                    @foreach ($rates as $rate)
                [ '{{ $rate->date_randomised }}', {{ $rate->total }}],
                @endforeach
            ];

            var data = google.visualization.arrayToDataTable(items);

            var options = {
                title: 'Randomization rate per day',
                // curveType: 'function',
                legend: { position: 'bottom' },
                height:300,
                pointSize: 30,
                pointShape: { type: 'star', sides: 4 }
            };

            var chart = new google.visualization.LineChart(document.getElementById('linechart'));

            chart.draw(data, options);
        }
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Randomization Log</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Randomization Log</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Randomization Log
            </div>

            <div class="card-body">

                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')

                <div class="row">

                    <div class="col-lg-12">
                        <form id="userform" action="{{ url('randomization') }}" method="post" id="user-form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{--                        spoofing--}}
                        <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>


                            <div class="row">
                                <div class="col-md-2">
                                    <select class="dropdown form-control" data-style="select-with-transition" title="Select Site" tabindex="-98"
                                            name="site_id" id="site_id" required>
                                        <option value="{{ isset($selectedSite) ? $selectedSite->id : '' }}">{{ isset($selectedSite) ? $selectedSite->site_name : 'Select Site'}}</option>


                                    @foreach( \App\Site::all() as $site)
                                            <option value="{{ $site->id  }}">{{ $site->site_name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-2">
                                    <select class="dropdown form-control" data-style="select-with-transition" title="Select Study" tabindex="-98"
                                            name="study_id" id="study_id" required>
                                        <option value="{{ isset($selectedStudy) ? $selectedStudy->id : '' }}">{{ isset($selectedStudy) ? $selectedStudy->study : 'Select Study'}}</option>

                                        @foreach( $studies as $study)
                                            <option value="{{ $study->id  }}">{{ $study->study }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-2">
                                    <select class="dropdown form-control" data-style="select-with-transition" title="Select Stratum" tabindex="-98"
                                            name="stratum_id" id="stratum_id" required>
                                        <option value="{{ isset($selectedStratum) ? $selectedStratum->id : '' }}">{{ isset($selectedStratum) ? $selectedStratum->stratum : 'Select Stratum'}}</option>

                                    </select>
                                </div>


                                <div class="col-md-2">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label" for="start_date">Start Date</label>

                                        <div class="col-sm-8">
                                            <input type="text" value="{{ empty($start_date) ? '' : $start_date }}" class="form-control datepicker" id="datepicker"  name="start_date"  />
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label" for="end_date">End Date</label>

                                        <div class="col-sm-8">
                                            <input type="text" value="{{ empty($end_date) ? '' : $end_date }}" class="form-control datepicker" id="datepicker"  name="end_date"  />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Filter</button>
                                    </div>
                                </div>
                            </div>





                        <input type="hidden" name="id" id="id"/>

                    </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie mr-1"></i>
                                @isset($selectedStudy)
                                    Treatment allocation: Study: <strong>{{$selectedStudy->study}}</strong> Stratum: <strong>{{ $selectedStratum->stratum}}</strong>
                                @else
                                    Please select a study to show treatment allocation
                                @endisset

                            </div>
                            <div id="piechart"></div>
                            <div class="card-footer small text-muted">Updated now</div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area mr-1"></i>
                                Randomization over time
                            </div>
                            <div id="linechart"></div>
                            <div class="card-footer small text-muted">Updated now</div>
                        </div>
                    </div>
                </div>


                <div class="table table-responsive">
                    <table id="random-dt" class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sequence</th>
                            <th>Study</th>
                            <th>Allocation</th>
                            <th>Stratum</th>
                            <th>Date Randomised</th>
                            <th>Participant ID</th>
                            <th>CT Staff</th>
                            <th>IPNO</th>
                            <th>Site</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Sequence</th>
                            <th>Study</th>
                            <th>Allocation</th>
                            <th>Stratum</th>
                            <th>Date Randomised</th>
                            <th>Participant ID</th>
                            <th>CT Staff</th>
                            <th>IPNO</th>
                            <th>Site</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--modal--}}
{{--    <div class="modal fade" id="user-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog ">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h4 class="modal-title" id="myModalLabel"> <span id="user-modal-title">Add </span> New User</h4>--}}
{{--                </div>--}}
{{--                <div class="modal-body" >--}}
{{--                    <form id="userform" action="{{ url('studies') }}" method="post" id="user-form" enctype="multipart/form-data">--}}
{{--                        {{ csrf_field() }}--}}
{{--                        --}}{{--spoofing--}}
{{--                        <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>--}}

{{--                        <div class="row">--}}
{{--                            <div class="col-md-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="control-label" for="first_name">First Name</label>--}}
{{--                                    <input type="text" value="{{ $edit ? $selected_user->first_name : old('first_name') }}" class="form-control" id="first_name" name="first_name" required />--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="control-label" for="first_name">Last Name</label>--}}
{{--                                    <input type="text" value="{{ $edit ? $selected_user->last_name : old('last_name') }}" class="form-control" id="last_name" name="last_name" required />--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group ">--}}
{{--                                    --}}{{--<label class="control-label" for="user_role" style="line-height: 6px;">User Role</label>--}}

{{--                                        <select class="dropdown form-control" data-style="select-with-transition" title="Choose User Group" tabindex="-98"--}}
{{--                                                name="user_group" id="user_group" required>--}}
{{--                                            @foreach( $user_roles as $user_role)--}}
{{--                                                <option value="{{ $user_role->id  }}">{{ $user_role->name }}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}

{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div>--}}


{{--                        <div class="row">--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="control-label" for="email">Email</label>--}}
{{--                                    <input type="email" value="{{$edit ? $selected_user->email :  old('email') }}" class="form-control pb-0 mt-2" name="email" id="email" required/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div>--}}

{{--                        <div class="row">--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="control-label" for="email">Phone Number</label>--}}
{{--                                    <input type="number" value="{{ old('phone_no') }}" class="form-control pb-0 mt-2" name="phone_no" id="phone_no" required/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </div>--}}



{{--                        <input type="hidden" name="id" id="id"/>--}}
{{--                        <div class="form-group">--}}
{{--                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-window-close"></i> Close</button>--}}
{{--                            <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>--}}
{{--                        </div>--}}

{{--                    </form>--}}
{{--                    --}}{{--hidden fields--}}

{{--                </div>--}}

{{--                <!--<div class="modal-footer">-->--}}
{{--                <!---->--}}
{{--                <!--</div>-->--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection
