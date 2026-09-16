const card = document.getElementById('timer-card');
if (card) {
    const taskId = card.dataset.taskId;
    const totalSeconds = parseInt(card.dataset.totalSeconds, 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const display = document.getElementById('timer-display');
    const idleControls = document.getElementById('timer-controls-idle');
    const activeControls = document.getElementById('timer-controls-active');
    const completeBlock = document.getElementById('timer-complete');
    const completeText = document.getElementById('timer-complete-text');
    const backLink = document.querySelector('.timer-back-link');

    const btnStart = document.getElementById('btn-start');
    const btnPause = document.getElementById('btn-pause');
    const btnFinish = document.getElementById('btn-finish');
    const btnCancel = document.getElementById('btn-cancel');

    let sessionId = card.dataset.activeSessionId || null;
    let intervalId = null;
    let isPaused = false;

    // Live JS-tracked state — source of truth after page load,
    // never re-read from card.dataset once the page has rendered.
    let startedAtIso = card.dataset.startedAt || null;
    let pausedSecondsTotal = parseInt(card.dataset.pausedSeconds, 10) || 0;

    function post(url, body = null) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: body ? JSON.stringify(body) : null,
        }).then((r) => r.json());
    }

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function computeRemaining(pausedAtIso) {
        const startedAt = new Date(startedAtIso).getTime();
        const now = Date.now();
        let excludedSeconds = pausedSecondsTotal;

        if (pausedAtIso) {
            const pausedAt = new Date(pausedAtIso).getTime();
            excludedSeconds += Math.floor((now - pausedAt) / 1000);
        }

        const activeSeconds = Math.floor((now - startedAt) / 1000) - excludedSeconds;
        return Math.max(totalSeconds - activeSeconds, 0);
    }

    function showActiveState() {
        idleControls.style.display = 'none';
        activeControls.style.display = 'flex';
        completeBlock.style.display = 'none';
    }

    function startTicking(remaining) {
        display.textContent = formatTime(remaining);
        clearInterval(intervalId);
        intervalId = setInterval(() => {
            remaining -= 1;
            display.textContent = formatTime(Math.max(remaining, 0));
            if (remaining <= 0) {
                clearInterval(intervalId);
                finishSession();
            }
        }, 1000);
    }

    async function startSession() {
        const data = await post('/study-sessions/start', { task_id: taskId });
        sessionId = data.id;
        startedAtIso = data.started_at;
        pausedSecondsTotal = 0;

        showActiveState();
        startTicking(totalSeconds);
    }

    async function pauseSession() {
        if (!sessionId) return;
        clearInterval(intervalId);
        await post(`/study-sessions/${sessionId}/pause`);
        isPaused = true;
        btnPause.textContent = 'Resume';
    }

    async function resumeSession() {
        if (!sessionId) return;
        const data = await post(`/study-sessions/${sessionId}/resume`);
        pausedSecondsTotal = data.paused_seconds;
        isPaused = false;
        btnPause.textContent = 'Pause';

        const remaining = computeRemaining(null);
        startTicking(remaining);
    }

    async function finishSession() {
        if (!sessionId) return;
        clearInterval(intervalId);

        const data = await post(`/study-sessions/${sessionId}/complete`);

        activeControls.style.display = 'none';
        completeText.textContent = `You studied for ${data.duration_minutes} minute${data.duration_minutes === 1 ? '' : 's'}.`;
        completeBlock.style.display = 'flex';
    }

    async function cancelSession() {
        if (!sessionId) return;
        clearInterval(intervalId);

        await post(`/study-sessions/${sessionId}/interrupt`);

        window.location.href = `/tasks/${taskId}`;
    }

    btnStart.addEventListener('click', startSession);
    btnPause.addEventListener('click', () => {
        isPaused ? resumeSession() : pauseSession();
    });
    btnFinish.addEventListener('click', finishSession);
    btnCancel.addEventListener('click', cancelSession);

    // Clicking "Back to task" pauses the session server-side first,
    // instead of leaving it orphaned as in_progress with no paused_at.
    if (backLink) {
        backLink.addEventListener('click', async (e) => {
            if (sessionId && !isPaused) {
                e.preventDefault();
                await post(`/study-sessions/${sessionId}/pause`);
                window.location.href = backLink.href;
            }
        });
    }

    // On page load: if an active session already exists (e.g. reopened
    // after "Back to task"), resume ticking or show the paused state
    // using the server-rendered values — this is the one legitimate
    // case where reading card.dataset is correct, since the session
    // genuinely existed before this page rendered.
    if (sessionId) {
        const pausedAtIso = card.dataset.pausedAt || null;

        showActiveState();

        if (pausedAtIso) {
            isPaused = true;
            btnPause.textContent = 'Resume';
            display.textContent = formatTime(computeRemaining(pausedAtIso));
        } else {
            startTicking(computeRemaining(null));
        }
    }
}