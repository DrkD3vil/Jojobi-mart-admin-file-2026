@foreach ($categories as $cat)
    <tr class="category-row animate-fade-in" style="animation-delay: {{ $loop->index * 0.05 }}s">
        <td class="category-name">
            <div class="name-content">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                    </svg>
                </div>
                <div>
                    <strong>{{ $cat->name }}</strong>
                    <small class="category-meta">{{ $cat->created_at->format('M d, Y') }}</small>
                </div>
            </div>
        </td>

        <td class="parent-category">
            @if ($cat->parent)
                <span class="parent-tag">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                    </svg>
                    {{ $cat->parent->name }}
                </span>
            @else
                <span class="root-tag">Root Category</span>
            @endif
        </td>

        <td class="barcode-cell">
            @if ($cat->barcode_svg)
                <div class="barcode-preview" onclick="zoomBarcode(this)">
                    <img src="{{ asset('storage/' . $cat->barcode_svg) }}" alt="Barcode"
                        loading="lazy" class="barcode-image">
                    <div class="zoom-hint">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </div>
                </div>
            @else
                <span class="no-barcode">No barcode</span>
            @endif
        </td>

        <td class="image-cell">
            @if ($cat->image)
                <div class="image-preview" onclick="zoomImage(this)">
                    <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}"
                        loading="lazy" class="category-image">
                    <div class="zoom-hint">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </div>
                </div>
            @else
                <div class="no-image">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                    </svg>
                </div>
            @endif
        </td>

        <td class="actions-cell">
            <div class="action-buttons">
                <a href="{{ route('categories.edit', $cat) }}" class="btn-action btn-edit"
                    title="Edit">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                    </svg>
                    Edit
                </a>

                <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                    class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-action btn-delete"
                        onclick="confirmDelete(this)" title="Delete">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach
