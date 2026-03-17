@extends('layouts.app')

@section('page-title')
    Models
@endsection

@section('content')

    @if(session('succeed'))
        <div class="alert alert-success flash mb-3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>{{ session('succeed') }}</strong>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <a href="" data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-icon-split">
                    <span class="text"><i class="fas fa-plus mr-1"></i> Add new model</span>
                </a>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>API</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Create Date</th>
                        <th>Update Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($models as $model)

                            <tr>
                                <td>{{ $model->id }}</td>
                                <td>{{ $model->name }}</td>
                                <td>{{ $model->api }}</td>
{{--                                <td>{{ str_replace(config('app.iv.placeholder.file'), '<span class="text-info">'.config('app.iv.placeholder.file').'</span>', $model->api) }}</td>--}}
                                <td>{{ $model->description }}</td>
                                <td>{{ config('app.iv.model_type')[$model->type] }}</td>
                                <td class="{{ $model->is_delete == 1 ? 'text-danger font-weight-bold' : '' }}">{{ config('app.iv.delete')[$model->is_delete] }}</td>
                                <td>{{ $model->created_at }}</td>
                                <td>{{ $model->updated_at }}</td>
                                <td>
                                    @if(Auth::user()->role == 1)
                                        @if($model->is_delete == 0)
                                            <a href="" data-toggle="modal" data-target="#editModal{{ $model->id }}" class="btn text-success btn-icon-split">
                                                <i class="fas fa-edit" title="Edit" alt="Edit" data-toggle="tooltip" data-placement="auto" title="Edit"></i>
                                            </a>

                                            @if($model->type == 9)
                                            <a href="" data-toggle="modal" data-target="#deleteModal{{ $model->id }}" class="btn text-danger btn-icon-split ml-3" alt="Disable group">
                                                <i class="fas fa-ban" title="Disable {{ $model->name }}" alt="Disable {{ $model->name }}" data-toggle="tooltip" data-placement="auto"></i>
                                            </a>
                                            @endif
                                        @else
                                            <a href="{{ url('models/'.$model->id.'/restore') }}" class="btn text-primary btn-icon-split" title="Activate" alt="Activate" data-toggle="tooltip" data-placement="auto">
                                                <i class="fas fa-undo-alt"></i>
                                            </a>
                                        @endif
                                    @endif

                                </td>
                            </tr>

                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>

    </div>

{{--    <small class="ml-3">--}}
        <ul class="text-muted small-font-size">
            <li>All the Base APIs are able to edit, but not allow to create or disable.</li>
            <li><b class="text-primary">{{ config('app.iv.placeholder.file') }}</b> is the placeholder of file path in the API url.</li>
            <li><b class="text-primary">{{ config('app.iv.placeholder.image') }}</b> is the placeholder of image ID in the API url.</li>
            <li><b class="text-primary">{{ config('app.iv.placeholder.annot') }}</b> is the placeholder of annotation ID in the API url.</li>
        </ul>
{{--    </small>--}}


    <!-- Add model -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add new model</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ url('models') }}" method="POST">
                    @csrf

                    <div class="modal-body px-5">
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">Name <span class="text-danger">*</span></label>

                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Model name" required autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

{{--                        <div class="form-group row">--}}
{{--                            <label for="registry" class="col-md-3 col-form-label text-md-right">Registry <span class="text-danger">*</span></label>--}}

{{--                            <div class="col-md-8">--}}
{{--                                <input id="registry" type="text" class="form-control @error('registry') is-invalid @enderror" name="registry" placeholder="Model registry parameter" required autofocus>--}}

