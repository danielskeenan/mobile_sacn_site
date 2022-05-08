<h2>Downloads</h2>
<ul>
    @foreach($release->assets as $platform => $url)
        <li><a href="{{$url}}">{{$platform}}</a></li>
    @endforeach
</ul>
