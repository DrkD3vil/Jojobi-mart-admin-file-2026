@props(['title','value','money'=>false,'positive'=>false,'negative'=>false])

<div class="bg-white p-4 rounded shadow">
    <p class="text-gray-500 text-sm">{{ $title }}</p>
    <h2 class="text-2xl font-bold
        {{ $positive?'text-green-600':'' }}
        {{ $negative?'text-red-600':'' }}">
        {{ $money?number_format($value,2):$value }}
    </h2>
</div>
