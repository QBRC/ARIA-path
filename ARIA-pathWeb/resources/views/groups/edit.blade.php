@extends('layouts.app')

@section('page-title')
    Update {{ ucfirst($name['model']) }}
@endsection

@section('content')
    <div class="container my-5">
        <div class="row flex justify-content-center pt-20">
            <div class="col-md-10">

                @if($record->num > 0)
                    <div class="alert alert-warning mb-5">
                        <i class="fas fa-exclamation-triangle"></i> We find <a href="{{ url('datarecords/'.$name['model'] .'/'. $record->id.'/show') }}" class="alert-link">{{ $record->num }} {{ $record->num>1?'records':'record' }} {{ $record->delnum>0? "(".$record->delnum." of them ".($record->delnum>1?"are ":"is ")." deleted)":"" }}</a> which {{ $record->num>1?'are':'is' }} associated with <strong>{{ $record->name }}</strong>.
                    </div>
                @endif
                <form action="{{ url('/'.$name['route'].'/'.$record->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">{{ ucfirst($name['model']) }} Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name',$record->name) }}" required autofocus>

                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary mr-3">Back</a>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
