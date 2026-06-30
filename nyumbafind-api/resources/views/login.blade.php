@extends('layouts.app')

@section('title', 'Login - NyumbaFind')

@section('content')

    <div class="max-w-sm mx-auto mt-10">

        <h1 class="text-2xl font-bold text-center mb-6">Login or Sign Up</h1>

        <div id="alert" class="hidden mb-4 p-3 rounded text-sm"></div>

        <!-- STEP 1: phone -->
        <div id="step-phone">
            <label class="block text-sm font-medium mb-1">Phone Number</label>
            <input type="tel" id="phone" placeholder="0712345678"
                class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button id="btn-send-otp"
                class="w-full bg-emerald-600 text-white rounded-lg py-2 font-medium hover:bg-emerald-700">
                Send Code
            </button>
        </div>

        <!-- STEP 2: code (+ name if new user) -->
        <div id="step-code" class="hidden">
            <p class="text-sm text-gray-600 mb-3">
                Enter the 6-digit code sent to <span id="phone-display" class="font-medium"></span>
            </p>
            <input type="text" id="code" maxlength="6" placeholder="123456"
                class="w-full border rounded-lg px-3 py-2 mb-3 text-center text-2xl tracking-widest focus:outline-none focus:ring-2 focus:ring-emerald-500">

            <div id="name-field" class="hidden">
                <label class="block text-sm font-medium mb-1">Your Name</label>
                <input type="text" id="name" placeholder="Jane Wanjiru"
                    class="w-full border rounded-lg px-3 py-2 mb-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button id="btn-verify-otp"
                class="w-full bg-emerald-600 text-white rounded-lg py-2 font-medium hover:bg-emerald-700">
                Verify & Continue
            </button>
            <button id="btn-change-number"
                class="w-full text-sm text-gray-500 mt-2 hover:underline">
                Use a different number
            </button>
        </div>

        <!-- STEP 3: logged in -->
        <div id="step-done" class="hidden text-center">
            <p class="text-emerald-600 font-medium mb-2">You're logged in!</p>
            <p id="welcome-name" class="text-gray-600 mb-4"></p>
            <a href="{{ url('/') }}" class="text-emerald-600 hover:underline text-sm">Go to search &rarr;</a>
        </div>

    </div>

    <script>
        let currentPhone = '';
        let requiresName = false;

        const alertBox = document.getElementById('alert');
        function showAlert(message, type = 'error') {
            alertBox.textContent = message;
            alertBox.className = 'mb-4 p-3 rounded text-sm ' +
                (type === 'error' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700');
            alertBox.classList.remove('hidden');
        }
        function hideAlert() {
            alertBox.classList.add('hidden');
        }

        document.getElementById('btn-send-otp').addEventListener('click', () => {
            hideAlert();
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                showAlert('Please enter your phone number.');
                return;
            }

            fetch('/api/auth/send-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ phone })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    showAlert(data.message || 'Could not send code.');
                    return;
                }
                currentPhone = phone;
                document.getElementById('phone-display').textContent = data.phone;
                document.getElementById('step-phone').classList.add('hidden');
                document.getElementById('step-code').classList.remove('hidden');
            })
            .catch(() => showAlert('Network error. Is the server running?'));
        });

        document.getElementById('btn-change-number').addEventListener('click', () => {
            hideAlert();
            document.getElementById('step-code').classList.add('hidden');
            document.getElementById('step-phone').classList.remove('hidden');
            document.getElementById('code').value = '';
        });

        document.getElementById('btn-verify-otp').addEventListener('click', () => {
            hideAlert();
            const code = document.getElementById('code').value.trim();
            const name = document.getElementById('name').value.trim();

            if (!code || code.length !== 6) {
                showAlert('Enter the 6-digit code.');
                return;
            }
            if (requiresName && !name) {
                showAlert('Please enter your name.');
                return;
            }

            const payload = { phone: currentPhone, code };
            if (requiresName) payload.name = name;

            fetch('/api/auth/verify-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) {
                    showAlert(data.message || (data.errors?.code?.[0]) || 'Invalid code.');
                    return;
                }

                if (data.requires_name) {
                    requiresName = true;
                    document.getElementById('name-field').classList.remove('hidden');
                    showAlert('Welcome! Please tell us your name to finish signing up.', 'success');
                    return;
                }

                // Success: store token, show done state
                localStorage.setItem('nyumbafind_token', data.token);
                localStorage.setItem('nyumbafind_user', JSON.stringify(data.user));

                document.getElementById('step-code').classList.add('hidden');
                document.getElementById('step-done').classList.remove('hidden');
                document.getElementById('welcome-name').textContent = `Hi, ${data.user.name}.`;
            })
            .catch(() => showAlert('Network error. Is the server running?'));
        });
    </script>

@endsection