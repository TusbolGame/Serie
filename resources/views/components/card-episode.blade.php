<div class="card-episode col-xl-2 col-sm-12 px-1 mb-2" data-show="{{$episode->show->uuid}}" data-episode="{{$episode->uuid}}" data-airdate="{{Carbon\Carbon::parse($episode->airing_at)->format('Y-m-d H:i:s')}}" data-filter="{{$episode->show->name}}">
    <div class="card d-flex">
        <div class="episode-poster-container card-img-top">
            <img class="episode-poster" src="/{{config('custom.posterOriginalFolder').$episode->show->posters->first()->name}}.jpg" alt="">
        </div>
        <div class="card-body d-flex flex-column justify-content-between no-gutters p-1 bg-light">
            <div class="card-info">
                <div class="card-info-default">
                    <div class="row no-gutters card-info-default-container">
                        <div class="col-12 episode-show-name">
                            <a href="/show/{{$episode->show->uuid}}" class="card-title h4 text-dark">{{$episode->show->name}}</a>
                        </div>
                        <div class="col-12 episode-code">
                            <a href="/episode/{{$episode->uuid}}" class="card-title h4 font-weight-light cmn-light">{{$episode->episode_code}}</a>
                        </div>
                    </div>
                    <div class="episode-date font-weight-light cmn-lighter" data-date="{{Carbon\Carbon::parse($episode->airing_at)->format('Y-m-d H:i:s')}}">
                        {{Carbon\Carbon::parse($episode->airing_at)->diffForHumans()}}
                    </div>
                </div>
                <div class="card-info-extra pt-2 font-weight-light">
                    @if (isset($episode->show->description) && strlen($episode->show->description) > 100)
                        {{substr($episode->show->description, 0, 100)}}
                    @endif
                </div>
            </div>
            <div class="card-actions">
                <div class="actions-extra d-flex justify-content-between align-items-end">
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon c12
                        @endslot
                        @slot('type')
                            7
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                            Remove show
                        @endslot
                        @slot('icon')
                            default-clear
                        @endslot
                    @endcomponent
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon c3
                        @endslot
                        @slot('type')
                            8
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                            Add bookmark
                        @endslot
                        @slot('icon')
                            default-bookmark
                        @endslot
                    @endcomponent
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon c1
                        @endslot
                        @slot('type')
                            9
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                            Show more details
                        @endslot
                        @slot('icon')
                            default-list
                        @endslot
                    @endcomponent
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon c5
                        @endslot
                        @slot('type')
                            10
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                            Rate this episode
                        @endslot
                        @slot('icon')
                            default-star
                        @endslot
                    @endcomponent
                </div>
                <div class="actions-basic">
                    @if ($episode->torrent_count == 0 || $episode->torrent->first()->status == 5)
                        <a class="text-reset" href="https://rarbgway.org/torrents.php?search={{$episode->show->name}} {{$episode->episode_code}}&amp;category[]=18&amp;category[]=41&amp;category[]=49" target="_blank">
                            @component('components.cmn-button-icon')
                                @slot('class')
                                    icon c6
                                @endslot
                                @slot('type')
                                    1
                                @endslot
                                @slot('group')
                                    1
                                @endslot
                                @slot('title')
                                    Search for torrents
                                @endslot
                                    @slot('icon')
                                    default-search
                                @endslot
                            @endcomponent
                        </a>
                        @component('components.cmn-button-icon')
                            @slot('class')
                                icon c9
                            @endslot
                            @slot('type')
                                2
                            @endslot
                            @slot('group')
                                1
                            @endslot
                            @slot('title')
                                    Add magnet link
                            @endslot
                            @slot('icon')
                                default-torrent
                            @endslot
                        @endcomponent
                    @else
                        @if ($episode->torrent->first()->status == 0 || $episode->torrent->first()->status == 1)
                            @component('components.cmn-button-icon')
                                @slot('class')
                                    icon c9
                                @endslot
                                @slot('type')
                                    2
                                @endslot
                                @slot('group')
                                    1
                                @endslot
                                @slot('title')
                                    Add magnet link
                                @endslot
                                @slot('icon')
                                    default-torrent
                                @endslot
                            @endcomponent
                            @component('components.cmn-button-icon')
                                @slot('class')
                                    icon c11
                                @endslot
                                @slot('type')
                                    6
                                @endslot
                                @slot('group')
                                    1
                                @endslot
                                @slot('title')
                                    Check torrent status
                                @endslot
                                @slot('icon')
                                    default-torrent
                                @endslot
                            @endcomponent
                        @elseif ($episode->torrent->first()->status == 2)
                            @component('components.cmn-button-icon')
                                @slot('class')
                                    icon c1
                                @endslot
                                @slot('type')
                                    3
                                @endslot
                                @slot('group')
                                    1
                                @endslot
                                @slot('title')
                                        Convert Video
                                @endslot
                                @slot('icon')
                                    default-convert
                                @endslot
                            @endcomponent
                        @elseif ($episode->torrent->first()->status == 3)
                            @component('components.cmn-button-icon')
                                @slot('class')
                                    icon c2
                                @endslot
                                @slot('type')
                                    4
                                @endslot
                                @slot('group')
                                    1
                                @endslot
                                @slot('title')
                                        Play episode
                                @endslot
                                @slot('icon')
                                    default-play
                                @endslot
                            @endcomponent
                        @endif
                    @endif
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon c4
                        @endslot
                        @slot('type')
                            5
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                                Mark this episode as watched
                        @endslot
                        @slot('icon')
                            default-mark
                        @endslot
                    @endcomponent
                    @component('components.cmn-button-icon')
                        @slot('class')
                            icon neu
                        @endslot
                        @slot('type')
                            0
                        @endslot
                        @slot('group')
                            1
                        @endslot
                        @slot('title')
                                Show more information
                        @endslot
                        @slot('icon')
                                default-expand-up
                        @endslot
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
</div>
