<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/logo-smk.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#1e3a8a" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="BK Prima" />
    <meta name="mobile-web-app-capable" content="yes" />
    <title inertia>{{ config('app.name', 'BK Prima') }} - SMK PGRI 05 Jember</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
    @inertiaHead
    <style>
      #initial-loading-screen {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
        color: #ffffff;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: opacity 0.4s ease-out;
      }
      .splash-logo-box {
        position: relative;
        width: 88px;
        height: 88px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .splash-pulse-ring {
        position: absolute;
        inset: -12px;
        border-radius: 28px;
        border: 2px solid rgba(59, 130, 246, 0.4);
        animation: splash-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }
      .splash-pulse-ring-2 {
        position: absolute;
        inset: -22px;
        border-radius: 34px;
        border: 1px solid rgba(147, 197, 253, 0.2);
        animation: splash-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        animation-delay: 0.4s;
      }
      .splash-icon-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 20px 35px -10px rgba(37, 99, 235, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.3);
      }
      .splash-icon-wrapper svg {
        width: 44px;
        height: 44px;
        fill: none;
        stroke: #ffffff;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
      }
      .splash-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
        background: linear-gradient(to right, #ffffff, #93c5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }
      .splash-subtitle {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 4px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }
      .splash-bar-bg {
        width: 140px;
        height: 4px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 9999px;
        margin-top: 32px;
        overflow: hidden;
        position: relative;
      }
      .splash-bar-fill {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 40%;
        background: linear-gradient(90deg, #38bdf8, #3b82f6);
        border-radius: 9999px;
        animation: splash-indeterminate 1.4s ease-in-out infinite;
      }
      @keyframes splash-pulse {
        0%, 100% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.1); opacity: 0.15; }
      }
      @keyframes splash-indeterminate {
        0% { left: -40%; width: 40%; }
        50% { left: 30%; width: 60%; }
        100% { left: 100%; width: 40%; }
      }
    </style>
  </head>
  <body class="bg-slate-50 text-gray-800 antialiased selection:bg-blue-500 selection:text-white">
    <div id="initial-loading-screen">
      <div class="splash-logo-box">
        <div class="splash-pulse-ring-2"></div>
        <div class="splash-pulse-ring"></div>
        <div class="splash-icon-wrapper">
          <svg viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
            <path d="M2 17l10 5 10-5"></path>
            <path d="M2 12l10 5 10-5"></path>
          </svg>
        </div>
      </div>
      <h1 class="splash-title">BK PRIMA</h1>
      <p class="splash-subtitle">SMK PGRI 05 JEMBER</p>
      <div class="splash-bar-bg">
        <div class="splash-bar-fill"></div>
      </div>
    </div>
    @inertia
  </body>
</html>
