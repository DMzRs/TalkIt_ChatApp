<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TalkIt</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- Pusher JS --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d0d0f;
            --surface:   #16161a;
            --border:    #26262e;
            --accent:    #7c6af7;
            --accent2:   #f7a26a;
            --text:      #e8e8f0;
            --muted:     #6b6b80;
            --bubble-me: #7c6af7;
            --bubble-them: #1f1f28;
            --radius:    14px;
            --font-ui:   'Syne', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }

        html, body { height: 100%; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-ui);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Subtle grid overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,106,247,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,106,247,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        .shell {
            position: relative;
            z-index: 1;
            width: min(820px, 100vw);
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--surface);
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
        }

        /* ── Header ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
            flex-shrink: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-text {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(120deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .logo-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--accent2);
            box-shadow: 0 0 10px var(--accent2);
            animation: pulse 2.4s ease-in-out infinite;
            margin-top: 2px;
        }

        .header-divider {
            width: 1px;
            height: 24px;
            background: var(--border);
            margin: 0 4px;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 12px var(--accent); }
            50%       { opacity: .5; box-shadow: 0 0 4px var(--accent); }
        }

        .room-name {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -.3px;
        }

        .room-sub {
            font-size: 11px;
            color: var(--muted);
            font-family: var(--font-mono);
            margin-top: 1px;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 6px 14px 6px 8px;
        }

        .avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
        }

        .logout-btn {
            font-size: 11px;
            color: var(--muted);
            font-family: var(--font-mono);
            text-decoration: none;
            margin-left: 8px;
            transition: color .2s;
        }
        .logout-btn:hover { color: var(--accent2); }

        /* ── Message list ── */
        #messages {
            flex: 1;
            overflow-y: auto;
            padding: 28px 28px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            scroll-behavior: smooth;
        }

        #messages::-webkit-scrollbar { width: 4px; }
        #messages::-webkit-scrollbar-track { background: transparent; }
        #messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* Date divider */
        .date-divider {
            text-align: center;
            font-size: 10px;
            font-family: var(--font-mono);
            color: var(--muted);
            letter-spacing: .08em;
            margin: 12px 0 6px;
            position: relative;
        }

        .date-divider::before, .date-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 60px);
            height: 1px;
            background: var(--border);
        }
        .date-divider::before { left: 0; }
        .date-divider::after { right: 0; }

        /* Message row */
        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            animation: slideUp .18s ease-out both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .msg-row.mine {
            flex-direction: row-reverse;
        }

        .msg-row .avatar {
            width: 32px; height: 32px;
            font-size: 13px;
            flex-shrink: 0;
            align-self: flex-end;
        }

        .bubble-wrap { display: flex; flex-direction: column; max-width: 68%; }
        .msg-row.mine .bubble-wrap { align-items: flex-end; }

        .sender-name {
            font-size: 10px;
            font-family: var(--font-mono);
            color: var(--muted);
            margin-bottom: 3px;
            padding: 0 4px;
        }

        .bubble {
            padding: 10px 15px;
            border-radius: var(--radius);
            font-size: 14.5px;
            line-height: 1.55;
            word-break: break-word;
            position: relative;
        }

        .msg-row:not(.mine) .bubble {
            background: var(--bubble-them);
            border: 1px solid var(--border);
            border-bottom-left-radius: 4px;
            color: var(--text);
        }

        .msg-row.mine .bubble {
            background: var(--bubble-me);
            border-bottom-right-radius: 4px;
            color: #fff;
        }

        .bubble-time {
            font-size: 10px;
            font-family: var(--font-mono);
            color: var(--muted);
            margin-top: 3px;
            padding: 0 4px;
        }

        /* Grouped messages: hide avatar/name for consecutive same-sender msgs */
        .msg-row.grouped .avatar { visibility: hidden; }
        .msg-row.grouped .sender-name { display: none; }

        /* ── Composer ── */
        .composer {
            padding: 16px 28px 24px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            flex-shrink: 0;
        }

        .composer-inner {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: calc(var(--radius) + 4px);
            padding: 10px 10px 10px 18px;
            transition: border-color .2s;
        }

        .composer-inner:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(124,106,247,.12);
        }

        #message-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-family: var(--font-ui);
            font-size: 14.5px;
            resize: none;
            max-height: 120px;
            line-height: 1.5;
        }

        #message-input::placeholder { color: var(--muted); }

        .send-btn {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--accent);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .2s, transform .15s;
        }

        .send-btn:hover { background: #6a58e0; transform: scale(1.05); }
        .send-btn:active { transform: scale(.97); }
        .send-btn svg { width: 18px; height: 18px; }

        .send-btn:disabled { opacity: .4; cursor: default; transform: none; }

        /* ── Empty state ── */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--muted);
            font-family: var(--font-mono);
            font-size: 13px;
        }

        .empty-state svg { opacity: .25; }

        /* ── Typing indicator ── */
        #typing-indicator {
            height: 22px;
            padding: 0 28px 2px;
            font-size: 11px;
            font-family: var(--font-mono);
            color: var(--accent);
            transition: opacity .3s;
            opacity: 0;
        }
        #typing-indicator.visible { opacity: 1; }
    </style>
</head>
<body>

