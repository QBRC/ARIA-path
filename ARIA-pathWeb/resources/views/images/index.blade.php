@extends('layouts.app')

@section('page-title')
    Images
    {!! isset($subtitle)?'<span class="h6 ml-3 text-gray-600">'.$subtitle.'</span>':"" !!}
@endsection

@section('style')
    <link rel="stylesheet" type="text/css"
          href="{{ asset('vendor/fancytree-master/dist/skin-lion/ui.fancytree.css') }}"/>
@endsection

@section('content')

    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-2">Current image table:</h5>
                    <div class="d-flex align-items-center row">
                        <div class="col-md-5 text-right">
{{--                            <img src="{{ asset('img/viewer/1455554839_line-60_icon-icons.com_53339.png') }}" width="17px" alt="Project folder"--}}
                            <img src="{{ asset('img/viewer/folderblue.png') }}" width="17px" alt="Project folder"
                                 class="img-fluid mr-2"> Project :
                        </div>
                        <div class="col-md-7">
                            <b id="project_name"></b>
                        </div>
                    </div>
                    <div class="d-flex align-items-center row">
                        <div class="col-md-5 text-right">
{{--                            <img src="{{ asset('img/viewer/1455554738_line-65_icon-icons.com_53379.png') }}" width="17px" alt="Dataset folder"--}}
                            <img src="{{ asset('img/viewer/folderyellow.png') }}" width="17px" alt="Dataset folder"
                                 class="img-fluid mr-2"> Dataset :
                        </div>
                        <div class="col-md-7">
                            <b id="batch_name"></b>
                        </div>
                    </div>
                    <div class="d-flex align-items-center row">
                        <div class="col-md-5 text-right">
                            Created at :
                        </div>
                        <div class="col-md-7">
                            <b id="created_at"></b>
                        </div>
                    </div>
                    <div class="d-flex align-items-center row">
                        <div class="col-md-5 text-right">
                            # of Images :
                        </div>
                        <div class="col-md-7">
                            <b id="sum_img"></b>
                        </div>
                    </div>
                    <!-- <div class="d-flex align-items-center row">
                        <div class="col-md-5 text-right">
                            # of Annotations :
                        </div>
                        <div class="col-md-7">
                            <b id="sum_anno"></b>
                        </div>
                    </div> -->

                </div>
                <div class="col-md-8">
                    <h5>All available datasets:</h5>
                    <div id="tree">
                        <ul id="treeData" style="display: none;">
                            @foreach($data as $k=>$v)
                                <li id="id{{ $k }}" class="folder">{{ e($v['project']) }}
                                    <ul>
                                        @foreach($v['batch'] as $kk=>$vv)
                                            <li id="{{ $vv['id'] }}"
                                                data-batch-id="{{ $vv['id'] }}">
                                                {{ $vv['name'] }} [{{ $vv['nimg'] }} Images, {{ $vv['nanno'] }} Annotations]
                                        @endforeach
                                    </ul>
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>

        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        @foreach($columns as $v)
                            <th>{{ $v['title'] }}</th>
                        @endforeach
                    </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>

@endsection


