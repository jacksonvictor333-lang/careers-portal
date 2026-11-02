<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Applicant Portal') - {{ config('portal.name', 'Careers Portal') }}</title>

    {{-- Ikoni za dashboard (<i class="bi ...">). Kama CDN inazuiwa, pakia bootstrap-icons kupitia npm/Vite. --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --green:  #0A8A3F;
            --yellow: #FCD116;
            --black:  #0B1320;
            --blue:   #1E6FDC;
            --navy:   #1D4E89;
            --navy-d: #163C6B;
            --ink:    #111827;
            --muted:  #5B6472;
            --line:   #E5E7EB;
            --danger: #B42318;
            --bg:     #FAFAF8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            display: flex; flex-direction: column;
            font-family: Inter, "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            color: var(--ink); background: var(--bg);
        }

        /* ================= BANNER ================= */
        .banner {
            display: grid;
            grid-template-columns: 31% 31% 1fr;
            align-items: center;
            min-height: 120px;
            color: #fff;
            background: linear-gradient(115deg,
                var(--green)  0 29%,
                var(--yellow) 29% 32%,
                var(--black)  32% 62%,
                var(--yellow) 62% 65%,
                var(--blue)   65% 100%);
        }
        .banner-img { background: #0B1220; line-height: 0; }
        .banner-img img { display: block; width: 100%; height: auto; min-height: 64px; object-fit: cover; }
        .banner-brand { display: flex; align-items: center; gap: 14px; padding: 16px 24px; }
        .banner-logo {
            width: 56px; height: 56px; flex-shrink: 0;
            display: grid; place-items: center; overflow: hidden;
            border-radius: 50%;
            background: rgba(255,255,255,.18); border: 2px solid rgba(255,255,255,.4);
        }
        .banner-logo img { width: 100%; height: 100%; object-fit: contain; background: #fff; }
        .banner-logo svg { width: 28px; height: 28px; }
        .banner-name { font-family: Georgia, "Times New Roman", serif; font-style: italic; font-size: 24px; font-weight: 600; }
        .banner-title { text-align: center; padding: 14px 64px; line-height: 1.35; }
        .banner-title .l1 { font-size: 17px; font-weight: 700; letter-spacing: .02em; text-transform: uppercase; }
        .banner-title .l2 { font-size: 13px; font-weight: 600; text-transform: uppercase; opacity: .95; }

        /* ===== Banner + nav bar zisizosogea; sidebar inabaki chini yake ===== */
        .ap-top { position: sticky; top: 0; z-index: 60; }
        .ap-top .banner-img img { height: auto; min-height: 0; }   /* picha nzima, nembo ya taifa haikatwi */
        .shell > .side { position: sticky; top: var(--ap-top-h, 143px); align-self: start; height: calc(100vh - var(--ap-top-h, 143px)); overflow-y: auto; }

        /* ================= NAV ================= */
        .nav {
            display: flex; justify-content: flex-end; align-items: center; gap: 6px;
            padding: 10px 28px; background: #fff; border-bottom: 1px solid var(--line);
        }
        .nav a {
            padding: 9px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 500; text-transform: uppercase;
            color: #374151; text-decoration: none;
        }
        .nav a:hover { background: #F3F4F6; }
        .nav a.active { background: var(--navy); color: #fff; }

        .nav-user {
            display: flex; align-items: center; gap: 10px;
            margin-left: 10px; padding-left: 14px; border-left: 1px solid var(--line);
        }
        .nav-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            display: grid; place-items: center;
            font-size: 12px; font-weight: 700; color: var(--navy);
            background: #DBEAFE; border: 1px solid #93C5FD;
        }
        .nav-uname { font-size: 13px; font-weight: 600; max-width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .nav .nav-userlink {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 4px 12px 4px 4px; border-radius: 999px;
            text-transform: none; font-size: 13px; color: var(--ink);
            border: 1px solid transparent;
        }
        .nav .nav-userlink:hover { background: #F3F6FA; border-color: var(--line); }
        .nav .nav-userlink.active { background: #EAF1FA; border-color: #BFD3EC; color: var(--navy); }
        .nav-user { position: relative; }
        .nav .nav-userlink { background: none; font: inherit; cursor: pointer; }
        .nav-caret { font-size: 11px; color: var(--muted); transition: transform .15s; }
        .nav-userlink[aria-expanded="true"] .nav-caret { transform: rotate(180deg); }
        .user-dd {
            position: absolute; right: 0; top: calc(100% + 8px); min-width: 190px; z-index: 60;
            background: #fff; border: 1px solid var(--line); border-radius: 10px;
            box-shadow: 0 12px 30px rgba(16,24,40,.14); padding: 6px;
        }
        .user-dd[hidden] { display: none; }
        .user-dd a, .user-dd .dd-logout {
            display: flex; align-items: center; gap: 10px; width: 100%;
            padding: 9px 12px; border-radius: 7px; border: 0; background: none; cursor: pointer;
            font: inherit; font-size: 13.5px; font-weight: 500; color: var(--ink);
            text-transform: none; text-decoration: none; text-align: left;
        }
        .user-dd a:hover, .user-dd .dd-logout:hover { background: #F3F6FA; }
        .user-dd .dd-logout { color: var(--danger); }
        .user-dd .dd-logout:hover { background: #FEF3F2; }
        .user-dd form { margin: 4px 0 0; padding-top: 4px; border-top: 1px solid var(--line); }
        .nav-logout {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 12px; border-radius: 8px; cursor: pointer;
            font: inherit; font-size: 13px; font-weight: 600;
            color: var(--danger); background: #fff; border: 1px solid #FECDCA;
        }
        .nav-logout:hover { background: #FEF3F2; }

        /* ================= MAIN + CARD (inatumika na login na dashboard) ================= */
        .main { flex: 1; padding: 32px 16px 48px; }

        .ap-wrap { width: 100%; max-width: 440px; margin: 0 auto; }
        .ap-wrap.wide { max-width: 960px; }

        .ap-card {
            background: #fff; border: 1px solid var(--line); border-radius: 18px; overflow: hidden;
            box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 18px 40px -20px rgba(16,24,40,.18);
        }
        .ap-card-head { text-align: center; padding: 28px 32px 18px; }
        .ap-card-head h1 { margin: 0 0 4px; font-size: 22px; font-weight: 600; }
        .ap-card-head p  { margin: 0; font-size: 14px; color: var(--muted); }
        .ap-card-body { padding: 8px 32px 28px; }

        .ap-card-ico {
            width: 56px; height: 56px; margin: 0 auto 10px;
            display: grid; place-items: center; flex-shrink: 0;
            border-radius: 50%;
            font-size: 18px; font-weight: 700; letter-spacing: .02em;
            color: var(--navy); background: #DBEAFE; border: 1px solid #93C5FD;
        }

        .ap-alert {
            display: flex; gap: 8px; align-items: flex-start;
            padding: 10px 12px; border-radius: 8px; font-size: 13px;
            color: var(--danger); background: #FEF3F2; border: 1px solid #FECDCA;
        }
        .ap-alert.ok { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }
        .ap-alert i { margin-top: 1px; }

        /* ================= SIDEBAR ================= */
        .shell { flex: 1; display: grid; grid-template-columns: 272px minmax(0, 1fr); }
        .shell .main { padding: 28px 24px 48px; min-width: 0; }

        .side { background: #fff; border-right: 1px solid var(--line); padding: 22px 14px 26px; }

        .side-profile { text-align: center; padding: 0 6px 18px; margin-bottom: 12px; border-bottom: 1px solid var(--line); }
        .side-photo {
            width: 84px; height: 84px; margin: 0 auto 10px; overflow: hidden;
            display: grid; place-items: center;
            border-radius: 50%; font-size: 26px; font-weight: 700; color: var(--navy);
            background: #DBEAFE; border: 3px solid #fff; box-shadow: 0 0 0 1px #93C5FD, 0 6px 14px -6px rgba(29,78,137,.35);
        }
        .side-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .side-name { font-size: 15px; font-weight: 700; line-height: 1.3; word-break: break-word; }
        .side-no   { margin-top: 2px; font-size: 12px; color: var(--muted); }
        .side-updated { margin-top: 10px; font-size: 12px; color: var(--muted); line-height: 1.5; }
        .side-updated b { color: var(--ink); font-weight: 600; }

        .side-upload {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 12px; padding: 7px 14px; border-radius: 999px; cursor: pointer;
            font-size: 12.5px; font-weight: 600; color: var(--navy);
            background: #fff; border: 1px solid #BFD3EC; transition: background .15s;
        }
        .side-upload:hover { background: #EEF4FB; }
        .side-upload.is-disabled { opacity: .55; cursor: not-allowed; }
        .side-profile form { margin: 0; }

        /* Akaunti haijawashwa: sehemu zote zinaonekana hafifu na hazibonyezeki */
        .ap-inactive-banner { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px; padding: 14px 18px; font-size: 14px; line-height: 1.5; color: #7A2E0E; background: #FFF4ED; border: 1px solid #F9B98C; border-left: 4px solid #F79009; border-radius: 10px; }
        .ap-inactive-banner i { margin-top: 2px; font-size: 18px; color: #F79009; }
        .ap-inactive-banner b { display: block; }
        .is-inactive .side-menu a, .is-inactive .side-upload, .is-inactive .main-body { opacity: .38; filter: grayscale(.6); pointer-events: none; user-select: none; }
        .side-menu { list-style: none; margin: 0; padding: 0; }
        .side-menu a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; margin-bottom: 2px; border-radius: 8px;
            font-size: 14px; font-weight: 500; color: #374151; text-decoration: none;
            border-left: 3px solid transparent; transition: background .15s, color .15s;
        }
        .side-menu a i { width: 20px; text-align: center; font-size: 16px; color: #8A94A6; transition: color .15s; }
        .side-menu a:hover { background: #F3F4F6; color: var(--ink); }
        .side-menu a:hover i { color: var(--navy); }
        .side-menu a.active { background: #EAF1FA; color: var(--navy); font-weight: 600; border-left-color: var(--navy); }
        .side-menu a.active i { color: var(--navy); }

        .nav-toggle {
            display: none; align-items: center; gap: 6px; margin-right: auto;
            padding: 8px 12px; border-radius: 8px; cursor: pointer;
            font: inherit; font-size: 13px; font-weight: 600; color: var(--navy);
            background: #fff; border: 1px solid #BFD3EC;
        }

        /* ================= PAGE HEADER + FORMS + TABLES (kurasa za profile) ================= */
        .ap-pagehead { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .ap-pagehead .ico {
            width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center;
            border-radius: 12px; font-size: 20px; color: var(--navy);
            background: #DBEAFE; border: 1px solid #93C5FD;
        }
        .ap-pagehead h1 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -.01em; }
        .ap-pagehead p  { margin: 2px 0 0; font-size: 13px; color: var(--muted); }
        .ap-errlist { margin: 4px 0 0; padding-left: 18px; }

        .ap-card-title { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 24px 8px; }
        .ap-card-title h2 { margin: 0; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .ap-count { font-size: 12px; font-weight: 600; padding: 2px 9px; border-radius: 999px; color: var(--navy); background: #EAF1FA; }

        .ap-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 16px; }
        .ap-field label { display: block; margin-bottom: 5px; font-size: 13px; font-weight: 500; color: #374151; }
        .ap-field .req { color: var(--danger); }
        .ap-field input[type=text], .ap-field input[type=number], .ap-field input[type=date],
        .ap-field input[type=email], .ap-field input[type=password], .ap-field select, .ap-field textarea {
            width: 100%; height: 42px; padding: 0 12px;
            font: inherit; font-size: 14px; color: var(--ink); background: #fff;
            border: 1px solid #D1D5DB; border-radius: 8px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .ap-field textarea { height: auto; min-height: 88px; padding: 10px 12px; resize: vertical; line-height: 1.5; }
        .ap-field input:disabled { background: #F3F4F6; color: var(--muted); cursor: not-allowed; }
        .ap-field input[type=file] { width: 100%; font-size: 13px; padding: 8px; border: 1px dashed #BFD3EC; border-radius: 8px; background: #F8FBFF; }
        .ap-field input:focus, .ap-field select:focus, .ap-field textarea:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }
        .ap-field.has-error input, .ap-field.has-error select, .ap-field.has-error textarea { border-color: #F04438; background: #FFFBFA; }
        .ap-error { margin-top: 4px; font-size: 12.5px; color: var(--danger); }
        .ap-hint  { margin-top: 4px; font-size: 12px; color: var(--muted); line-height: 1.5; }
        .ap-check { display: flex; align-items: center; gap: 8px; min-height: 42px; margin: 0 !important; font-size: 14px !important; cursor: pointer; }
        .ap-check input { width: 17px; height: 17px; accent-color: var(--navy); }
        .ap-link { color: var(--navy); font-weight: 600; text-decoration: none; white-space: nowrap; }
        .ap-link:hover { text-decoration: underline; }

        .ap-form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; }
        .ap-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            height: 40px; padding: 0 18px; border-radius: 8px; cursor: pointer;
            font: inherit; font-size: 14px; font-weight: 600; text-decoration: none; border: 1px solid transparent;
        }
        .ap-btn.primary { background: var(--navy); color: #fff; }
        .ap-btn.primary:hover { background: var(--navy-d); }
        .ap-btn.ghost { background: #fff; color: #374151; border-color: #D1D5DB; }
        .ap-btn.ghost:hover { background: #F3F4F6; }
        .ap-btn.sm { height: 34px; padding: 0 14px; font-size: 13px; }
        [hidden] { display: none !important; }

        .ap-table-wrap { overflow-x: auto; }
        .ap-table { width: 100%; border-collapse: collapse; }
        .ap-table th { padding: 10px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--muted); background: #F9FAFB; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); white-space: nowrap; }
        .ap-table td { padding: 11px 16px; font-size: 13.5px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .ap-table tbody tr:last-child td { border-bottom: 0; }
        .ap-table tbody tr:hover td { background: #FCFCFD; }
        .ap-table tr.is-editing td { background: #F3F8FE; }
        .ap-table th.r, .ap-table td.r { text-align: right; }
        .ap-actions { display: inline-flex; gap: 6px; }
        .ap-actions form { margin: 0; }
        .ap-icon-btn {
            width: 32px; height: 32px; display: inline-grid; place-items: center;
            border-radius: 8px; font-size: 14px; cursor: pointer; text-decoration: none;
            color: var(--muted); background: #fff; border: 1px solid var(--line);
        }
        .ap-icon-btn:hover { color: var(--navy); border-color: #BFD3EC; background: #EEF4FB; }
        .ap-icon-btn.danger:hover { color: var(--danger); border-color: #FECDCA; background: #FEF3F2; }

        .ap-empty { padding: 34px 20px 38px; text-align: center; color: var(--muted); }
        .ap-empty i { display: block; margin-bottom: 8px; font-size: 28px; color: #CBD5E1; }
        .ap-empty p { margin: 0; font-size: 13.5px; }

        .ap-statement { margin-bottom: 18px; padding: 16px 18px; font-size: 14px; line-height: 1.7; background: #F9FAFB; border: 1px solid var(--line); border-radius: 10px; }
        .ap-statement p { margin: 0 0 6px; font-weight: 600; }
        .ap-statement ol { margin: 0; padding-left: 20px; }

        /* ---- CV ---- */
        .cv-head { display: flex; align-items: center; gap: 16px; padding-bottom: 14px; border-bottom: 2px solid var(--navy); }
        .cv-photo { width: 76px; height: 76px; flex-shrink: 0; overflow: hidden; display: grid; place-items: center; border-radius: 50%; font-size: 24px; font-weight: 700; color: var(--navy); background: #DBEAFE; border: 1px solid #93C5FD; }
        .cv-photo img { width: 100%; height: 100%; object-fit: cover; }
        .cv-head h2 { margin: 0 0 4px; font-size: 22px; }
        .cv-meta { display: flex; flex-wrap: wrap; gap: 4px 16px; font-size: 13px; color: var(--muted); }
        .cv-h { margin: 20px 0 8px; padding-bottom: 4px; font-size: 13px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--navy); border-bottom: 1px solid var(--line); }
        .cv-dl { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px 16px; margin: 0; }
        .cv-dl dt { font-size: 11.5px; color: var(--muted); }
        .cv-dl dd { margin: 0; font-size: 14px; font-weight: 500; }
        .cv-row { display: grid; grid-template-columns: 150px minmax(0, 1fr); gap: 12px; padding: 6px 0; font-size: 14px; }
        .cv-row .when { font-size: 13px; color: var(--muted); }
        .cv-row .sub { margin-top: 1px; font-size: 13px; color: var(--muted); }
        .cv-row .duties { margin-top: 4px; font-size: 13px; line-height: 1.6; }
        .cv-none { margin: 4px 0; font-size: 13px; color: var(--muted); font-style: italic; }
        .cv-decl { font-size: 13.5px; line-height: 1.7; }

        @media print {
            .banner, .banner-img, .nav, .side, .footer, .no-print { display: none !important; }
            .shell { display: block; }
            .shell .main, .main { padding: 0 !important; }
            body { background: #fff; }
            .ap-card { border: 0; box-shadow: none; }
        }

        /* ================= CROPPER YA PICHA ================= */
        .crop-modal {
            position: fixed; inset: 0; z-index: 9999; display: grid; place-items: center; padding: 16px;
            background: rgba(16,24,40,.55);
        }
        .crop-box {
            width: 100%; max-width: 400px; padding: 22px 22px 20px; border-radius: 16px; background: #fff;
            box-shadow: 0 24px 48px -16px rgba(16,24,40,.35);
        }
        .crop-box h3 { margin: 0 0 4px; font-size: 18px; }
        .crop-hint { margin: 0 0 14px; font-size: 13px; line-height: 1.5; color: var(--muted); }
        .crop-stage { position: relative; width: 320px; max-width: 100%; margin: 0 auto; border-radius: 10px; overflow: hidden; background: #111827; touch-action: none; }
        .crop-stage canvas { display: block; width: 100%; height: auto; cursor: grab; touch-action: none; }
        .crop-stage canvas:active { cursor: grabbing; }
        /* Duara: sehemu ya ndani inaonekana, nje imefunikwa kwa giza */
        .crop-mask {
            position: absolute; inset: 0; pointer-events: none;
            background: radial-gradient(circle at center, transparent 0, transparent 139px, rgba(17,24,39,.62) 140px);
        }
        .crop-mask::after {
            content: ''; position: absolute; left: 50%; top: 50%; width: 280px; height: 280px; transform: translate(-50%, -50%);
            border-radius: 50%; border: 2px solid rgba(255,255,255,.9);
        }
        .crop-zoom { display: flex; align-items: center; gap: 10px; margin: 14px 4px 4px; color: var(--muted); }
        .crop-zoom input[type=range] { flex: 1; accent-color: var(--navy); }
        .crop-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; }

        /* ================= FOOTER ================= */
        .footer { padding: 16px; text-align: center; font-size: 12px; color: var(--muted); border-top: 1px solid var(--line); background: #fff; }

        :focus-visible { outline: 3px solid rgba(29,78,137,.35); outline-offset: 2px; }

        /* ================= SIMU ================= */
        @media (max-width: 900px) {
            .banner {
                grid-template-columns: 1fr;
                background: linear-gradient(180deg, var(--green), var(--black) 55%, var(--blue));
            }
            .banner-brand { justify-content: center; padding-bottom: 4px; }
            .banner-title { padding: 6px 16px 16px; }
            .nav { justify-content: center; flex-wrap: wrap; padding: 8px 12px; }
            .nav-user { margin-left: 0; padding-left: 0; border-left: 0; }

            .shell { grid-template-columns: 1fr; }
            .shell > .side { position: static; height: auto; overflow: visible; }
            .side { display: none; border-right: 0; border-bottom: 1px solid var(--line); }
            .side.open { display: block; }
            .nav-toggle { display: inline-flex; }
            .shell .main { padding: 20px 14px 40px; }
        }
        @media (max-width: 640px) {
            .ap-form-grid { grid-template-columns: 1fr; }
            .ap-form-grid .ap-field { grid-column: auto !important; }
            .cv-dl { grid-template-columns: 1fr 1fr; }
            .cv-row { grid-template-columns: 1fr; gap: 2px; }
            .ap-card-title { padding: 14px 16px 2px; }
        }
        @media (max-width: 480px) {
            .ap-card-head { padding: 22px 18px 14px; }
            .ap-card-body { padding: 6px 18px 22px; }
            .banner-name { font-size: 21px; }
            .nav-uname { display: none; }
        }

        /* ================= KURASA ZA PROFILE: upana kamili, urefu mfupi ================= */
        .ap-wrap.wide { max-width: none; }   /* fomu na orodha zijae upana wote */
        .ap-pagehead { gap: 14px; margin-bottom: 14px; }
        .ap-pagehead .ico { width: 44px; height: 44px; font-size: 21px; border-radius: 12px; }
        .ap-pagehead h1 { font-size: 22px; }
        .ap-pagehead p { font-size: 13.5px; }

        .ap-card-title { padding: 14px 24px 6px; }
        .ap-card-title h2 { font-size: 16px; }
        .ap-count { font-size: 12px; padding: 2px 9px; }
        .ap-card-body { padding: 6px 24px 20px; }

        .ap-form-grid { gap: 12px 22px; }
        .ap-field label { margin-bottom: 4px; font-size: 13.5px; }
        .ap-field input[type=text], .ap-field input[type=number], .ap-field input[type=date],
        .ap-field input[type=email], .ap-field input[type=password], .ap-field select, .ap-field textarea {
            height: 38px; padding: 0 12px; font-size: 14px; border-radius: 8px;
        }
        .ap-field textarea { height: auto; min-height: 76px; padding: 8px 12px; }
        .ap-field input[type=file] { padding: 6px 8px; font-size: 13px; border-radius: 8px; }
        .ap-hint { margin-top: 3px; font-size: 12px; }
        .ap-error { margin-top: 3px; font-size: 12.5px; }
        .ap-check { font-size: 14px !important; min-height: 38px; }

        .ap-form-actions { gap: 10px; margin-top: 14px; }
        .ap-btn { height: 38px; padding: 0 18px; font-size: 14px; border-radius: 8px; }
        .ap-btn.sm { height: 34px; padding: 0 14px; font-size: 13px; }

        .ap-table th { padding: 9px 18px; font-size: 12.5px; }
        .ap-table td { padding: 9px 18px; font-size: 14px; }
        .ap-icon-btn { width: 30px; height: 30px; font-size: 14px; border-radius: 8px; }
        .ap-actions { gap: 6px; }
        .ap-empty { padding: 28px 20px 32px; }
        .ap-empty i { font-size: 28px; }
        .ap-empty p { font-size: 14px; }

        @media (max-width: 640px) {
            .ap-card-title { padding: 12px 16px 6px; }
            .ap-card-body { padding: 6px 16px 18px; }
            .ap-table th, .ap-table td { padding: 9px 12px; }
            .ap-pagehead h1 { font-size: 20px; }
        }
    
        /* ================= RADIUS NDOGO: fomu zote zinafanana na dashboard ================= */
        :root { --r-card: 6px; --r-ctl: 4px; }
        .ap-card, .ap-statement, .ap-alert, .user-dd, .crop-box { border-radius: var(--r-card); }
        .ap-field input[type=text], .ap-field input[type=number], .ap-field input[type=date],
        .ap-field input[type=email], .ap-field input[type=password], .ap-field select, .ap-field textarea,
        .ap-field input[type=file], .ap-btn, .ap-icon-btn, .nav-logout,
        .nav a, .nav .nav-toggle, .side-menu a, .user-dd a, .user-dd .dd-logout { border-radius: var(--r-ctl); }
        .ap-pagehead .ico { border-radius: var(--r-card); }
        .crop-stage { border-radius: var(--r-card); }
</style>

    @stack('styles')
</head>

<body>
    @php
        $apUser = $applicant ?? auth('applicant')->user();

        // [Jina, ikoni, jina la route]
        $apMenu = [
            ['Dashboard',                   'bi-grid-1x2',            'applicant.dashboard'],
            ['Personal Details',            'bi-person-vcard',        'applicant.personal-details'],
            ['Academic Qualifications',     'bi-mortarboard',         'applicant.academic-qualifications'],
            ['Professional Qualifications', 'bi-award',               'applicant.professional-qualifications'],
            ['Language Proficiency',        'bi-translate',           'applicant.language-proficiency'],
            ['Working Experience',          'bi-briefcase',           'applicant.working-experience'],
            ['Training and Workshop',       'bi-easel',               'applicant.training-and-workshop'],
            ['Computer Literacy',           'bi-pc-display',          'applicant.computer-literacy'],
            ['Referees',                    'bi-people',              'applicant.referees'],
            ['Other Attachments',           'bi-paperclip',           'applicant.other-attachments'],
            ['Declarations',                'bi-patch-check',         'applicant.declaration'],
            ['CV Preview',                  'bi-file-earmark-person', 'applicant.cv-preview'],
        ];
    @endphp

    {{-- Banner na nav bar vinabaki juu ukiscroll; ni fomu/ukurasa tu unaosogea --}}
    <div class="ap-top" id="apTop">
    {{-- ================= BANNER ================= --}}
    @if(file_exists(public_path('images/banner.webp')))
        {{-- Picha ya banner (public/images/banner.webp); ukiiondoa, banner ya CSS hapa chini inatumika --}}
        <header class="banner-img">
            <img src="{{ asset('images/banner.webp') }}" alt="{{ config('portal.name', 'Careers Portal') }} - {{ config('portal.institution', '') }}">
        </header>
    @else
    <header class="banner">
        <div class="banner-brand">
            <div class="banner-logo" aria-hidden="true">
                @if(config('portal.logo'))
                    <img src="{{ asset(config('portal.logo')) }}" alt="">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                @endif
            </div>
            <span class="banner-name">{{ config('portal.name', 'Careers Portal') }}</span>
        </div>

        <div class="banner-title">
            <div class="l1">The United Republic of Tanzania</div>
            <div class="l2">{{ config('portal.institution', 'Your Ministry or Institution') }}</div>
            <div class="l2">Recruitment Portal</div>
        </div>

        <div></div>
    </header>
    @endif

    {{-- ================= NAV ================= --}}
    <nav class="nav" aria-label="Main">
        @if($apUser)
            <button type="button" class="nav-toggle" onclick="apToggleSide()" aria-controls="side" aria-expanded="false" id="sideToggle">
                <i class="bi bi-list"></i> Menu
            </button>
        @endif
        <a href="{{ $apUser && Route::has('applicant.dashboard') ? route('applicant.dashboard') : url('/') }}"
           class="{{ $apUser && request()->routeIs('applicant.dashboard') ? 'active' : '' }}">Home</a>
        <a href="{{ Route::has('vacancies') ? route('vacancies') : '#' }}">Vacancies</a>
        @if($apUser)
            <a href="{{ Route::has('applicant.my-applications') ? route('applicant.my-applications') : '#' }}"
               class="{{ request()->routeIs('applicant.my-applications') ? 'active' : '' }}">My Applications</a>
        @endif
        <a href="{{ Route::has('feedback') ? route('feedback') : '#' }}">Feedback</a>

        @if($apUser)
            <div class="nav-user" id="userMenu">
                <button type="button" class="nav-userlink {{ request()->routeIs('applicant.profile*') ? 'active' : '' }}"
                        id="userMenuBtn" aria-haspopup="true" aria-expanded="false" aria-controls="userMenuList">
                    <span class="nav-avatar">{{ $apUser->initials() }}</span>
                    <span class="nav-uname">{{ $apUser->full_name }}</span>
                    <i class="bi bi-chevron-down nav-caret"></i>
                </button>
                <div class="user-dd" id="userMenuList" role="menu" hidden>
                    <a role="menuitem" href="{{ Route::has('applicant.profile') ? route('applicant.profile') : '#' }}">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                    @if(Route::has('applicant.logout'))
                        <form method="POST" action="{{ route('applicant.logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="dd-logout"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <a href="{{ Route::has('applicant.login') ? route('applicant.login') : '#' }}"
               class="{{ request()->routeIs('applicant.login') ? 'active' : '' }}">Login</a>
        @endif
    </nav>
    </div>

    {{-- ================= CONTENT ================= --}}
    @if($apUser)
        <div class="shell {{ ! empty($apInactive) ? 'is-inactive' : '' }}">

            <aside class="side" id="side" aria-label="Applicant menu">

                <div class="side-profile">
                    <div class="side-photo">
                        @if(!empty($apUser->photo_url))
                            <img src="{{ $apUser->photo_url }}" alt="{{ $apUser->full_name }}">
                        @else
                            {{ $apUser->initials() }}
                        @endif
                    </div>
                    <div class="side-name">{{ $apUser->full_name }}</div>
                    @if(!empty($apUser->application_number))
                        <div class="side-no">{{ $apUser->application_number }}</div>
                    @endif
                    <div class="side-updated">
                        Last updated:<br>
                        <b>{{ optional($apUser->updated_at)->format('M j, Y \a\t g:i A') ?: '—' }}</b>
                    </div>

                    @if(Route::has('applicant.photo.update'))
                        <form method="POST" action="{{ route('applicant.photo.update') }}" enctype="multipart/form-data" id="photoForm">
                            @csrf
                            <label class="side-upload">
                                <i class="bi bi-camera"></i> Update Picture
                                <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" hidden onchange="apPhotoChosen(this)">
                            </label>
                        </form>
                    @else
                        <span class="side-upload is-disabled" title="Haijawekwa bado"><i class="bi bi-camera"></i> Update Picture</span>
                    @endif
                </div>

                <ul class="side-menu">
                    @foreach($apMenu as [$label, $icon, $routeName])
                        @php $isActive = request()->routeIs($routeName, $routeName . '.*'); @endphp
                        <li>
                            <a href="{{ Route::has($routeName) ? route($routeName) : '#' }}"
                               class="{{ $isActive ? 'active' : '' }}"
                               @if($isActive) aria-current="page" @endif>
                                <i class="bi {{ $icon }}" aria-hidden="true"></i>
                                <span>{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

            </aside>

            <main class="main">
                @if(! empty($apInactive))
                    <div class="ap-inactive-banner" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ \App\Models\DocumentContent::text('activation_notice', 'Your account is not activated. Please wait for the Employment Secretariat to activate it. Until then you cannot fill in your profile or apply for jobs.') }}</div>
                    </div>
                @endif
                <div class="main-body">
                    @yield('content')
                </div>
            </main>

        </div>
    @else
        <main class="main">
            @yield('content')
        </main>
    @endif

    @if($apUser)
        @include('applicant.partials.photo-cropper')
    @endif

    <footer class="footer">
        &copy; {{ date('Y') }} {{ config('portal.name', 'Careers Portal') }} &middot; The United Republic of Tanzania
    </footer>

    <script>
(function(){
    var btn=document.getElementById('userMenuBtn'), list=document.getElementById('userMenuList');
    if(!btn||!list) return;
    function set(o){ list.hidden=!o; btn.setAttribute('aria-expanded',o?'true':'false'); }
    btn.addEventListener('click',function(e){ e.stopPropagation(); set(list.hidden); });
    document.addEventListener('click',function(e){ if(!list.contains(e.target)) set(false); });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape') set(false); });
})();
        // Fungua/funga sidebar kwenye simu
        function apToggleSide() {
            const side = document.getElementById('side');
            const btn  = document.getElementById('sideToggle');
            if (!side) return;
            const open = side.classList.toggle('open');
            if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    </script>

    @stack('scripts')
<script>
    (function () {
        var top = document.getElementById('apTop');
        function set() { if (top) { document.documentElement.style.setProperty('--ap-top-h', top.offsetHeight + 'px'); } }
        set(); window.addEventListener('resize', set); window.addEventListener('load', set);
        var im = top && top.querySelector('img'); if (im) { im.addEventListener('load', set); }
    })();
</script>
</body>
</html>
