$(function() {
    // map util
    var box = $("#mapCanvas");
    if(box.length > 0)
    {
        var mapOptions = {
            center: new google.maps.LatLng(45.4516675, -73.5904749),
            zoom: 8,
            disableDefaultUI: true,
            draggable: false,
            disableDoubleClickZoom: false,
            scrollwheel : false,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles : [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#193341"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#2c5a71"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#29768a"},{"lightness":-37}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#406d80"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#3e606f"},{"weight":2},{"gamma":0.84}]},{"elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"weight":0.6},{"color":"#1a3541"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#2c5a71"}]}]
        },
        map = new google.maps.Map(box.get(0), mapOptions);

    }

});
