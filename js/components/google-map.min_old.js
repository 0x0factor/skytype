var GoogleMap=function(){"use strict";var e=function(){function e(){function e(e,t){var n=1<<t,a=o(e);new google.maps.Point(Math.floor(a.x*n),Math.floor(a.y*n)),new google.maps.Point(Math.floor(a.x*n/l),Math.floor(a.y*n/l));return["277 Bedford Avenue, <br> Brooklyn, NY 11211, <br> New York, USA"].join("<br>")}function o(e){var o=Math.sin(e.lat()*Math.PI/180);return o=Math.min(Math.max(o,-.9999),.9999),new google.maps.Point(l*(.5+e.lng()/360),l*(.5-Math.log((1+o)/(1-o))/(4*Math.PI)))}var t=[{featureType:"all",stylers:[{saturation:-80}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{hue:"#00ffee"},{saturation:50}]},{featureType:"poi.business",elementType:"labels",stylers:[{visibility:"off"}]}],n=new google.maps.LatLng(41.85,(-73.961)),a=new google.maps.Map(document.getElementById("map"),{center:n,styles:t,scrollwheel:!1,zoom:6,streetViewControl:!1,mapTypeControl:!1,zoomControl:!1,scaleControl:!1}),r=new google.maps.InfoWindow;r.setContent(e(n,a.getZoom())),r.setPosition(n),r.open(a),a.addListener("zoom_changed",function(){r.setContent(e(n,a.getZoom())),r.open(a)});var l=256}google.maps.event.addDomListener(window,"load",e)};return{init:function(){e()}}}();$(document).ready(function(){GoogleMap.init()});