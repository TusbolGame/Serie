@extends('layouts.main', ['title' => $episode->show->name])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-sm-12 px-1 text-filter-container">
            <div class="row pb-3">
                <div class="col-xl-9">
                    <div class="row pb-3">
                        <h1 class="col-xl-9 col-sm-12 m-0 pt-2">
                            {{$show->name}}
                        </h1>
                    </div>
                    <div class="row pb-3">
                        <div class="col-xl-2 col-sm-12">
                            <div class="row no-gutters">
                                <div class="col-xl-6 col-sm-12">
                                    <span>Status</span>
                                </div>
                                <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                                    <span>{{$show->status->name}}</span>
                                </div>
                            </div>
                            <div class="row no-gutters">
                                <div class="col-xl-6 col-sm-12">
                                    <span>Network</span>
                                </div>
                                <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                                    <a href="{{$show->network->link}}" target="_blank">{{$show->network->name}}</a>
                                </div>
                            </div>
                            <div class="row no-gutters">
                                <div class="col-xl-6 col-sm-12">
                                    <span>Tvmaze</span>
                                </div>
                                <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                                    <a href="{{$show->api_link}}" target="_blank">{{$show->api_rating}}</a>
                                </div>
                            </div>
                            @if (isset($show->imdb_link) && isset($show->imdb_rating))
                                <div class="row no-gutters">
                                    <div class="col-xl-6 col-sm-12">
                                        <span>IMDb</span>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                                        <a href="{{$show->imdb_link}}" target="_blank">{{$show->imdb_rating}}</a>
                                    </div>
                                </div>
                            @endif
                            <div class="row no-gutters">
                                <div class="col-xl-6 col-sm-12">
                                    <span>Schedule</span>
                                </div>
                                <div class="col-xl-6 col-sm-12 d-xl-flex justify-content-xl-end">
                                    <span>{{Carbon\Carbon::parse($show->airing_time)->format('H:i')}} ({{$show->running_time}} min)</span>
                                </div>
                            </div>
                            <div class="row no-gutters">
                                <div class="col-xl-12 col-sm-12">
                                    @foreach ($show->genres as $genre)
                                        <span class="badge badge-info text-white">{{$genre->name}}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7 col-sm-12">
                            <span>{{$show->description}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-12">
                    <img class="w-100" src="/{{config('custom.posterOriginalFolder').$show->posters->first()->name}}.jpg" >
                </div>
            </div>
            <div class="row pb-3">
                <h2 class="col-xl-9 col-sm-12 m-0 pt-2">Episodes</h2>
                <div class="col-xl-3 col-sm-12">
                    <input class="form-control text-filter-input" type="text" placeholder="Filter" autocomplete="off">
                </div>
            </div>
            <div class="row pb-3">
                <div class="col-12">
                    @foreach($show->season as $season)
                        <div class="card mb-4">
                            <div class="card-header p-2">
                                <div class="row">
                                    <a class="col-xl-9 col-sm-12 text-black-50" href="{{$show->api_link}}" target="_blank">
                                        <h4 class="m-0">Season {{$season->season}}</h4>
                                    </a>
                                    <div class="col-xl-3 col-sm-12 text-black-50">
                                        <div class="row pt-1">
                                            <div class="col-4 d-xl-flex justify-content-xl-end">
                                                {{$season->episodes}} episodes
                                            </div>
                                            <div class="col-8 d-xl-flex justify-content-xl-end">
                                                {{Carbon\Carbon::parse($season->date_start)->format('d-m-Y')}} - {{Carbon\Carbon::parse($season->date_end)->format('d-m-Y')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-sm-12">
                                    @if (isset($season->posters->first()->name))
                                    <img class="w-100" src="/{{config('custom.seasonOriginalFolder').$season->posters->first()->name}}.jpg" >
                                    @endif
                                </div>
                                <div class="col-xl-10 col-sm-12">
                                    <ul class="list-group list-group-flush">
                                        @foreach($season->episode as $episode)
                                            <li class="list-group-item p-2">
                                                <div class="row no-gutters">
                                                    <span class="episode-number col-1">{{$episode->episode_number}}</span>
                                                    <span class="episode-time col-1
                                                        @if ($episode->videoView->first() !== NULL)
                                                            {{' cmn-light'}}
                                                        @elseif (Carbon\Carbon::parse($episode->airing_at) < \Carbon\Carbon::now())
                                                            {{''}}
                                                        @else
                                                            {{' cmn-lighter'}}
                                                        @endif" title="@if ($episode->videoView->first() !== NULL){{'Watched'}}@elseif (Carbon\Carbon::parse($episode->airing_at) < \Carbon\Carbon::now()){{'Not yet watched'}}@else{{'Upcoming'}}@endif">
                                                        {{Carbon\Carbon::parse($episode->airing_at)->format('d-m-Y')}}
                                                    </span>
                                                    <a class="episode-title col-2" href="/episode/{{$episode->uuid}}">
                                                        <span>{{$episode->title}}</span>
                                                    </a>
                                                    <span class="episode-summary col-8" data-summary="{{$episode->summary}}">{{substr($episode->summary, 0, 120).'...'}}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
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


