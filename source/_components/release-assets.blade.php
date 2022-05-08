<h3>Downloads</h3>
<ul>
    @foreach($release->assets as $asset)
        @php
            $iconClass = match($asset['platform']) {
                'Windows' => 'fa-brands fa-windows',
                'MacOs' => 'fa-brands fa-apple',
                'Ubuntu' => 'fa-brands fa-ubuntu',
                default => null,
            }
        @endphp
        <li>
            <a href="{{$asset['url']}}">
                @if($iconClass)
                    <i class="{{$iconClass}}"></i>
                @endif
                {{$asset['platformTitle']}}
            </a>
        </li>
    @endforeach
</ul>

@push('head_scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.1.1/js/all.min.js"></script>
@endpush
