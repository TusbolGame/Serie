@extends('layouts.main')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-sm-12 px-1 text-filter-container">
            <div class="row pb-3">
                <h1 class="col-xl-9 col-sm-12 m-0 pt-2"><a href="/show/{{$episode->show->uuid}}">{{$episode->show->name}}</a> - {{$episode->episode_code}}</h1>
                <div class="col-xl-3 col-sm-12">
                </div>
            </div>
            <div class="row pb-3">
                <div class="col-xl-2 col-sm-12">
                    <div class="row no-gutters">
                        <div class="col-xl-6 col-sm-12">
                            <span>@if ($episode->videoView->first() !== NULL){{'Watched'}}@endif</span>
                        </div>
                        <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-xl-12 col-sm-12">
                        </div>
                    </div>
                </div>
                <div class="col-xl-7 col-sm-12">
                    {{$episode->summary}}
                </div>
                <div class="col-xl-3 col-sm-12">
                    <img class="w-100" src="/{{config('custom.episodeOriginalFolder').$episode->posters->first()->name}}.jpg" >
                </div>
            </div>
            <div class="row pb-3">
                <div class="col-xl-12 col-sm-12">
                    @if ($episode->torrent->first() !== NULL && $episode->torrent->first()->status == 3)
                        {{--TODO Add episode video streaming capability--}}
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-12">
            <div class="row pb-3">
                <h2 class="col-12 m-0 pt-2">Admin Tools</h2>
            </div>
            <div class="row" id="admin-tools">
                <div class="col-12">
                    <div class="card">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
    </div>
@endsection


