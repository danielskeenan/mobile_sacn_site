@extends('_layouts.master')

@section('body')
    <section class="container max-w-8xl mx-auto px-6 md:px-8 py-4">
        <div class="flex flex-col lg:flex-row">
            <nav id="js-nav-menu" class="nav-menu hidden lg:block">
                @include('_nav.menu', ['items' => $page->navigation])
            </nav>

            <div class="w-full lg:w-3/5 break-words lg:pl-4 border-b border-blue-200 mb-10 pb-4" v-pre>
                <h1 class="leading-none mb-2">{{ $page->title }}</h1>
                <p>Released {{ $page->getDate()->toDateString() }}</p>
                <div class="DocSearch-content">
                    @yield('content')
                </div>
                <div>
                    @include('_components.release-assets', ['release' => $page])
                </div>
            </div>
        </div>
    </section>
@endsection
