@props(['seriesList'])

<div class="controls row">
    <div class="col pt-1" id="attendance_table_loading" hidden>
        <span class="spinner-border text-success" role="status"></span>
        <span class="fw-bold text-success">Loading attendance table...</span>
    </div>
    <div class="col d-flex justify-content-end">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-dark" disabled id="clear_filter_btn">Clear Filter</button>
            <a class="btn btn-outline-dark disabled" id="export_series_btn">Export</a>
            <button class="btn btn-outline-dark" disabled id="init_notif_btn">Send Initial Mail</button>
        </div>

        <select class="form-select" name="series" id="series">
            <option selected disabled value="">Select Series</option>
            @foreach ($seriesList as $series)
            <option value="{{ $series->id }}">{{ date('F Y', strtotime(str_replace('_', '-', $series->series))) }}
            </option>
            @endforeach
        </select>
    </div>
</div>