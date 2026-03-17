@extends('layouts.app')

@section('style')
    <link href="{{ asset('css/annotorious2.7.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/viewer.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/jstree/themes/default/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb" id="image_desc">
            <ol class="breadcrumb d-flex justify-content-between align-items-center mb-0">
                <div class="d-flex">
                    <a href="{{ url('/slides') }}?batch_id={{ $image->batch->id }}" class="breadcrumb-item"> {{ $image->batch->project }}  <span class="breadcrumb-separator">/</span>  {{ $image->batch->name }}</a>
                    <li class="breadcrumb-item active" aria-current="page">{{ $image->name }}</li>
                </div>
                <div class="slide-navigation">   
                    @if ($previousImageId)
                        <a href="{{ url('/slides/' . $previousImageId . '/0') }}" class="text-decoration-none mr-3">
                            <i class="fas fa-angle-left"></i> Previous
                        </a>
                    @endif

                    @if ($nextImageId)
                        <a href="{{ url('/slides/' . $nextImageId . '/0') }}" class="text-decoration-none">
                            Next <i class="fas fa-angle-right"></i>
                        </a>
                    @endif
                </div>
            </ol>
        </nav>

        @if($status)
        <!-- Annotation Toolbar -->
        <div class="row" id="toolbar">
            <div id="manual-toolbar" class="inner"></div>
            <div class="vertical-line"></div>
            <div class="model-btn-grp btn-group">
                    <!-- Dynamically generated dropdown items -->
                @php
                    $defaultModel = $modelBtn[0] ?? null;
                    $defaultModelJson = json_encode($defaultModel ?? []);
                @endphp
                <div class="dropdown-btn">
                    <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split btn-sm model-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="modelSelector">
                        @foreach($modelBtn as $model)
                            <a class="dropdown-item" href="#" onclick='selectModel(@json($model))'>{{ $model['text'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div id="modelDisplay" class="model-display">
                    {{ $defaultModel['text'] ?? 'Not available' }}
                </div>
                <div class="run-stop-btn">
                    <button id="runStopButton" type="button" class="btn btn-secondary btn-sm model-btn">
                        <i id="runStopIcon" class="fa fa-play"></i>
                    </button>
                </div>
                <!-- AI Segment Button and Annotation Field -->
                <div class="model-container">
                    <button id="toggleTextFieldButton" type="button" class="btn btn-secondary btn-sm model-btn">
                        <i class="fa fa-tags" aria-hidden="true"></i>
                    </button>
                    <!-- Adjustable text field, positioned like a dropdown -->
                    <input id="tag-input" class="form-control dropdown-text" placeholder="Enter labels..."/>
                </div>   
                <!-- Second Button: Display AI or Model -->
                <button type="button" id="smartpoly" class="btn btn-secondary  btn-sm model-btn">AI Segmentation</button>

                <!-- Third Button: Toggle On/Off -->
                <div class="run-stop-btn">
                    <button id="ai-runStopButton" type="button" class="btn btn-secondary btn-sm model-btn">
                        <i id="ai-runStopIcon" class="fa fa-play"></i>
                    </button>
                </div>  

                <!-- Metastasis Detection Button -->
                <!-- <div class="model-container"></div>
                <button type="button" id="mets-detection" class="btn btn-secondary  btn-sm model-btn">Mets Detection</button>

                <div class="run-stop-btn">
                    <button id="mets-runStopButton" type="button" class="btn btn-secondary btn-sm model-btn">
                        <i id="mets-runStopIcon" class="fa fa-play"></i>
                    </button>
                </div>   -->
            </div>
            <div class="vertical-line"></div>
            <div class="viewer-btn">
                <button class="btn btn-outline-primary btn-sm d-inline-block mr-2 mb-2" onclick="toggleSingleDual()">
                    <i class="far fa-images"></i> Single/Dual View
                </button>
                <button class="btn btn-outline-primary btn-sm d-inline-block mr-2 toggle-full mb-2"
                        onclick="fullScreen()">
                    <i class="fas fa-expand-arrows-alt"></i> Fullscreen
                </button>
                <button class="btn btn-outline-primary btn-sm d-inline-block mr-2 mb-2" id="showColor" value="off">
                    <i class="fas fa-palette"></i> Palette
                </button>
            </div>
        </div>
         <!-- Legend -->
        <div class="mt-2 h6" id="color-legend"></div>
        <!-- Dual Viewer -->
        <div id="viewer" class="w-100" style="height: 700px">
            <div id="leftViewer" class="border border-secondary d-inline-block float-left m-0 w-100 h-100"></div>
            <div id="rightViewer" class="border border-secondary d-none float-right m-0 w-50 h-100"></div>
        </div>

        {{-- Annotation history --}}
        <div class="card shadow my-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Annotation history</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    Please select at least 1 annotation record.
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    </table>
                </div>
            </div>
        </div>
        @else
            <p>This image does not exist</p>
        @endif
    </div>

    <!-- floating window for Left Layer-->
    <div id="floatingWindow-left" title="Annotators (Left Viewer)" >
        <div class="alert alert-warning zoomin-message-left hidden" role="alert">Zoom in on the image for viewing HDYolo annotations</div>
        <div class="layers" id="layers-left"></div>
    </div>
    <div id="floatingWindow-right" title="Annotator (Right Viewer)" >
        <div class="alert alert-warning zoomin-message-right hidden" role="alert">Zoom in on the image for viewing HDYolo annotations</div>
        <div class="layers" id="layers-right"></div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/openseadragon/openseadragon.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/openseadragon-annotorious2.7.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/openseadragon-scalebar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/axios.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/annotorious-selector-pack.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/annotorious-better-polygon.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/viewer/annotorious-toolbar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/utils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/ARIA-path-utils.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/annotorious-editor.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/jszip.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fileSaver.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/jstree/jstree.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/konva.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/openseadragon-ARIA-path-annotation.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const baseUrl = "{{asset('')}}";
        var data =@json($data);
        var image =@json($image);
        var folders =@json(config('app.iv.folder'));

        var imgid = "{{ $image->uuid }}";
        var imgname = "{{ $image->name }}";
        var width = "{{ $image->width }}";
        var hasmask = "{{$mask}}";
        var userId = "{{ Auth::user()->id }}";
        var userName = "{{ Auth::user()->name }}";
        const modelBtnData = @json($modelBtn);
        // console.log("modelbtndATA", modelBtnData)
        const chatAPI = data['original']['chat'];
        const segmentAPI = data['original']['seg'];

        var originalTileImg = data['original']['tile'], maskTileImg = data['mask']['tile'];
        var height = "{{ $image->height }}";

        /**
         * ----- Fetch mpp value from API ------
         */
        var paramsURL = data['original']['params']
        var mpp;
        var ppm;
        var magnitude;
        $.ajax({
            type: "GET",
            dataType: "json",
            url: paramsURL,
            async: false,
            success: function (data) {
                // if (data['slide_mpp']) {
                    mpp = data['slide_mpp'] ? data['slide_mpp'] : 0.125;
                    ppm = mpp ? (1e6 / mpp) : 0
                    magnitude = Math.round(10 / data['slide_mpp']);
                // }
                // else {
                //     mpp = ppm = magnitude = null;
                // }
            }
        });
        let max_zoomRation = mpp ? mpp / 0.125 * 4 : 4;

        /**
         * Initiate a viewer
         **/
        var leftViewer = OpenSeadragon({
            id: "leftViewer",
            prefixUrl: "{{ asset('img/viewer/') }}/",
            preserveViewport: true,
            tileSources: originalTileImg,
            maxZoomPixelRatio: max_zoomRation,
            preserveImageSizeOnResize: true
        });

        var rightViewer = OpenSeadragon({
            id: "rightViewer",
            prefixUrl: "{{ asset('img/viewer/') }}/",
            preserveViewport: true,
            tileSources: originalTileImg,
            maxZoomPixelRatio: max_zoomRation,
        });
    
        leftViewer.scalebar({
            type: OpenSeadragon.ScalebarType.MAP,
            pixelsPerMeter: ppm,
            minWidth: "75px",
            location: OpenSeadragon.ScalebarLocation.BOTTOM_LEFT,
            xOffset: 5,
            yOffset: 20,
            stayInsideImage: false,
            color: "rgb(150, 150, 150)",
            fontColor: "rgb(100, 100, 100)",
            backgroundColor: "rgba(255, 255, 255, 0.5)",
            fontSize: "small",
            barThickness: 2
        });

        //----------------zoom and pan in both viewers at same time----------------
        var leftViewerLeading = false;
        var rightViewerLeading = false;
        var viewportzoom;
        var imagezoom;
        var mppzoom;
        var mag;

        $('.zoom-value span').html(Math.round(viewportzoom) + 'x');
        var leftViewerHandler = function () {
            if (rightViewerLeading) {
                return;
            }
            viewPortZoom = leftViewer.viewport.getZoom();
            leftViewerLeading = true;
            rightViewer.viewport.zoomTo(viewPortZoom); //listened by handler
            rightViewer.viewport.panTo(leftViewer.viewport.getCenter());
            leftViewerLeading = false;

            imagezoom = leftViewer.viewport.viewportToImageZoom(viewPortZoom);
            mppzoom = (mpp ? mpp : 0.125) / imagezoom;
            mag = 10 / mppzoom;
            if (mag < 1) {
                $('.zoom-value span').html('<1x');
            } else {
                $('.zoom-value span').html(Math.round(mag) + 'x');
            }
            $('#zoom-slider').val(mag);
        };

        var rightViewerHandler = function () {
            if (leftViewerLeading) {
                return;
            }
            viewPortZoom = rightViewer.viewport.getZoom();
            rightViewerLeading = true;
            leftViewer.viewport.zoomTo(viewPortZoom);
            leftViewer.viewport.panTo(rightViewer.viewport.getCenter());
            rightViewerLeading = false;

            imagezoom = leftViewer.viewport.viewportToImageZoom(viewPortZoom);
            mppzoom = (mpp ? mpp : 0.125) / imagezoom;
            mag = 10 / mppzoom;
            if (mag < 1) {
                $('.zoom-value span').html('<1x');
            } else {
                $('.zoom-value span').html(Math.round(mag) + 'x');
            }
            $('#zoom-slider').val(mag);
        };

        leftViewer.addHandler('zoom', leftViewerHandler);
        rightViewer.addHandler('zoom', rightViewerHandler);
        leftViewer.addHandler('pan', leftViewerHandler);
        rightViewer.addHandler('pan', rightViewerHandler);

        // hide and show button
        let annotatorTreeBtn_left= new OpenSeadragon.Button({
            tooltip: 'Hide/Show Annotations',
            id: "left_switch",
            srcRest: "{{ asset('img/viewer/mask_rest.png') }}",
            srcGroup: "{{ asset('img/viewer/mask_grouphover.png') }}",
            srcHover: "{{ asset('img/viewer/mask_hover.png') }}",
            srcDown: "{{ asset('img/viewer/mask_pressed.png') }}",
        });
        let annotatorTreeBtn_right = new OpenSeadragon.Button({
            tooltip: 'Hide/Show Annotations',
            id: "right_switch",
            srcRest: "{{ asset('img/viewer/mask_rest.png') }}",
            srcGroup: "{{ asset('img/viewer/mask_grouphover.png') }}",
            srcHover: "{{ asset('img/viewer/mask_hover.png') }}",
            srcDown: "{{ asset('img/viewer/mask_pressed.png') }}",
        });
        let rightRefreshBtn = new OpenSeadragon.Button({
            tooltip: 'Refresh',
            id: "right_refresh",
            srcRest: "{{ asset('img/viewer/sync_rest.png') }}",
            srcGroup: "{{ asset('img/viewer/sync_grouphover.png') }}",
            srcHover: "{{ asset('img/viewer/sync_hover.png') }}",
            srcDown: "{{ asset('img/viewer/sync_pressed.png') }}",
        });
        var offLeft = true, onRight = true;
        rightRefreshBtn.addHandler('click', function(event) {
            annotationLayer_right.removeAllShapes();
            annotationLayer_right.renderActive();
            annotationLayer_right.draw();
        })
        function toggleSingleDual() {
            $("#leftViewer").toggleClass('w-100 w-50');
            $("#rightViewer").toggleClass('d-none d-inline-block');
            $("#leftmousetrack").toggleClass('w-100 w-50');
            $("#rightmousetrack").toggleClass('d-none d-inline-block');
            if ($("#floatingWindow-right").hasClass("ui-dialog-content")) {
                $("#floatingWindow-right").dialog("close");
            }
        }

        leftViewer.addControl(annotatorTreeBtn_left.element, {anchor: OpenSeadragon.ControlAnchor.TOP_LEFT});
        rightViewer.addControl(annotatorTreeBtn_right.element, {anchor: OpenSeadragon.ControlAnchor.TOP_LEFT});
        rightViewer.addControl(rightRefreshBtn.element, {anchor: OpenSeadragon.ControlAnchor.TOP_LEFT});


        /**
         * ----- Zoom slider panel configuration Start ------
         */
        if (mpp) {
            var zoom_btns = '';
            zoom_levels = [1, 5, 10, 20, 40];
            for (let i of zoom_levels) {
                zoom_btns += '<li value="' + i + '">' + i + 'x</li>';
            }

            $('#leftViewer .openseadragon-container').append('<div class="zoom-control" style="display: none;"><div class="d-flex">' +
                '<div><ul>' + zoom_btns + '</ul></div>' +
                '<div class="range-wrapper"><input orient="vertical" type="range" class="form-range" min="1" max="40" id="zoom-slider"></div></div>' +
                '<div class="zoom-value"><span>1x</span></div></div>');

            $('.form-range').on('input', function () {
                leftViewer.viewport.zoomTo(leftViewer.viewport.imageToViewportZoom(this.value * mpp / 10));
            });

            $('.zoom-control').on('click', 'li', function () {
                $('.form-range').val(this.value);
                leftViewer.viewport.zoomTo(leftViewer.viewport.imageToViewportZoom(this.value * mpp / 10));
            })

            let zoomSliderBtn = new OpenSeadragon.Button({
                tooltip: 'Toggle zoom slider',
                srcRest: "{{ asset('img/viewer/zoomControl_rest.png') }}",
                srcGroup: "{{ asset('img/viewer/zoomControl_grouphover.png') }}",
                srcHover: "{{ asset('img/viewer/zoomControl_hover.png') }}",
                srcDown: "{{ asset('img/viewer/zoomControl_pressed.png') }}",
                onClick: toggleSlider,
                class: "zoomControl-icon"
            });

            leftViewer.addControl(zoomSliderBtn.element, {
                anchor: OpenSeadragon.ControlAnchor.TOP_LEFT
            });

            function toggleSlider() {
                $('.zoom-control').toggle();
            }
        } 
        // else {
            // $('#leftViewer .openseadragon-container').append('<div class="mt-5 ml-2 position-relative" style="z-index: 999">' +
            //     '<i class="fas fa-info-circle mr-2"></i>slide mpp value not found.</div>');
        // }
        /**
         * ----- Zoom slider panel configuration End ------
         */

        //Setup mouse Tracking
        const mousetrack1 = document.createElement('div');
        mousetrack1.className = 'mouse-track';
        leftViewer.container.appendChild(mousetrack1);

        const mousetrack2 = document.createElement('div');
        mousetrack2.className = 'mouse-track';
        rightViewer.container.appendChild(mousetrack2);

        function updateMouseTracking(viewer, mousetrack, position) {
            const { x, y } = viewer.viewport.viewportToImageCoordinates(
                viewer.viewport.pointFromPixel(position)
            );
            mousetrack.textContent = `X: ${x.toFixed(2)}, Y: ${y.toFixed(2)}`;
        }
        let trackingEnabled = true;
        function setupSharedMouseTracker(sourceViewer, targetViewer, sourceTrack, targetTrack) {
            return new OpenSeadragon.MouseTracker({
                element: sourceViewer.container,
                moveHandler: event => {
                    if (trackingEnabled) {
                        const position = event.position;
                        updateMouseTracking(sourceViewer, sourceTrack, position);
                        updateMouseTracking(targetViewer, targetTrack, position);
                    }
                }
            });
        }
        const tracker1 = setupSharedMouseTracker(leftViewer, rightViewer, mousetrack1, mousetrack2);
        const tracker2 = setupSharedMouseTracker(rightViewer, leftViewer, mousetrack2, mousetrack1);
    </script>

<!-- Initiate annotation layer  -->
<script>
    const globalColorCodes = {
            'bg': "#ffffff",
            'tumor_nuclei': "#00ff00",
            'stromal_nuclei': "#ff0000",
            'immune_nuclei': "#0000ff",
            'blood_cell': "#ff00ff",
            'macrophage': "#ffff00",
            'dead_nuclei': "#0094e1",
            'other_nuclei': "#b581fe"         
    };

    // APIs Configuration
    var annoAPI = {
        createDB: "{!! $annoAPI['createDB'] !!}",
        getAnnotator: "{!! $annoAPI['getAnnotator'] !!}",
        getLabels: "{!! $annoAPI['getLabels'] !!}",
        insert: "{!! $annoAPI['insert'] !!}",
        read: "{!! $annoAPI['read'] !!}",
        update: "{!! $annoAPI['update'] !!}",
        delete: "{!! $annoAPI['delete'] !!}",
        search: "{!! $annoAPI['search'] !!}",
        stream: "{!! $annoAPI['stream'] !!}",
        countAnnos: "{!! $annoAPI['countAnnos'] !!}"
    };

    const userPalette = @json($color->color_palette ?? []);
    const mergedGlobalPalette = Object.keys(userPalette).length > 0 ? userPalette : globalColorCodes;


    var colorPalette = new ColorPalette(
            document.getElementById("showColor"),
            mergedGlobalPalette,
            '#E8E613'
        );

    var annotationLayer_left = new ViewerAnnotation(leftViewer, {
        'layers': [{'capacity': 4096}],
        'widgets': [
            'COMMENT',
            {
                widget: 'TAG',
                vocabulary: Object.values(colorPalette),
            },
            aiChatBox,
            AnnotatorWidget,
        ],
        'drawingTools': {
            tools: ['point', 'rect', 'polygon', 'circle', 'ellipse', 'freehand'],
            container: document.getElementById('manual-toolbar'),
        },
        colorPalette: colorPalette,
    });

    var annotationLayer_right = new ViewerAnnotation(rightViewer, {
        'layers': [{'capacity': 4096}],
        colorPalette: colorPalette,
    });

    buildConnections(annotationLayer_left, annoAPI);
    buildConnections(annotationLayer_right, annoAPI);

    annotationLayer_left.enableEditing(userId);
    annotationLayer_left.draw();
    annotationLayer_right.draw();


    //--- HDYolo models selecting ---
    var helpMessage = createOverlayElement(leftViewer);
    var layerQueuesCheckInterval;
    let modelIsRunning = false;
    let currentModel = @json($defaultModel ?? []); 

    function toggleRunning(start = true, toggleModel) {
        const controlBtn = document.getElementById('runStopButton');
        const runStopIcon = document.getElementById("runStopIcon");
        const modelDisplay = document.getElementById('modelDisplay');
        const splitButton = document.querySelector('.dropdown-toggle-split');

        if (start) {
            if (toggleModel) {
                annotationLayer_left.addAnnotator(toggleModel.id);
                layerQueuesCheckInterval = setInterval(checkViewportLevelAndDisplayHelpMessage, 1000);
                addTile(leftViewer, toggleModel.path);
                if ($("#floatingWindow-left").hasClass("ui-dialog-content")) {
                    // The dialog is open, so close it
                    $("#floatingWindow-left").dialog("close");
                }
                if ($("#floatingWindow-right").hasClass("ui-dialog-content")) {
                    // The dialog is open, so close it
                    $("#floatingWindow-right").dialog("close");
                }
            }
            controlBtn.classList.remove('btn-secondary');
            controlBtn.classList.add('btn-primary');
            splitButton.classList.remove('btn-secondary');
            splitButton.classList.add('btn-primary');
            runStopIcon.classList.add('fa-stop')
            runStopIcon.classList.remove('fa-play')
            modelDisplay.classList.remove('model-display-secondary');
            modelDisplay.classList.add('model-display-primary');
            modelIsRunning = true;
        } else {
            // Stop the model
            if(toggleModel){
                clearInterval(layerQueuesCheckInterval); 
                hideInstructions(helpMessage);
                removeTile(leftViewer, toggleModel.path);
            }
            controlBtn.classList.remove('btn-primary');
            controlBtn.classList.add('btn-secondary');
            splitButton.classList.remove('btn-primary');
            splitButton.classList.add('btn-secondary');
            runStopIcon.classList.add('fa-play')
            runStopIcon.classList.remove('fa-stop')
            modelDisplay.classList.remove('model-display-primary');
            modelDisplay.classList.add('model-display-secondary');
            modelIsRunning = false;
        }      
    }   

    function selectModel(model) {
        if (modelIsRunning) {
            const previousModel = currentModel;
            toggleRunning(false, previousModel); 
        }
        // Update button text with gear icon
        const div = document.getElementById('modelDisplay');
        div.innerHTML = model.text;
        currentModel = model;
        console.log("curren model", currentModel.text)
    }

    document.getElementById('runStopButton').addEventListener('click', function() {
        toggleRunning(!modelIsRunning, currentModel);
    });
    //--- End of HDYolo models selecting ---

    //--- Toggle Sam2 segment models  ---
    const smartPolyButton = document.getElementById('smartpoly');
    const toggleInput = document.getElementById('toggleTextFieldButton')
    $('#toggleTextFieldButton').click(function() {
      $('#tag-input').toggle(); 
    });
    let aiseg_isRunning = false;
    $('#ai-runStopButton').click(function() {
        aiseg_isRunning = !aiseg_isRunning;
      $('#ai-runStopIcon').toggleClass('fa-play fa-stop');
      if (aiseg_isRunning) {
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        toggleInput.classList.remove('btn-secondary');
        toggleInput.classList.add('btn-primary');
        smartPolyButton.classList.add('btn-primary');
        smartPolyButton.classList.remove('btn-secondary');
        smartPolyButton.value = 'on';
      } else {
        $(this).removeClass('btn-primary').addClass('btn-secondary');
        toggleInput.classList.remove('btn-primary');
        toggleInput.classList.add('btn-secondary');
        smartPolyButton.classList.add('btn-secondary');
        smartPolyButton.classList.remove('btn-primary');
        smartPolyButton.value = 'off';
        if ($('#tag-input').is(":visible")) {
            $('#tag-input').hide(); // Hide if it's visible
        }
      }
    });
    //---End of Sam2 segment model ---

    //-- Toggle Mets Detection Model --
    const metsDetButton = document.getElementById('mets-detection');
    const metsRunStopIcon = document.getElementById("mets-runStopIcon");
    let mets_isRunning = false;
    $('#mets-runStopButton').click(function() {
        mets_isRunning = !mets_isRunning;
        $('#mets-runStopIcon').toggleClass('fa-play fa-stop');
        if (mets_isRunning) {
            $(this).removeClass('btn-secondary').addClass('btn-primary');
            metsDetButton.classList.add('btn-primary');
            metsDetButton.classList.remove('btn-secondary');
        }else{
            $(this).removeClass('btn-primary').addClass('btn-secondary');
            metsDetButton.classList.add('btn-secondary');
            metsDetButton.classList.remove('btn-primary');
        }

    });
    //-- End of Mets Detection Model --

    //set default selected annotators and draw the annotator datatable
    let userIdNameMap=@json($userIdMap);
    let api = annoAPI.getAnnotator;
    let tablequery={"annotator":[]};   
    var initialannotatorids = new Set();

    getAnnotators(api).then(annotatorIds => {
        let annotatorsMap = {};
        annotatorIds.forEach(key => {
            annotatorsMap[key] = userIdNameMap[key];
        });
        for (const key in annotatorsMap) {
            if (annotatorsMap[key] === null || typeof annotatorsMap[key] === 'undefined') {
                continue;
            }
            if (!isNaN(key)) { // Check if key is a number
                initialannotatorids.add(key)
                tablequery.annotator.push(key);
                // annotationLayer_left.addAnnotator(key); 
            } 
        }
        annotationLayer_left.updateAnnotators(initialannotatorids);
        drawNUpdateDatatable(annotationLayer_left.APIs.annoSearchAPI, tablequery);   
            
    })

    annotatorTreeBtn_left.addHandler('click', function(event) {
        $("#floatingWindow-left").dialog({
            // autoOpen: false,
            open: function(event, ui) {
                // Remove the hidden class when the dialog is opened
                $(".zoomin-message-left").removeClass("hidden");
            }
        });
        annotatorTreeBtnClick(annotationLayer_left, userIdNameMap, "#layers-left");
    });
    annotatorTreeBtn_right.addHandler('click', function(event){
        $("#floatingWindow-right").dialog({
            // autoOpen: false,
            open: function(event, ui) {
                // Remove the hidden class when the dialog is opened
                $(".zoomin-message-right").removeClass("hidden");
            }
        });
        annotatorTreeBtnClick(annotationLayer_right, userIdNameMap, "#layers-right");
    });
    

    function checkViewportLevelAndDisplayHelpMessage() {
        var currentLevel = leftViewer.viewport.getZoom(true); 
        var desiredLevel=5;
        showInstructions(helpMessage, "Zoom in to see HDYolo result");
    }

    // select manual annotation tools event (cancel selected annotorious)
    document.querySelectorAll(".a9s-toolbar-btn").forEach((btnTip) => {
        btnTip.addEventListener("click", (e) => {
            if ($("#floatingWindow-left").hasClass("ui-dialog-content")) {
                // The dialog is open, so close it
                $("#floatingWindow-left").dialog("close");
            }
            if ($("#floatingWindow-right").hasClass("ui-dialog-content")) {
                // The dialog is open, so close it
                $("#floatingWindow-right").dialog("close");
            }
        });
    });

</script>
<script>
        /**
         *
         * ----- Annotation Section Start ------
         *
         */
        var loadanno, annofile, annoVisible;
        var leftAnnoNewList = [], rightAnnoNewList = [], annoNewResult = {};
        var annos;

        var fullScreenFlag = false,
            imageDesc = document.getElementById("image_desc"),
            sideBar = document.getElementById("accordionSidebar"),
            pageTitle = document.getElementById("page_title"),
            pageTop = document.getElementById("show_top");

        function fullScreen() {
            if (fullScreenFlag) {
                imageDesc.style.display = "block";
                // lastAnnoShown.style.display = "block";
                sideBar.style.display = "block";
                pageTitle.style.display = "block";
                pageTop.style.display = "";
                // buttonWarning.style.display = "block";
                $("#viewer").css("height", "600px");
                fullScreenFlag = false;
                $('.toggle-full').find('i').removeClass('fa-compress-arrows-alt').addClass('fa-expand-arrows-alt');
                $('.toggle-full').contents().last().replaceWith(' Fullscreen');
            } else {
                imageDesc.style.display = "none";
                // lastAnnoShown.style.display = "none";
                sideBar.style.display = "none";
                pageTitle.style.display = "none";
                pageTop.style.display = "none";
                // buttonWarning.style.display = "none";
                var setHeight = $(window).height() * 0.75;
                $("#viewer").css("height", setHeight);
                fullScreenFlag = true;
                $('.toggle-full').find('i').removeClass('fa-expand-arrows-alt').addClass('fa-compress-arrows-alt');
                $('.toggle-full').contents().last().replaceWith(' Exit Fullscreen');
            }
        }

        function roundFloatsInSVG(svgString) {
            // Regular expression to match floating-point numbers in the points attribute
            var floatRegex = /(\d+\.\d+)/g;

            // Replace each match with its rounded integer value
            var roundedSVG = svgString.replace(floatRegex, function(match) {
                return parseFloat(match).toFixed(3);
            });

            return roundedSVG;
        }

</script>

@endsection
