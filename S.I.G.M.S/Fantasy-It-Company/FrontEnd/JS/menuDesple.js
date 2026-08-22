document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.nav-group__toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var group = toggle.closest('.nav-group');
            var isOpen = group.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen);
        });
    });
});