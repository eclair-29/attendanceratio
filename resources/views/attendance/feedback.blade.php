@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Your rejection reason has successfully been sent') }}</div>

                <div class="card-body">
                    {{ __('You successfully rejected the application. We will review your response and get back to you.
                    Thank you.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection