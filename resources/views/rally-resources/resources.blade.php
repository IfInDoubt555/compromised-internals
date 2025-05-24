@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Resources | Compromised Internals',
        'description' => 'A curated collection of essential rally racing websites, YouTube channels, and social media communities for fans and enthusiasts.',
        'url'         => url()->current(),
        'image'       => asset('images/rally-resources-og.png'), // optional
    ];

    $resources = [
        'Official Sites' => [
            ['name' => 'WRC - World Rally Championship', 'url' => 'https://www.wrc.com/'],
            ['name' => 'FIA - International Motorsport', 'url' => 'https://www.fia.com/'],
            ['name' => 'DirtFish Rally School', 'url' => 'https://www.dirtfish.com/'],
            ['name' => 'Rally America', 'url' => 'https://rally-america.com/'],
            ['name' => 'American Rally Association (ARA)', 'url' => 'https://americanrallyassociation.org/'],
            ['name' => 'European Rally Championship (ERC)', 'url' => 'https://www.fiaerc.com/'],
            ['name' => 'Rally Australia', 'url' => 'https://www.rallyaustralia.com.au/'],
            ['name' => 'British Rally Championship (BRC)', 'url' => 'https://www.thebrc.co.uk/'],
            ['name' => 'FIA Cross Country Rally World Cup', 'url' => 'https://www.fia.com/events/cross-country-rally-world-cup/season-2025/calendar'],
            ['name' => 'RallyBase – Rally results & stats', 'url' => 'https://www.rallybase.nl/'],
            ['name' => 'RallySport Magazine', 'url' => 'https://www.rallysportmag.com/'],
            ['name' => 'Autosport Rally News', 'url' => 'https://www.autosport.com/wrc/'],
            ['name' => 'M-Sport WRT', 'url' => 'https://www.m-sport.co.uk/'],
            ['name' => 'Team Dynamics Motorsport', 'url' => 'https://www.teamdynamicsmotorsport.com/'],
            ['name' => 'Hyundai Motorsport', 'url' => 'https://www.hyundai-motorsport.com/'],
            ['name' => 'Toyota Gazoo Racing', 'url' => 'https://toyotagazooracing.com/'],
            ['name' => 'Ford Performance', 'url' => 'https://performance.ford.com/'],
            ['name' => 'Subaru Motorsports USA', 'url' => 'https://www.subarumotorsportsusa.com/'],
        ],
        'YouTube Channels' => [
            ['name' => 'DirtFish', 'url' => 'https://www.youtube.com/c/DirtFish'],
            ['name' => 'The Rally Show', 'url' => 'https://www.youtube.com/c/TheRallyShow'],
            ['name' => 'MotoSport.tv', 'url' => 'https://www.youtube.com/c/MotoSporttv'],
            ['name' => 'WRC - World Rally Championship', 'url' => 'https://www.youtube.com/user/OfficialWRC'],
            ['name' => 'Autosport', 'url' => 'https://www.youtube.com/user/autosportdotcom'],
            ['name' => 'Rally Raid TV', 'url' => 'https://www.youtube.com/c/RallyRaidTV'],
            ['name' => 'FIA ERC', 'url' => 'https://www.youtube.com/c/fiamotor1'],
            ['name' => 'Motorsport UK', 'url' => 'https://www.youtube.com/c/MotorsportUK'],
            ['name' => 'GNN - Garage Nation Network', 'url' => 'https://www.youtube.com/c/GarageNationNetwork'],
            ['name' => 'MotoMan TV', 'url' => 'https://www.youtube.com/c/MotoManTV'],
            ['name' => 'RallyX Nordic', 'url' => 'https://www.youtube.com/c/RallyxNordic'],
            ['name' => 'Official Dakar Rally', 'url' => 'https://www.youtube.com/user/DakarRally'],
        ],
        'Communities & Forums' => [
            ['name' => 'Reddit Rally Community', 'url' => 'https://www.reddit.com/r/rally/'],
            ['name' => 'Rally Raid Forum', 'url' => 'https://www.rallyraidforum.com/'],
            ['name' => 'RallySportsForum', 'url' => 'https://www.rallysportsforum.com/'],
            ['name' => 'Classic Rally Forum', 'url' => 'https://www.classicrallyforum.co.uk/'],
            ['name' => 'Facebook Rally Fans Group', 'url' => 'https://www.facebook.com/groups/rallyfans'],
            ['name' => 'Motorsport Forums', 'url' => 'https://www.motorsportforums.com/'],
            ['name' => 'PistonHeads Motorsport', 'url' => 'https://www.pistonheads.com/gassing/topic.asp?h=0&f=3&t=4569115'],
            ['name' => 'RallyX Nordic Forum', 'url' => 'https://forum.rallyx.se/'],
            ['name' => 'RallyeForum', 'url' => 'https://www.rallyeforum.com/'],
            ['name' => 'RallyeSport Magazine Forum', 'url' => 'https://www.rallyesportmag.com/forum/'],
            ['name' => 'RallyeNet', 'url' => 'https://www.rallyenet.com/'],
            ['name' => 'RallyeForum.de', 'url' => 'https://www.rallyeforum.de/'],
        ],
        'Rally History & Archives' => [
            ['name' => 'eWRC-Results.com', 'url' => 'https://www.ewrc-results.com/'],
            ['name' => 'RallyBase.nl', 'url' => 'https://www.rallybase.nl/'],
            ['name' => 'Jonkka’s Rally Archive', 'url' => 'https://www.jonkka.com/'],
            ['name' => 'Historic Rally Photos', 'url' => 'https://historicrallyphotos.com/'],
            ['name' => 'Rally Legends Facebook Group', 'url' => 'https://www.facebook.com/groups/rallylegends/'],
            ['name' => 'Classic Rally News', 'url' => 'https://classicsrallynews.com/'],
            ['name' => 'Rally Archive YouTube Channel', 'url' => 'https://www.youtube.com/c/RallyArchive/'],
            ['name' => 'The Vintage Rally Hub', 'url' => 'https://vintagerallyhub.com/'],
            ['name' => 'Rallye Historique', 'url' => 'https://www.rallyehistorique.com/'],
            ['name' => 'RallyeSport Magazine Archive', 'url' => 'https://www.rallyesportmag.com/archive/'],
            ['name' => 'RallyeForum History Section', 'url' => 'https://www.rallyeforum.com/history/'],
            ['name' => 'RallyeSport.de', 'url' => 'https://www.rallyesport.de/'],
        ],
        'Rally Merchandise' => [
            ['name' => 'RallySportDirect', 'url' => 'https://www.rallysportdirect.com/'],
            ['name' => 'RallyShop', 'url' => 'https://www.rallyshop.com/'],
            ['name' => 'RallyGear', 'url' => 'https://www.rallygear.com/'],
            ['name' => 'Motorsport.com Store', 'url' => 'https://store.motorsport.com/'],
            ['name' => 'Red Bull Racing Store', 'url' => 'https://store.redbullracing.com/'],
            ['name' => 'WRC Official Store', 'url' => 'https://shop.wrc.com/'],
            ['name' => 'M-Sport Shop', 'url' => 'https://shop.m-sport.co.uk/'],
            ['name' => 'Sparco Racing Gear', 'url' => 'https://www.sparco-official.com/'],
            ['name' => 'OMP Racing', 'url' => 'https://www.ompracing.com/'],
            ['name' => 'P1 Race Gear', 'url' => 'https://p1racegear.com/'],
            ['name' => 'Rally Monkey', 'url' => 'https://rallymonkey.com/'],
            ['name' => 'Fanatec Rally Collection', 'url' => 'https://fanatec.com/rally'],
            ['name' => 'Subaru Motorsports Store', 'url' => 'https://www.subaru.com/enthusiasts/motorsports.html'],
            ['name' => 'Ford Performance Store', 'url' => 'https://performanceparts.ford.com/'],
        ],
    ];
@endphp

@push('head')
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Compromised Internals">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image"       content="{{ $seo['image'] }}">
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold mb-6 text-center">Rally Resources</h1>
    <p class="text-center mb-12 text-gray-700">
        Explore these curated websites, YouTube channels, and social communities dedicated to rally racing.
    </p>

    @foreach ($resources as $category => $links)
    <div x-data="{ open: false }" class="mb-6 border rounded-lg shadow-sm">
        <button
            @click="open = !open"
            class="w-full flex justify-between items-center px-4 py-3 text-xl font-semibold bg-gray-100 hover:bg-gray-200 focus:outline-none"
            aria-expanded="false"
            :aria-expanded="open.toString()"
        >
            {{ $category }}
            <svg
                :class="{ 'rotate-180': open }"
                class="h-5 w-5 transition-transform"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <ul
            x-show="open"
            x-transition
            class="px-6 py-4 text-blue-600 list-disc list-inside space-y-2"
            style="display: none;"
        >
            @foreach ($links as $link)
            <li>
                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                    {{ $link['name'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endforeach
</div>
@endsection