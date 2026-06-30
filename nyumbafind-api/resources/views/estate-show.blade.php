@extends('layouts.app')

@section('title', 'Estate Details - NyumbaFind')

@section('content')

    <a href="{{ url('/') }}" class="text-sm text-emerald-600 hover:underline">&larr; Back to search</a>

    <div id="estate-loading" class="text-center text-gray-500 py-10">
        Loading...
    </div>

    <div id="estate-error" class="text-center text-red-600 py-10 hidden">
        Estate not found.
    </div>

    <div id="estate-detail" class="hidden mt-4">
        <h1 id="estate-name" class="text-3xl font-bold"></h1>
        <p id="estate-location" class="text-gray-600 mt-1"></p>

        <div class="mt-6 bg-white border rounded-lg p-6">
            <p class="text-sm text-gray-500">Active listings</p>
            <p id="estate-listing-count" class="text-2xl font-semibold text-emerald-600"></p>
        </div>

        <div class="mt-8 bg-gray-100 rounded-lg p-6 text-center text-gray-500">
            No listings yet in this estate. Be the first landlord to list here.
        </div>
    </div>

    <script>
        const slug = @json($slug);

        fetch(`/api/estates/${slug}`)
            .then(res => {
                if (!res.ok) throw new Error('Not found');
                return res.json();
            })
            .then(data => {
                document.getElementById('estate-loading').classList.add('hidden');
                document.getElementById('estate-detail').classList.remove('hidden');

                document.getElementById('estate-name').textContent = data.estate.name;
                document.getElementById('estate-location').textContent =
                    `${data.estate.sub_county}, ${data.estate.county}`;
                document.getElementById('estate-listing-count').textContent = data.listing_count;
            })
            .catch(err => {
                document.getElementById('estate-loading').classList.add('hidden');
                document.getElementById('estate-error').classList.remove('hidden');
                console.error(err);
            });
    </script>

@endsection