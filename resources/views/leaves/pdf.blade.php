<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application for Leave #{{ $leave->id }}</title>
    <style>
        @page { margin: 6px 6px 8px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #000; }
        * { box-sizing: border-box; }
        table { border-collapse: collapse; width: 100%; }
        .form td, .form th { border: 1px solid #000; vertical-align: top; padding: 3px 4px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .small { font-size: 9px; }
        .tiny { font-size: 8px; }
        .section { font-weight: 700; font-size: 10.5px; letter-spacing: .2px; }
        .hdr-wrap { position: relative; margin-bottom: 4px; border-bottom: 2px solid #000; padding-bottom: 4px; }
        .hdr-left { font-size: 10px; font-style: italic; font-weight: 700; line-height: 1.25; }
        .hdr-title { font-size: 22px; font-weight: 800; letter-spacing: .8px; }
        .logo { width: 64px; height: 64px; object-fit: contain; margin: 0 auto; }
        .stamp-box { border: 1px dashed #999; padding: 10px 8px; color: #666; text-align: center; font-size: 9px; }
        .qr-box {
            position: absolute;
            top: 0;
            right: 0;
            width: 48px;
            height: 48px;
            border: 1px solid #000;
            font-size: 7px;
            line-height: 46px;
            text-align: center;
            font-weight: 700;
        }
        .line {
            display: inline-block;
            min-height: 10px;
            border-bottom: 1px solid #000;
            width: 100%;
            vertical-align: bottom;
            padding: 0 2px;
        }
        .line.short { width: 120px; }
        .line.mid { width: 200px; }
        .line.long { width: 100%; }
        .chk {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #000;
            text-align: center;
            line-height: 10px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 4px;
            vertical-align: text-top;
        }
        .list-row { margin: 2px 0; }
        .name-val { font-size: 13px; font-weight: 800; text-transform: uppercase; text-align: center; letter-spacing: .3px; }
        .office-val { font-size: 14px; font-weight: 800; text-transform: uppercase; text-align: center; letter-spacing: .4px; }
        .no-pad td, .no-pad th { padding: 0; }
        .inner td, .inner th { border: 1px solid #000; padding: 1px 2px; }
        .sig { margin-top: 10px; text-align: center; }
        .sig .n { font-size: 12px; font-weight: 700; text-decoration: underline; text-transform: uppercase; }
        .sig .p { font-size: 9px; }
        .muted { color: #444; }
        .reminder {
            margin-top: 0;
            border: 1px solid #000;
            border-top: 0;
            color: #7a008c;
            font-weight: 700;
            font-size: 8px;
            padding: 5px 4px;
            letter-spacing: .1px;
            background: #cccccc;
        }
    </style>
</head>
<body>
@php
    $dj = is_array($leave->details_json ?? null) ? $leave->details_json : [];
    $user = $leave->user;

    $logoPath = public_path('logo.png');
    $logoSrc = file_exists($logoPath) ? ('file:///' . str_replace('\\', '/', $logoPath)) : null;

    $typeName = trim((string)($dj['type_of_leave']['name'] ?? $leave->category?->name ?? ''));
    $typeLower = mb_strtolower($typeName);
    $hasType = function (array $needles) use ($typeLower): bool {
        foreach ($needles as $needle) {
            if (str_contains($typeLower, mb_strtolower($needle))) {
                return true;
            }
        }
        return false;
    };

    $vac = (array)($dj['details_of_leave']['vacation'] ?? []);
    $sick = (array)($dj['details_of_leave']['sick'] ?? []);
    $women = trim((string)($dj['details_of_leave']['women'] ?? ''));
    $study = (array)($dj['details_of_leave']['study'] ?? []);
    $other = (array)($dj['details_of_leave']['other'] ?? []);
    $appliedDays = (string)($dj['working_days']['applied_days'] ?? $leave->days ?? '');
    $inclusive = trim((string)($dj['working_days']['inclusive_dates'] ?? ''));
    if ($inclusive === '') {
        $inclusive = trim((string)$leave->start_date).' to '.trim((string)$leave->end_date);
    }
    $commutation = mb_strtolower(trim((string)($dj['commutation'] ?? 'not_requested')));

    $rawReason = trim((string)($leave->reason ?? ''));
    if ($rawReason !== '') {
        $details = $rawReason;
        if (preg_match('/Details of Leave\s*[:\-–—]\s*(.*)$/uism', $rawReason, $m)) {
            $details = trim((string)$m[1]);
        } elseif (preg_match('/Details of Leave\s*(.*)$/uis', $rawReason, $m)) {
            $details = trim((string)$m[1]);
            $details = preg_replace('/^[\s:\-–—]+/u', '', $details) ?? $details;
        }

        $parts = preg_split('/\s*\|\s*/u', $details) ?: [];
        foreach ($parts as $p) {
            $p = trim((string)$p);
            if ($p === '') continue;

            if (stripos($p, 'Vacation/Special Privilege Leave:') === 0) {
                $vacBlock = trim(substr($p, strlen('Vacation/Special Privilege Leave:')));
                $vacParts = preg_split('/\s*;\s*/u', $vacBlock) ?: [];
                foreach ($vacParts as $vp) {
                    if (stripos($vp, 'Within the Philippines:') === 0 && empty($vac['within_ph'])) {
                        $vac['within_ph'] = trim(substr($vp, strlen('Within the Philippines:')));
                    } elseif (stripos($vp, 'Abroad:') === 0 && empty($vac['abroad'])) {
                        $vac['abroad'] = trim(substr($vp, strlen('Abroad:')));
                    }
                }
                continue;
            }

            if (stripos($p, 'Sick Leave:') === 0) {
                $sickBlock = trim(substr($p, strlen('Sick Leave:')));
                $sickParts = preg_split('/\s*;\s*/u', $sickBlock) ?: [];
                foreach ($sickParts as $sp) {
                    if (stripos($sp, 'In Hospital:') === 0 && empty($sick['hospital'])) {
                        $sick['hospital'] = trim(substr($sp, strlen('In Hospital:')));
                    } elseif (stripos($sp, 'Out Patient:') === 0 && empty($sick['outpatient'])) {
                        $sick['outpatient'] = trim(substr($sp, strlen('Out Patient:')));
                    }
                }
                continue;
            }

            if (stripos($p, 'Special Leave Benefits for Women:') === 0 && $women === '') {
                $women = trim(substr($p, strlen('Special Leave Benefits for Women:')));
                continue;
            }
            if (stripos($p, "Study Leave: Completion of Master's Degree") === 0) {
                $study['master'] = true;
                continue;
            }
            if (stripos($p, 'Study Leave: BAR/Board Examination Review') === 0) {
                $study['bar'] = true;
                continue;
            }
            if (stripos($p, 'Other Purpose: Monetization of Leave Credits') === 0) {
                $other['monetization'] = true;
                continue;
            }
            if (stripos($p, 'Other Purpose: Terminal Leave') === 0) {
                $other['terminal'] = true;
                continue;
            }
            if (stripos($p, 'Number of Working Days Applied For:') === 0 && $appliedDays === '') {
                $appliedDays = trim(substr($p, strlen('Number of Working Days Applied For:')));
                continue;
            }
            if (stripos($p, 'Inclusive Dates:') === 0) {
                $inclusive = trim(substr($p, strlen('Inclusive Dates:')));
                continue;
            }
            if (stripos($p, 'Commutation:') === 0) {
                $cv = mb_strtolower(trim(substr($p, strlen('Commutation:'))));
                $commutation = (str_contains($cv, 'request') && !str_contains($cv, 'not')) ? 'requested' : 'not_requested';
                continue;
            }
        }
    }

    $checks = [
        'vacation' => $hasType(['vacation']),
        'mandatory' => $hasType(['mandatory', 'forced']),
        'sick' => $hasType(['sick']),
        'maternity' => $hasType(['maternity']),
        'paternity' => $hasType(['paternity']),
        'special_privilege' => $hasType(['special privilege']),
        'solo_parent' => $hasType(['solo parent']),
        'study' => $hasType(['study']),
        'vawc10' => $hasType(['10-day vawc', '10 day vawc']),
        'rehab' => $hasType(['rehabilitation']),
        'women' => $hasType(['special leave benefits for women']),
        'calamity' => $hasType(['calamity', 'special emergency']),
        'adoption' => $hasType(['adoption']),
    ];
    $otherType = in_array(true, $checks, true) ? '' : $typeName;

    $tick = fn(bool $ok): string => $ok ? '&#10003;' : '&nbsp;';
    $safe = fn(?string $v, string $fallback = ''): string => trim((string)$v) !== '' ? trim((string)$v) : $fallback;

    $displayName = $safe($user?->display_name ?? null, $safe($user?->name ?? null, '-'));
    $dept = $safe($user?->province_office ?? null, $safe($user?->region ?? null, ''));
    $position = $safe($user?->position ?? null, '');
    $salary = $safe($user?->salary ?? null, '');
    $idNo = $safe($user?->id_no ?? null, '');
    $dateFiled = $leave->created_at?->timezone(config('app.timezone'))->format('F j, Y \a\t g:i A') ?? '';

    $credits = (array)($dj['credits'] ?? []);
    $vacCred = (array)($credits['vacation'] ?? []);
    $sickCred = (array)($credits['sick'] ?? []);
    $metaCred = (array)($credits['meta'] ?? []);
    $asOf = !empty($metaCred['updated_at'])
        ? \Carbon\Carbon::parse($metaCred['updated_at'])->timezone(config('app.timezone'))->format('F j, Y')
        : now()->timezone(config('app.timezone'))->format('F j, Y');

    $hrUser = !empty($leave->hr_approved_by) ? \App\Models\User::find($leave->hr_approved_by) : null;
    $dcUser = !empty($leave->dc_approved_by) ? \App\Models\User::find($leave->dc_approved_by) : null;
    $finalUser = !empty($leave->final_approved_by) ? \App\Models\User::find($leave->final_approved_by) : null;
    $hrName = $safe($hrUser?->display_name ?? null, $safe($hrUser?->name ?? null, ''));
    $dcName = $safe($dcUser?->display_name ?? null, $safe($dcUser?->name ?? null, ''));
    $finalName = $safe($finalUser?->display_name ?? null, $safe($finalUser?->name ?? null, ''));
    $finalPos = $safe($finalUser?->position ?? null, '');

    $reco = (array)($dj['dc_recommendation'] ?? []);
    $recoDecision = (string)($reco['decision'] ?? '');
    $recoReason = $safe($reco['reason'] ?? null, $safe($leave->dc_comment ?? null, ''));
@endphp

<div class="hdr-wrap">
    <div class="qr-box">QR</div>
    <table>
        <tr>
            <td style="width:16%; vertical-align:top;" class="hdr-left">
                Civil Service Form No. 6<br>
                Revised 2020
            </td>
            <td style="width:14%; vertical-align:middle;" class="center">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="DILG Logo" class="logo">
                @endif
            </td>
            <td style="width:48%; vertical-align:middle;" class="center hdr-title">APPLICATION FOR LEAVE</td>
            <td style="width:22%; vertical-align:middle;">
                <div class="stamp-box">Stamp of Date of Receipt</div>
            </td>
        </tr>
    </table>
</div>

<table class="form">
    <tr>
        <td colspan="3" style="height:20px;"></td>
    </tr>
    <tr>
        <td style="width:34%;">
            <div>1.&nbsp; OFFICE/DEPARTMENT</div>
            <div class="office-val">{{ strtoupper($safe($dept, '-')) }}</div>
        </td>
        <td colspan="2" style="width:66%;">
            <div>2.&nbsp; NAME :</div>
            <div class="name-val">{{ strtoupper($displayName) }}</div>
        </td>
    </tr>
    <tr>
        <td>
            <span>3.&nbsp; Date of Filing:</span>
            <span class="line mid">{{ $dateFiled }}</span>
        </td>
        <td>
            <span>4.&nbsp; POSITION:</span>
            <span>{{ strtoupper($position) }}</span>
        </td>
        <td>
            <span>5.&nbsp; Salary:</span>
            <span>{{ $salary !== '' ? ('P'.$salary) : '' }}</span>
        </td>
    </tr>

    <tr>
        <th colspan="3" class="center section">6.&nbsp; DETAILS OF APPLICATION</th>
    </tr>
    <tr>
        <td style="width:53%;">
            <div class="section" style="font-weight:400;">6.A&nbsp; TYPE OF LEAVE TO BE AVAILED OF</div>
            <div class="list-row"><span class="chk">{!! $tick($checks['vacation']) !!}</span>Vacation Leave <span class="tiny">(Sec. 51, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['mandatory']) !!}</span>Mandatory/Forced Leave<span class="tiny">(Sec. 25, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['sick']) !!}</span>Sick Leave <span class="tiny">(Sec. 43, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['maternity']) !!}</span>Maternity Leave <span class="tiny">(R.A. No. 11210 / IRR issued by CSC, DOLE and SSS)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['paternity']) !!}</span>Paternity Leave <span class="tiny">(R.A. No. 8187 / CSC MC No. 71, s. 1998, as amended)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['special_privilege']) !!}</span>Special Privilege Leave <span class="tiny">(Sec. 21, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['solo_parent']) !!}</span>Solo Parent Leave <span class="tiny">(RA No. 8972 / CSC MC No. 8, s. 2004)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['study']) !!}</span>Study Leave <span class="tiny">(Sec. 68, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['vawc10']) !!}</span>10-Day VAWC Leave <span class="tiny">(RA No. 9262 / CSC MC No. 15, s. 2005)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['rehab']) !!}</span>Rehabilitation Privilege <span class="tiny">(Sec. 55, Rule XVI, Omnibus Rules Implementing E.O. No. 292)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['women']) !!}</span>Special Leave Benefits for Women <span class="tiny">(RA No. 9710 / CSC MC No. 25, s. 2010)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['calamity']) !!}</span>Special Emergency (Calamity) Leave <span class="tiny">(CSC MC No. 2, s. 2012, as amended)</span></div>
            <div class="list-row"><span class="chk">{!! $tick($checks['adoption']) !!}</span>Adoption Leave <span class="tiny">(R.A. No. 8552)</span></div>
            <div style="margin-top:12px; font-style:italic;">Others:</div>
            <div class="line long">{{ $otherType }}</div>
        </td>
        <td colspan="2" style="width:47%;">
            <div class="section" style="font-weight:400;">6.B&nbsp; DETAILS OF LEAVE</div>
            <div style="margin:6px 0 2px; font-style:italic;">In case of Vacation/Special Privilege Leave:</div>
            <div class="list-row"><span class="chk">{!! $tick($safe($vac['within_ph'] ?? null) !== '') !!}</span>Within the Philippines: <span class="line mid">{{ $safe($vac['within_ph'] ?? null) }}</span></div>
            <div class="list-row"><span class="chk">{!! $tick($safe($vac['abroad'] ?? null) !== '') !!}</span>Abroad: <span class="line mid">{{ $safe($vac['abroad'] ?? null) }}</span></div>

            <div style="margin:8px 0 2px; font-style:italic;">In case of Sick Leave:</div>
            <div class="list-row"><span class="chk">{!! $tick($safe($sick['hospital'] ?? null) !== '') !!}</span>In Hospital (Specify Illness): <span class="line mid">{{ $safe($sick['hospital'] ?? null) }}</span></div>
            <div class="list-row"><span class="chk">{!! $tick($safe($sick['outpatient'] ?? null) !== '') !!}</span>Out Patient (Specify Illness): <span class="line mid">{{ $safe($sick['outpatient'] ?? null) }}</span></div>

            <div style="margin:8px 0 2px; font-style:italic;">In case of Special Leave Benefits for Women:</div>
            <div>(Specify Illness) <span class="line mid">{{ $women }}</span></div>
            <div class="line long" style="margin-top:2px;"></div>

            <div style="margin:8px 0 2px; font-style:italic;">In case of Study Leave:</div>
            <div class="list-row"><span class="chk">{!! $tick(!empty($study['master'])) !!}</span>Completion of Master's Degree</div>
            <div class="list-row"><span class="chk">{!! $tick(!empty($study['bar'])) !!}</span>BAR/Board Examination Review</div>

            <div style="margin:6px 0 2px; font-style:italic;">Other purpose:</div>
            <div class="list-row"><span class="chk">{!! $tick(!empty($other['monetization'])) !!}</span>Monetization of Leave Credits</div>
            <div class="list-row"><span class="chk">{!! $tick(!empty($other['terminal'])) !!}</span>Terminal Leave</div>
        </td>
    </tr>

    <tr>
        <td>
            <div class="section" style="font-weight:400;">6.C&nbsp; NUMBER OF WORKING DAYS APPLIED FOR</div>
            <div class="center" style="margin-top:4px;">
                <div class="line short center">{{ $appliedDays }}</div>
            </div>
            <div class="center" style="margin-top:4px;">INCLUSIVE DATES</div>
            <div style="margin-top:4px;">
                <span class="line long">{{ $inclusive }}</span>
            </div>
        </td>
        <td colspan="2">
            <div class="section" style="font-weight:400;">6.D&nbsp; COMMUTATION</div>
            <div class="list-row"><span class="chk">{!! $tick($commutation === 'not_requested' || $commutation === '') !!}</span>Not Requested</div>
            <div class="list-row"><span class="chk">{!! $tick($commutation === 'requested') !!}</span>Requested</div>
            <div class="center" style="margin-top:14px;">
                <span class="line mid"></span><br>
                <span style="font-size:11px;">(Signature of Applicant)</span>
            </div>
        </td>
    </tr>

    <tr>
        <th colspan="3" class="center section">7.&nbsp; DETAILS OF ACTION ON APPLICATION</th>
    </tr>
    <tr>
        <td>
            <div class="section" style="font-weight:400;">7.A&nbsp; CERTIFICATION OF LEAVE CREDITS</div>
            <div style="margin:6px 0 4px 34px; text-decoration: underline;">As of {{ $asOf }}</div>
            <table class="inner small" style="width:92%; margin:0 auto;">
                <tr>
                    <th></th>
                    <th class="center">Vacation Leave</th>
                    <th class="center">Sick Leave</th>
                </tr>
                <tr>
                    <td class="center"><em>Total Earned</em></td>
                    <td class="center">{{ number_format((float)($vacCred['total'] ?? 0), 3) }}</td>
                    <td class="center">{{ number_format((float)($sickCred['total'] ?? 0), 3) }}</td>
                </tr>
                <tr>
                    <td class="center"><em>Less this application</em></td>
                    <td class="center">{{ number_format((float)($vacCred['less'] ?? 0), 3) }}</td>
                    <td class="center">{{ number_format((float)($sickCred['less'] ?? 0), 3) }}</td>
                </tr>
                <tr>
                    <td class="center"><em>Balance</em></td>
                    <td class="center">{{ number_format((float)($vacCred['balance'] ?? 0), 3) }}</td>
                    <td class="center">{{ number_format((float)($sickCred['balance'] ?? 0), 3) }}</td>
                </tr>
            </table>
            <div class="sig">
                <div class="n">{{ $hrName !== '' ? $hrName : '________________________' }}</div>
                <div class="p">{{ $safe($hrUser?->position ?? null, 'Administrative Officer V, HRRS') }}</div>
            </div>
        </td>
        <td colspan="2">
            <div class="section" style="font-weight:400;">7.B&nbsp; RECOMMENDATION</div>
            <div style="margin-top:4px;">
                <span class="chk">{!! $tick($recoDecision === 'approval') !!}</span>For approval
            </div>
            <div style="margin-top:4px;">
                <span class="chk">{!! $tick($recoDecision === 'disapproval') !!}</span>For disapproval due to <span class="line mid">{{ $recoDecision === 'disapproval' ? $recoReason : '' }}</span>
            </div>
            <div style="margin-top:6px;"><span class="line long"></span></div>
            <div style="margin-top:3px;"><span class="line long"></span></div>
            <div class="sig" style="margin-top:20px;">
                <div class="n">{{ $dcName !== '' ? $dcName : '________________________' }}</div>
                <div class="p">{{ $safe($dcUser?->position ?? null, 'Division Chief, LGMED') }}</div>
            </div>
        </td>
    </tr>

    <tr>
        <td>
            <div class="section" style="font-weight:400;">7.C&nbsp; APPROVED FOR:</div>
            <div style="margin-top:8px;">&nbsp;&nbsp;&nbsp;&nbsp;<span class="line short">{{ $leave->status === 'approved' ? $leave->days : '' }}</span> day/s with pay</div>
            <div style="margin-top:2px;">&nbsp;&nbsp;&nbsp;&nbsp;<span class="line short"></span> day/s without pay</div>
            <div style="margin-top:2px;">&nbsp;&nbsp;&nbsp;&nbsp;<span class="line short"></span> others (Specify)</div>
            <div class="center bold" style="margin-top:10px; text-decoration: underline;">by Authority of&nbsp; the&nbsp; Regional Director</div>
            <div class="sig" style="margin-top:20px;">
                <div class="n">{{ $finalName !== '' ? $finalName : '________________________' }}</div>
                <div class="p">{{ $finalPos !== '' ? $finalPos : 'Assistant Regional Director' }}</div>
            </div>
        </td>
        <td colspan="2">
            <div class="section" style="font-weight:400;">7.D&nbsp; DISAPPROVED DUE TO:</div>
            <div style="margin-top:6px;"><span class="line long">{{ $leave->status === 'rejected' ? $safe($leave->final_comment ?? null, $safe($leave->dc_comment ?? null, '')) : '' }}</span></div>
            <div style="margin-top:4px;"><span class="line long"></span></div>
            <div style="margin-top:4px;"><span class="line long"></span></div>
        </td>
    </tr>
</table>

<div class="reminder">
    REMINDER: PLEASE FILL-OUT CSS FORM THROUGH: https://ecsm.dilg.gov.ph/ph/?survey=CSS-LeaveApplication OR THROUGH QR CODE ABOVE
</div>
</body>
</html>
