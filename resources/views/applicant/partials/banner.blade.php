{{-- Banner ya portal (public/images/banner.webp). Ina CSS yake mwenyewe, kwa hiyo inafanya kazi kwenye ukurasa wowote,
     hata ule usiotumia applicant.layout (login, makosa, n.k.). Matumizi:  @include('applicant.partials.banner') --}}
<style>
    .pb-banner { margin: 0; background: #0B1220; line-height: 0; }
    .pb-banner img { display: block; width: 100%; height: auto; min-height: 64px; object-fit: cover; }
    .pb-banner.plain { display: flex; align-items: center; justify-content: center; min-height: 90px; line-height: 1.3; color: #fff; font: 700 20px Georgia, serif; background: linear-gradient(90deg, #0A8F3C, #0B1220 50%, #1D6FD8); }
    @media print { .pb-banner { display: none; } }
</style>
<header class="pb-banner @if(! file_exists(public_path('images/banner.webp'))) plain @endif">
    @if(file_exists(public_path('images/banner.webp')))
        <img src="{{ asset('images/banner.webp') }}" alt="{{ config('portal.name', 'Careers Portal') }}">
    @else
        {{ config('portal.name', 'Careers Portal') }}
    @endif
</header>
