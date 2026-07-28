@props([
    'disabled' => false,
    'error' => false,
])

<input 
    @disabled($disabled) 
    {{ $attributes->merge([
        'class' => 'w-full rounded-xl border ' . ($error ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20') . ' bg-slate-50/50 text-slate-900 text-sm p-3 focus:bg-white focus:outline-none focus:ring-4 transition-all duration-200 placeholder:text-slate-400 disabled:bg-slate-100 disabled:cursor-not-allowed disabled:text-slate-400 shadow-sm'
    ]) }}
>