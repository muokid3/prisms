@extends('layouts.app')


@section('content')
    <div class="container-fluid">
        <h1 class="mt-4">Allocation List</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Allocation List</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                Upload allocation list from this page
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
               Upload List
            </div>
            <div class="card-body">

                <div class="toolbar">
                    <a href="{{url('assets/sample_allocation_list.csv')}}" class="btn btn-primary btn-sm" >
                        <i class="fa fa-download"></i> Download sample file
                    </a>
                </div>
                @include('layouts.success')
                @include('layouts.warnings')
                @include('layouts.warning')

                <form id="userform" action="{{ url('allocation/upload') }}" method="post" id="user-form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" id="user-spoof-input" value="PUT" disabled/>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="control-label" for="site" style="line-height: 6px;">Site</label>

                                <select class="dropdown form-control" data-style="select-with-transition" title="Choose Site" tabindex="-98"
                                        name="site" id="site" required>
                                    <option value="">Select site</option>
                                    @foreach( $sites as $site)
                                        <option value="{{ $site->id  }}">{{ $site->site_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="control-label" for="study" style="line-height: 6px;">Study</label>

                                <select class="dropdown form-control" data-style="select-with-transition" title="Choose Study" tabindex="-98"
                                        name="study" id="study" required>
                                    <option value="">Select study</option>
                                    @foreach( $studies as $study)
                                        <option value="{{ $study->id  }}">{{ $study->study }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="control-label" for="stratum" style="line-height: 6px;">Stratum</label>

                                <select class="dropdown form-control" data-style="select-with-transition" title="Choose Stratum" tabindex="-98"
                                        name="stratum" id="stratum" >
                                    <option value="">Select stratum</option>
                                    @foreach( $strata as $stratum)
                                        <option value="{{ $stratum->id  }}">{{ $stratum->stratum }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="file">Select File</label>
                                <input type="file" name="file"  class="form-control" required />
                            </div>
                        </div>

                    </div>



                    <input type="hidden" name="id" id="id"/>
                    <div class="form-group">
                        <button class="btn btn-success" id="save-brand"><i class="fa fa-save"></i> Save</button>
                    </div>

                </form>



            </div>
        </div>
    </div>

@endsection
