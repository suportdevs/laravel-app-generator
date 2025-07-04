<option value="{{ $category->id }}" {{($category->id == ($data->id ?? NULL) || in_array($category->id, ($selectedCategories ?? []))) ? 'selected' : ''}}>
    {{ str_repeat('--', $level) }} {{ $category->name }}
</option>

@if ($category->children && $category->children->isNotEmpty())
    @foreach ($category->children as $child)
        @include('admin.layouts.partials.category-option', ['category' => $child, 'level' => $level + 1])
    @endforeach
@endif
