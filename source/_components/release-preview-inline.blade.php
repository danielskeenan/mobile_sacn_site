<article class="flex flex-col mb-4">
    <h2 class="text-3xl my-0">
        <a href="{{ $release->getUrl() }}"
           title="Read more - {{ $release->title }}"
           class="text-gray-900 font-extrabold">
            {{ $release->title }}
        </a>
    </h2>

    <div>
        <p class="mt-0">Released {{ $release->getDate()->toFormattedDateString() }}</p>
        {!! $release->getContent() !!}
    </div>

    <div>
        @include('_components.release-assets', ['release' => $release])
    </div>

    <a href="{{ $release->getUrl() }}"
       title="Read more - {{ $release->title }}"
       class="uppercase font-semibold tracking-wide mb-2">
        More Info
    </a>
</article>
