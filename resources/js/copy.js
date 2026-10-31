// Copy text to the clipboard, with a fallback for older phones.
export default (text) => ({
    copied: false,
    async copy() {
        try {
            await navigator.clipboard.writeText(text);
        } catch {
            const el = document.createElement('textarea');
            el.value = text;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            el.remove();
        }
        this.copied = true;
        setTimeout(() => (this.copied = false), 2000);
    },
});
