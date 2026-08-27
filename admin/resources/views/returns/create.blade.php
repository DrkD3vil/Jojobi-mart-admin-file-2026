<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Return</title>
    <style>
        body{font-family: Arial; margin:20px; background: var(--background, #fff); color: var(--foreground, #222);}
        input, select, textarea{padding:8px; width: 100%; margin: 5px 0; border: 1px solid var(--border, #ccc); border-radius: calc(var(--radius, 0.625rem) - 2px); background: var(--input, #fff); color: var(--foreground, #222); transition: border-color var(--transition-fast, 150ms) ease;}
        .row{display:flex; gap:10px;}
        .col{flex:1;}
        .box{border:1px solid var(--border, #ddd); border-radius: var(--radius, 0.625rem); background: var(--card, #fff); padding:12px; margin:12px 0; box-shadow: var(--card-shadow, none);}
        button{padding:10px 14px; border-radius: calc(var(--radius, 0.625rem) - 2px); border: 1px solid transparent; background: var(--sidebar-primary, #f0ad4e); color: var(--sidebar-primary-foreground, #fff); cursor: pointer; transition: all var(--transition-fast, 150ms) ease;}
        .ok{background: color-mix(in oklch, var(--success, #3c9d5c) 15%, white); border: 1px solid color-mix(in oklch, var(--success, #3c9d5c) 35%, var(--border, #ddd)); color: var(--success, #2f6f3e); border-radius: var(--radius, 0.625rem); padding:10px; margin-bottom:10px;}
        .err{background: color-mix(in oklch, var(--danger, #d9534f) 15%, white); border: 1px solid color-mix(in oklch, var(--danger, #d9534f) 35%, var(--border, #ddd)); color: var(--danger, #a94442); border-radius: var(--radius, 0.625rem); padding:10px; margin-bottom:10px;}

        @media (max-width: 768px) {
            .row{flex-direction: column;}
        }
    </style>
</head>
<body>

<h2>Return (Stock IN + Refund)</h2>

@if(session('ok')) <div class="ok">{{ session('ok') }}</div> @endif
@if($errors->any())
<div class="err">
    <b>Errors:</b>
    <ul>
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<form method="POST" action="/returns" data-reveal>
    @csrf

    <div class="row">
        <div class="col">
            <label>Order</label>
            <select name="order_id" required>
                <option value="">Select order</option>
                @foreach($orders as $o)
                    <option value="{{ $o->id }}">{{ $o->order_no ?? ('Order#'.$o->id) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label>Location (Return stock IN)</label>
            <select name="location_id" required>
                <option value="">Select location</option>
                @foreach($locations as $l)
                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <label>Refund Method</label>
    <select name="refund_method">
        <option value="">(optional)</option>
        <option value="cash">cash</option>
        <option value="bkash">bkash</option>
        <option value="card">card</option>
        <option value="wallet">wallet</option>
        <option value="adjust_customer_balance">adjust_customer_balance</option>
    </select>

    <label>Note</label>
    <textarea name="note" rows="2"></textarea>

    <div class="box" data-reveal>
        <h3>Return Item #1 (demo)</h3>
        <p style="color: var(--muted-foreground, #555);">For demo you manually input product/batch/order_item. In POS you will pick from order items.</p>

        <div class="row">
            <div class="col">
                <label>Order Item ID</label>
                <input name="items[0][order_item_id]" type="number" required>
            </div>
            <div class="col">
                <label>Product ID</label>
                <input name="items[0][product_id]" type="number" required>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label>Batch ID</label>
                <input name="items[0][product_batch_id]" type="number" required>
            </div>
            <div class="col">
                <label>Qty</label>
                <input name="items[0][qty]" type="number" step="0.0001" required>
            </div>
        </div>

        <label>Condition</label>
        <select name="items[0][condition]">
            <option value="GOOD">GOOD</option>
            <option value="DAMAGED">DAMAGED</option>
            <option value="EXPIRED">EXPIRED</option>
        </select>

        <label>Reason Code</label>
        <input name="items[0][reason_code]" type="text" placeholder="e.g. wrong_item, defect, size_issue">
    </div>

    <button type="submit">Submit Return</button>
</form>

</body>
</html>
