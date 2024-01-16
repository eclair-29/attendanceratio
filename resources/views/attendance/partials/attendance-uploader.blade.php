<div class="col">
    <div class="card">
        <div class="card-header">
            <span class="text-danger fw-bold">Step 2 </span> Upload Raw Attendance File
        </div>
        <div class="card-body">
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="upload" class="form-label">Upload Attendance</label>
                    <input type="file" class="form-control" name="upload"
                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    <small class="form-text">*Only accepts excel (.xlsx) files</small>
                    @error('upload')
                    <small class="form-text text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-outline-success">Upload File</button>
            </form>
            <div class="progress mt-3" hidden>
                <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0"
                    aria-valuemax="100">25%</div>
            </div>
        </div>
    </div>
</div>