<div x-data="{
    message: @entangle('message'),
    show: false
}" x-init="$watch('message', value => {
    if (value) {
        show = true;
        setTimeout(() => {
            show = false
            message = '' // reset message ke kosong setelah hilang
        }, 3000)
    }
})" x-show="show" x-transition.opacity.duration.300ms>
    @php
        $colors = [
            'info' => ['text' => 'text-blue-800', 'bg' => 'bg-blue-50'],
            'danger' => ['text' => 'text-red-800', 'bg' => 'bg-red-50'],
            'success' => ['text' => 'text-green-800', 'bg' => 'bg-green-50'],
            'warning' => ['text' => 'text-yellow-800', 'bg' => 'bg-yellow-50'],
            'dark' => ['text' => 'text-gray-800', 'bg' => 'bg-gray-50'],
        ];
        $c = $colors[$type] ?? $colors['info'];
    @endphp

    <div class="p-4 mb-4 text-sm {{ $c['text'] }} rounded-lg {{ $c['bg'] }} shadow" role="alert">
        <span class="font-bold">{{ $title }}!</span> {{ $message }}
    </div>
</div>
