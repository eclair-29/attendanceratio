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
                        <x-uploader :action="route('upload.base')" :input="'upload_base'"
                            :inputLabel="'Upload Master File'" :btn="'base_upload_btn'" />
                        <x-uploaded-files :files="$currentUploadedBaseFile->file ?? null" :type="'base_data'" />
                        <x-progressbar :id="'base_upload_progress'" />
                        </li>

                        <li class="list-group-item">
                            <x-uploader :action="route('upload.attendance')" :input="'upload'"
                                :inputLabel="'Upload Raw Attendance File'" :btn="'upload_btn'" />
                            <x-uploaded-files :files="$recentUploadedAttendanceFile ?? []" :type="'attendance'" />
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

<x-popup :size="'xl'" :id="'uploaded_files_history'" :title="'Uploaded Files History'">
    <table class="table table-bordered py-3" id="uploaded_files_history_table" width="100%">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">FILE NAME</th>
                <th scope="col">ADDED AT</th>
                <th scope="col">FILE SIZE</th>
                <th scope="col">TYPE</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($uploadedFileHistory ?? [] as $row)
            <tr>
                <td>
                    {{ $row->id }}
                </td>
                <td>
                    <a href="{{ route('download', ['file' => $row->file, 'type' => 'attendance']) }}"
                        class="link-success fw-bold">
                        {{ $row->file }}
                    </a>
                </td>
                <td>{{ $row->updated_at }}</td>
                <td>{{ round($row->size / 1048576, 2) }} MB</td>
                <td>{{ $row->type }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-popup>
@endsection