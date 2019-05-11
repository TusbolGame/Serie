@extends('layouts.main', ['title' => 'Home'])
@section('title', 'S')
@section('content')
    <div class="row justify-content-center mx-n1">
        <div id="UnwatchedEpisodes" class="col-xl-9 col-sm-12 px-1 text-filter-container">
            <div class="row mx-n1 align-items-center pb-2">
                <h2 class="col-xl-9 col-sm-12 px-1 mb-0">Unwatched Episodes</h2>
                <div class="col-xl-3 col-sm-12 form-group pt-2 pt-xl-0 px-1 mb-0">
                    <label for="episodeSearchInput" class="sr-only">Search</label>
                    <input type="text" class="w-100 form-control form-control-md text-filter-input" id="episodeSearchInput" name="episodeSearchInput" v-model="episodeSearchQuery" placeholder="Search new shows">
                </div>
            </div>
            <transition-group name="fade" tag="div" class="row mx-n1 episodes text-filter-target" appear>
                <episode-component v-for="(unwatchedEpisode, index) in {{$episodes}}"
                                   v-bind:index="index"
                                   v-bind:data="unwatchedEpisode"
                                   v-bind:key="unwatchedEpisode.uuid"
                                   v-bind:uuid="unwatchedEpisode.uuid"
                                   v-bind:show_name="unwatchedEpisode.show.name"
                                   v-bind:show_uuid="unwatchedEpisode.show.uuid"
                                   v-bind:show_posters="unwatchedEpisode.show.posters"
                                   v-bind:episode_code="unwatchedEpisode.episode_code"
                                   v-bind:airing_at="unwatchedEpisode.airing_at"
                                   v-bind:summary="unwatchedEpisode.summary"
                                   v-bind:torrent_count="unwatchedEpisode.torrent_count"
                                   v-bind:torrent="unwatchedEpisode.torrent">
                </episode-component>
            </transition-group>
        </div>
        <div class="col-xl-3 col-md-12 px-1">
            <div id="admin-tools" class="row pb-3 mx-n1">
                <h2 class="col-12 pb-1 pt-2 px-1 mb-0">Admin Tools</h2>
                <div class="col-12 px-1">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li id="ShowUpdate" class="list-group-item p-2">
                                {{-- TODO Add transitions on logic elements--}}
                                <div id="show-update-container" class="row no-gutters cmn-admin-action">
                                    <div class="col-xl-7 col-sm-12 pr-1 pt-2">
                                        <span v-if="!active">Update all shows</span>
                                        <span class="col-12 text-center" v-if="updating">Updating...</span>
                                    </div>
                                    <div class="col-xl-5 col-sm-12 d-flex justify-content-end">
                                        <button type="button" @click="updateAllShows" class="btn btn-md btn-primary" data-group="0" data-type="0" v-if="!active">Update</button>
                                        {{--// TODO Implement reset update functionality--}}
                                        <button type="button" @click="" class="btn btn-md btn-primary" data-group="0" data-type="5" v-if="active">Reset</button>
                                    </div>
                                </div>
                                <div class="row no-gutters" v-if="active">
                                    <div class="col-12 progress-bar-container">
                                        <progress-bar-component v-bind:width="updateProgress.percentage">
                                        </progress-bar-component>
                                    </div>
                                    <div class="col-12 mb-2" v-if="active">
                                        <div class="row no-gutters">
                                            <span class="col-12 text-center" v-if="active && !updating && !completed">Starting...</span>
                                            <div class="col-12 text-center" v-if="active && updating">
                                                <span>@{{updateProgress.current + ' / ' + updateProgress.total}} - </span>
                                                <span v-bind:class="'font-weight-bold' + (updateProgress.showEnded ? ' text-black-50' : '')">@{{updateProgress.currentShow}}</span>
                                            </div>
                                            <span class="col-12 text-center" v-if="completed">Complete</span>
                                            <span class="col-12 text-center" v-if="error">There was an error</span>
                                        </div>
                                        <div class="row no-gutters font-weight-light">
                                            <span class="col-12 text-center" v-if="active && !updating && !completed">-</span>
                                            <span class="col-12 text-center" v-if="active && updating">
                                            @{{Math.round(updateProgress.percentage) + '%' + ' - ' + timeRemaining}}
                                            </span>
                                            <span class="col-12 text-center" v-if="completed">100%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters mt-3" v-if="updated">
                                    <div class="col-12 cmn-admin-result">
                                        <div class="row no-gutters mb-2 d-flex justify-content-between">
                                            <h4>New Episodes</h4>
                                            <span class="h4">@{{updateProgress.counter}}</span>
                                        </div>
                                        <div class="row no-gutters results">
                                            <transition-group name="fade" tag="div" appear class="row no-gutters results w-100">
                                                <show-update-result-component v-for="updateResult in updateResults" v-bind:data="updateResult"
                                                                              v-bind:key="updateResult.id"
                                                                              v-bind:id="updateResult.id"
                                                                              v-bind:show_name="updateResult.show.name"
                                                                              v-bind:uuid="updateResult.uuid"
                                                                              v-bind:api_link="updateResult.api_link"
                                                                              v-bind:airing_at="updateResult.airing_at"
                                                                              v-bind:episode_code="updateResult.episode_code">
                                                </show-update-result-component>
                                            </transition-group>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li id="ShowSearch" class="list-group-item p-2">
                                {{-- TODO Add transitions on logic elements--}}
                                {{-- TODO Add transitions on result elements--}}
                                <div class="row no-gutters form-inline cmn-admin-action">
                                    <div class="col-xl-8 col-sm-12 form-group pr-1">
                                        <label for="showSearch" class="sr-only">Search</label>
                                        <input v-model="showSearchQuery" type="text" class="w-100 form-control form-control-md" id="showSearchInput" name="showSearchInput" placeholder="Search new shows">
                                    </div>
                                    <div class="col-xl-4 col-sm-12 d-flex justify-content-end">
                                        <button v-if="searched" @click="resetSearch" type="button" class="btn btn-md btn-primary mr-2" data-group="0" data-type="4">Reset</button>
                                        <button @click="searchShow" type="button" class="btn btn-md btn-primary" data-group="0" data-type="1">Search</button>
                                    </div>
                                </div>
                                <div class="row no-gutters mt-3" v-if="active">
                                    <div class="col-12 cmn-admin-result">
                                        <h4 class="row no-gutters mb-2">Results</h4>
                                        <div class="row no-gutters results">
                                            <show-search-result-component v-for="searchResult in searchResults" v-bind:data="searchResult"
                                                                          v-bind:key="searchResult.api_id"
                                                                          v-bind:api_id="searchResult.api_id"
                                                                          v-bind:show_name="searchResult.show_name"
                                                                          v-bind:api_link="searchResult.api_link"
                                                                          v-bind:api_rating="searchResult.api_rating"
                                                                          v-bind:description="searchResult.description | truncate(200)"
                                                                          v-bind:poster="searchResult.poster"
                                                                          v-bind:existing="searchResult.existing"
                                                                          v-bind:owned="searchResult.owned">
                                            </show-search-result-component>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters mt-3" v-if="noResults">
                                    <span class="col-12 text-center">No shows were found.</span>
                                </div>
                                <div class="row no-gutters mt-3 text-center" v-if="searching">
                                    <span class="col-12 text-center">Searching...</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="schedule" class="row pb-3 mt-4 mx-n1">
                <h2 class="col-12 pb-1 pt-2 px-1">Schedule</h2>
                <div class="col-12 px-1">
                    <div class="col-12 px-0">
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
    </div>
@endsection
