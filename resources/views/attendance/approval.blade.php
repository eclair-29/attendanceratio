@extends('layouts.app')

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
                    <p class="card-text">We noticed that you have rejected the initial attendance ratio result. For
                        our
                        reference, kindly tell us the reason why you rejected the result.</p>
                    <p class="card-text">Thank you!</p>
                    <form action="" class="mb-0">
                        <div class="input-group py-3">
                            <input type="text" class="form-control" placeholder="Reason on rejection"
                                name="rejection_reason_btn">
                            <button class="btn btn-outline-dark" id="rejection_reason_btn">Post
                                Rejection</button>
                        </div>
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