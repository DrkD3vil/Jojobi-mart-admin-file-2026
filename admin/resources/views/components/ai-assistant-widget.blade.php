{{-- Global floating AI Assistant launcher + quick-access modal.
     Present on every authenticated page; talks to the same session-backed
     endpoints as the full /ai-assistant page, so a conversation started
     here continues there and vice versa. --}}
<button type="button" id="aiwFab" class="aiw-fab" title="AI Assistant" aria-label="Open AI Assistant">
    <i data-lucide="bot" class="w-6 h-6"></i>
</button>

<div id="aiwOverlay" class="aiw-overlay hidden">
    <div class="aiw-modal">
        <div class="aiw-head">
            <div>
                <span class="aiw-eyebrow">AI Assistant</span>
                <h2>Quick ask</h2>
            </div>
            <div class="aiw-head-actions">
                <button type="button" id="aiwClear" class="aiw-icon-btn" title="Clear this conversation view (your activity log is kept)">
                    <i data-lucide="eraser" class="w-4 h-4"></i>
                </button>
                <a href="{{ route('ai_assistant.index') }}" class="aiw-icon-btn" title="Open full page">
                    <i data-lucide="maximize-2" class="w-4 h-4"></i>
                </a>
                <button type="button" id="aiwClose" class="aiw-icon-btn" title="Close">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div id="aiwBody" class="aiw-body">
            <div class="aiw-loading">Loading…</div>
        </div>
    </div>
</div>

