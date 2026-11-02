{{-- Ukurasa wa kawaida wa sehemu ya orodha.
     Inahitaji: $config, $items, $editing
     Orodha inaonekana kwanza; fomu inafunguka kwa kitufe cha "Add" (juu kulia),
     au yenyewe ikiwa unahariri rekodi au kuna makosa ya uthibitisho.
     Kama $config['details'] ipo, kila safu ina kitufe cha "View" kinachoonyesha maelezo zaidi. --}}
@php
    $isEdit     = (bool) $editing;
    $base       = $config['route'];
    $showForm   = $isEdit || $errors->any();
    $hasDetails = !empty($config['details']);
    $canAdd     = $config['can_add'] ?? true;
@endphp

@if($hasDetails)
    @push('styles')
    <style>
        .ap-detail-row td { background: #F6F9FD !important; border-bottom: 1px solid var(--line); padding-top: 14px; padding-bottom: 16px; }
        .ap-detail { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 12px 28px; margin: 0; }
        .ap-detail-item dt { margin-bottom: 2px; font-size: 12px; color: var(--muted); }
        .ap-detail-item dd { margin: 0; font-size: 14.5px; font-weight: 500; }
        .ap-detail-none { margin: 0; font-size: 13.5px; color: var(--muted); font-style: italic; }
        .ap-btn.xs { height: 32px; padding: 0 12px; font-size: 13px; gap: 5px; border-radius: 8px; }
    </style>
    @endpush
@endif

<div class="ap-wrap wide">

    @include('applicant.partials.header', [
        'title'    => $config['title'],
        'subtitle' => $config['subtitle'],
        'icon'     => $config['icon'],
    ])

    {{-- ===== FOMU: ongeza / hariri (imefichwa mpaka ubonyeze Add) ===== --}}
    <div class="ap-card" id="formCard" style="margin-bottom:16px" @if(!$showForm) hidden @endif>
        <div class="ap-card-title">
            <h2>{{ $isEdit ? 'Edit ' . strtolower($config['singular']) : 'Add ' . strtolower($config['singular']) }}</h2>
        </div>
        <div class="ap-card-body" style="padding-top:6px">
            <form method="POST" enctype="multipart/form-data" novalidate
                  action="{{ $isEdit ? route($base . '.update', $editing->id) : route($base . '.store') }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                @include('applicant.partials.fields', ['fields' => $config['fields'], 'record' => $editing])

                <div class="ap-form-actions">
                    @if($isEdit)
                        <a href="{{ route($base) }}" class="ap-btn ghost">Cancel</a>
                    @else
                        <button type="button" class="ap-btn ghost" onclick="apToggleForm(false)">Cancel</button>
                    @endif
                    <button type="submit" class="ap-btn primary">
                        <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== ORODHA ===== --}}
    <div class="ap-card">
        <div class="ap-card-title">
            <h2>Your records <span class="ap-count">{{ $items->count() }}</span></h2>
            @if($canAdd)
                <button type="button" class="ap-btn primary sm" id="addBtn" onclick="apToggleForm(true)" @if($showForm) hidden @endif>
                    <i class="bi bi-plus-lg"></i> Add {{ strtolower($config['singular']) }}
                </button>
            @endif
        </div>

        @if(!$canAdd && !empty($config['full_message']) && !$isEdit)
            <div style="margin:0 20px 12px; padding:10px 14px; border-radius:8px; font-size:13.5px; background:#ECFDF3; color:#067647; border:1px solid #ABEFC6">
                <i class="bi bi-check-circle"></i> {{ $config['full_message'] }}
            </div>
        @endif

        @if($items->isEmpty())
            <div class="ap-empty">
                <i class="bi {{ $config['icon'] }}"></i>
                <p>You have not added anything yet.</p>
                <button type="button" class="ap-btn primary" style="margin-top:12px" onclick="apToggleForm(true)">
                    <i class="bi bi-plus-lg"></i> Add {{ strtolower($config['singular']) }}
                </button>
            </div>
        @else
            <div class="ap-table-wrap">
                <table class="ap-table">
                    <thead>
                        <tr>
                            @if(!empty($config['serial']))
                                <th style="width:64px">SN</th>
                            @endif
                            @foreach($config['columns'] as $label)
                                <th>{{ $label }}</th>
                            @endforeach
                            <th class="r">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr @if($isEdit && $editing->id === $item->id) class="is-editing" @endif>
                                @if(!empty($config['serial']))
                                    <td>{{ $loop->iteration }}</td>
                                @endif
                                @foreach($config['columns'] as $col => $label)
                                    <td @if(str_ends_with($col, '_display')) style="white-space:nowrap" @endif>
                                        @include('applicant.partials.cell', ['item' => $item, 'col' => $col, 'config' => $config])
                                    </td>
                                @endforeach
                                <td class="r">
                                    <div class="ap-actions">
                                        @if($hasDetails)
                                            <button type="button" class="ap-btn ghost xs" aria-expanded="false"
                                                    onclick="apToggleDetail({{ $item->id }}, this)">
                                                <i class="bi bi-eye"></i><span>View</span>
                                            </button>
                                        @endif
                                        <a href="{{ route($base . '.edit', $item->id) }}" class="ap-icon-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route($base . '.destroy', $item->id) }}"
                                              onsubmit="return confirm('Delete this record? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ap-icon-btn danger" title="Delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Maelezo zaidi: yanaonekana akibonyeza "View" --}}
                            @if($hasDetails)
                                <tr class="ap-detail-row" id="detail-{{ $item->id }}" hidden>
                                    <td colspan="{{ count($config['columns']) + 1 + (!empty($config['serial']) ? 1 : 0) }}">
                                        @php $shown = 0; @endphp
                                        <dl class="ap-detail">
                                            @foreach($config['details'] as $dcol => $dlabel)
                                                @php $dv = $item->{$dcol}; @endphp
                                                @if($dv !== null && $dv !== '')
                                                    @php $shown++; @endphp
                                                    <div class="ap-detail-item">
                                                        <dt>{{ $dlabel }}</dt>
                                                        <dd>@include('applicant.partials.cell', ['item' => $item, 'col' => $dcol, 'config' => $config])</dd>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </dl>
                                        @if($shown === 0)
                                            <p class="ap-detail-none">No more details.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    // Onyesha / ficha fomu ya kuongeza
    function apToggleForm(show) {
        const card = document.getElementById('formCard');
        const btn  = document.getElementById('addBtn');
        if (!card) return;
        card.hidden = !show;
        if (btn) btn.hidden = show;
        if (show) {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            const first = card.querySelector('input:not([type=hidden]), select, textarea');
            if (first) first.focus({ preventScroll: true });
        }
    }

    // Onyesha / ficha maelezo ya safu moja ("View" / "Hide")
    function apToggleDetail(id, btn) {
        const row = document.getElementById('detail-' + id);
        if (!row) return;
        const opening = row.hidden;
        row.hidden = !opening;
        btn.setAttribute('aria-expanded', opening ? 'true' : 'false');
        btn.querySelector('span').textContent = opening ? 'Hide' : 'View';
        btn.querySelector('i').className = opening ? 'bi bi-eye-slash' : 'bi bi-eye';
    }
</script>
@endpush
