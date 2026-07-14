@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20 flex-1 flex flex-col justify-center text-center space-y-6">
    <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-3xl mx-auto shadow-sm">
        🔒
    </div>
    <div class="space-y-2">
        <h1 class="text-3xl font-black font-display text-slate-800 uppercase tracking-tight">Access Denied</h1>
        <p class="text-xs text-slate-400">{{ $exception->getMessage() ?: 'You do not have the necessary administrative permissions to view this secure resource.' }}</p>
    </div>
    <div class="flex items-center justify-center gap-3 pt-2">
        <a href="/" class="btn-primary">Back to Home</a>
        <a href="{{ route('login') }}" class="btn-outline">Login Screen</a>
    </div>
</div>
@endsection
