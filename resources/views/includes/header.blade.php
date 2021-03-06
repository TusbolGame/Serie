<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="Description" content="A website to follow and track TV shows.">
<meta name="theme-color" content="#0078DB"/>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{csrf_token()}}">

<title>
    @isset($title)
        {{$title}} |
    @endisset
        {{config('app.name')}}
</title>

<!-- Styles -->
<link href="{{asset('css/app.css')}}" rel="stylesheet">
<link href="{{asset('css/generic.css')}}" rel="stylesheet">
<link href="{{asset('css/common.css')}}" rel="stylesheet">
<link href="{{asset('css/components.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins.css')}}" rel="stylesheet">
<link rel="shortcut icon" href="{{{asset('img/ui/favicon.png')}}}">
<script>
    window.Laravel = {!! json_encode([
        'user' => auth()->check() ? auth()->user()->id : null,
    ]) !!};
</script>
@stack('pagespecificstyles')
