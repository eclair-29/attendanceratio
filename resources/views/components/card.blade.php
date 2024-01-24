@props(['header'])

<div class="card-header">{{ $header }}</div>
<div class="card-body">
    {{ $slot }}
</div>