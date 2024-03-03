@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col" style="margin: 20px auto;">
            <div class="card">
                @if ($approval->is_expired == 'yes')
                <x-card :header="'Expired'">
                    <p class="card-text">This request has been expired</p>
                </x-card>
                @endif

                @if ($approval->status == 'rejected' && $approval->is_expired == 'no')
                <x-card :header="'Request Rejected'">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <p class="card-text">We noticed that you have rejected the initial attendance ratio result. For
                        our
                        reference, kindly tell us the reason why you rejected the result.</p>
                    <p class="card-text">Thank you!</p>
                    <form action="{{ route('notifications.rejection', $approval->id) }}" enctype="multipart/form-data"
                        method="post" class="mb-0">
                        @csrf
                        @method('put')
                        <div class="pb-3">
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="5"
                                placeholder="Reason for rejection"></textarea>
                            @error('reason')
                            <div class="form-text text-danger fw-bold">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="pb-3">
                            <label for="division_ratio_changes" class="form-label">Attach File with Attendance
                                Changes</label>
                            <input type="file" name="division_ratio_changes" class="form-control">
                        </div>
                        <button class="btn btn-outline-success">
                            Post Rejection
                        </button>
                    </form>
                </x-card>
                @endif

                @if ($approval->status == 'approved' && $approval->is_expired == 'no')
                <x-card :header="'Request Approved'">
                    <p class="card-text">We successfully got your approval</p>
                    <p class="card-text">Thank you!</p>
                </x-card>
                @endif
            </div>
        </div>
    </div>
</div>
@endSection