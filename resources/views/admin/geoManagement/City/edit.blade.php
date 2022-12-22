@extends('adminlte::page')

@section('title', 'Update City')

@section('css')
    <link rel="stylesheet" href="/css/styles.css">
@stop

@section('content_header')

    <div class="row">
        <div class="col">
            <a href="{{ route('geo.city.index') }}" class="btn btn-primary">
                <i class="fas fa-long-arrow-alt-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <h1 class="m-0 text-dark text-center">Update City</h1>
        </div>
    </div>

@stop

@section('content')

    <div class="ml-5 mr-5 p-2 bg-gradient-light text-white rounded">
        <form class="text-center" method="POST" action="/update_city/{{ $cities->id }}">
            @csrf
            <div class="m-2">
                <select class="form-control w-25 m-auto" name="state" aria-label="Default select example">
                    <option selected>Select State</option>
                    @foreach ($states as $state1)
                        <option value="{{ $state1->id }}" {{ $state1->id == $cities->state_id ? 'selected' : '' }}>
                            {{ $state1->name }}</option>
                    @endforeach
                </select>
                <br>
                <input type="text" class="form-control w-25 m-auto" id="city" name="city_name" placeholder="City"
                    value={{ $cities->name }} autofocus required autocomplete="off">
                <span class="text-danger">
                    @error('city_name')
                        {{ $message = 'City is Required' }}
                    @enderror
            </div>
            <br>
            <x-adminlte-button label=" Submit" theme="primary" icon="fas fa-plus" type="submit" />
        </form>
    </div>

@stop
