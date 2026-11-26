{{-- The name and the folder number of the beneficiary, above the folders. --}}
@props(['benefiter' => null])
@if ($benefiter)
    <div class="benefiter-info">
        <h2>{{ $benefiter->name }} {{ $benefiter->lastname }} / {{ $benefiter->folder_number }}</h2>
    </div>
@endif
