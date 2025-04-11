/**
 * Real-time Updates System for RESCUE
 * 
 * This script handles real-time updates between the Agency Dashboard,
 * Agency Network, and Emergency Coordination Center.
 */

class RealTimeUpdates {
    constructor() {
        this.updateInterval = 10000; // 10 seconds
        this.activeIntervals = [];
        this.lastUpdateTime = {};
        this.isInitialized = false;
    }

    /**
     * Initialize the real-time update system
     */
    init() {
        if (this.isInitialized) return;
        
        // Check if user is logged in
        this.checkSession()
            .then(response => {
                if (response.loggedIn) {
                    this.agencyId = response.agencyId;
                    this.setupEventListeners();
                    this.isInitialized = true;
                } else {
                    console.error('User not logged in. Redirecting to login page.');
                    window.location.href = 'index.html';
                }
            })
            .catch(error => {
                console.error('Error checking session:', error);
            });
    }

    /**
     * Check if user is logged in
     */
    async checkSession() {
        try {
            const response = await fetch('php/check_session.php');
            return await response.json();
        } catch (error) {
            console.error('Error checking session:', error);
            return { loggedIn: false };
        }
    }

    /**
     * Set up event listeners for real-time updates
     */
    setupEventListeners() {
        // Listen for status changes
        document.addEventListener('statusChange', (e) => {
            this.broadcastStatusChange(e.detail);
        });

        // Listen for emergency declarations
        document.addEventListener('emergencyDeclared', (e) => {
            this.broadcastEmergency(e.detail);
        });

        // Listen for resource updates
        document.addEventListener('resourceUpdate', (e) => {
            this.broadcastResourceUpdate(e.detail);
        });
    }

    /**
     * Start polling for updates for a specific module
     * @param {string} module - The module to start updates for (dashboard, network, emergency)
     * @param {function} callback - The callback function to handle updates
     */
    startUpdates(module, callback) {
        // Clear any existing interval for this module
        this.stopUpdates(module);
        
        // Initialize last update time if not set
        if (!this.lastUpdateTime[module]) {
            this.lastUpdateTime[module] = Date.now();
        }

        // Create a new interval
        const intervalId = setInterval(() => {
            this.fetchUpdates(module)
                .then(data => {
                    if (data && data.updates) {
                        this.lastUpdateTime[module] = Date.now();
                        callback(data.updates);
                    }
                })
                .catch(error => {
                    console.error(`Error fetching ${module} updates:`, error);
                });
        }, this.updateInterval);

        // Store the interval ID
        this.activeIntervals[module] = intervalId;
    }

    /**
     * Stop polling for updates for a specific module
     * @param {string} module - The module to stop updates for
     */
    stopUpdates(module) {
        if (this.activeIntervals[module]) {
            clearInterval(this.activeIntervals[module]);
            delete this.activeIntervals[module];
        }
    }

    /**
     * Fetch updates for a specific module
     * @param {string} module - The module to fetch updates for
     */
    async fetchUpdates(module) {
        try {
            const timestamp = this.lastUpdateTime[module] || 0;
            const response = await fetch(`php/get_updates.php?module=${module}&timestamp=${timestamp}`);
            return await response.json();
        } catch (error) {
            console.error(`Error fetching ${module} updates:`, error);
            return null;
        }
    }

    /**
     * Broadcast a status change to the server
     * @param {object} statusData - The status data to broadcast
     */
    async broadcastStatusChange(statusData) {
        try {
            const response = await fetch('php/update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    status: statusData.status,
                    notes: statusData.notes || ''
                })
            });
            
            const result = await response.json();
            if (result.success) {
                console.log('Status updated successfully');
                // Trigger immediate update for all modules
                this.triggerImmediateUpdates();
            } else {
                console.error('Error updating status:', result.error);
            }
        } catch (error) {
            console.error('Error broadcasting status change:', error);
        }
    }

    /**
     * Broadcast an emergency declaration to the server
     * @param {object} emergencyData - The emergency data to broadcast
     */
    async broadcastEmergency(emergencyData) {
        try {
            const response = await fetch('php/declare_emergency.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    type: emergencyData.type,
                    description: emergencyData.description,
                    location: emergencyData.location,
                    severity: emergencyData.severity
                })
            });
            
            const result = await response.json();
            if (result.success) {
                console.log('Emergency declared successfully');
                // Trigger immediate update for all modules
                this.triggerImmediateUpdates();
            } else {
                console.error('Error declaring emergency:', result.error);
            }
        } catch (error) {
            console.error('Error broadcasting emergency:', error);
        }
    }

    /**
     * Broadcast a resource update to the server
     * @param {object} resourceData - The resource data to broadcast
     */
    async broadcastResourceUpdate(resourceData) {
        try {
            const response = await fetch('php/update_resources.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(resourceData)
            });
            
            const result = await response.json();
            if (result.success) {
                console.log('Resources updated successfully');
                // Trigger immediate update for all modules
                this.triggerImmediateUpdates();
            } else {
                console.error('Error updating resources:', result.error);
            }
        } catch (error) {
            console.error('Error broadcasting resource update:', error);
        }
    }

    /**
     * Trigger immediate updates for all active modules
     */
    triggerImmediateUpdates() {
        Object.keys(this.activeIntervals).forEach(module => {
            this.fetchUpdates(module)
                .then(data => {
                    if (data && data.updates) {
                        // Dispatch a custom event with the updates
                        const event = new CustomEvent(`${module}Updated`, {
                            detail: data.updates
                        });
                        document.dispatchEvent(event);
                    }
                })
                .catch(error => {
                    console.error(`Error fetching immediate ${module} updates:`, error);
                });
        });
    }

    /**
     * Update the agency network map with new data
     * @param {object} mapData - The map data to update with
     */
    updateNetworkMap(mapData) {
        // This function will be implemented by the agency network page
        document.dispatchEvent(new CustomEvent('networkMapUpdate', {
            detail: mapData
        }));
    }
}

// Create a global instance of the real-time updates system
const realTimeUpdates = new RealTimeUpdates();

// Initialize the system when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    realTimeUpdates.init();
});
