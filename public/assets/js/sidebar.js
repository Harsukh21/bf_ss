/**
 * Responsive Sidebar Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    
    if (!sidebar || !sidebarToggle) return;
    
    // Check if we're on mobile initially
    const isMobile = () => window.innerWidth < 768;
    
    // Get sidebar state from localStorage
    const isSidebarOpen = () => {
        const saved = localStorage.getItem('sidebarOpen');
        return saved === null ? true : saved === 'true'; // Default to open
    };
    
    // Set sidebar state in localStorage
    const setSidebarState = (isOpen) => {
        localStorage.setItem('sidebarOpen', isOpen);
    };
    
    // Initialize sidebar state
    function initSidebar() {
        if (isMobile()) {
            // On mobile, sidebar starts closed
            sidebar.classList.remove('sidebar-open');
            sidebar.style.transform = 'translateX(-100%)';
            updateMainContentMargin(false);
            const header = document.getElementById('topHeader');
            if (header) {
                header.style.width = '100%';
            }
        } else {
            // On desktop, use saved state
            const shouldBeOpen = isSidebarOpen();
            if (shouldBeOpen) {
                sidebar.classList.add('sidebar-open');
                sidebar.style.transform = 'translateX(0)';
                updateMainContentMargin(true);
            } else {
                sidebar.classList.remove('sidebar-open');
                sidebar.style.transform = 'translateX(-100%)';
                updateMainContentMargin(false);
            }
        }
        updateOverlay();
    }
    
    // Update main content and header based on sidebar state
    function updateMainContentMargin(open) {
        if (!mainContent) return;
        
        if (isMobile()) return; // No margin adjustment on mobile
        
        const header = document.getElementById('topHeader');
        
        if (open) {
            mainContent.style.marginLeft = '256px'; // 64 * 4 = 256px
            if (header) {
                header.style.left = '256px';
                header.style.width = 'calc(100% - 256px)';
            }
        } else {
            mainContent.style.marginLeft = '0';
            if (header) {
                header.style.left = '0';
                header.style.width = '100%';
            }
        }
    }
    
    // Toggle sidebar
    function toggleSidebar() {
        const isCurrentlyOpen = sidebar.classList.contains('sidebar-open');
        
        if (isCurrentlyOpen) {
            // Hide sidebar
            sidebar.classList.remove('sidebar-open');
            sidebar.style.transform = 'translateX(-100%)';
            updateMainContentMargin(false);
            setSidebarState(false);
        } else {
            // Show sidebar
            sidebar.classList.add('sidebar-open');
            sidebar.style.transform = 'translateX(0)';
            updateMainContentMargin(true);
            setSidebarState(true);
        }
        
        // Update overlay for mobile
        updateOverlay();
    }
    
    // Update overlay state
    function updateOverlay() {
        if (!sidebarOverlay) return;
        
        if (isMobile() && sidebar.classList.contains('sidebar-open')) {
            sidebarOverlay.classList.add('sidebar-overlay-active');
        } else {
            sidebarOverlay.classList.remove('sidebar-overlay-active');
        }
    }
    
    // Close sidebar (used when clicking overlay or outside)
    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        setSidebarState(false);
        updateMainContentMargin(false);
        updateOverlay();
    }
    
    // Event listeners
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
    });
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Window resize handler
    window.addEventListener('resize', function() {
        initSidebar();
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (isMobile() && sidebar.classList.contains('sidebar-open')) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                closeSidebar();
            }
        }
    });
    
    // Escape key to close sidebar on mobile
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile() && sidebar.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });
    
    // Initialize
    initSidebar();
});
