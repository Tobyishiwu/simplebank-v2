<x-app-layout>
    {{-- 0. Variable Safety & SB-Format Initialization --}}
    @php
        $account = $account ?? Auth::user()->account ?? null;
        $transactions = $transactions ?? collect();
    @endphp

    {{-- 1. Pro Max Toast System --}}
    @if(session('success') || session('error') || $errors->any())
    <div id="status-toast" class="pm-toast-container">
        <div class="pm-toast {{ session('success') ? 'success' : 'error' }}">
            <div class="toast-pill">{{ session('success') ? '✓' : '✕' }}</div>
            <span>{{ session('success') ?? (session('error') ?? $errors->first()) }}</span>
        </div>
    </div>
    @endif

    {{-- Loading Guard --}}
    <div id="loading-guard" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.7); backdrop-filter:blur(4px); z-index:100000; display:none; align-items:center; justify-content:center; flex-direction:column;">
        <div class="pm-loader"></div>
        <p style="margin-top:15px; font-weight:900; color:var(--p); font-size:12px; letter-spacing:1px;">PROCESSING TRANSACTION...</p>
    </div>

    <div class="pm-app-shell">
        {{-- 2. Pro Max Header --}}
        <header class="pm-header">
            <div class="pm-user-block">
                <div class="pm-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="pm-user-text">
                    <small>PRO MAX MEMBER</small>
                    <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="pm-exit-pill">EXIT ⏻</button>
            </form>
        </header>

        <main class="pm-body">
            {{-- 3. The 2026 Floating Card --}}
            <section class="pm-balance-card">
                <div class="pm-glow"></div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p class="pm-label">AVAILABLE BALANCE</p>
                    <div onclick="toggleBalance()" style="cursor:pointer; opacity: 0.8; font-size: 18px;" id="eye-icon">👁️</div>
                </div>

                <h1 class="pm-main-amount" id="real-balance">₦{{ number_format($account->balance ?? 0.00, 2) }}</h1>
                <h1 class="pm-main-amount" id="hidden-balance" style="display: none;">₦ * * * * * *</h1>

                <div class="pm-acct-pill" onclick="copyProMax('{{ $account->account_number ?? 'No Account' }}', this, event)">
                    <div class="pm-acct-details">
                        <small>ACCOUNT NUMBER</small>
                        <p>{{ $account->account_number ?? 'SB-00000000' }} <span class="pm-copy-icon">📋</span></p>
                    </div>
                    <div class="pm-brand-tag">SimpleBank™</div>
                </div>
            </section>

            {{-- 4. Pro Max Action Grid --}}
            <nav class="pm-action-grid">
                @foreach([
                    ['Send','📤','modal-transfer'],
                    ['Airtime','📱','modal-airtime'],
                    ['Bills','🧾','modal-utilities'],
                    ['Fund','📥','modal-deposit']
                ] as [$label, $icon, $modal])
                <div class="pm-nav-card" onclick="openM('{{ $modal }}')">
                    <div class="pm-icon-box">{{ $icon }}</div>
                    <span>{{ $label }}</span>
                </div>
                @endforeach
            </nav>

            {{-- 5. Activity Panel --}}
            <section class="pm-activity-panel">
                <div class="pm-activity-header">
                    <h3>Recent Activity</h3>
                    <div class="pm-filter-group">
                         <a href="{{ route('dashboard', ['filter' => 'all']) }}" class="pm-f-btn {{ request('filter', 'all') == 'all' ? 'pm-active' : '' }}">All</a>
                         <a href="{{ route('dashboard', ['filter' => 'credit']) }}" class="pm-f-btn {{ request('filter') == 'credit' ? 'pm-active' : '' }}">In</a>
                         <a href="{{ route('dashboard', ['filter' => 'debit']) }}" class="pm-f-btn {{ request('filter') == 'debit' ? 'pm-active' : '' }}">Out</a>
                    </div>
                </div>

                <div class="pm-tx-list">
                    @forelse($transactions as $tx)
                        @php
                            $isOut = in_array($tx->type, ['debit', 'withdrawal', 'transfer', 'airtime']);
                            $icons = [
                                'airtime' => '📱',
                                'data' => '📶',
                                'electricity' => '💡',
                                'cabletv' => '📺',
                                'transfer' => $isOut ? '📤' : '📥',
                                'deposit' => '💰'
                            ];
                        @endphp
                        <div class="pm-tx-item" onclick="showReceipt({
                            'amount': '{{ number_format($tx->amount, 2) }}',
                            'desc': '{{ $tx->description }}',
                            'date': '{{ $tx->created_at->format('M d, H:i') }}',
                            'ref': '{{ $tx->reference }}',
                            'token': '{{ $tx->token }}',
                            'isOut': {{ $isOut ? 'true' : 'false' }}
                        })">
                            <div class="pm-tx-info">
                                <div class="pm-tx-icon {{ $isOut ? 'pm-out' : 'pm-in' }}">
                                    {{ $icons[$tx->category] ?? ($isOut ? '➘' : '➚') }}
                                </div>
                                <div>
                                    <p class="pm-tx-title">{{ ucfirst($tx->category ?? $tx->type) }}</p>
                                    <p class="pm-tx-date">{{ $tx->created_at->format('d M, g:i A') }}</p>
                                </div>
                            </div>
                            <div class="pm-tx-amount {{ $isOut ? 'pm-out' : 'pm-in' }}">
                                {{ $isOut ? '-' : '+' }}₦{{ number_format($tx->amount, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="pm-empty" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 700;">No transactions yet.</div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    {{-- 6. Bottom Dock --}}
    <nav class="pm-dock">
        <div onclick="location.reload()" class="pm-dock-item active">🏠<span>HOME</span></div>
        <div onclick="openM('modal-transfer')" class="pm-dock-item">📤<span>SEND</span></div>
        <div onclick="openM('modal-security')" class="pm-dock-item">🛡️<span>SECURE</span></div>
        <div onclick="openM('modal-utilities')" class="pm-dock-item">🧾<span>BILLS</span></div>
    </nav>

    <style>
        /* Preserve your styles + New Loader */
        nav[class*="bg-white"], .min-h-screen > nav, footer, .max-w-7xl + div { display: none !important; }
        :root { --p: #00338D; --s: #16A34A; --e: #E11D48; --bg: #F8FAFC; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; overflow-x: hidden; }
        .pm-app-shell { max-width: 500px; margin: 0 auto; min-height: 100vh; padding-bottom: 120px; }
        .pm-header { position: sticky; top: 0; z-index: 999; background: rgba(255,255,255,0.8); backdrop-filter: blur(15px); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .pm-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, var(--p), #0056D2); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; box-shadow: 0 4px 12px rgba(0,51,141,0.2); }
        .pm-user-text small { font-size: 9px; color: #94A3B8; font-weight: 800; letter-spacing: 1px; }
        .pm-user-text strong { display: block; font-size: 15px; font-weight: 900; }
        .pm-exit-pill { background: #FFF1F0; border: none; padding: 10px 16px; border-radius: 12px; color: var(--e); font-size: 10px; font-weight: 900; cursor: pointer; }
        .pm-balance-card { background: linear-gradient(135deg, #001A4D, var(--p)); border-radius: 32px; padding: 30px; color: white; margin: 20px 15px; position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(0,51,141,0.25); }
        .pm-glow { position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%; }
        .pm-label { font-size: 11px; opacity: 0.7; font-weight: 700; letter-spacing: 1px; margin: 0; }
        .pm-main-amount { font-size: 38px; font-weight: 900; margin: 10px 0 25px; letter-spacing: -1.5px; }
        .pm-acct-pill { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1); padding: 14px 18px; border-radius: 20px; display: flex; justify-content: space-between; align-items: flex-end; cursor: pointer; }
        .pm-acct-details p { font-size: 15px; font-weight: 800; margin: 0; letter-spacing: 1.5px; }
        .pm-brand-tag { font-size: 10px; font-weight: 900; opacity: 0.8; }
        .pm-action-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding: 0 15px; margin-bottom: 30px; }
        .pm-nav-card { background: white; padding: 18px 5px; border-radius: 24px; text-align: center; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.02); transition: 0.2s; }
        .pm-nav-card:active { transform: scale(0.9); }
        .pm-icon-box { font-size: 20px; margin-bottom: 8px; }
        .pm-nav-card span { font-size: 11px; font-weight: 800; color: #475569; }
        .pm-activity-panel { background: white; border-radius: 32px; padding: 24px; margin: 0 15px; }
        .pm-activity-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .pm-filter-group { display: flex; background: #F1F5F9; padding: 4px; border-radius: 12px; }
        .pm-f-btn { text-decoration: none; font-size: 10px; font-weight: 900; padding: 6px 12px; color: #64748B; border-radius: 9px; }
        .pm-f-btn.pm-active { background: white; color: var(--p); box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        .pm-tx-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid #F8FAFC; cursor: pointer; }
        .pm-tx-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .pm-tx-icon.pm-in { background: #F0FDF4; color: var(--s); }
        .pm-tx-icon.pm-out { background: #FFF1F0; color: var(--e); }
        .pm-tx-amount { font-weight: 900; }
        .pm-tx-amount.pm-in { color: var(--s); }
        .pm-tx-amount.pm-out { color: var(--e); }
        .pm-dock { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 400px; background: rgba(30, 41, 59, 0.98); backdrop-filter: blur(20px); display: flex; justify-content: space-around; padding: 14px; border-radius: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); z-index: 10000; border: 1px solid rgba(255,255,255,0.1); }
        .pm-dock-item { text-align: center; cursor: pointer; flex: 1; color: #94A3B8; }
        .pm-dock-item span { display: block; font-size: 9px; font-weight: 900; margin-top: 5px; }
        .pm-dock-item.active { color: white; }
        .pm-toast-container { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 20000; width: 90%; max-width: 400px; }
        .pm-toast { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-radius: 20px; color: white; font-weight: 800; animation: slideIn 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards; }
        .pm-toast.success { background: rgba(22, 163, 74, 0.95); }
        .pm-toast.error { background: rgba(225, 29, 72, 0.95); }
        .pm-loader { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid var(--p); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes slideIn { from { transform: translateY(-100px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>

    <script>
        // Core Logic
        function toggleBalance() {
            const real = document.getElementById('real-balance');
            const hidden = document.getElementById('hidden-balance');
            const icon = document.getElementById('eye-icon');
            const isHidden = real.style.display === 'none';
            real.style.display = isHidden ? 'block' : 'none';
            hidden.style.display = isHidden ? 'none' : 'block';
            icon.innerText = isHidden ? '👁️' : '🙈';
        }

        function openM(id) {
            const m = document.getElementById(id);
            if(m) { m.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        }

        function closeM(id) {
            const m = document.getElementById(id);
            if(m) { m.style.display = 'none'; document.body.style.overflow = 'auto'; }
        }

        // Standardized Receipt Logic for all Transactions
        function showReceipt(d) {
            const amt = document.getElementById('receipt-amount');
            if(!amt) return;

            amt.innerText = (d.isOut ? '-' : '+') + '₦' + d.amount;
            amt.style.color = d.isOut ? '#E11D48' : '#16A34A';
            document.getElementById('receipt-desc').innerText = d.desc;
            document.getElementById('receipt-date').innerText = d.date;
            document.getElementById('receipt-ref').innerText = d.ref;

            const tknS = document.getElementById('receipt-token-section');
            if(d.token && d.token !== 'null' && d.token !== '') {
                document.getElementById('receipt-token').innerText = d.token;
                if(tknS) tknS.style.display = 'block';
            } else {
                if(tknS) tknS.style.display = 'none';
            }
            openM('modal-receipt');
        }

        // New: AJAX Form Handler to prevent refreshes on modal actions
        document.querySelectorAll('.secure-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const loader = document.getElementById('loading-guard');
                loader.style.display = 'flex';

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const res = await response.json();
                    loader.style.display = 'none';

                    if(res.status === 'success') {
                        const m = this.closest('.pm-modal');
                        if(m) closeM(m.id);
                        showReceipt(res.data || { amount: '0', desc: res.message, date: 'Just now', ref: 'N/A', isOut: true });
                    } else {
                        alert(res.message || 'Error processing request');
                    }
                } catch(err) {
                    loader.style.display = 'none';
                    alert('Network error. Please check connection.');
                }
            });
        });

        // Copy Account Logic
        function copyProMax(text, el, event) {
            if (event) event.stopPropagation();
            navigator.clipboard.writeText(text).then(() => spawnPMToast("Account Copied! ✅"));
        }

        function spawnPMToast(msg) {
            const t = document.createElement('div');
            t.className = 'pm-toast-container';
            t.innerHTML = `<div class="pm-toast" style="background:#1E293B; box-shadow:0 10px 20px rgba(0,0,0,0.2);"><div style="width:24px; height:24px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">✓</div><span>${msg}</span></div>`;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 500); }, 2500);
        }
    </script>
    <script>
// This intercepts the form so you don't see raw JSON
document.querySelectorAll('.secure-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); 

        const formData = new FormData(this);
        document.getElementById('loading-guard').style.display = 'flex';

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();
            document.getElementById('loading-guard').style.display = 'none';

            if (result.status === 'success') {
                // Fill the receipt modal with the data from your Controller
                document.getElementById('receipt-amount').innerText = result.data.amount;
                document.getElementById('receipt-ref').innerText = result.data.reference;
                document.getElementById('receipt-desc').innerText = result.data.description;
                
                if(result.data.token) {
                    document.getElementById('receipt-token').innerText = result.data.token;
                    document.getElementById('receipt-token-section').style.display = 'block';
                }

                closeM(this.closest('.modal').id); // Close the pay modal
                openM('modal-receipt');           // Show the success receipt
            } else {
                alert(result.message);
            }
        } catch (error) {
            document.getElementById('loading-guard').style.display = 'none';
            alert("Error processing transaction");
        }
    });
});
</script>

    @include('components.bank-modals')
</x-app-layout>
