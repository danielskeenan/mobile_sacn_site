@extends('_layouts.master')

@section('body')
    <section class="container max-w-6xl mx-auto px-6 py-10 md:py-12">
        <div class="flex flex-col-reverse mb-10 lg:flex-row lg:mb-24">
            <div class="mt-8">
                <h1>{{ $page->siteName }}</h1>

                <h2 id="intro-powered-by-jigsaw" class="font-light mt-4">{{ $page->siteDescription }}</h2>

                <p class="text-lg">
                    Mobile sACN allows remote troubleshooting of sACN signals. Your mobile device connects to a program
                    on your computer that handles sACN traffic.
                </p>

                <div class="flex my-10">
                    <a href="/docs/getting-started" title="{{ $page->siteName }} getting started"
                       class="bg-blue-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">
                        Get Started
                    </a>

                    <a href="/releases"
                       class="bg-green-500 hover:bg-blue-600 font-normal text-white hover:text-white rounded mr-4 py-2 px-6">
                        Downloads
                    </a>
                </div>
            </div>
        </div>

        <hr class="block my-8 border lg:hidden">

        <div class="md:flex -mx-2 -mx-4">
            <div class="mb-8 mx-3 px-2 md:w-1/3">
                <h3 class="text-2xl text-blue-900 mb-0">
                    Channel Check
                </h3>

                <img src="/assets/img/screenshots/chancheck.png"/>

                <p>
                    Run through addresses in any universe. Use per-address-priority to avoid affecting ranges you're not
                    interested in.
                </p>
            </div>

            <div class="mb-8 mx-3 px-2 md:w-1/3">
                <h3 id="intro-markdown" class="text-2xl text-blue-900 mb-0">
                    Control
                </h3>

                <img src="/assets/img/screenshots/control_keypad.png"/>

                <p>
                    Adjust multiple channels in one universe using faders or a simple command-line interface.
                </p>
            </div>

            <div class="mx-3 px-2 md:w-1/3">
                <h3 class="text-2xl text-blue-900 mb-0">
                    View Levels
                </h3>

                <img src="/assets/img/screenshots/levels_bars.png"/>

                <p>
                    View active levels from multiple sources.
                </p>
            </div>
        </div>
    </section>
@endsection
