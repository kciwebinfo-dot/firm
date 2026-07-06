async function safeFetch(url, options = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const button = options.button || null;
    if (button) {
        button.disabled = true;
        button.dataset.oldText = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Please wait';
    }
    try {
        options.headers = options.headers || {};
        options.headers['X-CSRF-Token'] = token;
        const response = await fetch(url, options);
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            data = {
                success: false,
                message: response.ok ? 'Server returned an invalid response.' : 'Server error. Please try again.'
            };
        }
        if (!data.success) {
            Swal.fire('Error', data.message || 'Something went wrong.', 'error');
        }
        return data;
    } catch (error) {
        Swal.fire('Error', 'Request failed. Please refresh the page and try again.', 'error');
        return { success: false, message: 'Request failed' };
    } finally {
        if (button) {
            button.disabled = false;
            button.innerHTML = button.dataset.oldText;
        }
    }
}

function bindAjaxForm(selector, successMessage) {
    document.querySelectorAll(selector).forEach(form => {
        form.addEventListener('submit', async event => {
            event.preventDefault();
            const button = form.querySelector('[type="submit"]');
            const data = await safeFetch(form.action, { method: 'POST', body: new FormData(form), button });
            if (data.success) {
                Swal.fire('Success', successMessage || data.message, 'success').then(() => {
                    if (data.redirect) window.location.href = data.redirect;
                });
            }
        });
    });
}
