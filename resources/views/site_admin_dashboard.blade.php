@extends('layouts.app')

@push('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawSmsOverTimeChart);

        function drawSmsOverTimeChart() {

            var items = [
                ['Date', 'Inbox', 'Outbox'],
                    @foreach ($final as $sms)
                [ '{{ $sms['date'] }}', {{ $sms['inbox'] }}, {{ $sms['outbox'] }}],
                @endforeach
            ];

            var data = google.visualization.arrayToDataTable(items);

            var options = {
                title: 'Randomization rate per day',
                // curveType: 'function',
                legend: { position: 'bottom' },
                height:300,
                pointSize: 15,
                pointShape: { type: 'star', sides: 4 }
            };

            var chart = new google.visualization.LineChart(document.getElementById('linechart'));

            chart.draw(data, options);
        }

    </script>
@endpush



@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Site: {{$site->site_name}}</li>
        </ol>


        <div class="row">

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card text-white bg-primary">
                    <div class="row" style="padding: 0.75rem 1.25rem;" >
                        <div class="col-sm-6">
                            <div class="card-icon">
                                <i class="fas fa-clinic-medical fa-3x"></i>
                            </div>
                            <p class="card-category">Total Allocations </p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="card-title">{{ $allocations->count() }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('randomization')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card text-white bg-success">
                    <div class="row" style="padding: 0.75rem 1.25rem;" >
                        <div class="col-sm-6">
                            <div class="card-icon">
                                <i class="fas fa-search-plus fa-3x"></i>
                            </div>
                            <p class="card-category">Total Studies</p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="card-title">{{ $studies->count() }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('studies')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
{{--            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">--}}
{{--                <div class="card text-white bg-warning">--}}
{{--                    <div class="row" style="padding: 0.75rem 1.25rem;" >--}}
{{--                        <div class="col-sm-6">--}}
{{--                            <div class="card-icon">--}}
{{--                                <i class="fas fa-school fa-3x"></i>--}}
{{--                            </div>--}}
{{--                            <p class="card-category">Site Studies</p>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-6">--}}
{{--                            <h3 class="card-title">{{ $siteStudies }}</h3>--}}

{{--                        </div>--}}

{{--                    </div>--}}
{{--                    <div class="card-footer d-flex align-items-center justify-content-between">--}}
{{--                        <a class="small text-white stretched-link" href="{{url('site_studies')}}">View Details</a>--}}
{{--                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">--}}
{{--                <div class="card text-white bg-danger">--}}
{{--                    <div class="row" style="padding: 0.75rem 1.25rem;" >--}}
{{--                        <div class="col-sm-6">--}}
{{--                            <div class="card-icon">--}}
{{--                                <i class="fas fa-layer-group fa-3x"></i>--}}
{{--                            </div>--}}
{{--                            <p class="card-category">Invalid messages</p>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-6">--}}
{{--                            <h3 class="card-title">{{ $invalid }}</h3>--}}

{{--                        </div>--}}

{{--                    </div>--}}
{{--                    <div class="card-footer d-flex align-items-center justify-content-between">--}}
{{--                        <a class="small text-white stretched-link" href="{{url('sms')}}">View Details</a>--}}
{{--                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

        </div>





        <div class="row">

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table mr-1"></i>
                        Site studies for {{$site->site_name}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Study</th>
                                    <th>Status</th>
                                    <th>Coordinator</th>
                                    <th>Start date</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>Study</th>
                                    <th>Status</th>
                                    <th>Coordinator</th>
                                    <th>Start date</th>
                                </tr>
                                </tfoot>
                                <tbody>

                                @foreach($studies as $study)
                                    <tr>
                                        <td>{{$study->study->study}}</td>
                                        <td>{{$study->status}}</td>
                                        <td>{{$study->coordinator->first_name.' '.$study->coordinator->last_name}}</td>
                                        <td>{{$study->date_initiated}}</td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area mr-1"></i>
                        SMS activity log over time
                    </div>
                    <div id="linechart"></div>
                    <div class="card-footer small text-muted">Updated now</div>
                </div>
            </div>

        </div>


    </div>
@endsection
