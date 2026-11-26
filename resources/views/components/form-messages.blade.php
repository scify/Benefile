{{-- The validation errors of the last form, or else its success message. --}}
@props(['success' => null])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@elseif ($success)
    <div class="alert alert-success">
        <ul>
            <li>{{ $success }}</li>
        </ul>
    </div>
@endif
