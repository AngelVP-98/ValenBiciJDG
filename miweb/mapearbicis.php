<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Estaciones Valenbisi JDG</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="estilosMapa.css">
</head>

<body>
    <div id="titulo">
        <h1>
            <span>Mapeo de Bicicletas&nbsp;&nbsp;</span>
            <img src="logo.png" width="150px" alt="Logo de ValenBisi">
        </h1>
    </div>
    <div id="filtro-estacion">
        <label for="busqueda">Buscar estación:</label>
        <input type="text" id="busqueda" placeholder="Introduce el nombre...">
    </div>
    <div id="map"></div>
    <h2>Leyenda de Colores</h2>
    <div id="leyenda">
        <div class="leyenda-item">
            <span class="marker black"></span>⚫️ Sin bicicletas disponibles
        </div>
        <div class="leyenda-item">
            <span class="marker red"></span>🔴 Menos de 5 disponibles
        </div>
        <div class="leyenda-item">
            <span class="marker orange"></span>🟠 Entre 5 y 10 disponibles
        </div>
        <div class="leyenda-item">
            <span class="marker green"></span>🟢 Entre 10 y 20 disponibles
        </div>
        <div class="leyenda-item">
            <span class="marker blue"></span>🔵 Más de 20 disponibles
        </div>
    </div>
    <script>
        // Inicializa el mapa centrado en Valencia
        var map = L.map('map').setView([39.47, -0.37], 13);

        // Añadir capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Función para definir el color del marcador según las bicicletas disponibles
        function getMarkerColor(available) {
            if (available == 0) {
                return 'black'
            } else if (available < 5 && available > 0) {
                return 'red';
            } else if (available >= 5 && available < 10) {
                return 'orange';
            } else if (available >= 10 && available < 20) {
                return 'green';
            } else {
                return 'blue';
            }
        }

        // Cargar el archivo data.json
        fetch('data.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error al cargar data.json: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                const estaciones = Object.values(data);
                const input = document.getElementById('busqueda');

                function actualizarMarcadores(filtro) {
                    map.eachLayer(layer => {
                        if (layer instanceof L.CircleMarker) {
                            map.removeLayer(layer);
                        }
                    });

                    estaciones.forEach(station => {
                        const { lat, lon, address, available, free, total } = station;
                        if (lat && lon && address.toLowerCase().includes(filtro.toLowerCase())) {
                            L.circleMarker([lat, lon], {
                                color: getMarkerColor(available),
                                radius: 8,
                                fillOpacity: 0.1
                            })
                                .addTo(map)
                                .bindPopup(`
            <strong>${address}</strong><br>
            <b>Disponibles:</b> ${available}<br>
            <b>Libres:</b> ${free}<br>
            <b>Total:</b> ${total}
            `);
                        }
                    });
                }

                // Evento para actualizar en cada caracter
                input.addEventListener('input', () => {
                    actualizarMarcadores(input.value);
                });

                // Mostrar todos al inicio
                actualizarMarcadores('');
            })
            .catch(error => {
                console.error('Error cargando los datos:', error);
            });
    </script>
</body>

</html>