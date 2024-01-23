@props(['seriesList'])

<div class="controls row">
    <div class="col d-flex justify-content-end">
        <a class="btn btn-outline-dark disabled" id="export_series_btn">Export</a>
        <button class="btn btn-outline-dark" disabled id="init_notif_btn">Send Initial
            Notif</button>
        <select class="form-select" name="series" id="series">
            <option selected disabled value="">Select Series</option>
            @foreach ($seriesList as $series)
            <option value="{{ $series->id }}">{{ str_replace('_', ' ', $series->series) }}</option>
            @endforeach
        </select>
    </div>
</div>