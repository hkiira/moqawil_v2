var latitude = document.getElementById("latitude");
var longitude = document.getElementById("longitude");

     window.onload = function() {
         if (navigator.geolocation) {
             navigator.geolocation.getCurrentPosition(setPosition);
         } else {
             x.innerHTML = "Geolocation is not supported by this browser.";
         }
      }

      function setPosition(position) {
         latitude.value = position.coords.latitude;
         longitude.value = position.coords.longitude;

      }