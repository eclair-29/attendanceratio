@props(['action', 'input', 'inputLabel', 'btn'])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="pb-2 text-dark">{{ $inputLabel }}</div>
    <div class="input-group">
        <input type="file" class="form-control" name="{{ $input }}"
            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
        <button class="btn btn-outline-dark" id="{{ $btn }}">
            Upload
        </button>
    </div>
    @error($input)
    <div class="form-text text-danger">{{ $message }}</div>
    @enderror
    <!-- <div class="alert alert-info mt-3 mb-0 d-flex align-items-center" role="alert">
        <i data-feather="info"></i>
        <div>
            Master file staff count should be greater than or equal to the staff
            count of
            the attendance file to be
            uploaded to avoid data integrity issues.
        </div>
    </div> -->
</form>