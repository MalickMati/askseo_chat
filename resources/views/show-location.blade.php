<!DOCTYPE html>
<html>
<head>
    <title>Location Checker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-top: 10px;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
        }
    </style>
</head>
<body>

    <h2>User Location Status</h2>
    <p id="location">Detecting location...</p>
    <p id="distance"></p>
    <p id="atsite"></p>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const targetLat = 31.418517;
        const targetLon = 73.115623;

        let mapInitialized = false;
        let map, userMarker, accuracyCircle, lineToTarget;

        function getDistanceInMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function updateMap(userLat, userLon, accuracy) {
            if (!mapInitialized) {
                map = L.map('map').setView([userLat, userLon], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                userMarker = L.marker([userLat, userLon]).addTo(map).bindPopup('📍 You are here!').openPopup();

                accuracyCircle = L.circle([userLat, userLon], {
                    radius: accuracy,
                    color: 'blue',
                    fillColor: '#aaddff',
                    fillOpacity: 0.3
                }).addTo(map);

                L.marker([targetLat, targetLon], {
                    icon: L.icon({
                        iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                        iconSize: [32, 32],
                        iconAnchor: [16, 32]
                    })
                }).addTo(map).bindPopup('📌 Target Location');

                lineToTarget = L.polyline([[userLat, userLon], [targetLat, targetLon]], {
                    color: 'green',
                    weight: 2,
                    opacity: 0.6
                }).addTo(map);

                mapInitialized = true;
            } else {
                userMarker.setLatLng([userLat, userLon]);
                accuracyCircle.setLatLng([userLat, userLon]).setRadius(accuracy);
                lineToTarget.setLatLngs([[userLat, userLon], [targetLat, targetLon]]);
                map.setView([userLat, userLon]);
            }
        }

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    const distance = getDistanceInMeters(userLat, userLon, targetLat, targetLon);

                    document.getElementById('location').innerText =
                        `📍 Your Location: Latitude: ${userLat.toFixed(6)}, Longitude: ${userLon.toFixed(6)}, Accuracy: ±${accuracy.toFixed(2)}m`;

                    document.getElementById('distance').innerText =
                        `📏 Distance from site: ${distance.toFixed(2)} meters`;

                    if (accuracy <= 100 && distance <= 100) {
                        document.getElementById('atsite').innerText = '✅ At Site = true';
                    } else if (accuracy > 100) {
                        document.getElementById('atsite').innerText = `⚠️ Accuracy too low (±${accuracy.toFixed(2)}m). Retrying...`;
                    } else {
                        document.getElementById('atsite').innerText = '❌ At Site = false';
                    }

                    updateMap(userLat, userLon, accuracy);
                },
                function (error) {
                    document.getElementById('location').innerText = '❌ Location error: ' + error.message;
                },
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 10000
                }
            );
        } else {
            document.getElementById('location').innerText =
                "❌ Geolocation not supported by this browser.";
        }
    </script>
</body>
</html>
