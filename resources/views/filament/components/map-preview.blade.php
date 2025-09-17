<div x-data="{
    latitude: @entangle('data.latitude'),
    longitude: @entangle('data.longitude'),
    address: @entangle('data.address'),
    loading: false,
    init() {
        // Listen for get current location event
        window.addEventListener('get-current-location', () => {
            if (navigator.geolocation) {
                this.loading = true;
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.latitude = position.coords.latitude.toFixed(6);
                        this.longitude = position.coords.longitude.toFixed(6);
                        this.$wire.set('data.latitude', this.latitude);
                        this.$wire.set('data.longitude', this.longitude);
                        
                        // Reverse geocode to get address
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.latitude}&lon=${this.longitude}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    // Update the address field
                                    this.address = data.display_name;
                                    this.$wire.set('data.address', this.address);
                                    
                                    // Show notification
                                    window.$wireui?.notify({
                                        title: 'Location Updated',
                                        description: 'Your current location has been set',
                                        icon: 'success'
                                    });
                                }
                                this.loading = false;
                            })
                            .catch(error => {
                                console.error('Error getting address:', error);
                                this.loading = false;
                            });
                        
                        this.updateMap();
                    },
                    (error) => {
                        alert('Error getting location: ' + error.message);
                        this.loading = false;
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser');
            }
        });
        
        // Listen for geocode address event
        window.addEventListener('geocode-address', (event) => {
            const address = event.detail.address;
            if (address) {
                this.loading = true;
                // Simple geocoding using Nominatim (OpenStreetMap)
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            this.latitude = parseFloat(data[0].lat).toFixed(6);
                            this.longitude = parseFloat(data[0].lon).toFixed(6);
                            this.$wire.set('data.latitude', this.latitude);
                            this.$wire.set('data.longitude', this.longitude);
                            this.updateMap();
                            
                            // Show notification
                            window.$wireui?.notify({
                                title: 'Address Found',
                                description: 'Location coordinates have been updated',
                                icon: 'success'
                            });
                        } else {
                            alert('Address not found. Please try a different address.');
                        }
                        this.loading = false;
                    })
                    .catch(error => {
                        alert('Error looking up address: ' + error.message);
                        this.loading = false;
                    });
            }
        });
        
        this.updateMap();
    },
    updateMap() {
        if (this.latitude && this.longitude) {
            const mapFrame = this.$refs.mapFrame;
            if (mapFrame) {
                // Use OpenStreetMap embed
                mapFrame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${parseFloat(this.longitude) - 0.01},${parseFloat(this.latitude) - 0.01},${parseFloat(this.longitude) + 0.01},${parseFloat(this.latitude) + 0.01}&layer=mapnik&marker=${this.latitude},${this.longitude}`;
            }
        }
    }
}" 
x-init="init"
class="space-y-2">
    <div class="text-sm text-gray-600">
        <span x-show="loading" class="text-blue-600">
            <svg class="inline animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Getting location...
        </span>
        <span x-show="!loading && latitude && longitude">
            Map Preview: <span x-text="latitude"></span>, <span x-text="longitude"></span>
        </span>
        <span x-show="!loading && (!latitude || !longitude)" class="text-gray-400">
            Enter coordinates or use the buttons above to show map preview
        </span>
    </div>
    
    <div x-show="latitude && longitude" class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden">
        <iframe 
            x-ref="mapFrame"
            width="100%" 
            height="100%" 
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0"
            class="absolute inset-0"
        ></iframe>
    </div>
</div>