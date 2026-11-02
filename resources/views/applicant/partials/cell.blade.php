{{-- Thamani ya seli moja (jedwali na maelezo). Inahitaji: $item, $col, $config --}}
@php
    $v      = $item->{$col};
    $isFile = (($config['fields'][$col]['type'] ?? '') === 'file');
@endphp
@if($isFile)
    @if($v)
        <a class="ap-link" href="{{ asset('storage/' . $v) }}" target="_blank" rel="noopener">
            <i class="bi bi-paperclip"></i> Open
        </a>
    @else
        —
    @endif
@elseif($v instanceof \Carbon\CarbonInterface)
    {{ $v->format('d M Y') }}
@elseif(is_bool($v))
    {{ $v ? 'Yes' : 'No' }}
@else
    {{ ($v !== null && $v !== '') ? $v : '—' }}
@endif
