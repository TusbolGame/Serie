<li class="list-group-item p-2">
    <div class="row no-gutters">
        <div class="col-xl-2 col-sm-12 pr-1 text-black-50 font-weight-light">
            {{\Carbon\Carbon::parse($episode->airing_at)->format('H:i')}}
        </div>
        <div class="col-xl-7 col-sm-12 pr-1">
            <a class="text-dark" href="/show/{{$episode->show->uuid}}">{{$episode->show->name}}</a>
        </div>
        <div class="col-xl-3 col-sm-12 d-flex justify-content-end">
            <a class="text-black-50 font-weight-light" href="/show/{{$episode->uuid}}">{{$episode->episode_code}}</a>
        </div>
    </div>
</li>
