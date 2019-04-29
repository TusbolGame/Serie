@extends('layouts.main', ['title' => 'Home'])
@section('title', 'S')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-sm-12 px-1 text-filter-container">
            <div class="row pb-3">
                <h2 class="col-xl-9 col-sm-12 m-0 pt-2">Unwatched Episodes</h2>
                <div class="col-xl-3 col-sm-12">
                    <input class="form-control text-filter-input" type="text" placeholder="Filter" autocomplete="off">
                </div>
            </div>
            <div class="row episodes text-filter-target">
                @foreach($episodes as $episode)
                    @component('components.card-episode', ['episode' => $episode])
                    @endcomponent
                @endforeach
            </div>
        </div>
        <div class="col-xl-3 col-md-12">
            <div class="row pb-3">
                <h2 class="col-12 m-0 pt-2">Admin Tools</h2>
            </div>
            <div class="row" id="admin-tools">
                <div class="col-12">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item p-2">
                                <div class="row no-gutters">
                                    <div class="col-xl-7 col-sm-12 pr-1 pt-2">
                                        Update all shows
                                    </div>
                                    <div class="col-xl-5 col-sm-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-primary" data-group="0" data-type="0">Update</button>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item p-2">
                                <form class="row no-gutters form-inline">
                                    <div class="col-xl-8 col-sm-12 form-group pr-1">
                                        <label for="showSearch" class="sr-only">Email</label>
                                        <input type="text" class="w-100 form-control form-control-sm" id="showSearch" placeholder="Search new shows">
                                    </div>
                                    <div class="col-xl-4 col-sm-12 d-flex justify-content-end">
                                        <a href="/data/update/0" type="button" class="btn btn-sm btn-primary" data-group="0" data-type="0">Search</a>
                                    </div>
                                </form>
                            </li>
                        </ul>
                        <div class="card-body">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pb-3 mt-4">
                <h2 class="col-12 m-0 pt-2">Schedule</h2>
            </div>
            <div class="row" id="schedule">
                <div class="col-12">
                    @foreach($schedule as $dayKey => $day)
                    <div class="card mb-2">
                        <div class="card-header bg- p-2">
                            <h4 class="text-black-50 m-0">{{\Carbon\Carbon::parse($dayKey)->format('l')}}</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            @if($day->count() != 0)
                                @foreach($day as $episode)
                                    @component('components.schedule-episode', ['episode' => $episode])
                                    @endcomponent
                                @endforeach
                            @else
                                <li class="list-group-item p-2">
                                    <h5 class="cmn-lighter font-weight-light text-center m-0">Empty</h5>
                                </li>
                            @endif
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
    </div>
@endsection