<div class="shell">

    {{-- Header --}}
    <header>
        <div class="header-left">
            <div class="logo-mark">
                <span class="logo-text">TalkIt</span>
                <div class="logo-dot"></div>
            </div>
            <div class="header-divider"></div>
            <div>
                <div class="room-name">Global Room</div>
                <div class="room-sub">public · everyone</div>
            </div>
        </div>
        <div class="user-chip">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <span class="user-name">{{ auth()->user()->name }}</span>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="logout-btn">sign out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
                @csrf
            </form>
        </div>
    </header>

    {{-- Messages --}}
    @if ($messages->isEmpty())
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span>No messages yet — say hello!</span>
        </div>
    @else
        <div id="messages">
            @php $prevUserId = null; @endphp
            @foreach ($messages as $msg)
                @php
                    $isMe = $msg->user_id === auth()->id();
                    $grouped = $prevUserId === $msg->user_id;
                    $prevUserId = $msg->user_id;
                @endphp
                <div class="msg-row {{ $isMe ? 'mine' : '' }} {{ $grouped ? 'grouped' : '' }}"
                     data-user="{{ $msg->user_id }}">
                    <div class="avatar">{{ strtoupper(substr($msg->user->name, 0, 1)) }}</div>
                    <div class="bubble-wrap">
                        @if (!$grouped)
                            <div class="sender-name">{{ $isMe ? 'you' : $msg->user->name }}</div>
                        @endif
                        <div class="bubble">{{ $msg->content }}</div>
                        <div class="bubble-time">{{ $msg->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Typing indicator --}}
    <div id="typing-indicator"></div>

    {{-- Composer --}}
    <div class="composer">
        <div class="composer-inner">
            <textarea id="message-input"
                      rows="1"
                      placeholder="Type a message…"
                      autofocus></textarea>
            <button class="send-btn" id="send-btn" disabled title="Send">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>

</div>

<script>
    // ── Config ──────────────────────────────────────────────
    const ME       = {{ auth()->id() }};
    const ME_NAME  = @json(auth()->user()->name);
    const CSRF     = document.querySelector('meta[name="csrf-token"]').content;

    // ── DOM ─────────────────────────────────────────────────
    const msgList  = document.getElementById('messages');
    const input    = document.getElementById('message-input');
    const sendBtn  = document.getElementById('send-btn');
    const typingEl = document.getElementById('typing-indicator');

    // Create message list if empty state was rendered
    function getOrCreateList() {
        let list = document.getElementById('messages');
        if (!list) {
            const empty = document.querySelector('.empty-state');
            if (empty) empty.remove();
            list = document.createElement('div');
            list.id = 'messages';
            list.className = '';
            list.style.cssText = 'flex:1;overflow-y:auto;padding:28px 28px 16px;display:flex;flex-direction:column;gap:6px;scroll-behavior:smooth';
            document.querySelector('.shell').insertBefore(list, document.getElementById('typing-indicator'));
        }
        return list;
    }

    // ── Helpers ─────────────────────────────────────────────
    function formatTime(iso) {
        const d = new Date(iso);
        return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }

    function lastUserId() {
        const rows = document.querySelectorAll('.msg-row');
        return rows.length ? parseInt(rows[rows.length - 1].dataset.user) : null;
    }

    function appendMessage(msg, isOptimistic = false) {
        const list    = getOrCreateList();
        const isMe    = msg.user.id === ME;
        const grouped = lastUserId() === msg.user.id;
        const name    = isMe ? 'you' : msg.user.name;
        const initial = msg.user.name.charAt(0).toUpperCase();
        const time    = formatTime(msg.created_at);

        const row = document.createElement('div');
        row.className = `msg-row${isMe ? ' mine' : ''}${grouped ? ' grouped' : ''}`;
        row.dataset.user = msg.user.id;
        if (isOptimistic) row.dataset.optimistic = '1';

        row.innerHTML = `
            <div class="avatar">${initial}</div>
            <div class="bubble-wrap">
                ${grouped ? '' : `<div class="sender-name">${name}</div>`}
                <div class="bubble">${escHtml(msg.content)}</div>
                <div class="bubble-time">${time}</div>
            </div>`;

        list.appendChild(row);
        list.scrollTop = list.scrollHeight;
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                  .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // ── Auto-resize textarea ────────────────────────────────
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = input.scrollHeight + 'px';
        sendBtn.disabled = input.value.trim() === '';
        notifyTyping();
    });

    // ── Send message ────────────────────────────────────────
    async function sendMessage() {
        const content = input.value.trim();
        if (!content) return;

        sendBtn.disabled = true;
        input.value = '';
        input.style.height = 'auto';

        // Optimistic render
        appendMessage({
            id: 'tmp-' + Date.now(),
            content,
            created_at: new Date().toISOString(),
            user: { id: ME, name: ME_NAME },
        }, true);

        try {
            await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({ content }),
            });
        } catch (e) {
            console.error('Send failed', e);
        }
    }

    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // ── Scroll to bottom on load ────────────────────────────
    if (msgList) msgList.scrollTop = msgList.scrollHeight;

    // ── Pusher / real-time ───────────────────────────────────
    Pusher.logToConsole = false;

    const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        forceTLS: true,
    });

    const channel = pusher.subscribe('chat');

    channel.bind('App\\Events\\MessageSent', (data) => {
        // Skip messages from myself (already shown optimistically)
        if (data.user.id === ME) return;
        appendMessage(data);
    });

    // ── Typing indicator ─────────────────────────────────────
    let typingTimeout;
    const typingChannel = pusher.subscribe('presence-typing');

    // We keep it simple with a client-events approach using a private channel
    // or just hide this feature gracefully if not configured
    function notifyTyping() { /* requires private channel auth — see README */ }

    // ── Connection state badge ───────────────────────────────
    pusher.connection.bind('connected', () => {
        document.querySelector('.room-sub').textContent = 'public · live';
    });
    pusher.connection.bind('disconnected', () => {
        document.querySelector('.room-sub').textContent = 'public · reconnecting…';
    });
</script>
</body>
</html>
