
import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    if (!window.analyticsData) return;

    const { daily, subject, status } = window.analyticsData;

    const violet = '#8B7FD6';
    const teal = '#5EEAD4';
    const amber = '#f59e0b';
    const gridColor = 'rgba(255,255,255,0.08)';
    const textColor = 'rgba(255,255,255,0.6)';

    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
       new Chart(dailyCtx, {
    type: 'bar',
    data: { labels: daily.labels,
                datasets: [{
                    label: 'Minutes studied',
                    data: daily.data,
                    backgroundColor: violet,
                    borderRadius: 6,
                }], },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor } },
            y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true },
        },
    },
});
    }
    

    const subjectCtx = document.getElementById('subjectChart');
    if (subjectCtx) {
        new Chart(subjectCtx, {
    type: 'doughnut',
    data: { labels: subject.labels,
                datasets: [{
                    data: subject.data,
                    backgroundColor: [violet, teal, amber, '#60a5fa', '#f472b6', '#a78bfa'],
                }], },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { color: textColor } } },
    },
});
    }
    

    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
    type: 'doughnut',
    data: { labels: status.labels,
                datasets: [{
                    data: status.data,
                    backgroundColor: [teal, violet, amber],
                }]},
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { color: textColor } } },
    },
});
    }
});
