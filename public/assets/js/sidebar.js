/**
 * Sidebar Collapse Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Check if sidebar is collapsed from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    console.log('Sidebar elements found:', {
        sidebar: !!sidebar,
        mainContent: !!mainContent,
        sidebarToggle: !!sidebarToggle,
        sidebarOverlay: !!sidebarOverlay,
        isCollapsed: isCollapsed
    });
    
    // Force hide overlay on initialization
    if (sidebarOverlay) {
        sidebarOverlay.classList.add('hidden');
        sidebarOverlay.style.display = 'none';
    }

    // Initialize sidebar state
    if (isCollapsed) {
        // Force collapse
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.style.transform = 'translateX(-100%)';
        }
        if (mainContent) {
            mainContent.classList.remove('ml-64');
            mainContent.classList.add('ml-0');
            mainContent.style.marginLeft = '0';
        }
        
        // Set top header position for collapsed sidebar
        const topHeader = document.querySelector('header');
        if (topHeader) {
            topHeader.classList.remove('left-64');
            topHeader.classList.add('left-0');
            topHeader.style.left = '0';
        }
        
        if (sidebarOverlay && window.innerWidth < 768) {
            sidebarOverlay.classList.remove('hidden');
            sidebarOverlay.style.display = 'block';
        }
        updateToggleIcon(true);
    } else {
        // Force expand
        if (sidebar) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.style.transform = 'translateX(0)';
        }
        if (mainContent) {
            mainContent.classList.remove('ml-0');
            mainContent.classList.add('ml-64');
            mainContent.style.marginLeft = '16rem';
        }
        
        // Set top header position for expanded sidebar
        const topHeader = document.querySelector('header');
        if (topHeader) {
            topHeader.classList.remove('left-0');
            topHeader.classList.add('left-64');
            topHeader.style.left = '16rem';
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('hidden');
            sidebarOverlay.style.display = 'none';
        }
        updateToggleIcon(false);
    }
    
    // Toggle button click event
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Toggle button clicked');
            toggleSidebar();
        });
    }
    
    // Overlay click event (mobile)
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            console.log('Overlay clicked');
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    }
    
    // Window resize event
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            // Desktop: remove overlay
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('hidden');
            }
            if (sidebar) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.style.transform = 'translateX(0)';
            }
            if (mainContent) {
                mainContent.classList.remove('ml-0');
                mainContent.classList.add('ml-64');
                mainContent.style.marginLeft = '16rem';
            }
            
            // Set top header position for desktop
            const topHeader = document.querySelector('header');
            if (topHeader) {
                topHeader.classList.remove('left-0');
                topHeader.classList.add('left-64');
                topHeader.style.left = '16rem';
            }
        } else {
            // Mobile: handle mobile behavior
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && sidebarOverlay) {
                sidebarOverlay.classList.remove('hidden');
            }
            
            // Set top header position for mobile
            const topHeader = document.querySelector('header');
            if (topHeader) {
                topHeader.classList.remove('left-64');
                topHeader.classList.add('left-0');
                topHeader.style.left = '0';
            }
        }
    });
    
    function toggleSidebar() {
        console.log('Toggling sidebar...');
        const isCollapsed = sidebar.classList.contains('-translate-x-full');
        console.log('Current state - isCollapsed:', isCollapsed);
        
        if (isCollapsed) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    }
    
    function collapseSidebar() {
        console.log('Collapsing sidebar...');
        if (sidebar) {
            sidebar.classList.add('-translate-x-full');
            sidebar.style.transform = 'translateX(-100%)';
        }
        if (mainContent) {
            mainContent.classList.remove('md:ml-64', 'ml-64');
            mainContent.classList.add('ml-0');
            mainContent.style.marginLeft = '0';
        }
        
        // Update top header position when sidebar is collapsed
        const topHeader = document.querySelector('header');
        if (topHeader) {
            topHeader.classList.remove('left-64');
            topHeader.classList.add('left-0');
            topHeader.style.left = '0';
        }
        
        if (sidebarOverlay && window.innerWidth < 768) {
            sidebarOverlay.classList.remove('hidden');
            sidebarOverlay.style.display = 'block';
        }
        
        // Update toggle button icon
        updateToggleIcon(true);
        
        // Save state
        localStorage.setItem('sidebarCollapsed', 'true');
        console.log('Sidebar collapsed and state saved');
    }
    
    function expandSidebar() {
        console.log('Expanding sidebar...');
        if (sidebar) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.style.transform = 'translateX(0)';
        }
        if (mainContent) {
            mainContent.classList.remove('ml-0');
            mainContent.classList.add('ml-64');
            mainContent.style.marginLeft = '16rem'; // 256px = 16rem
        }
        
        // Update top header position when sidebar is expanded
        const topHeader = document.querySelector('header');
        if (topHeader) {
            topHeader.classList.remove('left-0');
            topHeader.classList.add('left-64');
            topHeader.style.left = '16rem';
        }
        
        // Always hide overlay when expanding
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('hidden');
            sidebarOverlay.style.display = 'none';
        }
        
        // Update toggle button icon
        updateToggleIcon(false);
        
        // Save state
        localStorage.setItem('sidebarCollapsed', 'false');
        console.log('Sidebar expanded and state saved');
    }
    
    function updateToggleIcon(isCollapsed) {
        if (!sidebarToggle) return;
        
        const toggleIcon = sidebarToggle.querySelector('svg');
        if (!toggleIcon) return;
        
               if (isCollapsed) {
                   // Show expand icon (hamburger)
                   toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>';
               } else {
                   // Show collapse icon (X)
                   toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
               }
        console.log('Toggle icon updated - collapsed:', isCollapsed);
    }
    
    // Initialize icon based on current state
    const initialState = localStorage.getItem('sidebarCollapsed') === 'true';
    updateToggleIcon(initialState);
    
    console.log('Sidebar functionality initialized');
    
    // Test function - can be called from browser console
    window.testSidebar = function() {
        console.log('Testing sidebar toggle...');
        toggleSidebar();
    };
    
    // Clear localStorage on first load (optional - remove this line if you want to keep state)
    // localStorage.removeItem('sidebarCollapsed');
});
