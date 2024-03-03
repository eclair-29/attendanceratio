@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Download Attendance Ratio') }}</div>

                <div class="card-body mb-2" class="">
                    <p>Please click on below link to download {{ $division }} Attendance Ratio.</p>
                    <p>Thank you.</p>
                    <a href="{{ route('export.division', ['division' => $division, 'series' => $series]) }}"
                        class="btn btn-outline-secondary" id="download_division">Download Excel File</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection