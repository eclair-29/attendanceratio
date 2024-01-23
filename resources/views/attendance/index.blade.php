@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">{{ __('Uploader ') }}</div>
                    <ul class="list-group list-group-flush"">
                        <li class=" list-group-item">
                        <x-uploader :action="route('uploadbase')" :input="'upload_base'"
                            :inputLabel="'Upload Master File'" :btn="'base_upload_btn'" />
                        <x-progressbar :id="'base_upload_progress'" />
                        </li>

                        <li class="list-group-item">
                            <x-uploader :action="route('upload')" :input="'upload'"
                                :inputLabel="'Upload Raw Attendance File'" :btn="'upload_btn'" />
                            <x-progressbar :id="'upload_progress'" />
                        </li>

                        <li class="list-group-item">
                            <x-controls :seriesList="$seriesList" />
                            <x-result />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection