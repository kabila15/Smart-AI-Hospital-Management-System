<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Live Queue Tracking – Global Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --success: #22c55e;
            --warning: #f59e0b;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .tracking-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .header {
            padding: 30px 20px 10px;
            text-align: center;
        }

        .hospital-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .badge-live {
            background: #fee2e2;
            color: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .token-display {
            background: var(--primary-light);
            margin: 0 20px 24px;
            padding: 30px 20px;
            border-radius: 20px;
            text-align: center;
        }

        .token-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .token-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin: 10px 0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 0 20px 30px;
            gap: 15px;
        }

        .metric-item {
            padding: 15px;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            text-align: center;
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .value-serving {
            color: var(--success);
        }

        .value-ahead {
            color: var(--warning);
        }

        .info-footer {
            background: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        #refresh-status {
            cursor: pointer;
            color: var(--primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            background: none;
            border: 1px solid var(--primary);
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.2s;
        }

        #refresh-status:hover {
            background: var(--primary);
            color: white;
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            .tracking-card {
                border-radius: 16px;
            }
            .token-number {
                font-size: 2.8rem;
            }
            .metrics-grid {
                gap: 10px;
                padding: 0 15px 20px;
            }
            .metric-item {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="tracking-card">
        <div class="header">
            <div class="hospital-logo">
                <i class="fa fa-hospital-o"></i> Global Hospital
            </div>
            <h3 class="mb-1">Live Queue Status</h3>
            <p id="doc-info" class="text-muted small">Loading appointment data...</p>
        </div>

        <div class="token-display">
            <div class="token-label">Your Token Number</div>
            <div id="my-token" class="token-number">#--</div>
            <div class="mt-2">
                <span class="text-muted small">Expected at:</span>
                <span id="expected-time" class="font-weight-bold">--:--</span>
            </div>
            <div id="delay-msg" class="text-danger small mt-1 font-weight-bold" style="display:none;"></div>
        </div>

        <div class="metrics-grid">
            <div class="metric-item">
                <div class="metric-label">Currently Serving</div>
                <div id="serving-token" class="metric-value value-serving">#--</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Tokens Ahead</div>
                <div id="ahead-count" class="metric-value value-ahead">--</div>
            </div>
            <div class="metric-item" style="grid-column: span 2;">
                <div class="metric-label">Estimated Wait Time</div>
                <div id="wait-time" class="metric-value text-primary">-- mins</div>
            </div>
        </div>

        <div class="info-footer">
            <p class="mb-0">Show your WhatsApp QR code at reception to check-in instantly.</p>
            <button id="refresh-status"><i class="fa fa-refresh"></i> Refresh Status</button>
            <div style="margin-top: 14px; display: flex; gap: 8px; flex-direction: column;">
                <a href="admin-panel.php" style="display:flex; align-items:center; justify-content:center; gap:8px; padding: 10px 14px; background: var(--primary); color:#fff; border-radius:12px; font-size:0.85rem; font-weight:600; text-decoration:none;">
                    <i class="fa fa-tachometer"></i> Go to Patient Portal
                </a>
                <a href="index.php?register=1" style="display:flex; align-items:center; justify-content:center; gap:8px; padding: 9px 14px; background: transparent; color: var(--primary); border: 1px solid var(--primary); border-radius:12px; font-size:0.82rem; font-weight:600; text-decoration:none;">
                    <i class="fa fa-user-plus"></i> New Patient? Register here
                </a>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');

        function updateStatus() {
            if (!token) return;

            fetch(`get_queue_status.php?token=${token}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('doc-info').innerText = `Dr. ${data.doctor} | ${data.date}`;
                        document.getElementById('my-token').innerText = `#${data.my_token}`;
                        document.getElementById('expected-time').innerText = data.expected_time;
                        document.getElementById('serving-token').innerText = `#${data.serving_token}`;
                        document.getElementById('ahead-count').innerText = data.ahead;
                        document.getElementById('wait-time').innerText = `${data.wait_mins} mins`;

                        if (data.delayed_mins > 0) {
                            document.getElementById('delay-msg').innerText = `⚠ Includes ${data.delayed_mins} mins delay`;
                            document.getElementById('delay-msg').style.display = 'block';
                        } else {
                            document.getElementById('delay-msg').style.display = 'none';
                        }
                    }
                })
                .catch(err => console.error('Status check failed:', err));
        }

        // Initial update
        updateStatus();

        // Refresh every 10 seconds
        setInterval(updateStatus, 10000);

        document.getElementById('refresh-status').onclick = function () {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            updateStatus();
            setTimeout(() => icon.classList.remove('fa-spin'), 1000);
        };
    </script>
</body>

</html>