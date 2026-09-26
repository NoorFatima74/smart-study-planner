

export function showXpToast({ xp_gained, leveled_up, new_level, new_achievements }) {
    const toast = document.createElement('div');
    toast.className = 'xp-toast';

    let html = `<div><span class="xp-amount">+${xp_gained} XP</span></div>`;

    if (leveled_up) {
        html += `<div class="level-up">🎉 Level Up! You're now Level ${new_level}</div>`;
    }

    if (new_achievements && new_achievements.length > 0) {
        new_achievements.forEach(a => {
            html += `<div class="level-up">${a.icon} Achievement unlocked: ${a.name}</div>`;
        });
    }

    toast.innerHTML = html;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}