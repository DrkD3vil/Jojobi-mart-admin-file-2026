<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Return</title>
    <style>
        body{font-family: Arial; margin:20px;}
        input, select, textarea{padding:8px; width: 100%; margin: 5px 0;}
        .row{display:flex; gap:10px;}
        .col{flex:1;}
        .box{border:1px solid #ddd; padding:12px; margin:12px 0;}
        button{padding:10px 14px;}
        .ok{background:#dff0d8; padding:10px; margin-bottom:10px;}
        .err{background:#f2dede; padding:10px; margin-bottom:10px;}
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

<form method="POST" action="/returns">
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

    <div class="box">
        <h3>Return Item #1 (demo)</h3>
        <p style="color:#555;">For demo you manually input product/batch/order_item. In POS you will pick from order items.</p>

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
