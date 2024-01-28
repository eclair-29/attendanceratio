@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-8" style="margin: 20px auto;">
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
                    <form action="{{ route('rejection', $approval->id) }}" method="POST" class="mb-0">
                        @csrf
                        @method('PUT')
                        <div class="input-group pt-3">
                            <input type="text" class="form-control" placeholder="Reason on rejection" name="reason">
                            <button class="btn btn-outline-dark" id="rejection_reason_btn">Post
                                Rejection</button>
                        </div>
                        @error('reason')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
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