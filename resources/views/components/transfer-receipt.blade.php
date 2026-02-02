@if(session('transfer_success'))
@php $data = session('transfer_success'); @endphp
<div id="receipt-overlay" style="position: fixed; inset: 0; z-index: 10001; background: rgba(16, 24, 40, 0.85); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div id="receipt-content" style="background: #ffffff; width: 100%; max-width: 400px; border-radius: 24px; overflow: hidden; animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);">

        {{-- Receipt Header --}}
        <div style="background: #00338D; padding: 30px 20px; text-align: center; color: #ffffff;">
            <div style="width: 60px; height: 60px; background: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                ✅
            </div>
            <h2 style="margin: 0; font-size: 20px; font-weight: 900; letter-spacing: -0.5px;">Transaction Successful</h2>
            <p style="margin: 5px 0 0; font-size: 13px; opacity: 0.8; font-weight: 600;">SimpleBank Business Receipt</p>
        </div>

        {{-- Receipt Body --}}
        <div style="padding: 25px; background: #ffffff;">
            <div style="text-align: center; margin-bottom: 25px;">
                <p style="margin: 0; font-size: 12px; font-weight: 800; color: #667085; text-transform: uppercase;">Amount Sent</p>
                <h1 style="margin: 5px 0; font-size: 36px; font-weight: 900; color: #101828;">₦{{ number_format($data['amount'], 2) }}</h1>
            </div>

            <div style="border-top: 1px dashed #D0D5DD; border-bottom: 1px dashed #D0D5DD; padding: 20px 0; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 13px; font-weight: 700; color: #667085;">Recipient</span>
                    <span style="font-size: 13px; font-weight: 800; color: #101828; text-align: right;">{{ $data['recipient_name'] ?? 'Account Holder' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 13px; font-weight: 700; color: #667085;">Account Number</span>
                    <span style="font-size: 13px; font-weight: 800; color: #101828;">{{ $data['account_number'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 13px; font-weight: 700; color: #667085;">Bank Name</span>
                    <span style="font-size: 13px; font-weight: 800; color: #101828;">SimpleBank</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 13px; font-weight: 700; color: #667085;">Transaction Ref</span>
                    <span style="font-size: 11px; font-weight: 800; color: #101828; font-family: monospace;">{{ strtoupper(Str::random(12)) }}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <button onclick="downloadReceipt()" style="background: #F2F4F7; color: #344054; border: none; padding: 14px; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer;">
                    Share
                </button>
                <button onclick="closeReceipt()" style="background: #00338D; color: #ffffff; border: none; padding: 14px; border-radius: 12px; font-weight: 800; font-size: 13px; cursor: pointer;">
                    Done
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div style="background: #F9FAFB; padding: 15px; text-align: center; border-top: 1px solid #EAECF0;">
            <p style="margin: 0; font-size: 10px; color: #98A2B3; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                Certified by SimpleBank Business
            </p>
        </div>
    </div>
</div>

<script>
    function closeReceipt() {
        document.getElementById('receipt-overlay').style.display = 'none';
    }
    function downloadReceipt() {
        alert("Preparing receipt for download...");
        // In a real app, you'd use html2canvas or a PDF generator here
    }
</script>

<style>
@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
@endif
