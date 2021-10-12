<!-- Map Drawing Field for Backpack for Laravel  -->
{{--
    Author: iMokhles
    Website: https://github.com/imokhles
    Addon: https://github.com/imokhles/map-drawing-field-for-backpack
--}}
@php
    $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
    $field['wrapper']['class'] = $field['wrapper']['class'] ?? "form-group col-sm-12";
@endphp
<!-- field_type_name -->
@include('crud::fields.inc.wrapper_start')
<div>
    <label>{!! $field['label'] !!}</label>
</div>

<!-- Wrap the image or canvas element with a block element (container) -->
<div class="row">
    <div class="col-md-6 map-warper mb-3" style="height: 300px;">
        <div id="map-canvas" style="height: 100%; margin:0px; padding: 0px;"></div>
    </div>
    <div class="col-md-6">
        <textarea
                type="text"
                rows="8"
                id="{{ $field['name'] }}_values"
                name="{{ $field['name'] }}"
            @include('crud::fields.inc.attributes')
        ></textarea>
    </div>
</div>



{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD EXTRA CSS  --}}
    {{-- push things in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- no styles -->
    @endpush

    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- no scripts -->
        <script async
                src="https://maps.googleapis.com/maps/api/js?v=3&key={{ $field['api_key'] ?? config('services.google_places.key') }}&libraries=drawing&callback=initMap">
        </script>

        <script>

            auto_grow();
            function auto_grow() {
                let element = document.getElementById("{{$field['name']}}_values");
                element.style.height = "5px";
                element.style.height = (element.scrollHeight)+"px";
            }

            var map; // Global declaration of the map
            var drawingManager;
            var lastpolygon = null;
            var myLatlng = null;
            var polygons = [];
            var currentCoords = [];

            function getCoordsValue() {
                @php
                    $comma = null;
                @endphp
                @foreach($crud->getCurrentEntry()->{$field['name']}[0] as $key=>$coords)
                @if(count($crud->getCurrentEntry()->{$field['name']}[0]) != $key+1)
                currentCoords.push("({{$coords->getLng()}}, {{$coords->getLat()}})");
                @endif
                @endforeach
                $("#{{$field['name']}}_values").val(currentCoords.join(', '));
                auto_grow();
                currentCoords = [];
            }

            function resetMap(controlDiv) {
                // Set CSS for the control border.
                const controlUI = document.createElement("div");
                controlUI.style.backgroundColor = "#fff";
                controlUI.style.border = "2px solid #fff";
                controlUI.style.borderRadius = "3px";
                controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
                controlUI.style.cursor = "pointer";
                controlUI.style.marginTop = "5px";
                controlUI.style.marginBottom = "22px";
                controlUI.style.textAlign = "center";
                controlUI.title = "Reset map";
                controlDiv.appendChild(controlUI);
                // Set CSS for the control interior.
                const controlText = document.createElement("div");
                controlText.style.color = "rgb(25,25,25)";
                controlText.style.fontFamily = "Roboto,Arial,sans-serif";
                controlText.style.fontSize = "17px";
                controlText.style.lineHeight = "20px";
                controlText.style.paddingLeft = "2px";
                controlText.style.paddingRight = "2px";
                controlText.style.height = "20px";
                controlText.style.width = "24px";
                controlText.innerHTML = "X";
                controlUI.appendChild(controlText);
                // Setup the click event listeners: simply set the map to Chicago.
                controlUI.addEventListener("click", () => {
                    lastpolygon.setMap(null);
                    $('#{{$field['name']}}_values').val('');
                    @if($crud->getCurrentEntry() !== null && $crud->getCurrentEntry()->{$field['name']})
                    getCoordsValue(currentCoords)
                    @endif
                    auto_grow();

                });
            }

            function initMap() {

                        @if($crud->getCurrentEntry() !== null && $crud->getCurrentEntry()->{$field['name']})
                var bounds = new google.maps.LatLngBounds();

                const polygonCoords = [
                        @foreach($crud->getCurrentEntry()->{$field['name']}[0] as $coords)
                    { lat: {{$coords->getLat()}}, lng: {{$coords->getLng()}} },
                    @endforeach
                ];
                        @endif

                var myOptions = {
                        zoom: 13,
                        center: { lat: 30.193000747841246, lng: 31.139526309011586 },
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }
                map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

                        @if($crud->getCurrentEntry() !== null && $crud->getCurrentEntry()->{$field['name']})

                var zonePolygon = new google.maps.Polygon({
                        paths: polygonCoords,
                        strokeColor: "#01439c",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillOpacity: 0,
                    });

                zonePolygon.setMap(map);

                zonePolygon.getPaths().forEach(function(path) {
                    path.forEach(function(latlng) {
                        bounds.extend(latlng);
                        map.fitBounds(bounds);
                    });
                });

                @endif


                    drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                    },
                    polygonOptions: {
                        editable: true
                    }
                });
                drawingManager.setMap(map);

                //get current location block
                // infoWindow = new google.maps.InfoWindow();
                // Try HTML5 geolocation.
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };
                            map.setCenter(pos);
                        });
                }

                @if($crud->getCurrentEntry() !== null && $crud->getCurrentEntry()->{$field['name']})
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                    var newShape = event.overlay;
                    newShape.type = event.type;
                });
                @endif
                google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
                    if(lastpolygon)
                    {
                        lastpolygon.setMap(null);
                    }
                    $("#{{$field['name']}}_values").val(event.overlay.getPath().getArray());
                    lastpolygon = event.overlay;
                    auto_grow();
                });

                const resetDiv = document.createElement("div");
                resetMap(resetDiv, lastpolygon);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);
            }

            jQuery('document').ready(function($) {
                @if($crud->getCurrentEntry() !== null && $crud->getCurrentEntry()->{$field['name']})
                getCoordsValue(currentCoords)
                @else
                $("#{{$field['name']}}_values").val("{{$field['default'] ?? ''}}");
                auto_grow();
                @endif
            });
        </script>
    @endpush
@endif
