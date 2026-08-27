@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<div class="dockline">
    <div class="dl-wrap">
        <div class="dl-head">
            <div>
                <p class="dl-eyebrow">AI Assistant</p>
                <h1>Ask about your store</h1>
                <p class="dl-sub">Sales, profit, and product questions answered from real data — powered by Gemini. Every action it takes is checked against your own permissions first.</p>
            </div>
            <div class="dl-head-actions">
                @if($hasAiAccess)
                    <a href="{{ route('ai_assistant.logs') }}" class="dl-btn dl-cut">Activity Log</a>
                    <button type="button" id="dlNewChat" class="dl-btn dl-cut" title="Clears this conversation view only — nothing is deleted from the activity log">Clear Chat</button>
                @endif
            </div>
        </div>

        @if($hasAiAccess && $canManageModel)
            <div class="dl-model-row">
                <span class="dl-eyebrow">Model</span>
                <select id="dlModelSelect">
                    @foreach($availableModels as $modelId => $label)
                        <option value="{{ $modelId }}" @selected($currentModel === $modelId)>{{ $modelId }} — {{ $label }}</option>
                    @endforeach
                </select>
                <span id="dlModelSaved" class="dl-model-saved"></span>
            </div>
        @endif

        @unless($hasAiAccess)
            <div class="dl-board dl-access-card">
                <span class="dl-eyebrow">Access needed</span>
                <p>You don't have permission to use the AI Assistant yet. An admin needs to grant the "AI Assistant" access key on the Access Keys page.</p>
                @if($requestStatus === 'pending')
                    <p class="dl-access-status">Your request is waiting for an admin to approve it.</p>
                @elseif($requestStatus === 'denied')
                    <p class="dl-access-status dl-access-denied">Your last request was denied. You can ask again.</p>
                    <button type="button" id="dlRequestAccess" class="dl-btn dl-stamp">Request Access</button>
                @else
                    <button type="button" id="dlRequestAccess" class="dl-btn dl-stamp">Request Access</button>
                @endif
            </div>
        @else

        @unless($hasKey)
            <div class="dl-notice">
                <span class="dl-eyebrow">Setup needed</span>
                <p>No Gemini API key is configured yet. Get a free key at <code>aistudio.google.com/apikey</code> and set <code>GEMINI_API_KEY</code> in the server's <code>.env</code> file, then reload this page.</p>
            </div>
        @endunless

        <div class="dl-board">
            <div class="dl-board-head">
                <span class="dl-dot"></span>
                <span>Live conversation</span>
            </div>

            <div class="dl-messages" id="dlMessages">
                @forelse($history as $entry)
                    @php
                        $role = $entry['role'] ?? null;
                        $part = $entry['parts'][0] ?? [];
                    @endphp
                    @if($role === 'user' && isset($part['text']))
                        <div class="dl-msg dl-msg-user">
                            <span class="dl-msg-tag">YOU</span>
                            <p>{{ $part['text'] }}</p>
                        </div>
                    @elseif($role === 'model' && isset($part['text']))
                        <div class="dl-msg dl-msg-assistant">
                            <span class="dl-msg-tag">ASSISTANT</span>
                            <p>{{ $part['text'] }}</p>
                        </div>
                    @elseif(isset($part['functionCall']))
                        <div class="dl-msg dl-msg-tool">
                            <span class="dl-msg-tag">TOOL</span>
                            <p>Used <code>{{ $part['functionCall']['name'] ?? 'unknown' }}</code></p>
                        </div>
                    @endif
                @empty
                    <div class="dl-empty">
                        <p>No messages yet — try "What were today's sales?" or "Which products sold best this month?"</p>
                    </div>
                @endforelse
            </div>

            @if($pending)
                <div class="dl-confirm" id="dlConfirm">
                    <span class="dl-eyebrow">Confirmation needed</span>
                    <p>{{ $pending['summary'] }}</p>
                    <div class="dl-confirm-actions">
                        <button type="button" class="dl-btn dl-stamp" data-decision="confirm">Confirm</button>
                        <button type="button" class="dl-btn dl-cut" data-decision="cancel">Cancel</button>
                    </div>
                </div>
            @endif

            <form id="dlForm" class="dl-input-row" autocomplete="off">
                <input type="text" id="dlInput" placeholder="Ask a question…" maxlength="2000" {{ $hasKey ? '' : 'disabled' }} {{ $pending ? 'disabled' : '' }}>
                <button type="submit" class="dl-btn dl-stamp" id="dlSend" {{ $hasKey ? '' : 'disabled' }} {{ $pending ? 'disabled' : '' }}>Send</button>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
    .dockline{
        --radius:2px; --ease:cubic-bezier(.22,.9,.32,1);
        --bg:#F4F0E6; --bg-2:#EAE3D2; --surface:#FFFFFF;
        --ink:#12181C; --ink-soft:#5A6570; --line:rgba(18,24,28,.14);
        --accent:#B96E10; --accent-text:#12181C; --teal-c:#177264; --danger:#B94A10;
        background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif;
        min-height:100vh; padding:32px 0;
    }
    html[data-theme="dark"] .dockline{
        --bg:#0A1420; --bg-2:#0E1926; --surface:#101B27;
        --ink:#ECE6D8; --ink-soft:#93A4B0; --line:rgba(236,230,216,.14);
        --accent:#FFB020; --accent-text:#0A1420; --teal-c:#2FD9C0; --danger:#FF6B4A;
    }
    .dockline *{box-sizing:border-box;}
    .dockline h1{font-family:'Fraunces',serif; font-weight:500; font-size:clamp(26px,3vw,36px); margin:6px 0 10px;}
    .dockline code{font-family:'IBM Plex Mono',monospace; background:var(--bg-2); padding:1px 6px; border-radius:3px; font-size:12.5px;}
    .dl-wrap{max-width:900px; margin:0 auto; padding:0 24px;}
    .dl-eyebrow{font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.16em; text-transform:uppercase; color:var(--accent); font-weight:600;}
    .dl-sub{color:var(--ink-soft); font-size:14px; max-width:60ch; line-height:1.6; margin-top:2px;}
    .dl-head{display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; align-items:flex-end; margin-bottom:24px;}
    .dl-head-actions{display:flex; gap:10px;}

    .dl-btn{font-family:'IBM Plex Mono',monospace; font-size:12.5px; letter-spacing:.06em; text-transform:uppercase; text-decoration:none; display:inline-flex; align-items:center; gap:8px; padding:11px 18px; cursor:pointer; border:none; transition:transform .35s var(--ease), box-shadow .35s var(--ease); border-radius:0;}
    .dl-stamp{background:var(--accent); color:var(--accent-text); font-weight:600; clip-path:polygon(0 0,100% 0,100% 70%,92% 100%,0 100%); box-shadow:0 1px 0 rgba(0,0,0,.15);}
    .dl-stamp:hover:not(:disabled){transform:translate(-2px,-3px) rotate(-.6deg); box-shadow:4px 6px 0 rgba(0,0,0,.3);}
    .dl-stamp:disabled{opacity:.45; cursor:not-allowed;}
    .dl-cut{background:transparent; color:var(--ink); border:1px dashed color-mix(in srgb, var(--ink) 35%, transparent);}
    .dl-cut:hover{background:var(--surface); border-color:var(--ink); transform:translateY(-2px);}

    .dl-model-row{display:flex; align-items:center; gap:10px; margin-bottom:16px;}
    .dl-model-row select{font-family:'IBM Plex Mono',monospace; font-size:12px; padding:8px 10px; border:1px solid var(--line); background:var(--surface); color:var(--ink); border-radius:4px; max-width:100%;}
    .dl-model-saved{font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--teal-c); opacity:0; transition:opacity .3s;}
    .dl-model-saved.show{opacity:1;}

    .dl-notice{background:var(--surface); border:1px solid var(--accent); border-left-width:4px; border-radius:4px; padding:16px 18px; margin-bottom:20px;}
    .dl-notice p{font-size:13.5px; color:var(--ink-soft); margin-top:6px; line-height:1.6;}

    .dl-access-card{padding:28px; text-align:center;}
    .dl-access-card p{font-size:14px; color:var(--ink-soft); max-width:52ch; margin:10px auto 16px; line-height:1.6;}
    .dl-access-status{font-family:'IBM Plex Mono',monospace; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:var(--accent) !important;}
    .dl-access-denied{color:var(--danger) !important;}

    .dl-board{background:var(--surface); border:1px solid var(--line); border-radius:4px; box-shadow:0 10px 30px rgba(0,0,0,.08);}
    html[data-theme="dark"] .dl-board{box-shadow:0 10px 30px rgba(0,0,0,.35);}
    .dl-board-head{display:flex; align-items:center; gap:8px; padding:14px 20px; border-bottom:1px solid var(--line); font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.12em; text-transform:uppercase; color:var(--ink-soft);}
    .dl-dot{width:8px; height:8px; border-radius:50%; background:var(--teal-c); animation:dlPulse 2s infinite;}
    @keyframes dlPulse{0%,100%{opacity:1;} 50%{opacity:.45;}}

    .dl-messages{padding:20px; display:flex; flex-direction:column; gap:14px; max-height:52vh; overflow-y:auto;}
    .dl-empty{color:var(--ink-soft); font-size:13.5px; text-align:center; padding:24px 0;}
    .dl-msg{max-width:78%; padding:12px 15px; border-radius:4px; border:1px solid var(--line); background:var(--bg-2);}
    .dl-msg p{font-size:14px; line-height:1.6; white-space:pre-wrap;}
    .dl-msg-tag{display:block; font-family:'IBM Plex Mono',monospace; font-size:10px; letter-spacing:.1em; color:var(--ink-soft); margin-bottom:4px;}
    .dl-msg-user{margin-left:auto; background:var(--surface);}
    .dl-msg-assistant{margin-right:auto;}
    .dl-msg-assistant .dl-msg-tag{color:var(--teal-c);}
    .dl-msg-tool{margin-right:auto; background:transparent; border-style:dashed; opacity:.85;}
    .dl-typing{display:flex; align-items:center; gap:8px;}
    .dl-dots{display:inline-flex; gap:4px;}
    .dl-dots i{width:6px; height:6px; border-radius:50%; background:var(--teal-c); display:inline-block; animation:dlTypingBounce 1.1s infinite ease-in-out;}
    .dl-dots i:nth-child(2){animation-delay:.15s;}
    .dl-dots i:nth-child(3){animation-delay:.3s;}
    @keyframes dlTypingBounce{0%,60%,100%{opacity:.3; transform:translateY(0);} 30%{opacity:1; transform:translateY(-3px);}}
    .dl-msg-tool p{font-size:12.5px; color:var(--ink-soft);}
    .dl-msg-error{border-color:var(--danger);}
    .dl-msg-error .dl-msg-tag{color:var(--danger);}

    .dl-confirm{margin:0 20px 16px; padding:16px 18px; border:1px solid var(--accent); border-radius:4px; background:color-mix(in srgb, var(--accent) 8%, var(--surface));}
    .dl-confirm p{font-size:14px; margin:8px 0 14px; line-height:1.5;}
    .dl-confirm-actions{display:flex; gap:10px;}

    .dl-input-row{display:flex; gap:10px; padding:16px 20px; border-top:1px solid var(--line);}
    .dl-input-row input{flex:1; font-family:'Inter',sans-serif; font-size:14px; padding:12px 14px; border:1px solid var(--line); border-radius:4px; background:var(--bg); color:var(--ink);}
    .dl-input-row input:focus{outline:none; border-color:var(--accent);}
    .dl-input-row input:disabled{opacity:.5;}

    @media (max-width:640px){
        .dl-msg{max-width:92%;}
    }
