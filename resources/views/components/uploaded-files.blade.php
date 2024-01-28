@props(['files', 'type' ])

<div class="pt-2 uploaded-files d-flex">
    @if ($type == 'base_data' && $files)
    <span class="">Current uploaded Master File: </span>
    <ul class="d-flex px-0">
        <li class="px-2">
            <a href="{{ route('downloadfile', ['file' => $files, 'type' => $type]) }}" class="link-success">{{
                $files }}</a>
        </li>
    </ul>
    @endif

    @if ($type == 'attendance' && count($files) > 0)
    <span class="">Uploaded Files: </span>
    <ul class="px-0">
        @foreach ($files->take(3) as $row)
        <li class="px-2 d-inline">
            <a href="{{ route('downloadfile', ['file' => $row->file, 'type' => $type]) }}" class="link-success">{{
                $row->file }}</a>
        </li>
        @endforeach
    </ul>
    @if (count($files) > 3)
    <a class="link-danger" data-bs-toggle='modal' data-bs-target='#uploaded_files_history'>Show More</a>
    @endif
    @endif
</div>