<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TalkIt</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,300&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #f5f0e8;
            --surface:      #fdfaf5;
            --surface2:     #f0ebe0;
            --border:       #e2d9c8;
            --accent:       #c17f4a;
            --accent-soft:  #f5e6d6;
            --accent-hover: #a86835;
            --green:        #4a7c59;
            --green-soft:   #e3efe6;
            --text:         #2c2416;
            --text2:        #6b5c45;
            --muted:        #a8967c;
            --bubble-me:    #c17f4a;
            --bubble-them:  #ffffff;
            --shadow:       rgba(44,36,22,.08);
            --radius:       18px;
            --font-display: 'Fraunces', Georgia, serif;
            --font-body:    'Nunito', sans-serif;
        }

        html, body { height: 100%; overflow: hidden; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            display: flex;
            align-items: stretch;
            justify-content: center;
        }

        /* Warm noise texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .shell {
            position: relative;
            z-index: 1;
            width: min(780px, 100vw);
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--surface);
            box-shadow: 0 0 60px var(--shadow), 0 0 0 1px var(--border);
        }

        /* ── Header ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .header-left { display: flex; align-items: center; gap: 12px; }

        .logo-wrap {
            display: flex;
            align-items: baseline;
            gap: 2px;
        }

        .logo-talk {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -.5px;
            line-height: 1;
        }

        .logo-it {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 300;
            font-style: italic;
            color: var(--accent);
            letter-spacing: -.5px;
            line-height: 1;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            background: var(--green-soft);
            border: 1px solid rgba(74,124,89,.2);
            border-radius: 100px;
            padding: 3px 10px 3px 7px;
            font-size: 11px;
            font-weight: 600;
            color: var(--green);
            letter-spacing: .01em;
        }

        .status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: breathe 2.5s ease-in-out infinite;
        }

        @keyframes breathe {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.8); }
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, #e8a96e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(193,127,74,.35);
        }

        .user-info { display: flex; flex-direction: column; }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .logout-btn {
            font-size: 11px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            transition: color .2s;
            line-height: 1;
        }
        .logout-btn:hover { color: var(--accent); }

        /* ── Messages ── */
        #messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px 24px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            scroll-behavior: smooth;
            background: var(--bg);
        }

        #messages::-webkit-scrollbar { width: 5px; }
        #messages::-webkit-scrollbar-track { background: transparent; }
        #messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        /* Date divider */
        .date-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .date-divider::before, .date-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Message row */
        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            animation: popIn .2s cubic-bezier(.34,1.56,.64,1) both;
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(.92) translateY(6px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .msg-row.mine { flex-direction: row-reverse; }

        .msg-row .avatar {
            width: 30px; height: 30px;
            font-size: 12px;
            align-self: flex-end;
            margin-bottom: 2px;
            flex-shrink: 0;
        }

        .bubble-wrap {
            display: flex;
            flex-direction: column;
            max-width: 65%;
            gap: 2px;
        }
        .msg-row.mine .bubble-wrap { align-items: flex-end; }

        .sender-name {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            padding: 0 6px;
            letter-spacing: .01em;
        }

        .bubble {
            padding: 10px 14px;
            border-radius: var(--radius);
            font-size: 14px;
            line-height: 1.6;
            word-break: break-word;
        }

        .msg-row:not(.mine) .bubble {
            background: var(--bubble-them);
            border: 1px solid var(--border);
            border-bottom-left-radius: 5px;
            color: var(--text);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .msg-row.mine .bubble {
            background: var(--bubble-me);
            border-bottom-right-radius: 5px;
            color: #fff;
            box-shadow: 0 2px 12px rgba(193,127,74,.3);
        }

        .bubble-time {
            font-size: 10px;
            font-weight: 600;
            color: var(--muted);
            padding: 0 6px;
            letter-spacing: .02em;
        }

        .msg-row.grouped .avatar { visibility: hidden; }
        .msg-row.grouped .sender-name { display: none; }

        /* ── Empty state ── */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            color: var(--muted);
        }

        .empty-icon {
            width: 64px; height: 64px;
            background: var(--accent-soft);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .empty-state h3 {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 500;
            color: var(--text2);
        }

        .empty-state p {
            font-size: 13px;
            color: var(--muted);
        }

        /* ── Typing ── */
        #typing-indicator {
            height: 20px;
            padding: 0 24px 2px;
            font-size: 11px;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: .01em;
            opacity: 0;
            transition: opacity .3s;
        }
        #typing-indicator.visible { opacity: 1; }

        /* ── Composer ── */
        .composer {
            padding: 12px 20px 20px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .composer-inner {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: 24px;
            padding: 10px 10px 10px 18px;
            transition: border-color .25s, box-shadow .25s;
        }

        .composer-inner:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(193,127,74,.12);
            background: #fff;
        }

        #message-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-family: var(--font-body);
            font-size: 14px;
            resize: none;
            max-height: 110px;
            line-height: 1.55;
        }

        #message-input::placeholder { color: var(--muted); }

        .send-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--accent);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 2px 8px rgba(193,127,74,.4);
        }

        .send-btn:hover {
            background: var(--accent-hover);
            transform: scale(1.08);
            box-shadow: 0 4px 14px rgba(193,127,74,.5);
        }
        .send-btn:active { transform: scale(.95); }
        .send-btn svg { width: 16px; height: 16px; margin-left: 1px; }
        .send-btn:disabled { opacity: .35; cursor: default; transform: none; box-shadow: none; }

        /* room sub text */
        .room-sub { font-size: 11px; color: var(--muted); font-weight: 500; }
    </style>
