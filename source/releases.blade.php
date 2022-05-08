---
#@formatter:off
pagination:
    collection: releases
#@formatter:on
---
@extends('_layouts.master')

@section('body')
    <section class="container max-w-8xl mx-auto px-6 md:px-8 py-4">
        <div class="flex flex-col lg:flex-row">
            <nav id="js-nav-menu" class="nav-menu hidden lg:block">
                @include('_nav.menu', ['items' => $page->navigation])
            </nav>

            @foreach($pagination->items as $release)
                @include('_components.release-preview-inline')

                @if (! $loop->last)
                    <hr class="w-full border-b mt-2 mb-6">
                @endif
            @endforeach
        </div>
    </section>
@endsection
