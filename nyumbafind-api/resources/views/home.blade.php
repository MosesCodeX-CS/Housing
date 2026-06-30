@extends('layouts.app')

@section('title', 'NyumbaFind - Search Rentals in Kenya')

@section('content')

    <div class="text-center py-10">
        <h1 class="text-3xl font-bold mb-2">Find a place to rent, hyperlocal.</h1>
        <p class="text-gray-600">Verified listings across Nairobi's most popular estates.</p>
    </div>

    <div id="estates-loading" class="text-center text-gray-500 py-10">
        Loading estates...
    </div>

    <div id="estates-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 hidden">
        <!-- populated by JS -->
    </div>

    <div id="estates-error" class="text-center text-red-600 py-10 hidden">
        Couldn't load estates. Is the API running?
    </div>

    <script>
        fetch('/api/estates')
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(estates => {
                document.getElementById('estates-loading').classList.add('hidden');

                if (!estates.length) {
                    document.getElementById('estates-error').textContent = 'No estates found yet.';
                    document.getElementById('estates-error').classList.remove('hidden');
                    return;
                }

                const grid = document.getElementById('estates-grid');
                grid.classList.remove('hidden');

                estates.forEach(estate => {
                    const card = document.createElement('a');
                    card.href = `/estates/${estate.slug}`;
                    card.className = 'block bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition';
                    card.innerHTML = `
                        <h3 class="font-semibold text-lg">${estate.name}</h3>
                        <p class="text-sm text-gray-500">${estate.sub_county}, ${estate.county}</p>
                        <p class="text-sm text-emerald-600 mt-2">${estate.listing_count} listing${estate.listing_count === 1 ? '' : 's'}</p>
                    `;
                    grid.appendChild(card);
                });
            })
            .catch(err => {
                document.getElementById('estates-loading').classList.add('hidden');
                document.getElementById('estates-error').classList.remove('hidden');
                console.error(err);
            });
    </script>

@endsection