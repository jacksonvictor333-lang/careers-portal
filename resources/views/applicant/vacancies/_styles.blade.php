<style>
    .vc-alert { display: flex; gap: 10px; align-items: flex-start; margin: 0 0 14px; padding: 11px 14px; font-size: 13.5px; border: 1px solid #FECDCA; border-radius: 6px; color: #B42318; background: #FEF3F2; }
    .vc-alert.ok { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }

    .vc-card { display: grid; grid-template-columns: minmax(0, 1fr) 220px; gap: 18px; margin-bottom: 12px; padding: 18px 20px; background: #fff; border: 1px solid var(--line); border-radius: 6px; }
    .vc-card.closed { opacity: .72; }
    .vc-title { margin: 0 0 2px; font-size: 17px; font-weight: 700; }
    .vc-title a { color: var(--ink); text-decoration: none; }
    .vc-title a:hover { color: var(--navy); text-decoration: underline; }
    .vc-employer { font-size: 13.5px; color: #374151; }
    .vc-ref { font-size: 12px; color: var(--muted); }
    .vc-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .vc-chip { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; font-size: 12px; color: #374151; background: #F3F4F6; border-radius: 999px; }
    .vc-side { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: 10px; text-align: right; }
    .vc-close { font-size: 12.5px; color: var(--muted); }
    .vc-close b { display: block; font-size: 14px; color: var(--ink); }
    .vc-badge { display: inline-block; padding: 2px 10px; font-size: 12px; font-weight: 600; border-radius: 999px; border: 1px solid; }
    .vc-badge.g { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }
    .vc-badge.a { color: #B54708; background: #FFFAEB; border-color: #FEDF89; }
    .vc-badge.r { color: #B42318; background: #FEF3F2; border-color: #FECDCA; }
    .vc-badge.b { color: #175CD3; background: #EFF8FF; border-color: #B2DDFF; }
    .vc-btn { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 14px; font: inherit; font-size: 13px; font-weight: 600; color: #fff; background: var(--navy); border: 0; border-radius: 4px; cursor: pointer; text-decoration: none; }
    .vc-btn:hover { opacity: .92; color: #fff; }
    .vc-btn.ghost { color: var(--navy); background: #fff; border: 1px solid var(--navy); }
    .vc-btn.ghost:hover { color: var(--navy); background: #F3F8FD; }
    .vc-btn[aria-disabled=true] { opacity: .45; pointer-events: none; }

    .vc-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 18px; align-items: start; }
    .vc-panel { margin-bottom: 14px; background: #fff; border: 1px solid var(--line); border-radius: 6px; }
    .vc-ph { padding: 13px 20px; font-size: 15px; font-weight: 700; border-bottom: 1px solid var(--line); }
    .vc-pb { padding: 14px 20px; font-size: 13.5px; line-height: 1.65; color: #374151; }
    .vc-pb ul { margin: 0; padding-left: 20px; }
    .vc-pb li { margin-bottom: 4px; }
    .vc-kv { display: flex; justify-content: space-between; gap: 10px; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #F3F4F6; }
    .vc-kv:last-child { border-bottom: 0; }
    .vc-kv span { color: var(--muted); }
    .vc-check { display: flex; gap: 10px; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #F3F4F6; }
    .vc-check:last-child { border-bottom: 0; }
    .vc-check i { margin-top: 2px; font-size: 16px; }
    .vc-check.ok i { color: #12B76A; }
    .vc-check.no i { color: #D92D20; }
    .vc-check small { display: block; color: var(--muted); }

    .vc-ol { margin: 0; padding-left: 22px; }
    .vc-ol li { margin-bottom: 5px; }
    .vc-addr { padding: 14px 16px; font-size: 14px; line-height: 1.7; color: #111827; background: #F6F9FD; border: 1px solid #D5E4F8; border-radius: 4px; }
    .vc-addr b { display: block; margin-bottom: 2px; font-size: 11.5px; letter-spacing: .06em; text-transform: uppercase; color: #175CD3; }
    .vc-cond { margin-bottom: 12px; padding: 10px 14px; font-size: 12.5px; color: #374151; background: #F9FAFB; border: 1px solid var(--line); border-radius: 4px; }
    .vc-cond summary { cursor: pointer; font-weight: 600; }
    .vc-cond ol { margin: 8px 0 0; padding-left: 20px; line-height: 1.6; }
    .vc-field { margin-bottom: 14px; }
    .vc-field label.t { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600; }
    .vc-field input[type=text], .vc-field textarea { width: 100%; padding: 9px 12px; font: inherit; font-size: 14px; color: var(--ink); background: #fff; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; }
    .vc-field textarea { min-height: 280px; line-height: 1.6; resize: vertical; }
    .vc-field input:focus, .vc-field textarea:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }
    .vc-hint { margin-top: 4px; font-size: 12px; color: var(--muted); }
    .vc-att { display: flex; align-items: center; gap: 10px; padding: 8px 12px; margin-bottom: 6px; font-size: 13.5px; border: 1px solid var(--line); border-radius: 4px; cursor: pointer; }
    .vc-att small { color: var(--muted); }
    .vc-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 8px; }

    .ba-modal { position: fixed; inset: 0; z-index: 300; display: flex; align-items: center; justify-content: center; padding: 18px; background: rgba(16,24,40,.55); }
    .ba-modal[hidden] { display: none; }
    .ba-box { width: min(580px, 100%); max-height: 94vh; overflow: auto; background: #fff; border-radius: 16px; box-shadow: 0 30px 70px -20px rgba(0,0,0,.5); }
    .ba-head { display: flex; gap: 12px; align-items: center; padding: 18px 24px; color: #fff; background: linear-gradient(135deg, #12335C, #1D4E89); }
    .ba-head i { font-size: 26px; } .ba-head h2 { margin: 0; font-size: 18px; } .ba-head p { margin: 2px 0 0; font-size: 12.5px; opacity: .85; }
    .ba-body { padding: 20px 24px; }
    .ba-lbl { margin-bottom: 6px; font-size: 11.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--navy); }
    .ba-addr { margin-bottom: 14px; padding: 13px 16px; font-size: 14px; line-height: 1.7; background: #F3F8FD; border: 1px solid #BFD3EC; border-left: 4px solid var(--navy); border-radius: 10px; }
    .ba-subj { margin-bottom: 14px; padding: 11px 14px; font-size: 13.5px; background: #FAFBFD; border: 1px solid var(--line); border-radius: 10px; }
    .ba-copy { display: inline-flex; align-items: center; gap: 6px; height: 32px; margin: 0 6px 4px 0; padding: 0 12px; font: inherit; font-size: 12.5px; font-weight: 600; color: var(--navy); background: #fff; border: 1px solid #BFD3EC; border-radius: 8px; cursor: pointer; }
    .ba-copy:hover { background: #EEF4FB; }
    .ba-foot { display: flex; justify-content: flex-end; gap: 10px; padding: 14px 24px; background: #F9FAFB; border-top: 1px solid var(--line); }
    .vc-confirm { display: flex; gap: 12px; padding: 14px 16px; font-size: 13.5px; line-height: 1.55; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; cursor: pointer; }
    .vc-confirm input { flex: none; width: 20px; height: 20px; margin-top: 2px; accent-color: #1D4E89; }
    .vc-confirm .a { margin-top: 6px; padding: 8px 12px; font-size: 13px; background: #fff; border: 1px solid var(--line); border-radius: 8px; }
    @media (max-width: 900px) { .vc-card { grid-template-columns: 1fr; } .vc-side { align-items: flex-start; text-align: left; } .vc-grid { grid-template-columns: 1fr; } }
</style>
