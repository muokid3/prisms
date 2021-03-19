@extends('layouts.app')

@push('js')

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

        $(function() {

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawSmsOverTimeChart);

            function drawSmsOverTimeChart() {

                var items = [
                    ['Date', 'Received', 'Processed'],
                        @foreach ($sms_chart as $sms)
                    [ '{{ $sms['date'] }}', {{ $sms['received'] }}, {{ $sms['processed'] }}],
                    @endforeach
                ];

                var data = google.visualization.arrayToDataTable(items);

                var options = {
                    title: 'Received VS Processed SMS',
                    // curveType: 'function',
                    legend: { position: 'bottom' },
                    height:300,
                    pointSize: 15,
                    pointShape: { type: 'star', sides: 4 }
                };

                var chart = new google.visualization.LineChart(document.getElementById('linechart'));

                chart.draw(data, options);
            }


        });
    </script>
@endpush



@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>


        <div class="row">

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card text-white bg-primary">
                    <div class="row" style="padding: 0.75rem 1.25rem;" >
                        <div class="col-sm-6">
                            <div class="card-icon">
                                <i class="fas fa-clinic-medical fa-3x"></i>
                            </div>
                            <p class="card-category">Total Sites</p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="card-title">{{ $sites }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('sites')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card text-white bg-warning">
                    <div class="row" style="padding: 0.75rem 1.25rem;" >
                        <div class="col-sm-6">
                            <div class="card-icon">
                                <i class="fas fa-layer-group fa-3x"></i>
                            </div>
                            <p class="card-category">Total Strata</p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="card-title">{{ $sites }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('strata')}}">View Details</a>
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
                            <h3 class="card-title">{{ $studies }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('studies')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card text-white bg-danger">
                    <div class="row" style="padding: 0.75rem 1.25rem;" >
                        <div class="col-sm-6">
                            <div class="card-icon">
                                <i class="fas fa-school fa-3x"></i>
                            </div>
                            <p class="card-category">Site Studies</p>
                        </div>
                        <div class="col-sm-6">
                            <h3 class="card-title">{{ $siteStudies }}</h3>

                        </div>

                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="{{url('site_studies')}}">View Details</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

        </div>





        <div class="row">

            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area mr-1"></i>
                        Daily SMS stats
                    </div>
                    <div id="linechart"></div>
                    <div class="card-footer small text-muted">Updated now</div>
                </div>
            </div>
        </div>


    </div>
@endsection
