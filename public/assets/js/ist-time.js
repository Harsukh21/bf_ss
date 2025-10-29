// IST Time Display - Updates every second
(function() {
    function updateISTTime() {
        const istTimeElement = document.getElementById('istTime');
        if (!istTimeElement) return;

        const now = new Date();
        // Create IST date (UTC + 5:30 hours)
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const ist = new Date(utc + (5.5 * 3600000)); // IST is UTC+5:30

        // Format date
        const dateOptions = { 
            year: 'numeric', 
            month: 'short', 
            day: '2-digit'
        };
        
        // Format time
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        
        const dateStr = ist.toLocaleDateString('en-US', dateOptions);
        const timeStr = ist.toLocaleTimeString('en-US', timeOptions);
        
        istTimeElement.textContent = `${dateStr}, ${timeStr}`;
    }

    // Update immediately on load
    updateISTTime();

    // Update every second
    setInterval(updateISTTime, 1000);
})();

