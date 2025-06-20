document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 992 && sidebar.classList.contains('active') && !sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
            sidebar.classList.remove('active');
            mainContent.classList.remove('sidebar-active');
        }
    });
});