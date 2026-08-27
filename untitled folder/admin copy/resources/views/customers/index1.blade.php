{{-- resources/views/customers/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-3">

    <style>
        /* ✅ Your palette (kept same names) */
        :root {
            --radius: 0.625rem;

            --transition-fast: 150ms;
            --transition-normal: 250ms;
            --transition-slow: 350ms;

            --background: oklch(0.145 0 0);
            --foreground: oklch(0.985 0 0);
            --card: oklch(0.205 0 0);
            --card-foreground: oklch(0.985 0 0);
            --popover: oklch(0.205 0 0);
            --popover-foreground: oklch(0.985 0 0);
            --primary: oklch(0.922 0 0);
            --primary-foreground: oklch(0.205 0 0);
            --secondary: oklch(0.269 0 0);
            --secondary-foreground: oklch(0.985 0 0);
            --muted: oklch(0.269 0 0);
            --muted-foreground: oklch(0.708 0 0);
            --accent: oklch(0.269 0 0);
            --accent-foreground: oklch(0.985 0 0);
            --destructive: oklch(0.704 0.191 22.216);
            --border: oklch(1 0 0 / 15%);
            --input: oklch(1 0 0 / 15%);
            --ring: oklch(0.556 0 0);

            --sidebar: oklch(0.18 0 0);
            --sidebar-foreground: oklch(0.985 0 0);
            --sidebar-primary: oklch(0.488 0.243 264.376);
            --sidebar-primary-foreground: oklch(0.985 0 0);
            --sidebar-accent: oklch(0.24 0 0);
            --sidebar-accent-foreground: oklch(0.985 0 0);
            --sidebar-border: oklch(1 0 0 / 15%);
            --sidebar-ring: oklch(0.556 0 0);

            --success: oklch(0.696 0.17 162.48);
            --warning: oklch(0.769 0.188 70.08);
            --info: oklch(0.488 0.243 264.376);
            --danger: oklch(0.704 0.191 22.216);

            --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
            --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);
            --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);

            --accent-color: var(--sidebar-primary);
            --accent-hover: oklch(0.488 0.243 264.376 / 0.8);
            --accent-glow: oklch(0.488 0.243 264.376 / 0.2);

            --bg-primary: var(--background);
            --bg-secondary: var(--card);
            --bg-tertiary: var(--secondary);
            --text-primary: var(--foreground);
            --text-secondary: var(--muted-foreground);
            --text-muted: oklch(0.708 0 0 / 0.7);
            --border-color: var(--border);
            --glass-base: oklch(0.205 0 0 / 0.7);
        }

        html[data-theme='light'] {
            --background: oklch(0.99 0 0);
            --foreground: oklch(0.12 0 0);
            --card: oklch(1 0 0);
            --card-foreground: oklch(0.12 0 0);
            --popover: oklch(1 0 0);
            --popover-foreground: oklch(0.12 0 0);
            --primary: oklch(0.15 0 0);
            --primary-foreground: oklch(0.99 0 0);
            --secondary: oklch(0.97 0 0);
            --secondary-foreground: oklch(0.15 0 0);
            --muted: oklch(0.96 0 0);
            --muted-foreground: oklch(0.5 0 0);
            --accent: oklch(0.96 0 0);
            --accent-foreground: oklch(0.15 0 0);
            --destructive: oklch(0.577 0.245 27.325);
            --border: oklch(0.9 0 0);
            --input: oklch(0.96 0 0);
            --ring: oklch(0.65 0 0);

            --sidebar: oklch(1 0 0);
            --sidebar-foreground: oklch(0.12 0 0);
            --sidebar-primary: oklch(0.646 0.222 41.116);
            --sidebar-primary-foreground: oklch(1 0 0);
            --sidebar-accent: oklch(0.97 0 0);
            --sidebar-accent-foreground: oklch(0.15 0 0);
            --sidebar-border: oklch(0.88 0 0);
            --sidebar-ring: oklch(0.65 0 0);

            --success: oklch(0.627 0.194 149.214);
            --warning: oklch(0.769 0.188 70.08);
            --info: oklch(0.623 0.214 259.815);
            --danger: oklch(0.577 0.245 27.325);

            --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
            --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);
            --dropdown-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15), 0 8px 10px -6px rgb(0 0 0 / 0.1);

            --accent-color: var(--sidebar-primary);
            --accent-hover: oklch(0.646 0.222 41.116 / 0.8);
            --accent-glow: oklch(0.646 0.222 41.116 / 0.1);

            --bg-primary: var(--background);
            --bg-secondary: var(--card);
            --bg-tertiary: var(--secondary);
            --text-primary: var(--foreground);
            --text-secondary: var(--muted-foreground);
            --text-muted: oklch(0.5 0 0 / 0.7);
            --border-color: var(--border);
            --glass-base: rgba(255, 255, 255, 0.85);
        }

        .page { color: var(--foreground); }

        .shell {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 14px;
        }
        @media (max-width: 992px) { .shell { grid-template-columns: 1fr; } }

        .cardx {
            background: var(--card);
            color: var(--card-foreground);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: box-shadow var(--transition-normal) ease, transform var(--transition-normal) ease;
        }
        .cardx:hover { box-shadow: var(--card-shadow-hover); transform: translateY(-1px); }

        .cardx-hd {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
        }
        .title { font-size: 18px; font-weight: 900; margin: 0; }
        .subtle { font-size: 12px; color: var(--muted-foreground); }
        .strong { font-weight: 900; }

        .inputx, .selectx, .textareax {
            background: color-mix(in oklch, var(--card) 92%, black 8%);
            border: 1px solid var(--border);
            color: var(--foreground);
            border-radius: calc(var(--radius) - 4px);
            padding: 10px 12px;
            outline: none;
            width: 100%;
            transition: border-color var(--transition-fast) ease, box-shadow var(--transition-fast) ease;
        }
        .textareax { min-height: 90px; resize: vertical; }
        .selectx { height: 42px; padding: 0 12px; }
        .inputx:focus, .selectx:focus, .textareax:focus {
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border) 45%);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .btnx {
            border: 1px solid transparent;
            background: var(--accent-color);
            color: white;
            border-radius: calc(var(--radius) - 4px);
            padding: 8px 12px;
            font-weight: 850;
            transition: transform var(--transition-fast) ease, background var(--transition-fast) ease;
            white-space: nowrap;
        }
        .btnx:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btnx:active { transform: translateY(0px); }

        .btnx-ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--foreground);
        }
        .btnx-ghost:hover {
            background: color-mix(in oklch, var(--secondary) 70%, transparent 30%);
        }

        .btnx-danger {
            background: color-mix(in oklch, var(--danger) 92%, black 8%);
        }
        .btnx-danger:hover {
            background: color-mix(in oklch, var(--danger) 82%, black 18%);
        }

        .pill {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: color-mix(in oklch, var(--secondary) 65%, transparent 35%);
            color: var(--foreground);
            font-weight: 850;
            display:inline-flex;
            gap: 6px;
            align-items:center;
        }
        .pill.ok { border-color: color-mix(in oklch, var(--success) 55%, var(--border) 45%); }
        .pill.warn { border-color: color-mix(in oklch, var(--warning) 55%, var(--border) 45%); }
        .pill.info { border-color: color-mix(in oklch, var(--info) 55%, var(--border) 45%); }

        .result-list { max-height: 360px; overflow:auto; }
        .result-row {
            display:flex;
            gap: 12px;
            align-items:center;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            animation: fadeInUp 220ms ease both;
            transition: background var(--transition-fast) ease, transform var(--transition-fast) ease;
        }
        .result-row:hover {
            background: color-mix(in oklch, var(--accent-glow) 35%, transparent 65%);
            transform: translateY(-1px);
        }
        .result-row:last-child { border-bottom: 0; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .avatar {
            width: 42px; height: 42px;
            border-radius: 14px;
            background: color-mix(in oklch, var(--secondary) 70%, transparent 30%);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight: 950;
            color: var(--foreground);
            flex: 0 0 auto;
            border: 1px solid var(--border);
        }
        .r-title { font-weight: 900; line-height: 1.15; }
        .r-meta { font-size: 12px; color: var(--muted-foreground); line-height: 1.25; }

        .tablex { width:100%; border-collapse:separate; border-spacing:0; }
        .tablex th, .tablex td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: top; }
        .tablex thead th {
            position: sticky;
            top: 0;
            background: color-mix(in oklch, var(--card) 88%, black 12%);
            z-index: 1;
            font-size: 12px;
            letter-spacing: 0.25px;
            text-transform: uppercase;
            color: var(--muted-foreground);
        }

        .money { text-align:right; font-variant-numeric: tabular-nums; }
        .muted { color: var(--muted-foreground); }

        .row-flash { animation: flash 700ms ease; }
        @keyframes flash {
            0% { box-shadow: inset 0 0 0 9999px color-mix(in oklch, var(--accent-glow) 70%, transparent 30%); }
            100% { box-shadow: inset 0 0 0 9999px transparent; }
        }

        .toast-mini {
            position: fixed; right: 16px; bottom: 16px;
            background: color-mix(in oklch, var(--card) 86%, black 14%);
            border: 1px solid var(--border);
            color: var(--foreground);
            padding: 10px 12px; border-radius: 12px;
            font-size: 13px; display:none;
            box-shadow: var(--card-shadow-hover);
            z-index: 9999;
            max-width: min(360px, calc(100vw - 32px));
        }

        .spin {
            display:inline-block;
            width: 14px; height: 14px;
            border-radius: 999px;
            border: 2px solid color-mix(in oklch, var(--border) 60%, transparent 40%);
            border-top-color: var(--accent-color);
            animation: sp 800ms linear infinite;
            vertical-align: -2px;
            margin-right: 6px;
        }
        @keyframes sp { to { transform: rotate(360deg); } }

        .grid2 { display:grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        @media (max-width: 576px) { .grid2 { grid-template-columns: 1fr; } }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 12px 0;
        }

        .hintbar {
            margin-top: 12px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: color-mix(in oklch, var(--card) 90%, black 10%);
            display:flex;
            gap: 10px;
            align-items:center;
            justify-content:space-between;
        }
    </style>

    <div class="page">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <div class="subtle">CRM / POS</div>
                <h3 class="title m-0">Customers</h3>
            </div>

            <div class="text-end">
                <div class="subtle">Live Search</div>
                <div class="strong" style="font-size: 18px;">Create • Update • Due/Advance • Rewards</div>
            </div>
        </div>

        <div class="shell">
            {{-- LEFT: search + list --}}
            <div class="cardx">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Find Customer</div>
                        <div class="subtle">Search by name / phone / email (2+ chars)</div>
                    </div>
                    <button class="btnx btnx-ghost" type="button" id="clearSearchBtn">Clear</button>
                </div>

                <div style="padding: 12px 14px;">
                    <input class="inputx" id="searchInput" placeholder="Type: Rahim / 01xxxxxxxxx / mail@example.com">
                </div>

                <div id="searchResults" class="result-list"></div>

                <div class="hintbar">
                    <div class="subtle" id="sweetHint">
                        💡 Tip: Click a customer to open details on the right. Use Balance/Rewards actions for POS.
                    </div>
                    <button class="btnx btnx-ghost" type="button" id="dismissHintBtn">OK</button>
                </div>
            </div>

            {{-- RIGHT: form + details --}}
            <div class="cardx">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Customer Details</div>
                        <div class="subtle" id="panelSub">Create new or select from left</div>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <button class="btnx btnx-ghost" type="button" id="newBtn">New</button>
                        <button class="btnx btnx-danger" type="button" id="deactivateBtn" style="display:none;">Deactivate</button>
                    </div>
                </div>

                <div style="padding: 12px 14px;">
                    <input type="hidden" id="customerId" value="">

                    <div class="grid2">
                        <div>
                            <div class="subtle mb-1">Name</div>
                            <input class="inputx" id="name" placeholder="Customer name">
                        </div>
                        <div>
                            <div class="subtle mb-1">Type</div>
                            <select class="selectx" id="type">
                                <option value="regular">Regular</option>
                                <option value="vip">VIP</option>
                                <option value="wholesale">Wholesale</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid2 mt-2">
                        <div>
                            <div class="subtle mb-1">Phone</div>
                            <input class="inputx" id="phone" placeholder="01xxxxxxxxx">
                        </div>
                        <div>
                            <div class="subtle mb-1">Email</div>
                            <input class="inputx" id="email" placeholder="mail@example.com">
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="subtle mb-1">Address</div>
                        <textarea class="textareax" id="address" placeholder="Address"></textarea>
                    </div>

                    <div class="mt-2">
                        <div class="subtle mb-1">Notes</div>
                        <textarea class="textareax" id="notes" placeholder="Notes (optional)"></textarea>
                    </div>

                    <div class="divider"></div>

                    <div class="grid2">
                        <div class="cardx" style="padding:10px 12px;">
                            <div class="subtle">Due</div>
                            <div class="strong" style="font-size: 18px;">
                                ৳ <span id="dueBalance">0.00</span>
                            </div>
                        </div>
                        <div class="cardx" style="padding:10px 12px;">
                            <div class="subtle">Advance</div>
                            <div class="strong" style="font-size: 18px;">
                                ৳ <span id="advanceBalance">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="cardx mt-2" style="padding:10px 12px;">
                        <div class="subtle">Reward Points</div>
                        <div class="strong" style="font-size: 18px;">
                            <span id="rewardPoints">0.00</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
                        <button class="btnx btnx-ghost" type="button" id="resetBtn">Reset</button>
                        <button class="btnx" type="button" id="saveBtn">Save</button>
                    </div>
                </div>

                {{-- Actions: Due/Advance + Rewards --}}
                <div style="border-top: 1px solid var(--border); padding: 12px 14px;">
                    <div class="strong">POS Actions</div>
                    <div class="subtle">Add due/advance or rewards. Supports online/offline (channel/terminal/idempotency).</div>

                    <div class="divider"></div>

                    <div class="grid2">
                        <div class="cardx" style="padding: 10px 12px;">
                            <div class="strong">Balance (Due / Advance)</div>

                            <div class="grid2 mt-2">
                                <div>
                                    <div class="subtle mb-1">Kind</div>
                                    <select class="selectx" id="balKind">
                                        <option value="due">Due</option>
                                        <option value="advance">Advance</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="subtle mb-1">Direction</div>
                                    <select class="selectx" id="balDir">
                                        <option value="debit">Debit (Increase)</option>
                                        <option value="credit">Credit (Decrease)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="subtle mb-1">Amount</div>
                                <input class="inputx" id="balAmount" type="number" step="0.01" min="0.01" value="0">
                            </div>

                            <div class="grid2 mt-2">
                                <div>
                                    <div class="subtle mb-1">Channel</div>
                                    <select class="selectx" id="balChannel">
                                        <option value="pos" selected>POS</option>
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="subtle mb-1">Terminal ID</div>
                                    <input class="inputx" id="balTerminal" placeholder="POS-01">
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="subtle mb-1">Note</div>
                                <input class="inputx" id="balNote" placeholder="Payment / Adjustment note">
                            </div>

                            <div class="mt-2" style="display:flex; justify-content:flex-end;">
                                <button class="btnx" type="button" id="postBalanceBtn">Post Balance</button>
                            </div>
                        </div>

                        <div class="cardx" style="padding: 10px 12px;">
                            <div class="strong">Rewards</div>

                            <div class="grid2 mt-2">
                                <div>
                                    <div class="subtle mb-1">Action</div>
                                    <select class="selectx" id="rwAction">
                                        <option value="earn">Earn</option>
                                        <option value="redeem">Redeem</option>
                                        <option value="adjust">Adjust</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="subtle mb-1">Direction</div>
                                    <select class="selectx" id="rwDir">
                                        <option value="add">Add</option>
                                        <option value="subtract">Subtract</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="subtle mb-1">Points</div>
                                <input class="inputx" id="rwPoints" type="number" step="0.01" min="0.01" value="0">
                            </div>

                            <div class="grid2 mt-2">
                                <div>
                                    <div class="subtle mb-1">Channel</div>
                                    <select class="selectx" id="rwChannel">
                                        <option value="pos" selected>POS</option>
                                        <option value="offline">Offline</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>
                                <div>
                                    <div class="subtle mb-1">Terminal ID</div>
                                    <input class="inputx" id="rwTerminal" placeholder="POS-01">
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="subtle mb-1">Note</div>
                                <input class="inputx" id="rwNote" placeholder="Reward note">
                            </div>

                            <div class="mt-2" style="display:flex; justify-content:flex-end;">
                                <button class="btnx" type="button" id="postRewardsBtn">Post Rewards</button>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="strong">Recent Activity</div>
                    <div class="subtle">Last 10 balance entries + last 10 rewards entries</div>

                    <div class="divider"></div>

                    <div style="max-height: 260px; overflow:auto;">
                        <table class="tablex">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Info</th>
                                    <th class="money">Amount</th>
                                    <th class="money">When</th>
                                </tr>
                            </thead>
                            <tbody id="activityBody">
                                <tr>
                                    <td colspan="4" class="subtle" style="padding: 14px;">Select a customer to load activity.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="toast-mini" id="miniToast"></div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toastEl = document.getElementById('miniToast');

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    const customerIdEl = document.getElementById('customerId');
    const panelSub = document.getElementById('panelSub');

    const nameEl = document.getElementById('name');
    const phoneEl = document.getElementById('phone');
    const emailEl = document.getElementById('email');
    const typeEl = document.getElementById('type');
    const addressEl = document.getElementById('address');
    const notesEl = document.getElementById('notes');

    const dueBalanceEl = document.getElementById('dueBalance');
    const advanceBalanceEl = document.getElementById('advanceBalance');
    const rewardPointsEl = document.getElementById('rewardPoints');

    const activityBody = document.getElementById('activityBody');

    const saveBtn = document.getElementById('saveBtn');
    const resetBtn = document.getElementById('resetBtn');
    const newBtn = document.getElementById('newBtn');
    const deactivateBtn = document.getElementById('deactivateBtn');

    const postBalanceBtn = document.getElementById('postBalanceBtn');
    const postRewardsBtn = document.getElementById('postRewardsBtn');

    const balKind = document.getElementById('balKind');
    const balDir = document.getElementById('balDir');
    const balAmount = document.getElementById('balAmount');
    const balChannel = document.getElementById('balChannel');
    const balTerminal = document.getElementById('balTerminal');
    const balNote = document.getElementById('balNote');

    const rwAction = document.getElementById('rwAction');
    const rwDir = document.getElementById('rwDir');
    const rwPoints = document.getElementById('rwPoints');
    const rwChannel = document.getElementById('rwChannel');
    const rwTerminal = document.getElementById('rwTerminal');
    const rwNote = document.getElementById('rwNote');

    const dismissHintBtn = document.getElementById('dismissHintBtn');
    const sweetHint = document.getElementById('sweetHint');

    let debounceTimer = null;

    function toast(msg) {
        toastEl.textContent = msg;
        toastEl.style.display = 'block';
        clearTimeout(toastEl._t);
        toastEl._t = setTimeout(() => toastEl.style.display = 'none', 1600);
    }

    function money(n) { return Number(n || 0).toFixed(2); }

    function setHint(msg) {
        sweetHint.textContent = msg || "💡 Tip: Click a customer to open details on the right. Use Balance/Rewards actions for POS.";
    }

    function initials(name) {
        const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!parts.length) return '?';
        return (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
    }

    async function jsonFetch(url, method, payload) {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: payload ? JSON.stringify(payload) : null
        });
        const data = await res.json().catch(() => ({}));
        return { res, data };
    }

    function resetForm() {
        customerIdEl.value = '';
        panelSub.textContent = 'Create new or select from left';

        nameEl.value = '';
        phoneEl.value = '';
        emailEl.value = '';
        typeEl.value = 'regular';
        addressEl.value = '';
        notesEl.value = '';

        dueBalanceEl.textContent = '0.00';
        advanceBalanceEl.textContent = '0.00';
        rewardPointsEl.textContent = '0.00';

        activityBody.innerHTML = `<tr><td colspan="4" class="subtle" style="padding: 14px;">Select a customer to load activity.</td></tr>`;

        deactivateBtn.style.display = 'none';
    }

    function fillCustomer(c) {
        customerIdEl.value = c.id;
        panelSub.textContent = `Selected: #${c.id} • ${c.name}`;

        nameEl.value = c.name ?? '';
        phoneEl.value = c.phone ?? '';
        emailEl.value = c.email ?? '';
        typeEl.value = c.type ?? 'regular';
        addressEl.value = c.address ?? '';
        notesEl.value = c.notes ?? '';

        dueBalanceEl.textContent = money(c.due_balance);
        advanceBalanceEl.textContent = money(c.advance_balance);
        rewardPointsEl.textContent = money(c.reward_points);

        deactivateBtn.style.display = 'inline-block';
    }

    function renderActivity(customer) {
        const balances = customer.balance_ledgers || [];
        const rewards = customer.reward_ledgers || [];

        const rows = [];

        balances.slice(0, 10).forEach(b => {
            rows.push({
                type: `Balance • ${b.kind}`,
                info: `${b.direction} • ${b.channel}${b.terminal_id ? ' • ' + b.terminal_id : ''}${b.note ? ' • ' + b.note : ''}`,
                amount: (b.direction === 'debit' ? '+' : '-') + money(b.amount),
                when: new Date(b.created_at).toLocaleString()
            });
        });

        rewards.slice(0, 10).forEach(r => {
            rows.push({
                type: `Rewards • ${r.action}`,
                info: `${r.direction} • ${r.channel}${r.terminal_id ? ' • ' + r.terminal_id : ''}${r.note ? ' • ' + r.note : ''}`,
                amount: (r.direction === 'add' ? '+' : '-') + money(r.points),
                when: new Date(r.created_at).toLocaleString()
            });
        });

        // newest first
        rows.sort((a,b) => new Date(b.when) - new Date(a.when));

        if (!rows.length) {
            activityBody.innerHTML = `<tr><td colspan="4" class="subtle" style="padding: 14px;">No activity yet.</td></tr>`;
            return;
        }

        activityBody.innerHTML = rows.slice(0, 20).map(r => `
            <tr>
                <td class="strong">${r.type}</td>
                <td class="muted">${escapeHtml(r.info)}</td>
                <td class="money strong">${escapeHtml(r.amount)}</td>
                <td class="money muted">${escapeHtml(r.when)}</td>
            </tr>
        `).join('');
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'","&#039;");
    }

    async function loadCustomer(id) {
        const { res, data } = await jsonFetch(`{{ url('/customers') }}/${id}`, 'GET');
        if (!res.ok || !data.success) {
            toast(data.message ?? 'Failed to load customer');
            return;
        }
        fillCustomer(data.customer);
        renderActivity(data.customer);
        toast('Loaded');
    }

    function renderSearch(rows) {
        searchResults.innerHTML = '';

        if (!Array.isArray(rows) || rows.length === 0) {
            searchResults.innerHTML = `<div style="padding: 12px 14px;" class="subtle">No customers found</div>`;
            return;
        }

        rows.forEach(c => {
            const row = document.createElement('div');
            row.className = 'result-row';

            const due = Number(c.due_balance || 0);
            const adv = Number(c.advance_balance || 0);
            const pts = Number(c.reward_points || 0);

            row.innerHTML = `
                <div class="avatar">${initials(c.name)}</div>
                <div style="flex:1;">
                    <div class="r-title">${escapeHtml(c.name)} <span class="subtle" style="font-weight:850;">(#${c.id})</span></div>
                    <div class="r-meta">
                        ${c.phone ? `📞 ${escapeHtml(c.phone)}` : '📞 —'}
                        ${c.email ? ` • ✉️ ${escapeHtml(c.email)}` : ''}
                    </div>
                    <div class="r-meta" style="margin-top:6px; display:flex; gap:8px; flex-wrap:wrap;">
                        <span class="pill ${due>0 ? 'warn' : 'ok'}">Due: <b>৳${money(due)}</b></span>
                        <span class="pill ${adv>0 ? 'info' : ''}">Adv: <b>৳${money(adv)}</b></span>
                        <span class="pill">Pts: <b>${money(pts)}</b></span>
                    </div>
                </div>
                <div>
                    <span class="pill">${escapeHtml(c.type || 'regular')}</span>
                </div>
            `;

            row.addEventListener('click', () => loadCustomer(c.id));
            searchResults.appendChild(row);
        });
    }

    async function doSearch(term) {
        if (term.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        searchResults.innerHTML = `<div style="padding: 12px 14px;" class="subtle"><span class="spin"></span>Searching...</div>`;

        // Uses your quick search endpoint
        const res = await fetch(`{{ route('customers.quick.search') }}?q=${encodeURIComponent(term)}`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await res.json().catch(() => []);
        renderSearch(data);
    }

    async function saveCustomer() {
        const id = customerIdEl.value;

        const payload = {
            name: nameEl.value.trim(),
            phone: phoneEl.value.trim() || null,
            email: emailEl.value.trim() || null,
            type: typeEl.value,
            address: addressEl.value.trim() || null,
            notes: notesEl.value.trim() || null,
        };

        if (!payload.name) {
            toast('Name is required');
            return;
        }

        saveBtn.disabled = true;
        saveBtn._old = saveBtn.innerHTML;
        saveBtn.innerHTML = `<span class="spin"></span>Saving`;

        const url = id ? `{{ url('/customers') }}/${id}` : `{{ url('/customers') }}`;
        const method = id ? 'PUT' : 'POST';

        const { res, data } = await jsonFetch(url, method, payload);

        saveBtn.disabled = false;
        saveBtn.innerHTML = saveBtn._old || 'Save';

        if (!res.ok || !data.success) {
            toast(data.message ?? 'Save failed');
            return;
        }

        const c = data.customer;
        fillCustomer(c);
        toast(id ? 'Updated' : 'Created');

        setHint('✅ Saved! You can now post due/advance or rewards for this customer.');

        // refresh search list if query exists
        const q = searchInput.value.trim();
        if (q.length >= 2) doSearch(q);
    }

    async function postBalance() {
        const id = customerIdEl.value;
        if (!id) { toast('Select a customer first'); return; }

        const amount = Number(balAmount.value || 0);
        if (amount <= 0) { toast('Amount must be > 0'); return; }

        postBalanceBtn.disabled = true;
        postBalanceBtn._old = postBalanceBtn.innerHTML;
        postBalanceBtn.innerHTML = `<span class="spin"></span>Posting`;

        const payload = {
            kind: balKind.value,
            direction: balDir.value,
            amount: amount,
            channel: balChannel.value,
            terminal_id: balTerminal.value.trim() || null,
            note: balNote.value.trim() || null,

            // ✅ optional idempotency key for offline protection
            idempotency_key: `${balChannel.value}-${balTerminal.value.trim() || 'NA'}-${Date.now()}`
        };

        const { res, data } = await jsonFetch(`{{ url('/customers') }}/${id}/balance`, 'POST', payload);

        postBalanceBtn.disabled = false;
        postBalanceBtn.innerHTML = postBalanceBtn._old || 'Post Balance';

        if (!res.ok || !data.success) {
            toast(data.message ?? 'Balance post failed');
            return;
        }

        // reload customer to get ledgers for activity section
        await loadCustomer(id);

        balAmount.value = 0;
        balNote.value = '';
        toast('Balance posted');

        setHint('✨ Balance updated. Tip: Use Due debit for credit sale, Due credit for payment.');
    }

    async function postRewards() {
        const id = customerIdEl.value;
        if (!id) { toast('Select a customer first'); return; }

        const points = Number(rwPoints.value || 0);
        if (points <= 0) { toast('Points must be > 0'); return; }

        postRewardsBtn.disabled = true;
        postRewardsBtn._old = postRewardsBtn.innerHTML;
        postRewardsBtn.innerHTML = `<span class="spin"></span>Posting`;

        const payload = {
            action: rwAction.value,
            direction: rwDir.value,
            points: points,
            channel: rwChannel.value,
            terminal_id: rwTerminal.value.trim() || null,
            note: rwNote.value.trim() || null,

            idempotency_key: `${rwChannel.value}-${rwTerminal.value.trim() || 'NA'}-${Date.now()}`
        };

        const { res, data } = await jsonFetch(`{{ url('/customers') }}/${id}/rewards`, 'POST', payload);

        postRewardsBtn.disabled = false;
        postRewardsBtn.innerHTML = postRewardsBtn._old || 'Post Rewards';

        if (!res.ok || !data.success) {
            toast(data.message ?? 'Rewards post failed');
            return;
        }

        await loadCustomer(id);

        rwPoints.value = 0;
        rwNote.value = '';
        toast('Rewards posted');

        setHint('✨ Rewards updated. Tip: Redeem subtracts points (must have enough).');
    }

    async function deactivateCustomer() {
        const id = customerIdEl.value;
        if (!id) return;

        const { res, data } = await jsonFetch(`{{ url('/customers') }}/${id}`, 'PUT', { is_active: false });

        if (!res.ok || !data.success) {
            toast(data.message ?? 'Deactivate failed');
            return;
        }

        toast('Deactivated');
        setHint('🛑 Customer is now inactive. They won’t appear in quick search (only active shown).');

        // refresh and reset
        resetForm();
        const q = searchInput.value.trim();
        if (q.length >= 2) doSearch(q);
    }

    // Events
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const term = this.value.trim();
        debounceTimer = setTimeout(() => doSearch(term), 220);
    });

    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchResults.innerHTML = '';
        searchInput.focus();
    });

    saveBtn.addEventListener('click', saveCustomer);
    resetBtn.addEventListener('click', resetForm);
    newBtn.addEventListener('click', () => { resetForm(); toast('New customer'); });

    deactivateBtn.addEventListener('click', () => {
        if (confirm('Deactivate this customer?')) deactivateCustomer();
    });

    postBalanceBtn.addEventListener('click', postBalance);
    postRewardsBtn.addEventListener('click', postRewards);

    dismissHintBtn.addEventListener('click', () => setHint('✅ Ready. Search or create customer, then post due/advance/rewards.'));

    // init
    resetForm();
});
</script>
@endsection
