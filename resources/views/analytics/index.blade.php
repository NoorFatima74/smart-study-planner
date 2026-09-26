
@extends('layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics & Reports')
@section('page-subtitle', '')

@section('content')
    <div class="grid" style="grid-template-columns: repeat(3, 1fr); gap:16px; margin-bottom:20px;">
        <div class="card" style="text-align:center;">
            <div style="font-size:28px; font-weight:700;">{{ $summary['total_hours'] }}h</div>
            <div class="lbl">Total study time</div>
        </div>
        <div class="card" style="text-align:center;">
            <div style="font-size:28px; font-weight:700;">{{ $summary['completion_rate'] }}%</div>
            <div class="lbl">Completion rate</div>
        </div>
        <div class="card" style="text-align:center;">
            <div style="font-size:28px; font-weight:700;">{{ $summary['top_subject'] }}</div>
            <div class="lbl">Most studied subject</div>
        </div>
    </div>

   
<div class="card" style="margin-bottom:20px;">
    <div class="section-label">Study time — last 7 days</div>
    <div style="height:220px; position:relative;">
        <canvas id="dailyChart"></canvas>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap:16px;">
    <div class="card">
        <div class="section-label">Time by subject</div>
        <div style="height:240px; position:relative;">
            <canvas id="subjectChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="section-label">Task status</div>
        <div style="height:240px; position:relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>
    <script>
        window.analyticsData = {
            daily: @json($dailyMinutes),
            subject: @json($subjectMinutes),
            status: @json($completionBreakdown),
        };
    </script>
@endsection