@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('BU Mailing List') }}</div>

                <div class="card-body">
                    <x-alert />

                    <table class="table table-bordered table-striped py-3" id="division_list" width="100%">
                        <thead>
                            <tr>
                                <th scope="col">Division</th>
                                <th scope="col">Email</th>
                                <th scope="col">Updated at</th>
                                <th scope="col">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($divisionList as $row)
                            <tr>
                                <td>{{ $row->division }}</td>
                                <td>{{ $row->div_head }}</td>
                                <td>{{ $row->updated_at }}</td>
                                <td>
                                    <a href="" class="link-success" data-bs-toggle="modal"
                                        data-bs-target="#update_division_popup_{{ $row->id }}">Edit</a>
                                </td>
                            </tr>

                            <x-popup :size="'lg'" :id="'update_division_popup_' . $row->id"
                                :title="'Update BU Email: ' . $row->division">
                                <form method="post" action="{{ route('utilities.bu.update', $row->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="division_email" class="form-label">Email address</label>
                                        <input value="{{ $row->div_head }}" type="email" class="form-control"
                                            id="division_email" name="division_email">
                                    </div>
                                    <button class="btn btn-outline-primary">Update</button>
                                </form>
                            </x-popup>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection