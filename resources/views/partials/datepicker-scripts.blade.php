{{-- The calendar of the date fields (.date-input), in the language of the page. Its CSS is vendor/vanillajs-datepicker/datepicker.min.css. --}}
<script src="{{ asset('vendor/vanillajs-datepicker/datepicker.min.js') }}"></script>
@if (app()->getLocale() === 'gr')
<script src="{{ asset('vendor/vanillajs-datepicker/locales/el.js') }}"></script>
@endif
<script src="{{ asset('js/records/custom_datepicker.js') }}"></script>
