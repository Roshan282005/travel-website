'use client';
import { useEffect, useRef, useState } from 'react';
import L from 'leaflet';
import 'leaflet-routing-machine';
import 'leaflet.markercluster';

export default function TravelMap() {
  const mapRef = useRef(null);
  const [map, setMap] = useState(null);
  const [userMarker, setUserMarker] = useState(null);
  const [routingControl, setRoutingControl] = useState(null);
  const [userCoords, setUserCoords] = useState([0, 0]);
  const clusterGroupRef = useRef(L.markerClusterGroup());

  useEffect(() => {
    if (!map) {
      const m = L.map(mapRef.current).setView([0,0], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(m);

      // Cluster group
      clusterGroupRef.current.addTo(m);

      setMap(m);
    }
  }, [map]);

  // Real-time user location
  useEffect(() => {
    if (!map) return;

    if(navigator.geolocation){
      navigator.geolocation.watchPosition(pos=>{
        const lat = pos.coords.latitude;
        const lon = pos.coords.longitude;
        setUserCoords([lat, lon]);

        if(!userMarker){
          const icon = L.icon({
            iconUrl: '/arrow-icon.png', // arrow for heading
            iconSize: [40, 40],
            iconAnchor: [20,20]
          });
          const marker = L.marker([lat, lon], {icon}).addTo(map).bindPopup("📍 You are here").openPopup();
          setUserMarker(marker);
        } else {
          userMarker.setLatLng([lat, lon]);
        }
        map.setView([lat, lon], 14, {animate:true});
      }, err => console.error(err), { enableHighAccuracy:true });
    }
  }, [map, userMarker]);

  // Destination search
  const findDestination = async (dest) => {
    if (!dest) return alert("Enter a destination");

    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(dest)}`);
    const data = await res.json();
    if (!data || data.length === 0) return alert("Destination not found");

    const destLat = parseFloat(data[0].lat);
    const destLon = parseFloat(data[0].lon);

    // Routing
    if(routingControl){
      map.removeControl(routingControl);
    }
    const rc = L.Routing.control({
      waypoints: [
        L.latLng(userCoords[0], userCoords[1]),
        L.latLng(destLat, destLon)
      ],
      routeWhileDragging:true,
      draggableWaypoints:true,
      show:true
    }).addTo(map);
    setRoutingControl(rc);

    // Fly to destination
    map.flyTo([destLat, destLon], 14, {animate:true, duration:2});

    // Add destination marker
    const destMarker = L.marker([destLat,destLon]).bindPopup(`<b>${dest}</b>`).addTo(map);
    clusterGroupRef.current.addLayer(destMarker);

    // Add simulated nearby POIs
    const nearby = [
      {lat: destLat+0.003, lon: destLon+0.004, name:"Hotel Lux"},
      {lat: destLat-0.003, lon: destLon-0.004, name:"Cafe Aroma"},
      {lat: destLat+0.004, lon: destLon-0.003, name:"Museum View"}
    ];
    nearby.forEach(p=>{
      const m = L.marker([p.lat,p.lon]).bindPopup(`<b>${p.name}</b>`);
      clusterGroupRef.current.addLayer(m);
    });
  }

  return (
    <div className="container my-4">
      <div className="text-center mb-3">
        <h2>📍 TravelGo Ultimate Map</h2>
        <p>Live tracking, routing, POIs, and animated map experience.</p>
      </div>
      <div className="d-flex justify-content-center mb-3">
        <input id="destInput" type="text" placeholder="Enter destination" className="form-control w-50 me-2"/>
        <button className="btn btn-primary" onClick={()=>{
          const val = document.getElementById('destInput').value;
          findDestination(val);
        }}>Go</button>
      </div>
      <div ref={mapRef} style={{height:'500px', borderRadius:'12px', boxShadow:'0 6px 20px rgba(0,0,0,0.15)'}}></div>
    </div>
  )
}
