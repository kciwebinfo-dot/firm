const themeState = {
    mode: document.body.dataset.themeMode || 'light',
    color: document.body.dataset.themeColor || 'royal',
    style: document.body.dataset.themeStyle || 'default'
};

function applyTheme() {
    document.body.className = document.body.className
        .replace(/theme-\w+/g, '')
        .replace(/color-\w+/g, '')
        .replace(/style-\w+/g, '')
        .trim();
    document.body.classList.add(`theme-${themeState.mode}`, `color-${themeState.color}`, `style-${themeState.style}`);
    document.body.dataset.themeMode = themeState.mode;
    document.body.dataset.themeColor = themeState.color;
    document.body.dataset.themeStyle = themeState.style;
}

async function saveTheme(showToast = true) {
    const fd = new FormData();
    fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
    fd.append('theme_mode', themeState.mode);
    fd.append('theme_color', themeState.color);
    fd.append('theme_style', themeState.style);
    const data = await safeFetch('ajax/theme-save.php', { method: 'POST', body: fd });
    if (showToast && data.success) {
        Swal.fire({ icon: 'success', title: 'Theme saved', timer: 1100, showConfirmButton: false });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('themeModeToggle')?.addEventListener('click', event => {
        event.preventDefault();
        themeState.mode = themeState.mode === 'dark' ? 'light' : 'dark';
        applyTheme();
        saveTheme(true);
    });

    document.querySelectorAll('.theme-swatches .swatch[data-theme-color]').forEach(swatch => {
        swatch.addEventListener('click', event => {
            event.preventDefault();
            themeState.color = swatch.dataset.themeColor;
            applyTheme();
            saveTheme(true);
        });
    });

    document.getElementById('themeStyleSelect')?.addEventListener('change', event => {
        themeState.style = event.target.value;
        applyTheme();
        saveTheme(true);
    });
});
