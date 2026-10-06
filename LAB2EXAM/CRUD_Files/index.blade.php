@extends('items.layout')

@section('content')
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <!-- Header banner inside card -->
    <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Inventory Items</h1>
            <p class="text-xs text-gray-500 mt-1">Manage and track your list of items easily</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full w-fit">
            <i class="fa-solid fa-layer-group text-gray-500"></i>
            Total: {{ count($items) }} item(s)
        </span>
    </div>

    @if($items->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-16 px-4">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-regular fa-folder-open"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900">No items found</h3>
            <p class="text-sm text-gray-500 mt-1 mb-6">Get started by creating your very first inventory item.</p>
            <a href="{{ route('items.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Add Item Now</span>
            </a>
        </div>
    @else
        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">ID</th>
                        <th class="py-3.5 px-6">Item Name</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6">Price</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @foreach ($items as $item)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-4 px-6 text-gray-400 font-mono text-xs">#{{ $item->id }}</td>
                        <td class="py-4 px-6 font-semibold text-gray-900">{{ $item->name }}</td>
                        <td class="py-4 px-6 text-gray-600 max-w-xs truncate">{{ $item->description }}</td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold text-xs rounded-md">
                                ${{ number_format($item->price, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('items.edit', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition"
                                   title="Edit Item">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition"
                                            title="Delete Item">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
