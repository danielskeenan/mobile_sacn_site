<div class="flex flex-col mb-4">
    <p class="text-gray-700 font-medium my-2">
        {{ $release->getDate()->toDateString() }}
    </p>

    <h2 class="text-3xl mt-0">
        <a href="{{ $release->getUrl() }}"
           title="Read more - {{ $release->title }}"
           class="text-gray-900 font-extrabold">
            {{ $release->title }}
        </a>
    </h2>

    <div>{!! $release->getContent() !!}</div>

    <div>
        @include('_components.release-assets', ['release' => $release])
    </div>

    <a href="{{ $release->getUrl() }}"
       title="Read more - {{ $release->title }}"
       class="uppercase font-semibold tracking-wide mb-2">
        More Info
    </a>
</div>
