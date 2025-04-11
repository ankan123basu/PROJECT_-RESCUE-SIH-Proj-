// Map Configuration
const MAP_CONFIG = {
    defaultCenter: [20.5937, 78.9629], // India center coordinates
    defaultZoom: 5,
    maxZoom: 18,
    tileLayer: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
};

// Custom Icons Configuration
const ICONS = {
    ambulance: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/ambulance-icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
        className: 'marker-pulse'
    }),
    helicopter: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/helicopter-icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
        className: 'marker-pulse'
    }),
    fire_truck: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/fire-truck-icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
        className: 'marker-pulse'
    }),
    hospital: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/hospital-colored.png',
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18]
    }),
    fireStation: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/fire-station-colored.png',
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18]
    }),
    helipad: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/helipad-colored.png',
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18]
    }),
    incident: L.icon({
        iconUrl: window.location.origin + '/assets/icons/colored/incident-colored.png',
        iconSize: [36, 36],
        iconAnchor: [18, 18],
        popupAnchor: [0, -18],
        className: 'marker-pulse'
    })
};

// Preload all icons to ensure they're available when needed
function preloadIcons() {
    console.log('Preloading map icons...');
    const iconPaths = [
        window.location.origin + '/assets/icons/colored/ambulance-icon.png',
        window.location.origin + '/assets/icons/colored/fire-truck-icon.png',
        window.location.origin + '/assets/icons/colored/helicopter-icon.png',
        window.location.origin + '/assets/icons/colored/hospital-colored.png',
        window.location.origin + '/assets/icons/colored/fire-station-colored.png',
        window.location.origin + '/assets/icons/colored/helipad-colored.png',
        window.location.origin + '/assets/icons/colored/incident-colored.png'
    ];
    
    iconPaths.forEach(path => {
        const img = new Image();
        img.src = path;
        img.onload = () => console.log(`Loaded: ${path}`);
        img.onerror = () => console.error(`Failed to load: ${path}`);
    });
}

// Call preload function when the page loads
window.addEventListener('DOMContentLoaded', preloadIcons);

// Initialize map with custom styles
function initMap(containerId) {
    const map = L.map(containerId).setView(MAP_CONFIG.defaultCenter, MAP_CONFIG.defaultZoom);
    
    L.tileLayer(MAP_CONFIG.tileLayer, {
        maxZoom: MAP_CONFIG.maxZoom,
        attribution: MAP_CONFIG.attribution
    }).addTo(map);

    return map;
}

// Create custom popup content
function createPopupContent(agency) {
    // Determine status color
    let statusColor = 'gray';
    if (agency.status === 'Available') statusColor = 'green';
    if (agency.status === 'Responding') statusColor = 'yellow';
    if (agency.status === 'On Call') statusColor = 'blue';
    if (agency.status === 'Busy') statusColor = 'red';

    return `
        <div class="glass-card dark p-4 rounded-lg shadow-lg max-w-sm">
            <h3 class="text-xl font-bold mb-2 text-white">${agency.name}</h3>
            <div class="space-y-2 text-gray-200">
                <p class="flex items-center">
                    <span class="status-dot ${agency.status.toLowerCase()}"></span>
                    <span>${agency.status}</span>
                </p>
                <p><strong>Type:</strong> ${agency.type.replace('_', ' ').toUpperCase()}</p>
                <p><strong>Contact:</strong> ${agency.contact_number || 'N/A'}</p>
                <p><strong>Address:</strong> ${agency.address || 'N/A'}</p>
            </div>
            <div class="mt-4 flex space-x-2">
                <button onclick="dispatchResource('${agency.id || Math.random().toString(36).substring(2, 10)}')" 
                        class="glossy-btn bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg">
                    Dispatch
                </button>
                <button onclick="viewDetails('${agency.id || Math.random().toString(36).substring(2, 10)}')"
                        class="glossy-btn bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg">
                    Details
                </button>
            </div>
        </div>
    `;
}

// Function to dispatch a resource
function dispatchResource(agencyId) {
    alert(`Dispatching resource: ${agencyId}`);
    // In a real application, this would make an API call to update the resource status
}

// Function to view agency details
function viewDetails(agencyId) {
    alert(`Viewing details for: ${agencyId}`);
    // In a real application, this would open a detailed view or modal
}

// UI Theme Configuration
const UI_CONFIG = {
    colors: {
        primary: {
            ambulance: {
                light: '#3b82f6', // blue-500
                dark: '#1d4ed8', // blue-700
                gradient: 'from-blue-500 to-blue-700'
            },
            fireTruck: {
                light: '#ef4444', // red-500
                dark: '#b91c1c', // red-700
                gradient: 'from-red-500 to-red-700'
            },
            helicopter: {
                light: '#8b5cf6', // purple-500
                dark: '#6d28d9', // purple-700
                gradient: 'from-purple-500 to-purple-700'
            }
        },
        status: {
            available: '#10b981', // green-500
            responding: '#f59e0b', // amber-500
            onCall: '#3b82f6', // blue-500
            busy: '#ef4444' // red-500
        }
    }
};

// Function to load agencies from API
async function loadAgencies(type = null) {
    try {
        const response = await fetch('php/get_agencies.php');
        let data;
        
        try {
            data = await response.json();
        } catch (parseError) {
            console.warn('Could not parse response, using sample data');
            return getSampleData(type);
        }
        
        if (data && data.success) {
            const agencies = type ? data.agencies.filter(agency => agency.type === type) : data.agencies;
            if (agencies.length > 0) {
                return agencies;
            } else {
                return getSampleData(type);
            }
        } else {
            return getSampleData(type);
        }
    } catch (error) {
        console.error('Error loading agency data:', error);
        return getSampleData(type);
    }
}

// Function to get sample data based on type
function getSampleData(type = null) {
    const sampleData = {
        ambulance: [
            {
                id: 'amb-001',
                name: 'Ambulance Unit 1',
                type: 'ambulance',
                status: 'Available',
                latitude: 20.5937,
                longitude: 78.9629,
                contact_number: '+91 9876543210',
                address: 'Delhi Medical Center'
            },
            {
                id: 'amb-002',
                name: 'Advanced Life Support 2',
                type: 'ambulance',
                status: 'Responding',
                latitude: 19.0760,
                longitude: 72.8777,
                contact_number: '+91 9876543211',
                address: 'Mumbai General Hospital'
            },
            {
                id: 'amb-003',
                name: 'Mobile ICU 3',
                type: 'ambulance',
                status: 'On Call',
                latitude: 12.9716,
                longitude: 77.5946,
                contact_number: '+91 9876543212',
                address: 'Bangalore Medical Institute'
            },
            {
                id: 'amb-004',
                name: 'Patient Transport 4',
                type: 'ambulance',
                status: 'Busy',
                latitude: 17.3850,
                longitude: 78.4867,
                contact_number: '+91 9876543213',
                address: 'Hyderabad Health Center'
            },
            {
                id: 'amb-005',
                name: 'Emergency Response 5',
                type: 'ambulance',
                status: 'Available',
                latitude: 22.5726,
                longitude: 88.3639,
                contact_number: '+91 9876543214',
                address: 'Kolkata Medical College'
            }
        ],
        fire_truck: [
            {
                id: 'fire-001',
                name: 'Fire Engine 1',
                type: 'fire_truck',
                status: 'Available',
                latitude: 28.6139,
                longitude: 77.2090,
                contact_number: '+91 9876543215',
                address: 'Delhi Fire Station'
            },
            {
                id: 'fire-002',
                name: 'Ladder Truck 2',
                type: 'fire_truck',
                status: 'Responding',
                latitude: 18.9220,
                longitude: 72.8347,
                contact_number: '+91 9876543216',
                address: 'Mumbai Fire Brigade'
            },
            {
                id: 'fire-003',
                name: 'Rescue Unit 3',
                type: 'fire_truck',
                status: 'On Call',
                latitude: 13.0827,
                longitude: 77.5090,
                contact_number: '+91 9876543217',
                address: 'Bangalore Fire Station'
            },
            {
                id: 'fire-004',
                name: 'Water Tanker 4',
                type: 'fire_truck',
                status: 'Busy',
                latitude: 17.4400,
                longitude: 78.3800,
                contact_number: '+91 9876543218',
                address: 'Hyderabad Fire Services'
            }
        ],
        helicopter: [
            {
                id: 'heli-001',
                name: 'Air Ambulance 1',
                type: 'helicopter',
                status: 'Available',
                latitude: 28.5500,
                longitude: 77.1000,
                contact_number: '+91 9876543219',
                address: 'Delhi Air Rescue'
            },
            {
                id: 'heli-002',
                name: 'Search & Rescue 2',
                type: 'helicopter',
                status: 'Responding',
                latitude: 19.1000,
                longitude: 72.8700,
                contact_number: '+91 9876543220',
                address: 'Mumbai Helipad'
            },
            {
                id: 'heli-003',
                name: 'Medical Evac 3',
                type: 'helicopter',
                status: 'On Call',
                latitude: 12.9000,
                longitude: 77.6200,
                contact_number: '+91 9876543221',
                address: 'Bangalore Air Base'
            }
        ]
    };
    
    if (type) {
        return sampleData[type] || [];
    } else {
        return [...sampleData.ambulance, ...sampleData.fire_truck, ...sampleData.helicopter];
    }
}

// Function to display agencies on the map
function displayAgencies(map, agencies, iconType = null) {
    // Clear existing markers
    map.eachLayer(layer => {
        if (layer instanceof L.Marker) {
            map.removeLayer(layer);
        }
    });
    
    // Add agency markers
    agencies.forEach(agency => {
        // Debug log to check icon type
        console.log('Agency type:', agency.type, 'Icon type:', iconType);
        
        // Make sure we have a valid icon
        let icon;
        if (iconType && ICONS[iconType]) {
            icon = ICONS[iconType];
        } else if (agency.type && ICONS[agency.type]) {
            icon = ICONS[agency.type];
        } else {
            // Default to a basic icon if the specified icon isn't found
            console.warn(`Icon not found for type: ${iconType || agency.type}`);
            icon = L.icon({
                iconUrl: window.location.origin + '/assets/icons/colored/incident-colored.png',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });
        }
        
        const marker = L.marker([agency.latitude, agency.longitude], {
            icon: icon
        }).addTo(map);
        
        marker.bindPopup(createPopupContent(agency));
    });
    
    // Adjust map view to fit all markers if there are any
    if (agencies.length > 0) {
        const bounds = agencies.map(a => [a.latitude, a.longitude]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

// Function to add facilities to the map (hospitals, fire stations, helipads)
function addFacilities(map, type) {
    const facilities = {
        hospitals: [
            { name: 'AIIMS Delhi', latitude: 28.5672, longitude: 77.2100, address: 'Ansari Nagar, New Delhi' },
            { name: 'Fortis Hospital', latitude: 19.1172, longitude: 72.8350, address: 'Mulund, Mumbai' },
            { name: 'Apollo Hospital', latitude: 13.0101, longitude: 77.5511, address: 'Bannerghatta Road, Bangalore' },
            { name: 'Care Hospital', latitude: 17.4400, longitude: 78.4480, address: 'Banjara Hills, Hyderabad' },
            { name: 'Ruby Hospital', latitude: 22.5400, longitude: 88.3700, address: 'E.M. Bypass, Kolkata' }
        ],
        fireStations: [
            { name: 'Delhi Central Fire Station', latitude: 28.6300, longitude: 77.2200, address: 'Connaught Place, New Delhi' },
            { name: 'Mumbai Fire Brigade HQ', latitude: 18.9442, longitude: 72.8330, address: 'Byculla, Mumbai' },
            { name: 'Bangalore City Fire Station', latitude: 12.9700, longitude: 77.5600, address: 'M.G. Road, Bangalore' },
            { name: 'Hyderabad Fire Control', latitude: 17.3850, longitude: 78.4570, address: 'Secretariat Road, Hyderabad' }
        ],
        helipads: [
            { name: 'Delhi Airport Helipad', latitude: 28.5562, longitude: 77.1000, address: 'IGI Airport, New Delhi' },
            { name: 'Juhu Aerodrome', latitude: 19.0980, longitude: 72.8330, address: 'Juhu, Mumbai' },
            { name: 'HAL Helipad', latitude: 12.9500, longitude: 77.6800, address: 'HAL Airport, Bangalore' },
            { name: 'Begumpet Airport', latitude: 17.4530, longitude: 78.4674, address: 'Begumpet, Hyderabad' }
        ]
    };
    
    if (!facilities[type]) return;
    
    facilities[type].forEach(facility => {
        const icon = type === 'hospitals' ? ICONS.hospital : 
                    type === 'fireStations' ? ICONS.fireStation : ICONS.helipad;
        
        const marker = L.marker([facility.latitude, facility.longitude], {
            icon: icon
        }).addTo(map);
        
        marker.bindPopup(`
            <div class="glass-card dark p-4 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-2 text-white">${facility.name}</h3>
                <p class="text-gray-200"><strong>Address:</strong> ${facility.address}</p>
            </div>
        `);
    });
}

// Function to add incidents to the map
function addIncidents(map) {
    const incidents = [
        { 
            name: 'Building Fire', 
            latitude: 28.6100, 
            longitude: 77.2300, 
            type: 'fire',
            severity: 'high',
            description: 'Commercial building on fire, multiple floors affected'
        },
        { 
            name: 'Traffic Accident', 
            latitude: 19.0500, 
            longitude: 72.8900, 
            type: 'accident',
            severity: 'medium',
            description: 'Multi-vehicle collision, injuries reported'
        },
        { 
            name: 'Flood Area', 
            latitude: 13.0500, 
            longitude: 77.5800, 
            type: 'flood',
            severity: 'high',
            description: 'Urban flooding, several streets submerged'
        },
        { 
            name: 'Building Collapse', 
            latitude: 17.4000, 
            longitude: 78.4800, 
            type: 'collapse',
            severity: 'critical',
            description: 'Partial building collapse, people trapped'
        }
    ];
    
    incidents.forEach(incident => {
        const marker = L.marker([incident.latitude, incident.longitude], {
            icon: ICONS.incident
        }).addTo(map);
        
        let severityClass = 'bg-yellow-500';
        if (incident.severity === 'high') severityClass = 'bg-orange-500';
        if (incident.severity === 'critical') severityClass = 'bg-red-600';
        
        marker.bindPopup(`
            <div class="glass-card dark p-4 rounded-lg shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-bold text-white">${incident.name}</h3>
                    <span class="px-2 py-1 rounded-full text-xs text-white ${severityClass}">${incident.severity.toUpperCase()}</span>
                </div>
                <p class="text-gray-200 mb-2"><strong>Type:</strong> ${incident.type.charAt(0).toUpperCase() + incident.type.slice(1)}</p>
                <p class="text-gray-200">${incident.description}</p>
                <div class="mt-4 flex space-x-2">
                    <button class="glossy-btn bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg w-full">
                        Respond
                    </button>
                </div>
            </div>
        `);
    });
}
