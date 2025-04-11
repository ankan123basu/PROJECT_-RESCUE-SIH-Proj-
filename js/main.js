// Map markers and layers
let markers = new Map();
let activeIncidentLayer = null;

// Initialize map markers
function initializeMarkers() {
    fetch('php/get_agencies.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.data.forEach(agency => {
                    addAgencyMarker(agency);
                });
            } else {
                console.error('Error loading agencies:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Add a marker for an agency
function addAgencyMarker(agency) {
    // Skip if no coordinates are available
    if (!agency.latitude || !agency.longitude || agency.latitude === 0 || agency.longitude === 0) {
        console.warn(`Agency ${agency.name} has invalid coordinates`);
        return;
    }

    const el = document.createElement('div');
    el.className = 'marker';
    
    // Set marker icon based on agency type
    const iconPath = `assets/icons/${agency.type.toLowerCase()}.png`;
    el.style.backgroundImage = `url(${iconPath})`;
    el.style.width = '32px';
    el.style.height = '32px';
    el.style.backgroundSize = '100%';
    
    // Create popup content
    const popupContent = `
        <div class="bg-white rounded-lg p-4 shadow-lg">
            <h3 class="text-lg font-bold text-gray-900">${agency.name}</h3>
            <p class="text-sm text-gray-600">Type: ${agency.type}</p>
            <p class="text-sm text-gray-600">Status: <span class="status-${agency.status ? agency.status.toLowerCase() : 'unknown'}">${agency.status || 'Unknown'}</span></p>
            <p class="text-sm text-gray-600">Phone: ${agency.phone}</p>
            <div class="mt-2">
                <h4 class="text-sm font-semibold text-gray-700">Available Resources:</h4>
                <ul class="list-disc list-inside text-sm text-gray-600">
                    ${agency.resources && Array.isArray(agency.resources) ? agency.resources.map(resource => `<li>${resource}</li>`).join('') : '<li>No resources available</li>'}
                </ul>
            </div>
            <button onclick="contactAgency(${agency.id})" class="mt-3 w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                Contact Agency
            </button>
        </div>
    `;

    // Create and add the marker
    const marker = new mapboxgl.Marker(el)
        .setLngLat([parseFloat(agency.longitude), parseFloat(agency.latitude)])
        .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent))
        .addTo(map);
    
    markers.set(agency.id, marker);
}

// Update marker status
function updateMarkerStatus(agencyId, status) {
    const marker = markers.get(agencyId);
    if (marker) {
        const el = marker.getElement();
        el.className = `marker status-${status.toLowerCase()}`;
    }
}

// Filter markers based on selected criteria
function filterMarkers() {
    const typeFilter = document.querySelector('select[name="type"]')?.value || '';
    const statusFilter = document.querySelector('select[name="status"]')?.value || '';
    const resourceFilter = document.querySelector('select[name="resource"]')?.value || '';

    fetch(`php/get_agencies.php?type=${typeFilter}&status=${statusFilter}&resource=${resourceFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear existing markers
                markers.forEach(marker => marker.remove());
                markers.clear();

                // Add filtered markers
                data.data.forEach(agency => {
                    addAgencyMarker(agency);
                });
            } else {
                console.error('Error filtering agencies:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Contact agency
function contactAgency(agencyId) {
    // Implement communication functionality (e.g., WebSocket, messaging system)
    console.log(`Contacting agency ${agencyId}`);
    // Show communication modal or redirect to messaging interface
}

// Add new incident
function addIncident(incident) {
    const coordinates = [incident.longitude, incident.latitude];

    // Create a pulsing dot for the incident
    const size = 200;
    const pulsingDot = {
        width: size,
        height: size,
        data: new Uint8Array(size * size * 4),
        
        onAdd: function() {
            const canvas = document.createElement('canvas');
            canvas.width = this.width;
            canvas.height = this.height;
            this.context = canvas.getContext('2d');
        },
        
        render: function() {
            const duration = 1000;
            const t = (performance.now() % duration) / duration;
            
            const radius = (size / 2) * 0.3;
            const outerRadius = (size / 2) * 0.7 * t + radius;
            const context = this.context;
            
            context.clearRect(0, 0, this.width, this.height);
            context.beginPath();
            context.arc(
                this.width / 2,
                this.height / 2,
                outerRadius,
                0,
                Math.PI * 2
            );
            context.fillStyle = `rgba(255, 0, 0, ${1 - t})`;
            context.fill();
            
            context.beginPath();
            context.arc(
                this.width / 2,
                this.height / 2,
                radius,
                0,
                Math.PI * 2
            );
            context.fillStyle = 'rgba(255, 0, 0, 1)';
            context.strokeStyle = 'white';
            context.lineWidth = 2 + 4 * (1 - t);
            context.fill();
            context.stroke();
            
            this.data = context.getImageData(
                0,
                0,
                this.width,
                this.height
            ).data;
            
            map.triggerRepaint();
            return true;
        }
    };

    map.addImage('pulsing-dot', pulsingDot, { pixelRatio: 2 });

    // Add incident layer
    if (activeIncidentLayer) {
        map.removeLayer(activeIncidentLayer);
        map.removeSource(activeIncidentLayer);
    }

    activeIncidentLayer = `incident-${incident.id}`;
    map.addSource(activeIncidentLayer, {
        type: 'geojson',
        data: {
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: coordinates
                },
                properties: incident
            }]
        }
    });

    map.addLayer({
        id: activeIncidentLayer,
        type: 'symbol',
        source: activeIncidentLayer,
        layout: {
            'icon-image': 'pulsing-dot'
        }
    });

    // Center map on incident
    map.flyTo({
        center: coordinates,
        zoom: 12,
        essential: true
    });
}

// Initialize real-time updates using WebSocket
function initializeRealTimeUpdates() {
    // In a real application, you would set up WebSocket connection here
    console.log('Initializing real-time updates...');
    
    // Simulate real-time updates for demo purposes
    setInterval(() => {
        fetch('php/get_agencies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update markers with new agency data
                    data.data.forEach(agency => {
                        const marker = markers.get(agency.id);
                        if (marker) {
                            // Update existing marker
                            updateMarkerStatus(agency.id, agency.status);
                        } else {
                            // Add new marker
                            addAgencyMarker(agency);
                        }
                    });
                }
            })
            .catch(error => console.error('Error updating agencies:', error));
    }, 30000); // Update every 30 seconds
}

// Initialize everything when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if map is available on this page
    if (typeof map !== 'undefined') {
        initializeMarkers();
        initializeRealTimeUpdates();
    }
    
    // Set up filter event listeners if filters exist
    const filterSelects = document.querySelectorAll('select[name="type"], select[name="status"], select[name="resource"]');
    filterSelects.forEach(select => {
        select?.addEventListener('change', filterMarkers);
    });
});