</head>
<body>
<div class="shell">

    {{-- Header --}}
    <header>
        <div class="header-left">
            <div class="logo-wrap">
                <span class="logo-talk">Talk</span><span class="logo-it">it</span>
            </div>
            <div class="status-pill">
                <div class="status-dot"></div>
                <span id="status-text">Global Room</span>
            </div>
        </div>
        <div class="user-area">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="logout-btn">Sign out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    {{-- Messages --}}
    @if ($messages->isEmpty())
        <div class="empty-state" id="empty-state">
            <div class="empty-icon">💬</div>
            <h3>Start the conversation</h3>
            <p>Be the first to say something!</p>
        </div>
    @else
        <div id="messages">
            @php $prevUserId = null; @endphp
            @foreach ($messages as $msg)
                @php
                    $isMe    = $msg->user_id === auth()->id();
                    $grouped = $prevUserId === $msg->user_id;
                    $prevUserId = $msg->user_id;
                @endphp
                <div class="msg-row {{ $isMe ? 'mine' : '' }} {{ $grouped ? 'grouped' : '' }}"
                     data-user="{{ $msg->user_id }}">
                    <div class="avatar">{{ strtoupper(substr($msg->user->name, 0, 1)) }}</div>
                    <div class="bubble-wrap">
                        @if (!$grouped)
                            <div class="sender-name">{{ $isMe ? 'You' : $msg->user->name }}</div>
                        @endif
                        <div class="bubble">{{ $msg->content }}</div>
                        <div class="bubble-time">{{ $msg->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div id="typing-indicator"></div>

    {{-- Composer --}}
    <div class="composer">
        <div class="composer-inner">
            <textarea id="message-input" rows="1" placeholder="Say something…" autofocus></textarea>
            <button class="send-btn" id="send-btn" disabled title="Send">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>

</div>

<script>
    const ME      = {{ auth()->id() }};
    const ME_NAME = @json(auth()->user()->name);
    const CSRF    = document.querySelector('meta[name="csrf-token"]').content;

    const msgList  = document.getElementById('messages');
    const input    = document.getElementById('message-input');
    const sendBtn  = document.getElementById('send-btn');

    function getOrCreateList() {
        let list = document.getElementById('messages');
        if (!list) {
            const empty = document.getElementById('empty-state');
            if (empty) empty.remove();
            list = document.createElement('div');
            list.id = 'messages';
            list.style.cssText = 'flex:1;overflow-y:auto;padding:24px 24px 12px;display:flex;flex-direction:column;gap:4px;scroll-behavior:smooth;background:var(--bg)';
            document.querySelector('.shell').insertBefore(list, document.getElementById('typing-indicator'));
        }
        return list;
    }

    function formatTime(iso) {
        return new Date(iso).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }

    function lastUserId() {
        const rows = document.querySelectorAll('.msg-row');
        return rows.length ? parseInt(rows[rows.length - 1].dataset.user) : null;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function appendMessage(msg) {
        const list    = getOrCreateList();
        const isMe    = msg.user.id === ME;
        const grouped = lastUserId() === msg.user.id;
        const initial = msg.user.name.charAt(0).toUpperCase();

        const row = document.createElement('div');
        row.className = `msg-row${isMe ? ' mine' : ''}${grouped ? ' grouped' : ''}`;
        row.dataset.user = msg.user.id;

        row.innerHTML = `
            <div class="avatar">${initial}</div>
            <div class="bubble-wrap">
                ${grouped ? '' : `<div class="sender-name">${isMe ? 'You' : escHtml(msg.user.name)}</div>`}
                <div class="bubble">${escHtml(msg.content)}</div>
                <div class="bubble-time">${formatTime(msg.created_at)}</div>
            </div>`;

        list.appendChild(row);
        list.scrollTop = list.scrollHeight;
    }

    // Auto-resize
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = input.scrollHeight + 'px';
        sendBtn.disabled = !input.value.trim();
    });

    async function sendMessage() {
        const content = input.value.trim();
        if (!content) return;
        sendBtn.disabled = true;
        input.value = '';
        input.style.height = 'auto';

        appendMessage({
            content,
            created_at: new Date().toISOString(),
            user: { id: ME, name: ME_NAME },
        });

        try {
            await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ content }),
            });
        } catch (e) { console.error('Send failed', e); }
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    if (msgList) msgList.scrollTop = msgList.scrollHeight;

    // Pusher
    const pusher  = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
        cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        forceTLS: true,
    });

    pusher.subscribe('chat').bind('App\\Events\\MessageSent', data => {
        if (data.user.id !== ME) appendMessage(data);
    });

    pusher.connection.bind('connected',    () => document.getElementById('status-text').textContent = 'Global Room · live');
    pusher.connection.bind('disconnected', () => document.getElementById('status-text').textContent = 'Reconnecting…');
</script>
</body>
</html>