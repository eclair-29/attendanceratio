@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <x-alert />

                    <form method="POST" action="{{ route('users.reset.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row pb-3">
                            <label for="current_password" class="form-label col-4 text-end">Current Password</label>
                            <div class="col-6">
                                <input type="password" name="current_password" class="form-control">
                            </div>
                        </div>

                        <div class="row pb-3">
                            <label for="new_password" class="form-label col-4 text-end">New Password</label>
                            <div class="col-6">
                                <input type="password" name="new_password" class="form-control">
                            </div>
                        </div>

                        <div class="row pb-3">
                            <label for="retype_new_password" class="form-label col-4 text-end">Re-type New
                                Password</label>
                            <div class="col-6">
                                <input type="password" name="retype_new_password" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6 offset-4">
                                <button class="btn btn-outline-success" type="submit">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection