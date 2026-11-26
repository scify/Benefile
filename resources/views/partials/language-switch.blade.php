{{-- The link to the other language of the interface. --}}
@php($other = app()->getLocale() === 'en' ? 'gr' : 'en')
<a class="language-switch" href="{{ route('language', $other) }}" lang="{{ \App\Http\Middleware\SetLocale::LANGUAGES[$other] }}" hreflang="{{ \App\Http\Middleware\SetLocale::LANGUAGES[$other] }}">
    {{ $other === 'en' ? 'English' : 'Ελληνικά' }}
</a>
