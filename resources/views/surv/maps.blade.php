
<script  src="https://maps.api.2gis.ru/2.0/loader.js"></script>

@extends('layouts.admin')
@section('title', 'Карта Мира')

    @section('content')
    <div id="coordinates-maps" data-json="{{json_encode($coordinates)}}"></div>

    <div id="map" style="width:100%;height:1500px;"></div>
    <script  type="application/javascript">
        var arrJson = document.getElementById("coordinates-maps").getAttribute('data-json');
        var kis = JSON.parse(arrJson);
        //
        // console.log(kis,'07777');


        DG.then(function() {
            var map,
                myIcon,
                myDivIcon;

            map = DG.map('map', {
                center:
                    [42.885933,71.369987]
                ,
                zoom: 4.5
            });



            myDivIcon = DG.divIcon({
                iconSize: [30,30],
                // myIcon: 'https://docs.2gis.com/img/mapgl/marker.svg',
                html: '<b>1</b>',
            });




            for (let i = 0; i < kis.length;i++){
                DG.marker([kis[i]['geo_lat'],kis[i]['geo_lon'] ], {
                    icon: myDivIcon
                }).addTo(map);


            }


        });
    </script>
    @endsection

@section('styles')
   <style>


                .leaflet-marker-icon{
                    margin-left: -15px;
                    margin-top: -15px;
                    padding-left: 10px;
                    padding-top: 5px;
                    width: 30px;
                    height: 30px;
                    transform: translate3d(679px, 264px, 0px);
                    z-index: 264;
                    border-radius: 50%;
                }
   </style>
@endsection




