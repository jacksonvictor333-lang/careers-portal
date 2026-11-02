{{-- Kichwa cha ukurasa + ujumbe wa mafanikio/makosa. Inahitaji: $title, $subtitle, $icon --}}
<div class="ap-pagehead">
    <div class="ico"><i class="bi {{ $icon }}"></i></div>
    <div>
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
    </div>
</div>

@if(session('success'))
    <div class="ap-alert ok" style="margin-bottom:14px" role="status">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="ap-alert" style="margin-bottom:14px" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
            <b>Please fix the following:</b>
            <ul class="ap-errlist">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