<style>
    .aiw-fab{
        position:fixed; right:24px; bottom:24px; z-index:1000;
        width:56px; height:56px; border-radius:50%; border:none; cursor:pointer;
        background:linear-gradient(135deg, #FFB020, #B96E10); color:#0A1420;
        display:flex; align-items:center; justify-content:center;
        box-shadow:0 6px 20px rgba(185,110,16,.45);
        transition:transform .3s cubic-bezier(.22,.9,.32,1), box-shadow .3s;
    }
    .aiw-fab:hover{transform:translateY(-3px) scale(1.05); box-shadow:0 10px 26px rgba(185,110,16,.55);}
    .aiw-fab.aiw-has-alert::after{
        content:''; position:absolute; top:2px; right:2px; width:12px; height:12px; border-radius:50%;
        background:#2FD9C0; border:2px solid #0A1420;
    }

    .aiw-overlay{
        position:fixed; inset:0; z-index:1001; background:rgba(10,20,32,.55);
        display:flex; align-items:flex-end; justify-content:flex-end; padding:24px;
        backdrop-filter:blur(2px);
    }
    .aiw-overlay.hidden{display:none;}

    .aiw-modal{
        width:min(420px, 100%); max-height:min(640px, 80vh); display:flex; flex-direction:column;
        background:#101B27; color:#ECE6D8; border:1px solid rgba(236,230,216,.14); border-radius:8px;
        box-shadow:0 20px 50px rgba(0,0,0,.45); font-family:'Inter',sans-serif;
        animation:aiwRise .25s cubic-bezier(.22,.9,.32,1);
    }
    html[data-theme="light"] .aiw-modal{background:#FFFFFF; color:#12181C; border-color:rgba(18,24,28,.14); box-shadow:0 20px 50px rgba(18,24,28,.18);}
    @keyframes aiwRise{from{opacity:0; transform:translateY(12px);} to{opacity:1; transform:none;}}

    .aiw-head{display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-bottom:1px solid rgba(236,230,216,.14);}
    html[data-theme="light"] .aiw-head{border-color:rgba(18,24,28,.14);}
    .aiw-head h2{font-family:'Fraunces',serif; font-weight:500; font-size:18px; margin:2px 0 0;}
    .aiw-eyebrow{font-family:'IBM Plex Mono',monospace; font-size:10px; letter-spacing:.14em; text-transform:uppercase; color:#FFB020; font-weight:600;}
    html[data-theme="light"] .aiw-eyebrow{color:#B96E10;}
    .aiw-head-actions{display:flex; gap:6px;}
    .aiw-icon-btn{width:30px; height:30px; border-radius:4px; border:1px solid rgba(236,230,216,.14); background:transparent; color:inherit; display:flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none;}
    html[data-theme="light"] .aiw-icon-btn{border-color:rgba(18,24,28,.14);}
    .aiw-icon-btn:hover{background:rgba(236,230,216,.08);}

    .aiw-body{flex:1; overflow-y:auto; padding:16px 18px; display:flex; flex-direction:column; gap:12px;}
    .aiw-loading, .aiw-empty{color:#93A4B0; font-size:13px; text-align:center; padding:24px 0;}

    .aiw-msg{max-width:88%; padding:10px 13px; border-radius:6px; border:1px solid rgba(236,230,216,.14); background:rgba(236,230,216,.04); font-size:13.5px; line-height:1.55;}
    html[data-theme="light"] .aiw-msg{border-color:rgba(18,24,28,.1); background:rgba(18,24,28,.03);}
    .aiw-msg-tag{display:block; font-family:'IBM Plex Mono',monospace; font-size:9.5px; letter-spacing:.08em; color:#93A4B0; margin-bottom:3px;}
    .aiw-msg-user{margin-left:auto; background:rgba(236,230,216,.08);}
    .aiw-msg-assistant .aiw-msg-tag{color:#2FD9C0;}
    .aiw-msg-tool{opacity:.8; font-size:12px; border-style:dashed;}
    .aiw-dots{display:inline-flex; gap:3px;}
    .aiw-dots i{width:5px; height:5px; border-radius:50%; background:#2FD9C0; display:inline-block; animation:aiwDotBounce 1.1s infinite ease-in-out;}
    .aiw-dots i:nth-child(2){animation-delay:.15s;}
    .aiw-dots i:nth-child(3){animation-delay:.3s;}
    @keyframes aiwDotBounce{0%,60%,100%{opacity:.3; transform:translateY(0);} 30%{opacity:1; transform:translateY(-2px);}}
    .aiw-msg-error{border-color:#FF6B4A;}
    .aiw-msg-error .aiw-msg-tag{color:#FF6B4A;}

    .aiw-confirm{border:1px solid #FFB020; border-radius:6px; padding:12px 14px; background:rgba(255,176,32,.08);}
    html[data-theme="light"] .aiw-confirm{border-color:#B96E10; background:rgba(185,110,16,.06);}
    .aiw-confirm p{font-size:13.5px; margin:6px 0 10px; line-height:1.5;}
    .aiw-confirm-actions{display:flex; gap:8px;}

    .aiw-access-card{text-align:center; padding:20px 8px;}
    .aiw-access-card p{font-size:13.5px; color:#93A4B0; margin:8px 0 14px; line-height:1.6;}

    .aiw-btn{font-family:'IBM Plex Mono',monospace; font-size:12px; letter-spacing:.05em; text-transform:uppercase; padding:9px 15px; border-radius:4px; cursor:pointer; border:none; transition:transform .2s;}
    .aiw-btn-primary{background:#FFB020; color:#0A1420; font-weight:600;}
    html[data-theme="light"] .aiw-btn-primary{background:#B96E10; color:#fff;}
    .aiw-btn-primary:hover:not(:disabled){transform:translateY(-1px);}
    .aiw-btn-primary:disabled{opacity:.5; cursor:not-allowed;}
    .aiw-btn-secondary{background:transparent; border:1px dashed rgba(236,230,216,.35); color:inherit;}
    html[data-theme="light"] .aiw-btn-secondary{border-color:rgba(18,24,28,.35);}

    .aiw-input-row{display:flex; gap:8px; padding:12px 14px; border-top:1px solid rgba(236,230,216,.14);}
    html[data-theme="light"] .aiw-input-row{border-color:rgba(18,24,28,.14);}
    .aiw-input-row input{flex:1; font-family:'Inter',sans-serif; font-size:13.5px; padding:9px 11px; border:1px solid rgba(236,230,216,.18); border-radius:4px; background:transparent; color:inherit;}
    html[data-theme="light"] .aiw-input-row input{border-color:rgba(18,24,28,.18);}
    .aiw-input-row input:focus{outline:none; border-color:#FFB020;}
    .aiw-input-row input:disabled{opacity:.5;}

    @media (max-width:480px){
        .aiw-overlay{padding:0;}
        .aiw-modal{width:100%; max-height:100vh; height:100vh; border-radius:0;}
    }
</style>

<script>
(function () {
    const fab = document.getElementById('aiwFab');
    const overlay = document.getElementById('aiwOverlay');
    const closeBtn = document.getElementById('aiwClose');
    const clearBtn = document.getElementById('aiwClear');
    const body = document.getElementById('aiwBody');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let loaded = false;

    function post(url, payload) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload || {}),
        }).then(async (r) => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error(data.text || data.message || 'Request failed.');
            return data;
        });
    }

    function get(url) {
        return fetch(url, { headers: { 'Accept': 'application/json' } }).then((r) => r.json());
    }

    function open() {
        overlay.classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
        if (!loaded) {
            loaded = true;
            refresh();
        }
    }

    function close() {
        overlay.classList.add('hidden');
    }

    fab.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    clearBtn.addEventListener('click', () => {
        clearBtn.disabled = true;
        post("{{ route('ai_assistant.reset') }}")
            .then(refresh)
            .finally(() => { clearBtn.disabled = false; });
    });
    overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !overlay.classList.contains('hidden')) close(); });

    function refresh() {
        body.innerHTML = '<div class="aiw-loading">Loading…</div>';
        get("{{ route('ai_assistant.state') }}")
            .then(renderState)
            .catch(() => { body.innerHTML = '<div class="aiw-loading">Could not load the assistant.</div>'; });
    }

    function renderState(state) {
        if (!state.has_access) {
            renderAccessScreen(state);
            return;
        }
        if (!state.has_key) {
            body.innerHTML = '<div class="aiw-access-card"><p>No Gemini API key is configured yet. Add <code>GEMINI_API_KEY</code> to the server\'s .env file.</p></div>';
            return;
        }
        renderChat(state);
    }

    function renderAccessScreen(state) {
        let action = `<button type="button" class="aiw-btn aiw-btn-primary" id="aiwRequest">Request Access</button>`;
        let statusLine = '';
        if (state.request_status === 'pending') {
            action = '';
            statusLine = '<p style="color:#FFB020;font-family:\'IBM Plex Mono\',monospace;font-size:11.5px;text-transform:uppercase;">Waiting for admin approval</p>';
        } else if (state.request_status === 'denied') {
            statusLine = '<p style="color:#FF6B4A;font-family:\'IBM Plex Mono\',monospace;font-size:11.5px;text-transform:uppercase;">Previous request denied</p>';
        }

        body.innerHTML = `
            <div class="aiw-access-card">
                <p>You don't have permission to use the AI Assistant yet.</p>
                ${statusLine}
                ${action}
            </div>
        `;

        document.getElementById('aiwRequest')?.addEventListener('click', (e) => {
            e.target.disabled = true;
            post("{{ route('ai_assistant.request_access') }}").then(refresh);
        });
    }

    function renderChat(state) {
        const items = (state.history || []).map((entry) => {
            const role = entry.role;
            const part = (entry.parts || [])[0] || {};
            if (role === 'user' && part.text) return msgHtml('YOU', part.text, 'user');
            if (role === 'model' && part.text) return msgHtml('ASSISTANT', part.text, 'assistant');
            if (part.functionCall) return msgHtml('TOOL', 'Used ' + (part.functionCall.name || 'a tool'), 'tool');
            return '';
        }).join('') || '<div class="aiw-empty">Ask something like "What were today\'s sales?"</div>';

        let confirmHtml = '';
        if (state.pending) {
            confirmHtml = `
                <div class="aiw-confirm" id="aiwConfirm">
                    <strong style="font-size:12px;">Confirmation needed</strong>
                    <p>${state.pending.summary}</p>
                    <div class="aiw-confirm-actions">
                        <button type="button" class="aiw-btn aiw-btn-primary" data-decision="confirm">Confirm</button>
                        <button type="button" class="aiw-btn aiw-btn-secondary" data-decision="cancel">Cancel</button>
                    </div>
                </div>
            `;
        }

        body.innerHTML = `
            <div id="aiwMessages" style="display:flex; flex-direction:column; gap:10px; flex:1; overflow-y:auto;">${items}</div>
            ${confirmHtml}
            <form id="aiwForm" class="aiw-input-row" autocomplete="off" style="margin:0 -18px -16px;">
                <input type="text" id="aiwInput" placeholder="Ask a question…" maxlength="2000" ${state.pending ? 'disabled' : ''}>
                <button type="submit" class="aiw-btn aiw-btn-primary" id="aiwSend" ${state.pending ? 'disabled' : ''}>Send</button>
            </form>
        `;

        const messages = document.getElementById('aiwMessages');
        messages.scrollTop = messages.scrollHeight;

        const form = document.getElementById('aiwForm');
        const input = document.getElementById('aiwInput');
        const sendBtn = document.getElementById('aiwSend');

        function addMessage(tag, text, variant) {
            const empty = messages.querySelector('.aiw-empty');
            if (empty) empty.remove();
            messages.insertAdjacentHTML('beforeend', msgHtml(tag, text, variant));
            messages.scrollTop = messages.scrollHeight;
        }

        function setBusy(busy) {
            input.disabled = busy;
            sendBtn.disabled = busy;
        }

        function showTyping() {
            const empty = messages.querySelector('.aiw-empty');
            if (empty) empty.remove();
            messages.insertAdjacentHTML('beforeend', '<div class="aiw-msg aiw-msg-assistant" id="aiwTyping"><span class="aiw-msg-tag">ASSISTANT</span><span class="aiw-dots"><i></i><i></i><i></i></span></div>');
            messages.scrollTop = messages.scrollHeight;
        }

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;
            addMessage('YOU', message, 'user');
            input.value = '';
            setBusy(true);
            showTyping();

            post("{{ route('ai_assistant.chat') }}", { message })
                .then((data) => handleResult(data, addMessage, setBusy))
                .catch((err) => { document.getElementById('aiwTyping')?.remove(); addMessage('ERROR', err.message, 'error'); setBusy(false); });
        });

        body.addEventListener('click', function handler(e) {
            const btn = e.target.closest('[data-decision]');
            if (!btn) return;
            document.getElementById('aiwConfirm')?.remove();
            setBusy(true);
            showTyping();
            post("{{ route('ai_assistant.resolve') }}", { decision: btn.dataset.decision })
                .then((data) => handleResult(data, addMessage, setBusy))
                .catch((err) => { document.getElementById('aiwTyping')?.remove(); addMessage('ERROR', err.message, 'error'); setBusy(false); });
        });
    }

    function handleResult(data, addMessage, setBusy) {
        document.getElementById('aiwTyping')?.remove();
        if (data.type === 'message') {
            addMessage('ASSISTANT', data.text, 'assistant');
            setBusy(false);
        } else if (data.type === 'confirm_required') {
            addMessage('TOOL', 'Wants to: ' + data.summary, 'tool');
            const messages = document.getElementById('aiwMessages');
            messages.insertAdjacentHTML('afterend', `
                <div class="aiw-confirm" id="aiwConfirm">
                    <strong style="font-size:12px;">Confirmation needed</strong>
                    <p>${data.summary}</p>
                    <div class="aiw-confirm-actions">
                        <button type="button" class="aiw-btn aiw-btn-primary" data-decision="confirm">Confirm</button>
                        <button type="button" class="aiw-btn aiw-btn-secondary" data-decision="cancel">Cancel</button>
                    </div>
                </div>
            `);
        } else if (data.type === 'error') {
            addMessage('ERROR', data.text, 'error');
            setBusy(false);
        }
    }

    function msgHtml(tag, text, variant) {
        const div = document.createElement('div');
        div.textContent = text;
        return `<div class="aiw-msg aiw-msg-${variant}"><span class="aiw-msg-tag">${tag}</span><p>${div.innerHTML}</p></div>`;
    }
})();
</script>
