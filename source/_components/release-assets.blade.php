<h3>Downloads</h3>
<ul class="list-none" role="list">
    @foreach($release->assets as $asset)
        @php
            $iconClass = match($asset['platform']) {
                'windows' => 'fa-brands fa-windows',
                'macos' => 'fa-brands fa-apple',
                'ubuntu' => 'fa-brands fa-ubuntu',
                default => null,
            }
        @endphp
        <li>
            <a href="{{$asset['url']}}">
                @if($iconClass)
                    <i class="{{$iconClass}}"></i>
                @endif
                {{$asset['platformTitle']}}
                ({{$asset['kind']}})
            </a>
        </li>
    @endforeach
</ul>

@push('head_scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/js/all.min.js"></script>
@endpush
