<div class="col">
    <div class="card">
        <div class="card-header">
            <span class="text-danger fw-bold">Step 1 </span> Upload Master Details File
        </div>
        <div class="card-body">
            <form action="{{ route('uploadbase') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="upload_base" class="form-label">Upload Attendance</label>
                    <input type="file" class="form-control" name="upload_base"
                        accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    <small class="form-text">*Only accepts excel (.xlsx) files</small>
                    @error('upload_base')
                    <small class="form-text text-danger d-block">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-outline-success">Upload File</button>
            </form>
        </div>
    </div>
</div>