</style>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const messages = document.getElementById('dlMessages');
    const form = document.getElementById('dlForm');
    const input = document.getElementById('dlInput');
    const sendBtn = document.getElementById('dlSend');
    const newChatBtn = document.getElementById('dlNewChat');

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(body || {}),
        }).then(async (r) => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error(data.text || 'Request failed.');
            return data;
        });
    }

    function addMessage(tag, text, variant) {
        if (messages.querySelector('.dl-empty')) messages.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'dl-msg dl-msg-' + variant;
        const span = document.createElement('span');
        span.className = 'dl-msg-tag';
        span.textContent = tag;
        const p = document.createElement('p');
        p.textContent = text;
        div.appendChild(span);
        div.appendChild(p);
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return div;
    }

    function setBusy(busy) {
        input.disabled = busy;
        sendBtn.disabled = busy;
    }

    function showTyping() {
        if (messages.querySelector('.dl-empty')) messages.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'dl-msg dl-msg-assistant dl-typing';
        div.id = 'dlTyping';
        div.innerHTML = '<span class="dl-msg-tag">ASSISTANT</span><span class="dl-dots"><i></i><i></i><i></i></span>';
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    function hideTyping() {
        document.getElementById('dlTyping')?.remove();
    }

    function renderResult(data) {
        hideTyping();
        if (data.type === 'message') {
            addMessage('ASSISTANT', data.text, 'assistant');
            setBusy(false);
        } else if (data.type === 'confirm_required') {
            addMessage('TOOL', 'Wants to: ' + data.summary, 'tool');
            showConfirm(data.summary);
        } else if (data.type === 'error') {
            addMessage('ERROR', data.text, 'error');
            setBusy(false);
        }
    }

    function showConfirm(summary) {
        let box = document.getElementById('dlConfirm');
        if (box) box.remove();

        box = document.createElement('div');
        box.className = 'dl-confirm';
        box.id = 'dlConfirm';
        box.innerHTML = `
            <span class="dl-eyebrow">Confirmation needed</span>
            <p>${summary}</p>
            <div class="dl-confirm-actions">
                <button type="button" class="dl-btn dl-stamp" data-decision="confirm">Confirm</button>
                <button type="button" class="dl-btn dl-cut" data-decision="cancel">Cancel</button>
            </div>
        `;
        form.parentNode.insertBefore(box, form);
        setBusy(true);
    }

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = input.value.trim();
        if (!message) return;

        addMessage('YOU', message, 'user');
        input.value = '';
        setBusy(true);
        showTyping();

        post("{{ route('ai_assistant.chat') }}", { message })
            .then(renderResult)
            .catch((err) => { hideTyping(); addMessage('ERROR', err.message, 'error'); setBusy(false); });
    });

    messages.parentNode.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-decision]');
        if (!btn) return;

        const box = document.getElementById('dlConfirm');
        box?.remove();
        setBusy(true);
        showTyping();

        post("{{ route('ai_assistant.resolve') }}", { decision: btn.dataset.decision })
            .then(renderResult)
            .catch((err) => { hideTyping(); addMessage('ERROR', err.message, 'error'); setBusy(false); });
    });

    newChatBtn?.addEventListener('click', () => {
        post("{{ route('ai_assistant.reset') }}").then(() => window.location.reload());
    });

    document.getElementById('dlRequestAccess')?.addEventListener('click', (e) => {
        const btn = e.currentTarget;
        btn.disabled = true;
        post("{{ route('ai_assistant.request_access') }}")
            .then((data) => { btn.outerHTML = `<p class="dl-access-status">${data.message}</p>`; })
            .catch(() => { btn.disabled = false; });
    });

    const modelSelect = document.getElementById('dlModelSelect');
    modelSelect?.addEventListener('change', () => {
        const saved = document.getElementById('dlModelSaved');
        post("{{ route('ai_assistant.update_model') }}", { model: modelSelect.value })
            .then(() => {
                saved.textContent = 'Saved';
                saved.classList.add('show');
                setTimeout(() => saved.classList.remove('show'), 2000);
            })
            .catch((err) => { saved.textContent = err.message; saved.classList.add('show'); });
    });
})();
</script>
@endsection