{{--                                @error('registry')--}}
{{--                                <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}


                        <div class="form-group row">
                            <label for="api" class="col-md-3 col-form-label text-md-right">API <span class="text-danger">*</span></label>

                            <div class="col-md-8">
                                <input id="api" type="text" class="form-control @error('api') is-invalid @enderror" name="api" placeholder="API URL" required autofocus>

                                @error('api')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="description" class="col-md-3 col-form-label text-md-right">Description <span class="text-danger">*</span></label>

                            <div class="col-md-8">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="What this model use for" required autofocus>

                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-3 col-form-label text-md-right">Type <span class="text-danger">*</span></label>

                            <div class="col-md-8">
                                <select id="type" class="form-control" name="type" required>
                                    @foreach(config('app.iv.model_type') as $k=>$v)
                                        @if($k==9)
                                            <option value="{{$k}}" selected>{{$v}}</option>
                                        @else
                                            <option value="{{$k}}" disabled>{{$v}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <ul>
                            <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.file') }}</b> as the placeholder of file path in the API url.</small></li>
                            <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.image') }}</b> as the placeholder of image ID in the API url.</small></li>
                            <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.annot') }}</b> as the placeholder of annotation ID in the API url.</small></li>
                        </ul>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    @foreach($models as $model)
        <!-- Edit model -->

        <div class="modal fade" id="editModal{{ $model->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update model</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form action="{{ url('models/'.$model->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body px-5">
                            <div class="form-group row">
                                <label for="name" class="col-md-3 col-form-label text-md-right">Name <span class="text-danger">*</span></label>

                                <div class="col-md-8">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $model->name) }}" placeholder="Model name" required autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
{{--                            <div class="form-group row">--}}
{{--                                <label for="registry" class="col-md-3 col-form-label text-md-right">Registry <span class="text-danger">*</span></label>--}}

{{--                                <div class="col-md-8">--}}
{{--                                    <input id="registry" type="text" class="form-control @error('registry') is-invalid @enderror" value="{{ old('registry', $model->registry) }}" name="registry" placeholder="Model registry parameter" required autofocus>--}}

{{--                                    @error('registry')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}


                            <div class="form-group row">
                                <label for="api" class="col-md-3 col-form-label text-md-right">API <span class="text-danger">*</span></label>

                                <div class="col-md-8">
                                    <input id="api" type="text" class="form-control @error('api') is-invalid @enderror" value="{{ old('api', $model->api) }}" name="api" placeholder="API URL" required autofocus>

                                    @error('api')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                            </div>


                            <div class="form-group row">
                                <label for="description" class="col-md-3 col-form-label text-md-right">Description <span class="text-danger">*</span></label>

                                <div class="col-md-8">
                                    <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" value="{{ old('name', $model->description) }}" name="description" placeholder="What this model use for" required autofocus>

                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="type" class="col-md-3 col-form-label text-md-right">Type <span class="text-danger">*</span></label>

                                <div class="col-md-8">
                                    <select id="type" class="form-control" name="type" required>
                                        @foreach(config('app.iv.model_type') as $k=>$v)
                                            <option value="{{$k}}" {{ $model->type==$k?'selected':'' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <ul>
                                <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.file') }}</b> as the placeholder of file path in the API url.</small></li>
                                <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.image') }}</b> as the placeholder of image ID in the API url.</small></li>
                                <li><small class="ml-3">Use <b class="text-primary">{{ config('app.iv.placeholder.annot') }}</b> as the placeholder of annotation ID in the API url.</small></li>
                            </ul>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Delete model -->

        <div class="modal fade" id="deleteModal{{ $model->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ url('models/'.$model->id.'/delete') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Model</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure to delete <b>{{ $model->name }}</b>?
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary mr-3" type="button" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach




@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "scrollX": true,
                // "order": [[ 3, "desc" ],[ 4, "desc" ]],
                columnDefs: [
                    {bSortable: false, targets: [8]} // Disable sorting on columns
                ],
            });

            // $('li.check-permission').click(function(e){
            //     var checkbox=$(this).find('input');
            //     if(!checkbox.prop("disabled")){
            //         checkbox.prop("checked", !checkbox.prop("checked"));
            //     }
            //
            // });
        });
    </script>
@stop
