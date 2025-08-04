document.addEventListener('DOMContentLoaded', function () {
    // Prevent right-click on images
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('contextmenu', e => e.preventDefault());
        img.addEventListener('dragstart', e => e.preventDefault());
    });

    // Disable keyboard shortcuts for saving (Ctrl+S, Ctrl+U, Ctrl+Shift+I etc.)
    document.addEventListener('keydown', function (e) {
        const forbiddenCombos = [
            e.ctrlKey && e.key === 's',
            e.ctrlKey && e.key === 'u',
            e.ctrlKey && e.shiftKey && e.key === 'i',
            // e.key === 'F12'
        ];
        if (forbiddenCombos.some(Boolean)) {
            e.preventDefault();
        }
    });
});
