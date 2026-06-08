define([], function () {
    'use strict';

    var googleMapsPromise = null;

    function loadGoogleMaps(apiKey) {
        if (window.google && window.google.maps) {
            return Promise.resolve(window.google.maps);
        }

        if (googleMapsPromise) {
            return googleMapsPromise;
        }

        googleMapsPromise = new Promise(function (resolve, reject) {
            var callbackName = 'foreverMapInitCallback';
            var script = document.createElement('script');

            window[callbackName] = function () {
                resolve(window.google.maps);
            };

            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&callback=' + callbackName;
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                reject(new Error('Google Maps script failed to load.'));
            };

            document.head.appendChild(script);
        });

        return googleMapsPromise;
    }

    return function (config, element) {
        var latitude = parseFloat(config.latitude);
        var longitude = parseFloat(config.longitude);
        var zoom = parseInt(config.zoom, 10) || 5;
        var address = config.address || '';

        if (!element || !config.apiKey || isNaN(latitude) || isNaN(longitude)) {
            return;
        }

        loadGoogleMaps(config.apiKey).then(function () {
            var mapLatLng = new window.google.maps.LatLng(latitude, longitude);
            var map = new window.google.maps.Map(element, {
                zoom: zoom,
                center: mapLatLng,
                mapTypeId: window.google.maps.MapTypeId.ROADMAP
            });
            var marker = new window.google.maps.Marker({
                position: mapLatLng,
                animation: window.google.maps.Animation.DROP,
                map: map
            });

            if (address) {
                var infoWindow = new window.google.maps.InfoWindow({
                    content: address
                });

                marker.addListener('click', function () {
                    infoWindow.open(map, marker);
                });
            }
        }).catch(function (error) {
            if (window.console && window.console.error) {
                window.console.error(error);
            }
        });
    };
});
