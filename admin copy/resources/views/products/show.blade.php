@extends('layouts.app')

@section('content')
<style>
    /* ===== CSS Variables & Base Styles ===== */
    :root {
        --radius: 0.75rem;
        --transition-fast: 150ms ease;
        --transition-normal: 250ms ease;
        --transition-slow: 350ms ease;

        /* Dark theme (default) */
        --bg-primary: #0a0a0a;
        --bg-secondary: #1a1a1a;
        --bg-tertiary: #2a2a2a;
        --text-primary: #ffffff;
        --text-secondary: #a1a1aa;
        --text-muted: #71717a;
        --border-color: #3f3f46;
        --accent-color: #3b82f6;
        --accent-hover: #2563eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;

        --card-shadow: 0 1px 3px rgba(0,0,0,0.3);
        --card-shadow-hover: 0 10px 25px rgba(0,0,0,0.5);
        --dropdown-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
        --accent-glow: rgba(59, 130, 246, 0.2);
    }

    [data-theme="light"] {
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --bg-tertiary: #f1f5f9;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border-color: #e2e8f0;
        --accent-color: #3b82f6;
        --accent-hover: #2563eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;

        --card-shadow: 0 1px 3px rgba(0,0,0,0.1);
        --card-shadow-hover: 0 10px 25px rgba(0,0,0,0.15);
        --dropdown-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        --accent-glow: rgba(59, 130, 246, 0.1);
    }

    body {
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: background-color var(--transition-normal);
    }

    /* Fix Bootstrap dropdown z-index issue */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        z-index: 1000;
        display: none;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        box-shadow: var(--dropdown-shadow);
        min-width: 200px;
        padding: 0.5rem;
    }

    .dropdown-menu.show {
        display: block;
    }

    /* Fix modal backdrop */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    /* ===== Page Header ===== */
    .page-header {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
    }

    .header-grid {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 768px) {
        .header-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    .product-avatar {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        overflow: hidden;
        background: var(--bg-tertiary);
        border: 2px solid var(--border-color);
        position: relative;
        flex-shrink: 0;
    }

    .avatar-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }

    .avatar-placeholder svg {
        width: 40px;
        height: 40px;
        opacity: 0.5;
    }

    .header-content {
        min-width: 0;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
        line-height: 1.2;
        overflow-wrap: break-word;
    }

    .header-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-badge.active {
        background: var(--success-color);
        color: white;
    }

    .status-badge.inactive {
        background: var(--danger-color);
        color: white;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .barcode-container {
        display: inline-block;
        background: white;
        padding: 0.5rem;
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all var(--transition-fast);
        white-space: nowrap;
        background: transparent;
        font-family: inherit;
    }

    .btn-primary {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--accent-hover);
        border-color: var(--accent-hover);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }

    .btn-outline {
        background: transparent;
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    .btn-outline:hover {
        background: var(--bg-tertiary);
        border-color: var(--accent-color);
    }

    .btn-icon {
        padding: 0.5rem;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon svg {
        width: 20px;
        height: 20px;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0.5rem 0.75rem;
        color: var(--text-primary);
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: background-color var(--transition-fast);
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }

    .dropdown-item:hover {
        background: var(--bg-tertiary);
    }

    .dropdown-item.danger {
        color: var(--danger-color);
    }

    .dropdown-item.danger:hover {
        background: rgba(239, 68, 68, 0.1);
    }

    .dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 0.5rem 0;
        border: none;
    }

    /* ===== Metrics Grid ===== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1.25rem;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem;
        align-items: center;
        transition: all var(--transition-normal);
        box-shadow: var(--card-shadow);
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
        border-color: var(--accent-color);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .metric-icon.stock { background: linear-gradient(135deg, var(--accent-color), var(--info-color)); }
    .metric-icon.batches { background: linear-gradient(135deg, var(--warning-color), #fbbf24); }
    .metric-icon.expiry { background: linear-gradient(135deg, var(--success-color), #34d399); }
    .metric-icon.images { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

    .metric-content {
        overflow: hidden;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.125rem;
        line-height: 1;
    }

    .metric-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== Main Content Grid ===== */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ===== Product Details Card ===== */
    .details-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-secondary);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    @media (max-width: 640px) {
        .card-body {
            padding: 1rem;
        }
    }

    .image-container {
        aspect-ratio: 16/9;
        background: var(--bg-tertiary);
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 1rem;
        position: relative;
    }

    .main-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: var(--bg-tertiary);
        transition: opacity var(--transition-fast);
    }

    .no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }

    .no-image svg {
        width: 64px;
        height: 64px;
        opacity: 0.3;
    }

    .thumbnails {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.25rem 0;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-color) var(--bg-tertiary);
    }

    .thumbnails::-webkit-scrollbar {
        height: 6px;
    }

    .thumbnails::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 3px;
    }

    .thumbnails::-webkit-scrollbar-thumb {
        background: var(--accent-color);
        border-radius: 3px;
    }

    .thumbnail {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        flex-shrink: 0;
        background: var(--bg-tertiary);
        transition: all var(--transition-fast);
        position: relative;
    }

    .thumbnail:hover {
        border-color: var(--accent-color);
        transform: translateY(-2px);
    }

    .thumbnail.active {
        border-color: var(--accent-color);
    }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-meta {
        display: grid;
        gap: 1rem;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .meta-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .meta-value {
        font-size: 0.875rem;
        color: var(--text-primary);
        line-height: 1.5;
    }

    .meta-value.empty {
        color: var(--text-muted);
        font-style: italic;
    }

    /* ===== Batches Card ===== */
    .batches-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 100%;
        overflow-y: auto;
        padding-right: 0.25rem;
        scrollbar-width: thin;
        scrollbar-color: var(--accent-color) var(--bg-tertiary);
    }

    .batches-container::-webkit-scrollbar {
        width: 6px;
    }

    .batches-container::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 3px;
    }

    .batches-container::-webkit-scrollbar-thumb {
        background: var(--accent-color);
        border-radius: 3px;
    }

    .batch-card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all var(--transition-normal);
    }

    .batch-card:hover {
        border-color: var(--accent-color);
        box-shadow: var(--card-shadow-hover);
    }

    .batch-card.out-of-stock {
        border-left: 4px solid var(--danger-color);
        opacity: 0.8;
    }

    .batch-card.expired {
        border-left: 4px solid var(--warning-color);
    }

    .batch-card.warning {
        border-left: 4px solid var(--warning-color);
    }

    .batch-summary {
        padding: 1rem;
        cursor: pointer;
        transition: background-color var(--transition-fast);
    }

    .batch-summary:hover {
        background-color: var(--bg-tertiary);
    }

    .batch-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .batch-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }
    }

    .batch-title {
        flex: 1;
        min-width: 0;
    }

    .batch-number {
        display: block;
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .batch-date {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .batch-status {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .batch-status {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .quantity-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
        display: inline-block;
    }

    .quantity-badge.in-stock {
        background: var(--success-color);
        color: white;
    }

    .quantity-badge.out-of-stock {
        background: var(--danger-color);
        color: white;
    }

    .quantity-badge.warning {
        background: var(--warning-color);
        color: white;
    }

    .expiry-badge {
        font-size: 0.75rem;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .expiry-badge.expired {
        color: var(--danger-color);
        font-weight: 600;
    }

    .expiry-badge.warning {
        color: var(--warning-color);
        font-weight: 600;
    }

    .batch-preview {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .price-info {
        display: flex;
        gap: 1.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    @media (max-width: 640px) {
        .price-info {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    .price-item {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .price-label {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .price-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .toggle-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
        flex-shrink: 0;
    }

    .toggle-btn:hover {
        background: var(--bg-tertiary);
        border-color: var(--accent-color);
        color: var(--text-primary);
    }

    .toggle-btn svg {
        width: 16px;
        height: 16px;
        transition: transform var(--transition-normal);
    }

    .batch-card.active .toggle-btn svg {
        transform: rotate(180deg);
    }

    .batch-details {
        padding: 1.25rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-tertiary);
        overflow: hidden;
        max-height: 0;
        transition: max-height var(--transition-normal) ease-out;
    }

    .batch-card.active .batch-details {
        max-height: 1000px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    .detail-section h6 {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .detail-value {
        font-size: 0.875rem;
        color: var(--text-primary);
        font-weight: 500;
        text-align: right;
    }

    .status-tag {
        padding: 0.125rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        display: inline-block;
    }

    .status-tag.active {
        background: var(--success-color);
        color: white;
    }

    .status-tag.inactive {
        background: var(--danger-color);
        color: white;
    }

    .expiry-warning {
        color: var(--danger-color);
        font-weight: 600;
        margin-left: 0.25rem;
        font-size: 0.75rem;
    }

    .batch-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 640px) {
        .batch-actions {
            flex-direction: column;
        }
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all var(--transition-fast);
        white-space: nowrap;
        cursor: pointer;
        font-family: inherit;
    }

    .action-btn:hover {
        background: var(--bg-primary);
        border-color: var(--accent-color);
        transform: translateY(-1px);
    }

    .action-btn.danger:hover {
        border-color: var(--danger-color);
        color: var(--danger-color);
    }

    .action-btn svg {
        width: 16px;
        height: 16px;
    }

    .filters-bar {
        display: flex;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .filters-bar {
            padding: 0.75rem;
        }
    }

    .filter-btn {
        padding: 0.375rem 0.75rem;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        white-space: nowrap;
    }

    .filter-btn:hover {
        background: var(--bg-secondary);
        border-color: var(--accent-color);
        color: var(--text-primary);
    }

    .filter-btn.active {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: var(--text-secondary);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state p {
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
    }

    /* ===== Sidebar Cards ===== */
    .sidebar-section {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1.25rem;
    }

    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }

    .summary-item {
        text-align: center;
        padding: 1rem;
        background: var(--bg-tertiary);
        border-radius: var(--radius);
    }

    .summary-value {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        line-height: 1;
    }

    .summary-value.success { color: var(--success-color); }
    .summary-value.warning { color: var(--warning-color); }
    .summary-value.danger { color: var(--danger-color); }

    .summary-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .total-value {
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--accent-color), var(--info-color));
        color: white;
        text-align: center;
    }

    .value-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
    }

    .value-amount {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .actions-list {
        padding: 0.75rem;
    }

    .action-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        text-decoration: none;
        transition: all var(--transition-fast);
        margin-bottom: 0.5rem;
        cursor: pointer;
        border: none;
        background: transparent;
        width: 100%;
        text-align: left;
    }

    .action-item:hover {
        background: var(--bg-secondary);
        border-color: var(--accent-color);
        transform: translateX(4px);
    }

    .action-item:last-child {
        margin-bottom: 0;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-icon svg {
        width: 20px;
        height: 20px;
        color: var(--accent-color);
    }

    .action-content {
        flex: 1;
        min-width: 0;
    }

    .action-title {
        font-weight: 600;
        margin-bottom: 0.125rem;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .action-desc {
        font-size: 0.75rem;
        color: var(--text-secondary);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .activity-list {
        padding: 0.75rem;
    }

    .activity-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        position: relative;
    }

    .activity-item:not(:last-child) {
        border-bottom: 1px solid var(--border-color);
    }

    .activity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .activity-dot.created { background: var(--accent-color); }
    .activity-dot.updated { background: var(--warning-color); }
    .activity-dot.batch { background: var(--success-color); }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 0.125rem;
        font-size: 0.875rem;
        color: var(--text-primary);
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* ===== Modal Styles ===== */
    .modal-content {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
    }

    .modal-header {
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        color: var(--text-primary);
        font-weight: 600;
        margin: 0;
        font-size: 1.25rem;
    }

    .btn-close {
        filter: invert(1);
        opacity: 0.7;
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    @media (max-width: 640px) {
        .modal-footer {
            flex-direction: column;
        }
    }

    /* ===== Animations ===== */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ===== Utility Classes ===== */
    .d-flex { display: flex; }
    .d-grid { display: grid; }
    .flex-column { flex-direction: column; }
    .align-items-center { align-items: center; }
    .justify-content-between { justify-content: space-between; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 1rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .text-center { text-align: center; }
    .text-muted { color: var(--text-muted); }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .fw-600 { font-weight: 600; }
    .fw-700 { font-weight: 700; }
</style>

<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="header-grid">
            <div class="product-avatar">
                @if ($product->images->count() > 0)
                    @php
                        $primaryImage = $product->images->firstWhere('is_primary', true);
                        $fallbackImage = $product->images->first();
                        $imagePath = $primaryImage->image_path ?? $fallbackImage->image_path;
                    @endphp
                    <img src="{{ asset('storage/' . $imagePath) }}"
                         alt="{{ $product->name }}"
                         class="avatar-image"
                         loading="lazy"
                         onerror="this.src='{{ asset('images/placeholders/product.png') }}'">
                @else
                    <div class="avatar-placeholder">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="header-content">
                <h1 class="page-title">{{ $product->name }}</h1>
                <div class="header-meta">
                    <div class="status-badge {{ $product->is_active ? 'active' : 'inactive' }}">
                        <span class="status-dot"></span>
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </div>

                    @if($product->category)
                        <span class="text-sm text-muted">{{ $product->category->name }}</span>
                    @endif

                    @if($product->brand)
                        <span class="text-sm text-muted">{{ $product->brand->name }}</span>
                    @endif
                </div>

                @if($product->barcode)
                    <div class="barcode-container mt-2">
                        {!! DNS1D::getBarcodeSVG($product->barcode, 'C128', 1.5, 40, 'black', true) !!}
                    </div>
                @endif
            </div>

            <div class="header-actions">
                <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                   class="btn btn-primary"
                   id="addBatchBtn">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Add Batch
                </a>

                <div class="dropdown">
                    <button class="btn btn-outline btn-icon" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('products.edit', $product) }}">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                            Edit Product
                        </a>
                        <a class="dropdown-item" href="{{ route('products.images.index', $product) }}">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                            Manage Images
                        </a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item danger" onclick="confirmProductDelete()">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                            Delete Product
                        </button>
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="btn btn-outline">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon stock">
                <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                    <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h10v2H4z"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ number_format($product->totalStockQuantity()) }}</div>
                <div class="metric-label">Total Stock Quantity</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon batches">
                <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                    <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ $product->batches->count() }}</div>
                <div class="metric-label">Active Batches</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon expiry">
                <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                    <path d="M17 12c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm1.65 7.35L16.5 17.2V14h1v2.79l1.85 1.85-.7.71zM18 3h-3.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H6c-1.1 0-2 .9-2 2v15c0 1.1.9 2 2 2h6.11c-1.26-1.29-2-3-2-4.89 0-3.87 3.13-7 7-7 1.9 0 3.62.8 4.89 2H18V3zm-6 2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-value">
                    @php
                        $nextExpiry = $product->batches
                            ->whereNotNull('expiry_date')
                            ->where('quantity', '>', 0)
                            ->where('expiry_date', '>=', now())
                            ->sortBy('expiry_date')
                            ->first();

                        if($nextExpiry) {
                            $daysUntilExpiry = now()->diffInDays($nextExpiry->expiry_date);
                            if($daysUntilExpiry <= 7) {
                                echo '<span class="text-warning">' . $nextExpiry->expiry_date->format('d M') . '</span>';
                            } else {
                                echo $nextExpiry->expiry_date->format('d M');
                            }
                        } else {
                            echo 'No expiry';
                        }
                    @endphp
                </div>
                <div class="metric-label">Next Expiry Date</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon images">
                <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                    <path d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4.86 8.86l-3 3.87L9 13.14 6 17h12l-3.86-5.14z"/>
                </svg>
            </div>
            <div class="metric-content">
                <div class="metric-value">{{ $product->images->count() }}</div>
                <div class="metric-label">Product Images</div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        {{-- Left Column --}}
        <div class="left-column">
            {{-- Product Details --}}
            <div class="details-card">
                <div class="card-header">
                    <h5 class="card-title">Product Details</h5>
                    <button class="toggle-btn" onclick="toggleProductDetails()" aria-label="Toggle product details">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                        </svg>
                    </button>
                </div>
                <div class="card-body" id="productDetailsContent">
                    <div class="row g-3">
                        <div class="col-md-6">
                            @if ($product->images->count() > 0)
                                <div class="image-container">
                                    @php
                                        $primaryImage = $product->images->firstWhere('is_primary', true);
                                        $fallbackImage = $product->images->first();
                                        $imagePath = $primaryImage->image_path ?? $fallbackImage->image_path;
                                    @endphp
                                    <img id="mainProductImage"
                                         src="{{ asset('storage/' . $imagePath) }}"
                                         alt="{{ $product->name }}"
                                         class="main-image"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('images/placeholders/product.png') }}'">
                                </div>

                                @if($product->images->count() > 1)
                                    <div class="thumbnails">
                                        @foreach($product->images as $image)
                                            <div class="thumbnail {{ $loop->first ? 'active' : '' }}"
                                                 onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}', this)">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                     alt="Product Image {{ $loop->iteration }}"
                                                     loading="lazy">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="image-container">
                                    <div class="no-image">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="product-meta">
                                <div class="meta-item">
                                    <div class="meta-label">SKU</div>
                                    <div class="meta-value {{ empty($product->sku) ? 'empty' : '' }}">
                                        {{ $product->sku ?? '—' }}
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-label">Category</div>
                                    <div class="meta-value {{ empty($product->category) ? 'empty' : '' }}">
                                        {{ $product->category->name ?? '—' }}
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-label">Brand</div>
                                    <div class="meta-value {{ empty($product->brand) ? 'empty' : '' }}">
                                        {{ $product->brand->name ?? '—' }}
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-label">Description</div>
                                    <div class="meta-value {{ empty($product->description) ? 'empty' : '' }}">
                                        {{ $product->description ?? '—' }}
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-label">Notes</div>
                                    <div class="meta-value {{ empty($product->note) ? 'empty' : '' }}">
                                        {{ $product->note ?? '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Batches List --}}
            <div class="details-card">
                <div class="card-header">
                    <h5 class="card-title">Product Batches</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline btn-icon" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                <a class="dropdown-item filter-option active" href="#" data-filter="all">All Batches</a>
                                <a class="dropdown-item filter-option" href="#" data-filter="in-stock">In Stock</a>
                                <a class="dropdown-item filter-option" href="#" data-filter="expired">Expired</a>
                                <a class="dropdown-item filter-option" href="#" data-filter="out-of-stock">Out of Stock</a>
                                <a class="dropdown-item filter-option" href="#" data-filter="warning">Expiring Soon</a>

                            </div>
                        </div>
                        <button class="toggle-btn" onclick="toggleAllBatches()" title="Expand/Collapse All" aria-label="Expand or collapse all batches">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                                <path d="M8 12h8v2H8zm10 6H6v2h12zm0-8H6v2h12z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="filters-bar">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="in-stock">In Stock</button>
                    <button class="filter-btn" data-filter="expired">Expired</button>
                    <button class="filter-btn" data-filter="out-of-stock">Out of Stock</button>
                    <button class="filter-btn" data-filter="warning">Expiring Soon</button>
                </div>

                <div class="card-body p-0">
                    @if ($product->batches->count() > 0)
                        <div class="batches-container" id="batchesContainer">
                            @foreach ($product->batches->sortByDesc('created_at') as $batch)
                                @php
                                    $batchStatus = 'in-stock';
                                    $expiryStatus = '';

                                    if ($batch->quantity <= 0) {
                                        $batchStatus = 'out-of-stock';
                                    } elseif ($batch->expiry_date && $batch->expiry_date->lt(now())) {
                                        $batchStatus = 'expired';
                                        $expiryStatus = 'expired';
                                    } elseif ($batch->expiry_date && $batch->expiry_date->diffInDays(now()) <= 7) {
                                        $batchStatus = 'warning';
                                        $expiryStatus = 'warning';
                                    }

                                    $margin = $batch->sell_price - $batch->buy_price;
                                    $marginPercentage = $batch->buy_price > 0 ? ($margin / $batch->buy_price) * 100 : 0;
                                @endphp

                                <div class="batch-card {{ $batchStatus }}"
                                     data-batch-status="{{ $batchStatus }}"
                                     data-batch-id="{{ $batch->id }}">
                                    <div class="batch-summary" onclick="toggleBatchDetails(this)">
                                        <div class="batch-header">
                                            <div class="batch-title">
                                                <span class="batch-number">
                                                    {{ $batch->batch_no ?? 'Batch #' . $batch->id }}
                                                </span>
                                                <span class="batch-date">
                                                    Added: {{ $batch->created_at->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="batch-status">
                                                <span class="quantity-badge {{ $batchStatus }}">
                                                    {{ $batch->quantity }} units
                                                </span>
                                                <span class="expiry-badge {{ $expiryStatus }}">
                                                    @if($batch->expiry_date)
                                                        Expires: {{ $batch->expiry_date->format('d M Y') }}
                                                    @else
                                                        No expiry
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="batch-preview">
                                            <div class="price-info">
                                                <div class="price-item">
                                                    <span class="price-label">Buy Price</span>
                                                    <span class="price-value">${{ number_format($batch->buy_price, 2) }}</span>
                                                </div>
                                                <div class="price-item">
                                                    <span class="price-label">Sell Price</span>
                                                    <span class="price-value">${{ number_format($batch->sell_price, 2) }}</span>
                                                </div>
                                                <div class="price-item">
                                                    <span class="price-label">Margin</span>
                                                    <span class="price-value">{{ number_format($marginPercentage, 1) }}%</span>
                                                </div>
                                            </div>
                                            <button class="toggle-btn" aria-label="Toggle batch details">
                                                <svg viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="batch-details">
                                        <div class="details-grid">
                                            <div class="detail-section">
                                                <h6>Batch Information</h6>
                                                <div class="detail-row">
                                                    <span class="detail-label">Batch Number:</span>
                                                    <span class="detail-value">{{ $batch->batch_no ?? 'N/A' }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Quantity:</span>
                                                    <span class="detail-value">{{ number_format($batch->quantity) }} units</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Status:</span>
                                                    <span class="detail-value">
                                                        <span class="status-tag {{ $batchStatus }}">
                                                            @if($batchStatus == 'in-stock') In Stock
                                                            @elseif($batchStatus == 'out-of-stock') Out of Stock
                                                            @elseif($batchStatus == 'expired') Expired
                                                            @elseif($batchStatus == 'warning') Expiring Soon
                                                            @endif
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="detail-section">
                                                <h6>Pricing</h6>
                                                <div class="detail-row">
                                                    <span class="detail-label">Buy Price:</span>
                                                    <span class="detail-value">${{ number_format($batch->buy_price, 2) }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Sell Price:</span>
                                                    <span class="detail-value">${{ number_format($batch->sell_price, 2) }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Margin:</span>
                                                    <span class="detail-value">
                                                        ${{ number_format($margin, 2) }}
                                                        ({{ number_format($marginPercentage, 1) }}%)
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="detail-section">
                                                <h6>Dates</h6>
                                                <div class="detail-row">
                                                    <span class="detail-label">Manufacture Date:</span>
                                                    <span class="detail-value">{{ $batch->manufacture_date?->format('d M Y') ?? 'N/A' }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Expiry Date:</span>
                                                    <span class="detail-value {{ $expiryStatus }}">
                                                        {{ $batch->expiry_date?->format('d M Y') ?? 'N/A' }}
                                                        @if($expiryStatus == 'expired')
                                                            <span class="expiry-warning">(Expired)</span>
                                                        @elseif($expiryStatus == 'warning')
                                                            <span class="expiry-warning">({{ $batch->expiry_date->diffInDays(now()) }} days left)</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="detail-row">
                                                    <span class="detail-label">Created:</span>
                                                    <span class="detail-value">{{ $batch->created_at->format('d M Y, h:i A') }}</span>
                                                </div>
                                            </div>


                                            <div class="detail-section">
    <h6>Location Stock</h6>

    @if($batch->stocks->count())
        @foreach($batch->stocks as $st)
            @php
                $locName = $st->location?->name ?? 'Unknown';
                $locCode = $st->location?->code ? ' ('.$st->location->code.')' : '';
            @endphp
            <div class="detail-row">
                <span class="detail-label">{{ $locName }}{!! $locCode !!}:</span>
                <span class="detail-value">{{ number_format((float)$st->on_hand, 2) }}</span>
            </div>
        @endforeach
    @else
        <div class="detail-row">
            <span class="detail-label">No stock rows</span>
            <span class="detail-value">—</span>
        </div>
    @endif
</div>

                                        </div>

                                        <div class="batch-actions">
                                            <a href="{{ route('product.batches.edit', ['product' => $product, 'batch' => $batch]) }}"
                                               class="action-btn">
                                                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                                Edit Batch
                                            </a>

                                            <a href="{{ route('product.batches.show', ['batch' => $batch]) }}"
                                               class="action-btn">
                                                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                View Details
                                            </a>




                                            <form action="{{ route('product.batches.destroy', ['product' => $product, 'batch' => $batch]) }}"
                                                  method="DELETE"
                                                  class="d-inline batch-delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="action-btn danger delete-batch-btn">
                                                    <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/>
                            </svg>
                            <p>No batches found for this product</p>
                            <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                               class="btn btn-primary">
                                Add First Batch
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column (Sidebar) --}}
        <div class="right-column">
            {{-- Stock Summary --}}
            <div class="sidebar-section">
                <div class="card-header">
                    <h5 class="card-title">Stock Summary</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $totalBatches = $product->batches->count();
                        $inStockBatches = $product->batches->where('quantity', '>', 0)->count();
                        $expiredBatches = $product->batches
                            ->where('expiry_date', '<', now())
                            ->where('quantity', '>', 0)
                            ->count();
                        $outOfStockBatches = $product->batches->where('quantity', '<=', 0)->count();
                        $warningBatches = $product->batches
                            ->where('expiry_date', '>=', now())
                            ->where('expiry_date', '<=', now()->addDays(7))
                            ->where('quantity', '>', 0)
                            ->count();
                        $totalValue = $product->batches->sum(function ($batch) {
                            return $batch->quantity * $batch->buy_price;
                        });
                    @endphp

                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-value">{{ $totalBatches }}</div>
                            <div class="summary-label">Total Batches</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value success">{{ $inStockBatches }}</div>
                            <div class="summary-label">In Stock</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value warning">{{ $warningBatches }}</div>
                            <div class="summary-label">Expiring Soon</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value danger">{{ $expiredBatches + $outOfStockBatches }}</div>
                            <div class="summary-label">Issues</div>
                        </div>
                    </div>

                    <div class="total-value">
                        <div class="value-label">Total Inventory Value</div>
                        <div class="value-amount">${{ number_format($totalValue, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="sidebar-section">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="actions-list">
                        <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                           class="action-item">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New Batch</div>
                                <div class="action-desc">Create new inventory batch</div>
                            </div>
                        </a>
<a href="{{ route('product.status.create', ['productUuid' => $product->uuid]) }}"
   class="action-item">
    <div class="action-icon">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2a2 2 0 0 1 2 2v6h6a2 2 0 0 1 0 4h-6v6a2 2 0 0 1-4 0v-6H4a2 2 0 0 1 0-4h6V4a2 2 0 0 1 2-2z"/>
        </svg>
    </div>
    <div class="action-content">
        <div class="action-title">Add Product Status</div>
        <div class="action-desc">Assign offer, featured or badge</div>
    </div>
</a>
                        <a href="{{ route('products.images.index', $product) }}"
                           class="action-item">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Manage Images</div>
                                <div class="action-desc">Upload product photos</div>
                            </div>
                        </a>

                        <a href="{{ route('products.edit', $product) }}"
                           class="action-item">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Edit Product</div>
                                <div class="action-desc">Update product details</div>
                            </div>
                        </a>

                        <button class="action-item" onclick="printBatchReport()">
                            <div class="action-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Print Report</div>
                                <div class="action-desc">Export batch summary</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="sidebar-section">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-dot created"></div>
                            <div class="activity-content">
                                <div class="activity-title">Product Created</div>
                                <div class="activity-time">{{ $product->created_at->format('M d, Y • h:i A') }}</div>
                            </div>
                        </div>

                        @if ($product->updated_at->gt($product->created_at))
                            <div class="activity-item">
                                <div class="activity-dot updated"></div>
                                <div class="activity-content">
                                    <div class="activity-title">Last Updated</div>
                                    <div class="activity-time">{{ $product->updated_at->format('M d, Y • h:i A') }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($product->batches->count() > 0)
                            @php
                                $latestBatch = $product->batches->sortByDesc('created_at')->first();
                            @endphp
                            <div class="activity-item">
                                <div class="activity-dot batch"></div>
                                <div class="activity-content">
                                    <div class="activity-title">Last Batch Added</div>
                                    <div class="activity-time">{{ $latestBatch->created_at->format('M d, Y • h:i A') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Batch Details Modal -->
<div class="modal fade" id="batchDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batch Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="batchModalContent">
                <!-- Dynamic content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="editBatchBtn">Edit Batch</a>
            </div>
        </div>
    </div>
    @php
    $batchesForPrint = [];

    foreach ($product->batches as $batch) {
        $batchesForPrint[] = [
            'batch_no'    => $batch->batch_no ?? 'N/A',
            'quantity'    => (int) $batch->quantity,
            'buy_price'   => (float) $batch->buy_price,
            'sell_price'  => (float) $batch->sell_price,
            'expiry_date' => $batch->expiry_date
                ? $batch->expiry_date->format('Y-m-d')
                : null,
            'created_at'  => $batch->created_at->format('Y-m-d H:i:s'),
        ];
    }
@endphp
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing product page...');

        // Initialize components
        initBatchFilters();
        initBatchDetails();
        initAddBatchButtons();
        initImageGallery();
        initDeleteButtons();

        // Make sure Bootstrap dropdowns work
        initBootstrapDropdowns();

        console.log('Product page initialized successfully');
    });

    // Initialize Bootstrap dropdowns
    function initBootstrapDropdowns() {
        // Use vanilla JS to handle dropdowns if Bootstrap JS isn't loaded
        const dropdownButtons = document.querySelectorAll('[data-bs-toggle="dropdown"]');

        dropdownButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    const isShowing = dropdownMenu.classList.contains('show');

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu !== dropdownMenu) {
                            menu.classList.remove('show');
                        }
                    });

                    // Toggle this dropdown
                    dropdownMenu.classList.toggle('show', !isShowing);
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }

    // ===== BATCH FILTERING =====
    function initBatchFilters() {
        console.log('Initializing batch filters...');
        const filterButtons = document.querySelectorAll('.filter-btn');
        const dropdownFilters = document.querySelectorAll('.dropdown-item.filter-option');
        const batchCards = document.querySelectorAll('.batch-card');

        if (filterButtons.length === 0) {
            console.log('No filter buttons found');
            return;
        }

        console.log(`Found ${filterButtons.length} filter buttons and ${batchCards.length} batch cards`);

        // Filter button click handler
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Filter button clicked:', this.dataset.filter);
                const filter = this.dataset.filter;
                updateActiveFilter(filter, this);
                filterBatches(filter);
            });
        });

        // Dropdown filter click handler
        dropdownFilters.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Dropdown filter clicked:', this.dataset.filter);
                const filter = this.dataset.filter;

                // Update dropdown active state
                dropdownFilters.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');

                // Update filter buttons
                filterButtons.forEach(btn => {
                    btn.classList.remove('active');
                    if(btn.dataset.filter === filter) {
                        btn.classList.add('active');
                    }
                });

                filterBatches(filter);

                // Close dropdown
                const dropdownMenu = this.closest('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
            });
        });

        // Filter function
        function filterBatches(filter) {
            console.log('Filtering batches by:', filter);
            let visibleCount = 0;

            batchCards.forEach(card => {
                const status = card.dataset.batchStatus;
                let shouldShow = false;

                if (filter === 'all') {
                    shouldShow = true;
                } else if (filter === 'in-stock' && status === 'in-stock') {
                    shouldShow = true;
                } else if (filter === 'expired' && status === 'expired') {
                    shouldShow = true;
                } else if (filter === 'out-of-stock' && status === 'out-of-stock') {
                    shouldShow = true;
                } else if (filter === 'warning' && status === 'warning') {
                    shouldShow = true;
                }

                if (shouldShow) {
                    card.style.display = 'block';
                    visibleCount++;
                    // Add fade in animation
                    card.style.animation = 'fadeIn 0.3s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });

            console.log(`Filter "${filter}" shows ${visibleCount} out of ${batchCards.length} batches`);
        }

        function updateActiveFilter(filter, activeButton) {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            activeButton.classList.add('active');
        }

        // Initial filter
        filterBatches('all');
    }

    // ===== BATCH DETAILS TOGGLE =====
    function initBatchDetails() {
        console.log('Initializing batch details...');

        // Add click handler for batch summaries
        document.addEventListener('click', function(e) {
            const summary = e.target.closest('.batch-summary');
            if (summary) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Batch summary clicked');
                toggleBatchDetails(summary);
            }

            // Also handle toggle button clicks
            const toggleBtn = e.target.closest('.toggle-btn');
            if (toggleBtn) {
                e.preventDefault();
                e.stopPropagation();
                const summary = toggleBtn.closest('.batch-summary');
                if (summary) {
                    console.log('Toggle button clicked');
                    toggleBatchDetails(summary);
                }
            }
        });
    }

    function toggleBatchDetails(element) {
        const batchCard = element.closest('.batch-card');
        const toggleBtn = batchCard.querySelector('.toggle-btn svg');

        if (!batchCard) return;

        const isExpanding = !batchCard.classList.contains('active');

        if (isExpanding) {
            // Expand
            batchCard.classList.add('active');
            toggleBtn.style.transform = 'rotate(180deg)';
        } else {
            // Collapse
            batchCard.classList.remove('active');
            toggleBtn.style.transform = 'rotate(0deg)';
        }

        console.log(`Batch ${isExpanding ? 'expanded' : 'collapsed'}`);
    }

    function toggleAllBatches() {
        console.log('Toggling all batches...');
        const batchCards = document.querySelectorAll('.batch-card');
        const allExpanded = Array.from(batchCards).every(card => card.classList.contains('active'));

        batchCards.forEach(card => {
            const toggleBtn = card.querySelector('.toggle-btn svg');

            if (allExpanded) {
                // Collapse all
                card.classList.remove('active');
                if (toggleBtn) {
                    toggleBtn.style.transform = 'rotate(0deg)';
                }
            } else {
                // Expand all
                card.classList.add('active');
                if (toggleBtn) {
                    toggleBtn.style.transform = 'rotate(180deg)';
                }
            }
        });

        console.log(`All batches ${allExpanded ? 'collapsed' : 'expanded'}`);
    }

    // ===== ADD BATCH BUTTONS =====
    function initAddBatchButtons() {
        console.log('Initializing add batch buttons...');
        const addBatchButtons = [
            document.getElementById('addBatchBtn'),
            document.querySelector('.empty-state .btn-primary'),
            document.querySelector('.action-item[href*="batches/create"]')
        ];

        addBatchButtons.forEach((btn, index) => {
            if (btn) {
                console.log(`Found add batch button ${index + 1}`);
                btn.addEventListener('click', function(e) {
                    // If it's a button (not a link), don't do anything special
                    if (this.tagName === 'BUTTON') return;

                    // Smooth scroll to batches section if not already on create page
                    if (this.href && !this.href.includes('create')) {
                        e.preventDefault();
                        const batchesCard = document.querySelector('.details-card:last-child');
                        if (batchesCard) {
                            batchesCard.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            // Highlight the batches section
                            highlightElement(batchesCard);
                        }
                    }
                });
            }
        });
    }

    // ===== IMAGE GALLERY =====
    function initImageGallery() {
        console.log('Initializing image gallery...');
        const thumbnails = document.querySelectorAll('.thumbnail');

        if (thumbnails.length > 0) {
            console.log(`Found ${thumbnails.length} thumbnails`);
            thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    console.log(`Thumbnail ${index + 1} clicked`);
                    changeMainImage(this.querySelector('img').src, this);
                });
            });
        }
    }

    function changeMainImage(imageUrl, clickedThumb) {
        const mainImage = document.getElementById('mainProductImage');
        if (!mainImage) {
            console.log('Main image not found');
            return;
        }

        console.log('Changing main image to:', imageUrl);

        // Update main image with fade effect
        mainImage.style.opacity = '0.5';

        setTimeout(() => {
            mainImage.src = imageUrl;
            mainImage.style.opacity = '1';
        }, 150);

        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        clickedThumb.classList.add('active');
    }

    // ===== PRODUCT DETAILS TOGGLE =====
    function toggleProductDetails() {
        console.log('Toggling product details...');
        const detailsContent = document.getElementById('productDetailsContent');
        const toggleBtn = document.querySelector('.details-card .toggle-btn svg');

        if (!detailsContent || !toggleBtn) {
            console.log('Product details elements not found');
            return;
        }

        if (detailsContent.style.display === 'none') {
            // Show details
            detailsContent.style.display = 'block';
            toggleBtn.style.transform = 'rotate(180deg)';
            console.log('Product details shown');
        } else {
            // Hide details
            detailsContent.style.display = 'none';
            toggleBtn.style.transform = 'rotate(0deg)';
            console.log('Product details hidden');
        }
    }

    // ===== DELETE BUTTONS =====
    function initDeleteButtons() {
        console.log('Initializing delete buttons...');

        // Batch delete buttons
        const deleteBatchButtons = document.querySelectorAll('.delete-batch-btn');
        deleteBatchButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const form = this.closest('.batch-delete-form');
                if (form) {
                    confirmBatchDelete(form);
                }
            });
        });

        console.log(`Found ${deleteBatchButtons.length} batch delete buttons`);
    }

    function confirmBatchDelete(form) {
        if (confirm('Are you sure you want to delete this batch? This action cannot be undone.')) {
            form.submit();
        }
    }

    function confirmProductDelete() {
        if (confirm('Are you sure you want to delete this product? This will also delete all associated batches. This action cannot be undone.')) {
            // Create and submit the form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('products.destroy', $product) }}';
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);

            form.submit();
        }
    }

    // ===== BATCH REPORT PRINTING =====

    // ===== BATCH REPORT PRINTING =====
    function printBatchReport() {
        console.log('Printing batch report...');

        const batches = @json($batchesForPrint);

         const productName = @json($product->name);
        const printDate = new Date().toLocaleDateString();


        const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Batch Report - ${productName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { margin: 0; color: #333; }
                .header .subtitle { color: #666; margin: 5px 0 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #f8f9fa; padding: 12px; border-bottom: 2px solid #dee2e6; }
                td { padding: 10px 12px; border-bottom: 1px solid #dee2e6; }
                .total-row { font-weight: bold; background: #f8f9fa; }
                .expired { color: #dc3545; }
                .warning { color: #ffc107; }
                .footer { margin-top: 30px; text-align: center; color: #666; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Batch Inventory Report</h1>
                <div class="subtitle">Product: ${productName}</div>
                <div class="subtitle">Generated: ${printDate}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Batch No</th>
                        <th>Quantity</th>
                        <th>Buy Price</th>
                        <th>Sell Price</th>
                        <th>Margin</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${batches.map(batch => {
                        const margin = batch.sell_price - batch.buy_price;
                        const marginPercent = batch.buy_price > 0
                            ? ((margin / batch.buy_price) * 100).toFixed(1)
                            : '0.0';

                        let status = 'Active';
                        let statusClass = '';

                        if (batch.quantity <= 0) {
                            status = 'Out of Stock';
                        } else if (batch.expiry_date) {
                            const expiryDate = new Date(batch.expiry_date);
                            const today = new Date();
                            const diffDays = Math.ceil((expiryDate - today) / 86400000);

                            if (expiryDate < today) {
                                status = 'Expired';
                                statusClass = 'expired';
                            } else if (diffDays <= 7) {
                                status = `${diffDays} days left`;
                                statusClass = 'warning';
                            }
                        }

                        return `
                            <tr>
                                <td>${batch.batch_no}</td>
                                <td>${batch.quantity}</td>
                                <td>$${batch.buy_price.toFixed(2)}</td>
                                <td>$${batch.sell_price.toFixed(2)}</td>
                                <td>$${margin.toFixed(2)} (${marginPercent}%)</td>
                                <td>${batch.expiry_date ?? 'N/A'}</td>
                                <td class="${statusClass}">${status}</td>
                            </tr>
                        `;
                    }).join('')}

                    <tr class="total-row">
                        <td>Total</td>
                        <td>${batches.reduce((s, b) => s + b.quantity, 0)}</td>
                        <td colspan="4"></td>
                        <td>${batches.filter(b => b.quantity > 0).length} active</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                <p>Report generated by Inventory Management System</p>
            </div>
        </body>
        </html>
        `;

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow pop-ups to print the report.');
            return;
        }

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 300);
    }


    // ===== ANIMATION HELPERS =====
    function highlightElement(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }

        if (!element) return;

        const originalBoxShadow = element.style.boxShadow;
        element.style.boxShadow = '0 0 0 3px var(--accent-glow)';

        setTimeout(() => {
            element.style.transition = 'box-shadow 1s ease-out';
            element.style.boxShadow = originalBoxShadow;
        }, 1000);
    }

    // ===== LOAD BATCH DETAILS FOR MODAL =====
    async function loadBatchDetails(batchId) {
        try {
            console.log(`Loading details for batch ${batchId}...`);
            const response = await fetch(`/api/batches/${batchId}/details`);
            if (response.ok) {
                const data = await response.json();
                document.getElementById('batchModalContent').innerHTML = data.html;

                // Update edit button link
                const editBtn = document.getElementById('editBatchBtn');
                editBtn.href = `/products/${@json($product->id)}/batches/${batchId}/edit`;

                // Show modal using Bootstrap
                const modal = new bootstrap.Modal(document.getElementById('batchDetailsModal'));
                modal.show();
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        } catch (error) {
            console.error('Error loading batch details:', error);
            alert('Failed to load batch details. Please try again.');
        }
    }
</script>
@endsection