@section('scripts')

    <script src="{{ asset('vendor/fancytree-master/dist/jquery.fancytree-all-deps.min.js') }}"></script>
    <script src="{{ asset('vendor/fancytree-master/src/jquery.fancytree.js') }}"></script>

    <script>
        var urlParams = new URLSearchParams(window.location.search);
        $(document).ready(function () {
            const baseUrl = "{{asset('')}}";
            var batch_id = parseInt(urlParams.get('batch_id'), 10);// If batch_id is not in the URL, use 1 as the default

            if (isNaN(batch_id)|| batch_id <= 0) {
                batch_id = 1; // default value if batch_id is not a valid number
            }

            var batchElement = $("li[data-batch-id='" + batch_id + "']");
            if (batchElement.length > 0) {
                batchElement.addClass("active focused"); // Apply the classes only if the element exists
                batchElement.get(0).scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            } else {
                console.warn("Element with batch_id " + batch_id + " not found.");
            }

            var searchCol = [1, 2];
            var dTable = $('#dataTable').DataTable({
                {{--processing: true,--}}
                    {{--serverSide: true,--}}
                    {{--ajax: "{{ url('/table') }}",--}}
                order:[],
                columns: @json($columns),
                columnDefs: [
                    {bSortable: false, targets: [0, 4]}, // Disable sorting on columns
                    {className: "align-middle text-center", "targets": [0]},
                    {className: "align-middle ", "targets": [1, 2, 3, 4]},
                ],
                ajax: {
                    'url': "{{ route("fetchBatch") }}",
                    'type': 'POST',
                    'headers': {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    'data': function (d) {
                        d.id = batch_id;
                    },
                    "dataSrc": function (json) {
                        $('#project_name').text(json.project_name);
                        $('#batch_name').text(json.batch_name);
                        $('#created_at').text(json.created_at);
                        $('#sum_img').text(json.n_img);
                        // $('#sum_anno').text(json.n_anno);

                        for (var i = 0, ien = json.data.length; i < ien; i++) {
                            json.data[i].Image = '<a href="image/' + json.data[i].Image + '"><i class="fas fa-eye"></i></a>';
                        }

                        return json.data;
                    },
                    statusCode: {
                        419: function () { // Page expired and csrf mismatch
                            window.location.href = '/';
                        }
                    }
                },
                fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    // var imgLink = aData['imageLink']; // if your JSON is 3D
                    var imgLink = aData.thumbnail; // where 4 is the zero-origin column for 2D
                    // console.log("----------------");
                    // console.log(aData);

                    var imgTag = imgLink == '' ? '<i class="far fa-image fa-3x" style="color: #CCCCCC;"></i>' : '<img src="' + imgLink + '" class="thumbnail" alt="thumbnail">';

                    var imgBtn = '<a href="slides/'+aData.image_id+'/0" class="text-primary"><i class="fas fa-eye"></i></a>'

                    $('td:eq(0)', nRow).html(imgTag); // First td
                    $('td:eq(4)', nRow).html(imgBtn); // Fifth td


                    return nRow;
                },
                // pageLength : 3,

                autoWidth: false,
                scrollX: true,
                initComplete: function () {
                    // this.api().columns().every(function (k) {
                    //     var title = this.header();
                    //     //replace spaces with dashes
                    //     title = $(title).html().replace(/[\W]/g, '-');
                    //
                    //     var column = this;
                    //
                    //     if (searchCol.includes(k)) {
                    //         $(column.footer()).html('<input type="text" placeholder="Search ' + title + '" class="col-search"/>');
                    //
                    //         $('input', this.footer()).on('keyup change clear', function () {
                    //             if (column.search() !== this.value) {
                    //                 column
                    //                     .search(this.value)
                    //                     .draw();
                    //             }
                    //         });
                    //     } else {
                    //         $(column.footer()).empty();
                    //     }
                    //
                    // });
                },
            });


            // var fTree=$("#tree").fancytree({
            //     selectMode: 1,
            //     {{--                source:@json($tree),--}}
            //     icon: function(event, data) {
            //         if( data.node.isFolder() ) {
            //             return "fancytree-project-icon";
            //         }else{
            //             return "fancytree-dataset-icon";
            //         }
            //         // Otherwise no value is returned, so continue with default processing
            //     },
            //     activate: function (event, data) {
            //         // console.log(typeof data.node.folder === 'undefined');
            //         // console.log(data.node.key);

            //         if (typeof data.node.folder === 'undefined') {
            //             batch_id = data.node.key;
            //             // sessionStorage.setItem('batchid', batch_id);
                        
            //             dTable.ajax.reload();
            //         }
            //     },
            // });

            var fTree = $("#tree").fancytree({
                selectMode: 1,
                icon: function(event, data) {
                    if (data.node.isFolder()) {
                        return "fancytree-project-icon";
                    } else {
                        return "fancytree-dataset-icon";
                    }
                },
                activate: function(event, data) {
                    if (typeof data.node.folder === 'undefined') {
                        batch_id = data.node.key;
                        // sessionStorage.setItem('batchid', batch_id); // Optional: store in session
                        dTable.ajax.reload(); // Reload data table with the selected batch
                    }
                },
                renderNode: function(event, data) {
                    if (data.node.isFolder()) {
                        if (data.node.folder && !data.node.isRendered) {
                        // Construct the download link URL using the project title
                        var downloadLinkUrl = baseUrl + 'images/csv/' + encodeURIComponent(data.node.title) + '-uuid.csv';

                        // Create the download link
                        var downloadLink = $('<a>', {
                            href: downloadLinkUrl, 
                            class: 'download-link',
                            style: 'margin-left: 8px; display: inline-flex; align-items: center;',
                            download: ''
                        });

                        var text = $('<span>').text('CSV');  // Text "uuid"

                        // Create the text and icon inside the link
                        var icon = $('<i>', {
                            class: 'fas fa-download',  // Font Awesome download icon
                            style: 'margin-right: 5px;'  // Space between icon and text
                        });
                        // Append the icon and text to the link
                        downloadLink.append(icon).append(text);
                        data.node.span.appendChild(downloadLink[0]);
                        data.node.isRendered = true;
                        
                    }
                }
                },
                create: function(event, data) {
                    // Show the tree after FancyTree initialization
                    $('#treeData').show();
                  
                }
            });

            @if(isset($batch_id))
            const node = $.ui.fancytree.getTree($("#tree")).getNodeByKey({{$batch_id}});
            if (node) {
                node.setActive();
                node.makeVisible();
            } else {
                console.error('Node not found with the provided key:', '{{$batch_id}}');
            }
            @endif


            // Use rowCallback to update annotation number for each row
            dTable.on('draw.dt', function () {
                //update # of manual annotations
                // const database_url = "{{ env('AnnotationDBURL') }}";
                var countAnnos =@json($annoCountApi);
                //Fetch annotators
                var annotatorPromise = fetchAnnotatorsBasedonBatchid(batch_id).then(function(users) {
                    const anntators = Object.keys(users).map(String);
                    return anntators;
                }).catch(function(error) {
                    console.error("Error fetching annotators:", error);
                    throw error;
                });
                annotatorPromise.then(annotators => {
                    dTable.rows().every(function () {
                        var rowData = this.data();
                        var annotationNumber = rowData.annotations;
                        var image_uuid=rowData.image_uuid;
                        var rowNode = this.node();
                        // var countapi = `${database_url}/count?image_id=${image_uuid}`;
                        var countapi = countAnnos+image_uuid
                        // console.log(countap2i)
                        fetchNewAnnotationNumber(countapi, annotators).then(function(newAnnotationNumber) {
                            var annotationCell = rowNode.getElementsByTagName('td')[3]; // Adjust this selector based on your HTML structure
                            annotationCell.innerHTML = newAnnotationNumber; // Update the cell content
                        }).catch(function(error) {
                            console.error("Error fetching new annotation number:", error);
                        });
                    
                    });
                });
            });

            function fetchAnnotatorsBasedonBatchid(batch_id){
                  //get annotators who can annotate
                  return fetch('{{ route('who-can-annotate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ batch_id: batch_id })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            
                            return response.json();
                            })
                        .catch(error => {
                            console.error('Error:', error);
                        });
            }

            function fetchNewAnnotationNumber(api, annotators) {
                return fetch(api, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({"annotator":annotators})
                }).then(response => {
                    if(response.statusText === "OK"){
                        return response.json().then(data => {
                            // return data.length;
                            return data;
                        });
                    }else if(response.statusText === "Not Found"){
                        return 0;
                    }else{
                        return null
                    }
                }).catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                    throw error;
                });
            }
            dTable.draw();
        });
    </script>

@stop
