/**
 * Contact Map Module
 * Handles the initialization and management of the contact page map
 */

export class ContactMap {
    constructor(mapContainer) {
        this.mapContainer = mapContainer;
        this.options = {
            lat: parseFloat(mapContainer.dataset.lat) || 32.109608,
            lng: parseFloat(mapContainer.dataset.lng) || 34.837384,
            zoom: 17,
            address: mapContainer.dataset.address || "18, Raoul Wallenberg, Atidim, Tel Aviv, Israel"
        };
    }

    init() {
        if (!this.mapContainer) return;

        // Initialize the map
        const map = L.map(this.mapContainer).setView([this.options.lat, this.options.lng], this.options.zoom);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add a marker for the office location
        const marker = L.marker([this.options.lat, this.options.lng]).addTo(map);
        
        // Add a popup with the address
        marker.bindPopup(this.options.address).openPopup();
        
        // Ensure the map container is visible and redraws properly
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }
}