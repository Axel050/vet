@props(['title', 'description'])

<div class="flex w-full flex-col text-center">
    <h1 class="text-2xl font-semibold text-white">{{ $title }}</h1>
    <p class="text-gray-400 mt-1">{{ $description }}</p>
</div>
