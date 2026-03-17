@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Teams</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['groups'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Images</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($count['images']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-images fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Annotations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($count['notes']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-copy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Manual</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body user-manual">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ asset('img/image-list.png') }}" class="shadow p-3 mb-2 bg-white rounded img-fluid" alt="Image list">
                            <p class="text-center font-weight-bold">Figure 1: Image list</p>
                        </div>
                        <div class="col-md-6">
                            <h4 class="font-weight-bold mb-3"><i class="fas fa-bookmark fa-sm"></i> Image list</h4>

                            <p>After successful login, click <span class="text-white bg-primary rounded px-1">Images</span> on the sidebar (see <span class="text-danger font">[1]</span> in Figure 1) to see all the images you can access. If you cannot see any images in the list table, please contact <a href="mailto:{{ env('ADMIN_EMAIL') }}">{{ env('ADMIN_EMAIL') }}</a> to grant you permissions.</p>
                            <p>All the columns are sortable by clicking the headers. Moreover, users can search by keywords globally (search box at the top right of the image list table) or in each column (search boxes at the foot of each column).</p>
                            <p>In the image list table, all images are sorted by update time initially and any added annotations would be counted as updates on the target image (see <span class="text-danger font">[2]</span> in Figure 1).</p>
                            <p>To access the images, click the 'eye' icon in the last column (see <span class="text-danger font">[3]</span> in Figure 1).</p>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h4 class="font-weight-bold mb-3"><i class="fas fa-bookmark fa-sm"></i> Image viewer</h4>

                            <!-- <p>On the image viewer page (see Figure 2), there are two main sections. One is the viewer area (see <span class="text-danger font">[1-6]</span> in Figure 2) and another one is the annotation history table (see <span class="text-danger font">[7]</span> in Figure 2).</p>

                            <p>Initially, the viewer area would load the latest annotation when users access an image. At the upper area of viewer, it shows some information such as, <span class="text-danger font">[1]</span>: Belonged batch name / Image name. <span class="text-danger font">[2]</span>: The current annotation information contains annotation name, editor name, created date and time. If the image has not been annotated ever, it shows 'No annotation'.</p>
                            <p>It provides a dual-view mode for the target image (see <span class="text-danger font">[6]</span> in Figure 2). In the dual-view mode, the left and right viewers would zoom in/out or move synchronously for cross-reference. Moreover, there are several functional buttons floating at left top corner of each of the viewers which includes
                                <img src="{{ asset('img/viewer/zoomin_rest.png') }}" alt="zoomin_btn"> zoom in,
                                <img src="{{ asset('img/viewer/zoomout_rest.png') }}" alt="zoomin_btn"> zoom out,
                                <img src="{{ asset('img/viewer/home_rest.png') }}" alt="zoomin_btn"> reset,
                                <img src="{{ asset('img/viewer/fullpage_rest.png') }}" alt="zoomin_btn"> full screen, 
                                <img src="{{ asset('img/viewer/mask_rest.png') }}" alt="zoomin_btn"> hide/show nuclei mask, and
                                <img src="{{ asset('img/viewer/zoomControl_rest.png') }}" alt="zoomin_btn"> hide/show scale slider (see Figure 3). Please notice that the switch hide/show nuclei mask button would disappear if the target image only has an original layer. </p>
                            <p>To annotate the image, there is an annotation tool kit (see <span class="text-danger font">[3]</span> in Figure 2) which allows users to draw multiple markers with different types of shapes (circle, rectangle, ellipse, or polygon, etc.) on the image. For each marker being drawn on the image, it will pop up a comment box by which users can write some notes for the marker or delete the marker. Also, users can click the existing markers to activate the comment box for further editing. After users select one of markers which was drawn on the viewer, the viewer automatically switches back to normal zoom &amp; pan behavior. But if users still need to create more current shape marker, please hold "SHIFT" key on the keyboard and draw more markers. To remove all the existing markers, just click <span class="border border-primary rounded text-primary px-1 text-nowrap"><i class="far fa-eye"></i> Hide/Show annotation</span> (see <span class="text-danger font">[4]</span> in Figure 2) and users could annotate from a fresh start. Click this button again, the previous saved annotation would be loaded back onto the image. But please notice that clicking this button would discard all your unsaved annotation.</p>
                            <p>When users finish annotating, click <span class="border border-primary rounded text-primary px-1 text-nowrap"> <i class="fas fa-save"></i> Save annotation</span> (see <span class="text-danger font">[4]</span> in Figure 2) and it then pop up a box for naming this annotation. We recommend users use a brief name to simply describe the annotation for future review. After that, the annotation would be saved in the database and the web page would reload the latest annotation when it was saved successfully. If there is no annotation marked on the image, it would not allow saving.</p>
                            <p>To extend the viewer section, click <span class="border border-primary rounded text-primary px-1 text-nowrap"> <i class="fas fa-expand-arrows-alt"></i> Toggle fullscreen</span> (see <span class="text-danger font">[4]</span> in Figure 2) to hide the non-viewer section and get users a better view for focusing on the image area. Click this button again to exit the fullscreen mode.</p>
                            <p>The mouse location tracking switch is used to show/hide the current mouse location on the slides. When it is turned on, it will display the x and y coordinates above the image viewer. (see <span class="text-danger font">[5] </span>in Figure 2).</p>
                            <p>In the annotation history table (see <span class="text-danger font">[7]</span> in Figure 2), users can see all the annotations on the target image. Click the 'eye' icon in the last column to load the desired annotation on the image. Users can also select specific annotation records from the table and export them by clicking on the "Download as JSON" or "Download as XML" buttons. The XML format can be directly displayed in Aperio ImageScope.</p> -->

                            <h4>Key Features</h4>
                                <ul class="feature-list">
                                    <li class="feature-item">
                                        <h5><strong>Annotation Toolbar <span class="text-danger font">[1]</span></strong></h5>
                                        <div class="feature-details">
                                            <ul>
                                                <li><p><strong>Manual Annotation Tools: </strong> <img src="{{ asset('img/manualtool.png') }}" alt="zoomin_btn" style="width: 180px; height: auto; max-width: 100%;"> Users can choose from five different shapes (freehand, circle, rectangle, ellipse, polygon) for annotations. Upon drawing a shape on the image, a comment box appears, allowing users to add descriptions/tags or delete the annotation. After completion, clicking the "OK" button restores normal zoom and pan behavior. Holding the "SHIFT" key enables drawing multiple annotations with the current shape.</p></li>
                                                <li><p><strong>Model Annotation Tools: </strong> The <span class="border border-primary rounded text-primary px-1 text-nowrap"><i class="fa fa-cog model-gear"></i>HDYolo</span> annotation provides real-time prediction. Annotations can be viewed by deeply zooming in and dragging the slide. Clicking the button again disables real-time annotation.</p></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="feature-item">
                                        <h5><strong>Annotator Tree <span class="text-danger font">[2]</span></strong></h5>
                                        <div class="feature-details">
                                            <p>Clicking this floating button <img src="{{ asset('img/viewer/mask_rest.png') }}" alt="zoomin_btn"> reveals the annotator tree, allowing users to control the display of corresponding annotations. Annotations created/updated using manual or model annotation tools automatically check the annotator's checkbox. Users can hide annotations by unchecking the checkbox.</p>
                                        </div>
                                    </li>
                                    <li class="feature-item">
                                        <h5><strong>Annotation Table <span class="text-danger font">[3]</span></strong></h5>
                                        <div class="feature-details">
                                            <p>Upon initial load, all manual annotations are displayed along with their information in a table. Users can easily locate specific annotations by clicking on the magnification icon to zoom to them. The table is updated in real-time when users make changes.</p>
                                        </div>
                                    </li>
                                </ul>

                                <h4>Additional Features</h4>
                                <ul class="feature-list">
                                    <li class="feature-item">
                                        <h5>Single/Dual-Viewer Mode</h5>
                                        <div class="feature-details">
                                            <p>Left and right viewers synchronize zooming and movement for cross-referencing. Users can select different annotations for each viewer to compare results from different models.</p>
                                        </div>
                                    </li>
                                    <li class="feature-item">
                                        <h5>Fullscreen Mode</h5>
                                        <div class="feature-details">
                                            <p>Toggle fullscreen to hide non-viewer sections and provide users with a better view of the image area. Click again to exit fullscreen mode.</p>
                                        </div>
                                    </li>
                                    <li class="feature-item">
                                        <h5>Mouse Location Tracking</h5>
                                        <div class="feature-details">
                                            <p>The mouse location tracking switch displays/hides the current mouse location on the slides. When enabled, it shows the x and y coordinates above the image viewer.</p>
                                        </div>
                                    </li>
                                </ul>


                        </div>
                        <div class="col-md-6">
                            <img src="{{ asset('img/usermanualfi1.png') }}" class="shadow p-3 mb-2 bg-white rounded img-fluid" alt="Image list">
                            <p class="text-center font-weight-bold">Figure 2: Image viewer</p>
                            <img src="{{ asset('img/floatingbtn.png') }}" class="shadow p-3 mb-2 bg-white rounded img-fluid" alt="Image list">
                            <p class="text-center font-weight-bold">Figure 3: Floating buttons</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>


{{--    <div class="container">--}}

{{--        <div class="text-center">--}}
{{--            <h1 class="my-4  text-primary">Spatial Molecular Profiling Gym</h1>--}}
{{--            <p class="h5">SMP Gym simulated datasets with documented assumptions, codes and real datasets with references. It provides a wide range of statistical tests and their benchmarking.</p>--}}

{{--            --}}{{--            <div class="row">--}}
{{--                <div class="col-lg-8">--}}
{{--                </div>--}}
{{--                <div class="col-lg-4">--}}
{{--                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="{{ asset('img/undraw_medical_research_qg4d.svg') }}" alt="...">--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </div>--}}
{{--        <!--Section: Main info-->--}}
{{--        <section class="mt-5 wow fadeIn" style="visibility: visible; animation-name: fadeIn;">--}}

{{--            <h3 class="h3 text-center mb-5">About</h3>--}}

{{--            <!--Grid row-->--}}
{{--            <div class="row">--}}

{{--                <!--Grid column-->--}}
{{--                <div class="col-md-6 mb-4">--}}

{{--                    <img src="{{ asset('img/figure1.gif') }}" class="img-fluid z-depth-1-half" alt="">--}}

{{--                </div>--}}
{{--                <!--Grid column-->--}}

{{--                <!--Grid column-->--}}
{{--                <div class="col-md-6 mb-4">--}}
{{--                    <!-- Main heading -->--}}
{{--                    <h3 class="h3 mb-3">Spatial Molecular Profiling Gym</h3>--}}
{{--                    <p class="text-justify">The Spatial Molecular Profiling Gym (SMP GYM) is an open platform for methodology research. It--}}
{{--                        provides resources including curated datasets from simulation and real studies (Dataset tab),--}}
{{--                        accessible analysis methods (Method tab), and reproducible analysis results (Analysis tab). It--}}
{{--                        also welcomes new datasets, methods, or analysis results contributed by the broad research--}}
{{--                        community <a href="contribution.php" target="_blank"><span class="fas fa-link text-primary"></span></a>.--}}
{{--                    </p>--}}
{{--                    <!-- Main heading -->--}}

{{--                    <hr>--}}

{{--                    <p>--}}
{{--                        <strong>400+</strong> Simulation Datasets,--}}
{{--                        <strong>600+</strong> Real Datasets,--}}
{{--                        <strong>5</strong> Spatial Patterns,--}}
{{--                        <strong>4</strong> Sparsity Settings.--}}
{{--                    </p>--}}

{{--                    <!-- CTA -->--}}
{{--                    <a target="_blank" href="contribution.php" class="btn btn-primary">Contribute--}}
{{--                        <i class="fas fa-upload ml-1"></i>--}}
{{--                    </a>--}}
{{--                    <a target="_blank" href="explorer.php" class="btn btn-primary ml-3">Explorer Now--}}
{{--                        <i class="far fa-image ml-1"></i>--}}
{{--                    </a>--}}

{{--                </div>--}}
{{--                <!--Grid column-->--}}

{{--            </div>--}}
{{--            <!--Grid row-->--}}

{{--        </section>--}}
{{--        <!--Section: Main info-->--}}

{{--        <hr class="my-5">--}}

{{--        <!--Section: Main features & Quick Start-->--}}
{{--        <section>--}}

{{--            <h3 class="h3 text-center mb-5">Features</h3>--}}

{{--            <!--Grid row-->--}}
{{--            <div class="row wow fadeIn" style="visibility: visible; animation-name: fadeIn;">--}}

{{--                <!--Grid column-->--}}
{{--                <div class="col-lg-6 col-md-12 px-4">--}}

{{--                    <!--First row-->--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-1 mr-3">--}}
{{--                            <i class="fas fa-code fa-2x indigo-text"></i>--}}
{{--                        </div>--}}
{{--                        <div class="col-10">--}}
{{--                            <h4 class="font-weight-bolder">10 MWAS Method</h4>--}}
{{--                            <p  class="text-justify">Metagenome-wide association studies (MWAS) can interrogate the--}}
{{--                                association between microbiota and diseases. With the increasing number of the available--}}
{{--                                MWAS methods, analysts may have the burden to learn, use or implement these in their--}}
{{--                                scientific studies, which can be repetitive for the whole science community.</p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!--/First row-->--}}

{{--                    <div style="height:30px"></div>--}}

{{--                    <!--Second row-->--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-1 mr-3">--}}
{{--                            <i class="fas fa-book fa-2x blue-text"></i>--}}
{{--                        </div>--}}
{{--                        <div class="col-10">--}}
{{--                            <h4 class="font-weight-bolder">Datasets</h4>--}}
{{--                            <p class="text-justify">Spatial Molecular Profiling Gym offers simulated datasets, synthetic--}}
{{--                                datasets and datasets from real studies. A wide-range of datasets provides the--}}
{{--                                opportunities to evaluate the existing and novel association methods from multiple--}}
{{--                                perspectives - from different human diseases to different ecological conditions.--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <!--/Second row-->--}}

{{--                    <div style="height:30px"></div>--}}

{{--                    <!--Third row-->--}}

{{--                    <!--/Third row-->--}}

{{--                </div>--}}
{{--                <!--/Grid column-->--}}

{{--                <!--Grid column-->--}}
{{--                <div class="col-lg-6 col-md-12">--}}

{{--                    <div class="row">--}}
{{--                        <div class="col-1 mr-3">--}}
{{--                            <i class="fas fa-graduation-cap fa-2x cyan-text"></i>--}}
{{--                        </div>--}}
{{--                        <div class="col-10">--}}
{{--                            <h4 class="font-weight-bolder">Reproducibility</h4>--}}
{{--                            <p class="text-justify">Documented data resources and analysis routines enables reproducible--}}
{{--                                results – a critical type of research for modern medical and statistical research.--}}
{{--                                MicrobiomeGym provide R codes with datasets, methods and analysis results. Users can--}}
{{--                                freely download the data, code and reproduce analysis results in their own computation--}}
{{--                                environments. </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--                <!--/Grid column-->--}}

{{--            </div>--}}
{{--            <!--/Grid row-->--}}

{{--        </section>--}}
{{--        <!--Section: Main features & Quick Start-->--}}

{{--        <hr class="my-5">--}}

{{--        <section class="mt-5 wow fadeIn animated" style="visibility: visible; animation-name: fadeIn;">--}}
{{--            <h3 class="h3 text-center mb-5">Contribution</h3>--}}
{{--            <div class="row">--}}
{{--                <section class="mx-2 pb-3">--}}
{{--                    <div class="row" style="padding-bottom:3rem">--}}
{{--                        <p>We design Spatial Transcriptomics Arena (STAR) as an open platform for the research--}}
{{--                            community. We--}}
{{--                            welcome contributed datasets, methods, or analysis results provided by external users. To--}}
{{--                            enable--}}
{{--                            the unbiasedness of this process, we provide the contribution guidelines as follows.</p>--}}
{{--                    </div>--}}
{{--                    <div class="row text-center">--}}
{{--                        <div class="col-md-4">--}}
{{--                            <span class="fas fa-database fa-3x text-primary"></span>--}}
{{--                            <h4 class="my-3">To contribute datasets</h4>--}}
{{--                            <p class="text-muted"><span class="teaser">Option one, you can provide the GEO link. For example, the mouse olfactory--}}
{{--                                experiment XXX. We will download and process using our pipeline.</span>--}}
{{--                                <span class="collapse" id="more">Option two, you can provide processed datasets accompanied with a JSON schema for--}}
{{--                                dataset description. In this way, you can contribute both simulated datasets and real--}}
{{--                                datasets. We have an example list in GitHub (link). For the JSON schema, we described it--}}
{{--                                in this link; for the datasets, we require an R dataset with four elements: gene,--}}
{{--                                ensemble id, location, expression matrix. </span>--}}
{{--                                <span><a href="#more" data-toggle="collapse">... <i class="fa fa-caret-down"></i></a></span>--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-4">--}}
{{--                            <span class="fas fa-cogs fa-3x text-primary"></span>--}}
{{--                            <h4 class="my-3">To contribute methods</h4>--}}
{{--                            <p class="text-muted">you can list the online location where the method is publicly--}}
{{--                                accessible. You need to provide an example that takes a spatial dataset and outputs the--}}
{{--                                statistical significance for the genes.--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-4">--}}
{{--                            <span class="fas fa-chart-bar fa-3x text-primary"></span>--}}
{{--                            <h4 class="my-3">To contribute analysis results</h4>--}}
{{--                            <p class="text-muted">you can provide the analysis results in a tabular format as shown in--}}
{{--                                the link. You will need to describe the datasets and methods. </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </section>--}}
{{--            </div>--}}
{{--        </section>--}}

{{--        <hr class="my-5">--}}

{{--        <section class="mt-5 wow fadeIn animated" style="visibility: visible; animation-name: fadeIn;">--}}
{{--            <h3 class="h3 text-center mb-5">Contact</h3>--}}
{{--            <div class="row">--}}
{{--                <section class="mx-2 pb-3">--}}

{{--                    <div class="row text-center">--}}
{{--                        <!--Grid column-->--}}
{{--                        <div class="col-lg-4 col-md-12 mb-4 mb-md-0">--}}

{{--                            <i class="fas fa fa-map-marker fa-3x text-primary"></i>--}}

{{--                            <p class="font-weight-bold my-3 font-gray-10">Address</p>--}}

{{--                            <p class="text-muted">Quantitative Biomedical Research Center--}}
{{--                                Department of Population and Data Sciences--}}
{{--                                Danciger Research Building, 5323 Harry Hines Blvd. Ste. H9.124, Dallas, TX 75390-8821 </p>--}}
{{--                        </div>--}}
{{--                        <!--Grid column-->--}}

{{--                        <!--Grid column-->--}}
{{--                        <div class="col-lg-4 col-md-6 mb-4 mb-md-0">--}}

{{--                            <i class="fas fa fa-phone fa-3x text-primary"></i>--}}

{{--                            <p class="font-weight-bold my-3 font-gray-10">Phone number</p>--}}

{{--                            <p class="text-muted">--}}
{{--                                <b>Guanghua Xiao</b><br>--}}
{{--                                Phone: 214-648-4110--}}
{{--                            </p>--}}
{{--                            <p class="text-muted">--}}
{{--                                <b>Xiaowei Zhan</b><br>--}}
{{--                                Phone: 214-648-5194<br>--}}
{{--                                Fax: 214.648.1663--}}
{{--                            </p>--}}

{{--                        </div>--}}
{{--                        <!--Grid column-->--}}

{{--                        <!--Grid column-->--}}
{{--                        <div class="col-lg-4 col-md-6 mb-4 mb-md-0">--}}

{{--                            <i class="fas fa fa-envelope fa-3x text-primary"></i>--}}

{{--                            <p class="font-weight-bold my-3 font-gray-10">E-mail</p>--}}

{{--                            <p class="text-muted">--}}
{{--                                <b>Guanghua Xiao</b><br>--}}
{{--                                Guanghua.Xiao@UTSouthwestern.edu--}}
{{--                            </p>--}}
{{--                            <p class="text-muted">--}}
{{--                                <b>Xiaowei Zhan</b><br>--}}
{{--                                Xiaowei.Zhan@UTSouthwestern.edu--}}
{{--                            </p>--}}
{{--                            <p class="text-muted">--}}
{{--                                <b>Qiwei Li</b><br>--}}
{{--                                Qiwei.Li@UTDallas.edu--}}
{{--                            </p>--}}

{{--                        </div>--}}

{{--                    </div></section>--}}
{{--            </div>--}}
{{--        </section>--}}

{{--    </div>--}}

@endsection
