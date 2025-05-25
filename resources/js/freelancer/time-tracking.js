// resources/js/freelancer/time-tracking.js

(function() {
    const LOCAL_STORAGE_KEY = 'offlineTimeLogs';
    const RUNNING_TIMER_KEY = 'runningOfflineTimer';

    // Helper to get current time in ISO format
    function getNowIso() {
        return new Date().toISOString();
    }

    // --- Local Storage Management ---
    function getOfflineTimeLogs() {
        const data = localStorage.getItem(LOCAL_STORAGE_KEY);
        return data ? JSON.parse(data) : [];
    }

    function saveOfflineTimeLogs(logs) {
        localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(logs));
    }

    function getRunningOfflineTimer() {
        const data = localStorage.getItem(RUNNING_TIMER_KEY);
        return data ? JSON.parse(data) : null;
    }

    function saveRunningOfflineTimer(timer) {
        localStorage.setItem(RUNNING_TIMER_KEY, JSON.stringify(timer));
    }

    function clearRunningOfflineTimer() {
        localStorage.removeItem(RUNNING_TIMER_KEY);
    }

    // --- Offline Timer Logic ---
    function startOfflineTimer(assignmentId) {
        const offlineId = 'offline-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const timer = {
            offline_id: offlineId,
            job_assignment_id: assignmentId,
            start_time: getNowIso(),
            status: 'running',
            is_offline_recorded: true
        };
        saveRunningOfflineTimer(timer);
        console.log('Offline timer started:', timer);
        return timer;
    }

    function stopOfflineTimer(notes = '', proofOfWork = null) {
        const runningTimer = getRunningOfflineTimer();
        if (!runningTimer) {
            console.warn('No running offline timer to stop.');
            return null;
        }

        const endTime = getNowIso();
        const startTime = new Date(runningTimer.start_time);
        const durationMinutes = Math.round((new Date(endTime).getTime() - startTime.getTime()) / (1000 * 60)); // Duration in minutes

        const stoppedLog = {
            ...runningTimer,
            end_time: endTime,
            duration_minutes: durationMinutes,
            notes: notes,
            proof_of_work: proofOfWork, // Base64 or URL if handled
            status: 'pending_sync' // Mark for synchronization
        };

        const logs = getOfflineTimeLogs();
        logs.push(stoppedLog);
        saveOfflineTimeLogs(logs);
        clearRunningOfflineTimer();
        console.log('Offline timer stopped and saved:', stoppedLog);
        return stoppedLog;
    }

    // --- Synchronization Logic (Placeholder) ---
    async function syncOfflineTimeLogs() {
        const logsToSync = getOfflineTimeLogs();
        if (logsToSync.length === 0) {
            console.log('No offline time logs to sync.');
            return;
        }

        console.log('Attempting to sync', logsToSync.length, 'offline time logs...');

        // This is where you'd send data to your backend
        // Example using fetch API (requires a backend endpoint)
        // for (const log of logsToSync) {
        //     try {
        //         const response = await fetch('/api/freelancer/time-logs/sync', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        //             },
        //             body: JSON.stringify(log)
        //         });
        //         if (response.ok) {
        //             console.log('Successfully synced log:', log.offline_id);
        //             // Remove synced log from local storage
        //             let remainingLogs = getOfflineTimeLogs().filter(l => l.offline_id !== log.offline_id);
        //             saveOfflineTimeLogs(remainingLogs);
        //         } else {
        //             console.error('Failed to sync log:', log.offline_id, await response.text());
        //         }
        //     } catch (error) {
        //         console.error('Network error during sync:', error);
        //     }
        // }
        console.log('Synchronization logic needs backend endpoint implementation.');
    }

    // --- Expose functions globally or via a module pattern ---
    window.TimeTracker = {
        startOfflineTimer,
        stopOfflineTimer,
        getRunningOfflineTimer,
        syncOfflineTimeLogs,
        getOfflineTimeLogs // For debugging/inspection
    };

    // --- Event Listeners for Online/Offline Status ---
    window.addEventListener('online', () => {
        console.log('App is online. Attempting to sync offline logs.');
        syncOfflineTimeLogs();
    });

    window.addEventListener('offline', () => {
        console.log('App is offline. Operations will be stored locally.');
    });

    // Initial sync attempt when page loads (if online)
    if (navigator.onLine) {
        syncOfflineTimeLogs();
    }

})();
