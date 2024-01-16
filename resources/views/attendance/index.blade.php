@extends('layouts.app')

@section('content')
<div class="container pt-5">
    <div class="row">
        @include('attendance.partials.base-uploader')
        @include('attendance.partials.attendance-uploader')
    </div>
</div>
@endsection