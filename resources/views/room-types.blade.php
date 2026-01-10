@extends('layouts.user')

@section('title', 'Room Categories')
@section('subtitle', 'Explore our diverse range of study spaces designed for your productivity')

@section('breadcrumb')
    <li><a href="{{ route('user.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">Dashboard</a></li>
    <li class="text-gray-500">Room Types</li>
@endsection

@section('content')
<style>
    .category-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: #6366f1;
    }

    .icon-box {
        width: 50px;
        height: 50px;
        background: #eef2ff;
        color: #6366f1;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .room-count-badge {
        background: #f8fafc;
        color: #64748b;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="max-w-6xl mx-auto">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">Available Categories</h3>
            <p class="text-slate-500 text-sm">Find the perfect environment for your study session.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100 flex items-center gap-3">
                <span class="text-indigo-600 font-bold text-lg">{{ $roomTypes->count() }}</span>
                <span class="text-slate-400 text-xs uppercase tracking-wider font-semibold">Total Types</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($roomTypes as $type)
            <div class="category-card flex flex-col h-full">
                <div class="p-6 pb-0 flex justify-between items-start">
                    <div class="icon-box">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="room-count-badge">
                        <i class="fas fa-door-open mr-1 text-indigo-400"></i> 
                        {{ $type->rooms->count() }} Rooms
                    </span>
                </div>

                <div class="p-6 flex-grow">
                    <h4 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">
                        {{ $type->name }}
                    </h4>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        {{ Str::limit($type->description, 120, '...') }}
                    </p>
                </div>

                <div class="p-6 pt-0 mt-auto">
                    <hr class="border-slate-100 mb-4">
                    <a href="{{ route('user.rooms', ['category' => $type->id]) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-slate-50 hover:bg-indigo-600 hover:text-white text-indigo-600 font-bold text-sm rounded-xl transition-all">
                        View Available Rooms
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <div class="text-slate-300 text-5xl mb-4">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">No categories found</h3>
                <p class="text-slate-400">Check back later for updated room types.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection