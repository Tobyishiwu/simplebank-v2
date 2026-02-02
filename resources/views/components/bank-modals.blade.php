{{-- 1. SEND MONEY (TRANSFER) --}}
<div id="modal-transfer" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-transfer')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Send Money</h3>
        <form action="{{ route('account.transfer.execute') }}" method="POST" class="secure-form">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">RECIPIENT ACCOUNT NUMBER</label>
                <input type="text" name="recipient_account" placeholder="SB-00000000" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">AMOUNT (₦)</label>
                <input type="number" name="amount" min="100" placeholder="0.00" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700;">
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">TRANSACTION PIN</label>
                <input type="password" name="pin" maxlength="4" minlength="4" placeholder="****" inputmode="numeric" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700;">
            </div>
            <button type="submit" style="width:100%; background:#00338D; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Send Now</button>
        </form>
    </div>
</div>

{{-- 2. AIRTIME PURCHASE --}}
<div id="modal-airtime" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-airtime')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Buy Airtime</h3>
        <form action="{{ route('utility.pay') }}" method="POST" class="secure-form">
            @csrf
            <select name="service_id" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700; margin-bottom:15px;">
                <option value="mtn">MTN</option>
                <option value="airtel">Airtel</option>
                <option value="glo">Glo</option>
                <option value="9mobile">9mobile</option>
            </select>
            <input type="number" name="phone" placeholder="Phone Number" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">
            <input type="number" name="amount" placeholder="Amount" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">
            <input type="password" name="pin" maxlength="4" placeholder="Transaction PIN" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:20px; font-weight:700;">
            <button type="submit" style="width:100%; background:#00338D; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Purchase Now</button>
        </form>
    </div>
</div>

{{-- 3. BILLS & DATA (VTpass Integrated) --}}
<div id="modal-utilities" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-utilities')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Bills & Data</h3>
        <form action="{{ route('utility.pay') }}" method="POST" class="secure-form">
            @csrf
            <label style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">SERVICE TYPE</label>
            <select name="service_id" id="service_selector" onchange="toggleUtilityFields()" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700; margin-bottom:15px;">
                <optgroup label="Electricity">
                    <option value="enugu-electric">EEDC (Enugu Electric)</option>
                    <option value="ikeja-electric">Ikeja Electric</option>
                    <option value="eko-electric">Eko Electric</option>
                </optgroup>
                <optgroup label="Data Bundles">
                    <option value="mtn-data">MTN Data</option>
                    <option value="airtel-data">Airtel Data</option>
                    <option value="glo-data">Glo Data</option>
                </optgroup>
            </select>

            <div id="data_plan_container" style="display:none; margin-bottom:15px;">
                <label style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">SELECT PLAN</label>
                <select name="variation_code" style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; font-weight:700;">
                    <option value="m1024">1GB Monthly</option>
                    <option value="m2048">2GB Monthly</option>
                    <option value="m5120">5GB Monthly</option>
                </select>
            </div>

            <label id="id_label" style="display:block; font-size:11px; font-weight:800; color:#667085; margin-bottom:6px;">METER / SMARTCARD NUMBER</label>
            <input type="text" name="billers_code" required placeholder="Enter ID number" style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">

            <input type="number" name="amount" placeholder="Amount (₦)" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">
            <input type="password" name="pin" maxlength="4" placeholder="Transaction PIN" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:20px; font-weight:700;">
            <button type="submit" style="width:100%; background:#00338D; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Confirm Payment</button>
        </form>
    </div>
</div>

{{-- 4. FUND ACCOUNT (DEPOSIT) --}}
<div id="modal-deposit" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-deposit')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Fund Account</h3>
        <form action="{{ route('account.deposit') }}" method="POST" class="secure-form">
            @csrf
            <input type="number" name="amount" placeholder="Amount to Fund (₦)" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:20px; font-weight:700;">
            <button type="submit" style="width:100%; background:#00338D; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Deposit Funds</button>
        </form>
    </div>
</div>

{{-- 5. WITHDRAW CASH --}}
<div id="modal-withdraw" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-withdraw')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Withdraw Cash</h3>
        <form action="{{ route('account.withdraw') }}" method="POST" class="secure-form">
            @csrf
            <input type="number" name="amount" placeholder="Amount (₦)" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">
            <input type="password" name="pin" maxlength="4" placeholder="Transaction PIN" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:20px; font-weight:700;">
            <button type="submit" style="width:100%; background:#E11D48; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Confirm Withdrawal</button>
        </form>
    </div>
</div>

{{-- 6. SECURITY SETTINGS --}}
<div id="modal-security" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:20000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:24px; padding:24px; position:relative;">
        <button onclick="closeM('modal-security')" style="position:absolute; right:20px; top:20px; border:none; background:none; font-size:20px; cursor:pointer;">✕</button>
        <h3 style="font-weight:900; margin-bottom:20px; color:#101828;">Security PIN</h3>
        <form action="{{ route('pin.update') }}" method="POST" class="secure-form">
            @csrf
            <input type="password" name="pin" maxlength="4" placeholder="New 4-Digit PIN" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:15px; font-weight:700;">
            <input type="password" name="pin_confirmation" maxlength="4" placeholder="Confirm PIN" required style="width:100%; padding:14px; border-radius:12px; border:1px solid #D0D5DD; margin-bottom:20px; font-weight:700;">
            <button type="submit" style="width:100%; background:#00338D; color:#fff; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Update PIN</button>
        </form>
    </div>
</div>

{{-- 7. TRANSACTION RECEIPT --}}
<div id="modal-receipt" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:30000; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#fff; width:100%; max-width:380px; border-radius:32px; padding:30px; text-align:center; position:relative;">
        <button onclick="closeM('modal-receipt')" style="position:absolute; right:24px; top:24px; border:none; background:#F2F4F7; width:32px; height:32px; border-radius:50%; cursor:pointer;">✕</button>
        <div style="width:70px; height:70px; border-radius:50%; background:#16A34A; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
            <span style="font-size:30px; color:white;">✓</span>
        </div>
        <h2 id="receipt-amount" style="font-size:32px; font-weight:900; margin-bottom:4px;"></h2>
        <p id="receipt-date" style="font-size:14px; color:#667085; margin-bottom:24px;"></p>
        <div style="background:#F8FAFC; border-radius:20px; padding:20px; text-align:left;">
            <div id="receipt-token-section" style="display:none; background:#FFFBEB; border:1px dashed #F59E0B; padding:12px; border-radius:12px; margin-bottom:15px; text-align:center;">
                <span style="color:#B45309; font-size:10px; font-weight:800; display:block;">TOKEN</span>
                <strong id="receipt-token" style="font-size:18px; color:#1E293B; letter-spacing:2px;"></strong>
            </div>
            <p style="font-size:12px; color:#64748B; margin-bottom:4px;">REF: <span id="receipt-ref" style="font-weight:800; color:#1E293B;"></span></p>
            <p id="receipt-desc" style="font-weight:700; color:#1E293B; font-size:14px;"></p>
        </div>
        <button onclick="closeM('modal-receipt')" style="width:100%; margin-top:20px; background:#00338D; color:white; padding:16px; border-radius:12px; border:none; font-weight:800; cursor:pointer;">Done</button>
    </div>
</div>

{{-- 8. LOADING OVERLAY --}}
<div id="loading-guard" style="display:none; position:fixed; inset:0; background:rgba(255,255,255,0.8); backdrop-filter:blur(8px); z-index:999999; flex-direction:column; align-items:center; justify-content:center;">
    <div class="loader-circle" style="width:48px; height:48px; border:5px solid #F1F5F9; border-radius:50%; border-top-color: #00338D; animation: spin 0.8s linear infinite;"></div>
    <p style="margin-top:18px; font-weight:900; color:#00338D;">PROCESSING...</p>
</div>

<style> @keyframes spin { to { transform: rotate(360deg); } } </style>

<script>
function openM(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeM(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}
function toggleUtilityFields() {
    const service = document.getElementById('service_selector').value;
    const dataContainer = document.getElementById('data_plan_container');
    const label = document.getElementById('id_label');
    if(service.includes('data')) {
        dataContainer.style.display = 'block';
        label.innerText = "RECIPIENT PHONE NUMBER";
    } else {
        dataContainer.style.display = 'none';
        label.innerText = "METER / SMARTCARD NUMBER";
    }
}
// Double-spend protection
document.querySelectorAll('.secure-form').forEach(form => {
    form.addEventListener('submit', function() {
        if (this.checkValidity()) document.getElementById('loading-guard').style.display = 'flex';
    });
});
</script>
