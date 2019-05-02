@extends('layouts.main', ['title' => 'Home'])
@section('title', 'S')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9 col-sm-12 px-1 text-filter-container">
            <div class="row pb-3">
                <h2 class="col-xl-9 col-sm-12 m-0 pt-2">Unwatched Episodes</h2>
                <div class="col-xl-3 col-sm-12 pt-2 pt-xl-0">
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
                            <li id="ShowUpdate" class="list-group-item p-2">
                                <div id="show-update-container" class="row no-gutters cmn-admin-action">
                                    <div class="col-xl-7 col-sm-12 pr-1 pt-2">
                                        Update all shows
                                    </div>
                                    <div class="col-xl-5 col-sm-12 d-flex justify-content-end">
                                        <button type="button" @click="updateAllShows" class="btn btn-md btn-primary" data-group="0" data-type="0">Update</button>
                                    </div>
                                </div>
                                <div class="row no-gutters cmn-admin-result mt-3" v-if="active">
                                    <h4 class="row no-gutters mb-2">Updated Episodes</h4>
                                    <div class="row no-gutters results">
                                        <show-update-result-component v-for="result in results" v-bind:data="result"
                                                                      v-bind:key="result.id"
                                                                      v-bind:id="result.id"
                                                                      v-bind:show_name="result.show_name"
                                                                      v-bind:uuid="result.uuid"
                                                                      v-bind:api_link="result.api_link"
                                                                      v-bind:airing_at="result.airing_at"
                                                                      v-bind:episode_code="result.episode_code"
                                                                      v-bind:summary="result.summary | truncate(200)">
                                        </show-update-result-component>
                                    </div>
                                </div>
                            </li>
                            <li id="ShowSearch" class="list-group-item p-2">
                                <div class="row no-gutters form-inline cmn-admin-action">
                                    <div class="col-xl-8 col-sm-12 form-group pr-1">
                                        <label for="showSearch" class="sr-only">Search</label>
                                        <input v-model="showSearchQuery" type="text" class="w-100 form-control form-control-md" id="showSearchInput" placeholder="Search new shows">
                                    </div>
                                    <div class="col-xl-4 col-sm-12 d-flex justify-content-end">
                                        <button v-if="searched" @click="resetSearch" type="button" class="btn btn-md btn-primary mr-2" data-group="0" data-type="4">Reset</button>
                                        <button @click="searchShow" type="button" class="btn btn-md btn-primary" data-group="0" data-type="1">Search</button>
                                    </div>
                                </div>
                                <div class="row no-gutters cmn-admin-result mt-3" v-if="active">
                                    <h4 class="row no-gutters mb-2">Results</h4>
                                    <div class="row no-gutters results">
                                        <show-search-result-component v-for="result in results" v-bind:data="result"
                                                                      v-bind:key="result.api_id"
                                                                      v-bind:api_id="result.api_id"
                                                                      v-bind:show_name="result.show_name"
                                                                      v-bind:api_link="result.api_link"
                                                                      v-bind:api_rating="result.api_rating"
                                                                      v-bind:description="result.description | truncate(200)"
                                                                      v-bind:poster="result.poster"
                                                                      v-bind:existing="result.existing"
                                                                      v-bind:owned="result.owned">
                                        </show-search-result-component>
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


