@extends('items.layout')

@section('content')
<div class="max-w-xl mx-auto">
    <!-- Back Link -->
    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 mb-4 transition font-medium">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to items list</span>
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900">Edit Item #{{ $item->id }}</h1>
            <p class="text-xs text-gray-500 mt-1">Modify details for this item below.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                <div class="font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside pl-1 mt-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('items.update', $item->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Item Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required>
            </div>

            <div>
                <label for="price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Price ($)</label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $item->price) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required>{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('items.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Update Item</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
