{{-- Sehemu za fomu. Inahitaji: $fields (array), $record (model au null) --}}
<div class="ap-form-grid">
    @foreach($fields as $name => $f)
        @php
            $type  = $f['type'] ?? 'text';
            $req   = in_array('required', $f['rules'] ?? [], true) || !empty($f['star']);

            // Sehemu inayoonekana kulingana na sehemu nyingine (mf. exam_number kwa O/A-Level)
            $cond = null;
            if (!empty($f['show_when'])) {
                $cond = ['field' => $f['show_when'][0], 'values' => $f['show_when'][1], 'mode' => 'show'];
            } elseif (!empty($f['hide_when'])) {
                $cond = ['field' => $f['hide_when'][0], 'values' => $f['hide_when'][1], 'mode' => 'hide'];
            }
            $value = old($name, $record ? $record->{$name} : ($f['default'] ?? null));
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d');
            }
            $id = 'f_' . $name;

            // Chaguo zinazotegemea sehemu nyingine (mf. programu kulingana na kiwango)
            $opts = $f['options'] ?? [];
            if (!empty($f['options_by'])) {
                $ctl  = old($f['options_by']['field'], $record ? $record->{$f['options_by']['field']} : null);
                $opts = $f['options_by']['map'][$ctl] ?? [];
            }
        @endphp

        <div class="ap-field @error($name) has-error @enderror"
             @if($cond) data-cond="{{ json_encode($cond) }}" @endif
             @if(($f['span'] ?? 1) == 2) style="grid-column: 1 / -1" @endif>

            @if($type !== 'checkbox')
                <label for="{{ $id }}">{{ $f['label'] }} @if($req)<span class="req">*</span>@endif</label>
            @endif

            @if($type === 'select')
                <select id="{{ $id }}" name="{{ $name }}"
                        @if(!empty($f['options_by'])) data-opts="{{ json_encode($f['options_by']) }}" @endif>
                    <option value="">Select...</option>
                    @foreach($opts as $opt)
                        @php
                            // Chaguo linaweza kuwa maandishi, au ['v' => thamani, 'l' => jina linaloonekana]
                            $ov = is_array($opt) ? $opt['v'] : $opt;
                            $ol = is_array($opt) ? $opt['l'] : $opt;
                        @endphp
                        <option value="{{ $ov }}" @selected((string) $value === (string) $ov)>{{ $ol }}</option>
                    @endforeach
                </select>

            @elseif($type === 'textarea')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="3" placeholder="{{ $f['placeholder'] ?? '' }}">{{ $value }}</textarea>

            @elseif($type === 'checkbox')
                <label class="ap-check" for="{{ $id }}">
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked((bool) $value)>
                    {{ $f['label'] }}
                </label>

            @elseif($type === 'file')
                <input type="file" id="{{ $id }}" name="{{ $name }}" accept=".pdf,.jpg,.jpeg,.png">
                <div class="ap-hint">
                    PDF, JPG or PNG, up to 2MB.
                    @if($record && $record->{$name})
                        Current file:
                        <a class="ap-link" href="{{ asset('storage/' . $record->{$name}) }}" target="_blank" rel="noopener">view</a>
                        (upload a new one only to replace it).
                    @endif
                </div>

            @elseif($type === 'readonly')
                <input type="text" id="{{ $id }}" value="{{ $value }}" disabled>

            @else
                <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
                       placeholder="{{ $f['placeholder'] ?? '' }}"
                       @if(isset($f['min'])) min="{{ $f['min'] }}" @endif
                       @if(isset($f['max'])) max="{{ $f['max'] }}" @endif
                       @if(isset($f['step'])) step="{{ $f['step'] }}" @endif>
            @endif

            @if(!empty($f['hint']))
                <div class="ap-hint">{{ $f['hint'] }}</div>
            @endif

            @error($name)
                <div class="ap-error">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>

@if(collect($fields)->contains(fn ($f) => !empty($f['show_when']) || !empty($f['hide_when']) || !empty($f['options_by'])))
    @push('scripts')
    <script>
        // Onyesha/ficha sehemu kulingana na chaguo (mf. Level of education)
        (function () {
            function apply(form) {
                form.querySelectorAll('[data-cond]').forEach(function (box) {
                    var c   = JSON.parse(box.dataset.cond);
                    var src = form.querySelector('[name="' + c.field + '"]');
                    if (!src) return;

                    // Kama sehemu inayodhibiti imefichwa (disabled), usiitegemee
                    var match   = !src.disabled && c.values.indexOf(src.value) !== -1;
                    var visible = (c.mode === 'show') ? match : !match;

                    box.hidden = !visible;
                    // Sehemu zilizofichwa hazitumwi wala hazithibitishwi
                    box.querySelectorAll('input, select, textarea').forEach(function (el) {
                        el.disabled = !visible;
                    });
                });
            }

            // Orodha inayotegemea sehemu nyingine (mf. programu kulingana na Level of education)
            function bindDependentSelects(form) {
                form.querySelectorAll('select[data-opts]').forEach(function (sel) {
                    var cfg = JSON.parse(sel.dataset.opts);
                    var src = form.querySelector('[name="' + cfg.field + '"]');
                    if (!src) return;

                    src.addEventListener('change', function () {
                        var list = cfg.map[src.value] || [];
                        var prev = sel.value;

                        sel.innerHTML = '';
                        sel.add(new Option('Select...', ''));
                        var values = [];
                        list.forEach(function (o) {
                            // Chaguo ni maandishi, au { v: thamani, l: jina linaloonekana }
                            var v = (typeof o === 'object') ? o.v : o;
                            var l = (typeof o === 'object') ? o.l : o;
                            values.push(v);
                            sel.add(new Option(l, v));
                        });

                        if (prev && values.indexOf(prev) !== -1) sel.value = prev;
                    });
                });
            }

            document.querySelectorAll('form').forEach(function (form) {
                if (!form.querySelector('[data-cond], select[data-opts]')) return;
                bindDependentSelects(form);   // lazima iwe kabla ya apply: inasasisha orodha kwanza
                apply(form);
                form.addEventListener('change', function () { apply(form); });
            });
        })();
    </script>
    @endpush
@endif
