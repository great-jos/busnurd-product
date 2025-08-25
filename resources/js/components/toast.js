document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        visible: false,
        title: '',
        message: '',
        type: 'success',

        show({ title = '', message = '', type = 'success' }) {
            this.title = title;
            this.message = message;
            this.type = type;
            this.visible = true;
            setTimeout(() => this.close(), 5000);
        },
        close() {
            this.visible = false;
        }
    });

    window.addEventListener('toast', e => {
        Alpine.store('toast').show(e.detail);
    });
});