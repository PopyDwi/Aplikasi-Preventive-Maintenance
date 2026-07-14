<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Maintenance RCM')</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endif

    <style>
        :root {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color-scheme: light;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
            color: #0f172a;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
            overflow: hidden;
            transition: background 0.2s ease;
        }

        .sidebar {
            width: 280px;
            min-width: 280px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 30;
            flex-shrink: 0;
            transition: width 0.3s ease, padding 0.3s ease, box-shadow 0.3s ease;
        }

        .sidebar.collapsed {
            width: 88px;
            min-width: 88px;
            padding: 24px 10px;
        }

        .app-shell.collapsed .main-area {
            margin-left: 88px;
        }

        .app-shell.collapsed header.topbar {
            left: 88px;
        }

        .sidebar .menu {
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar .menu {
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #475569;
            background: transparent;
            border-radius: 18px;
            padding: 12px 14px;
            transition: all .25s ease;
            min-height: 48px;
        }

        .sidebar .menu-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .sidebar .menu-item.active {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 700;
            border-left: 4px solid #0ea5e9;
        }

        .menu-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            color: #0ea5e9;
            flex-shrink: 0;
        }

        .menu-label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity .25s ease, width .25s ease;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
        }

        .sidebar.collapsed .menu-label {
            opacity: 0;
            width: 0;
            max-width: 0;
            overflow: hidden;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease;
            z-index: 29;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: visible;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        header.topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 24px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            z-index: 9999;
            flex-wrap: wrap;
            height: 78px;
            transition: left 0.3s ease, width 0.3s ease, padding 0.3s ease;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 108px 24px 32px;
            overflow-y: auto;
            overflow-x: auto;
            margin-top: 0;
            min-width: 0;
        }

        .content {
            display: flex;
            flex-direction: column;
            gap: 28px;
            min-width: 0;
        }

        .sidebar-toggle {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #0f172a;
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
            flex-shrink: 0;
        }

        .sidebar-toggle:hover {
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: contain;
            background: #f8fafc;
            padding: 6px;
        }

        .brand-title,
        .brand-subtitle {
            margin: 0;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .brand-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .brand-subtitle {
            font-size: 12px;
            color: #64748b;
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 102px 24px 32px;
            overflow-y: auto;
            overflow-x: auto;
            margin-top: 0;
            min-width: 0;
        }

        .content {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
        }

        .page-title {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .page-title h1 {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.05;
        }

        .page-title p {
            font-size: 15px;
            color: #64748b;
            max-width: 840px;
        }

        .cards {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            margin-bottom: 24px;
        }

        @media (min-width: 640px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .cards {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        /* ====== CARD COLOR VARIATIONS - DISTINCT VISUAL COUNTERS ====== */
        .card,
        .section,
        .content-card,
        .table-container,
        .detail-card,
        .form-card,
        .table-card,
        .result-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
            overflow-x: auto;
            transition: all 0.2s ease;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
            overflow-x: auto;
            transition: all 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.1);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .card-header .card-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.55);
            color: inherit;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.45);
        }

        .card .card-value {
            font-size: 2.75rem;
            line-height: 1;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .card .card-label {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: rgba(15, 23, 42, 0.84);
        }

        /* Card - Elegant Blue (Machines/Assets) */
        .card.card-blue,
        .card.card-machines {
            background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            box-shadow: 0 10px 30px rgba(7, 89, 133, 0.08);
        }

        .card.card-blue h3,
        .card.card-machines h3 {
            color: #0c4a6e;
        }

        .card.card-blue h1,
        .card.card-machines h1 {
            color: #0369a1;
        }

        /* Card - Purple/Teal (Maintenance/Process) */
        .card.card-purple,
        .card.card-maintenance {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            border: 2px solid #e9d5ff;
            box-shadow: 0 10px 30px rgba(126, 34, 206, 0.08);
        }

        .card.card-purple h3,
        .card.card-maintenance h3 {
            color: #6b21a8;
        }

        .card.card-purple h1,
        .card.card-maintenance h1 {
            color: #a855f7;
        }

        /* Card - Red/Amber (Active Damage/High Risk) */
        .card.card-red,
        .card.card-damage,
        .card.card-risk {
            background: linear-gradient(135deg, #fef2f2 0%, #fef3c7 100%);
            border: 2px solid #fecaca;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.08);
        }

        .card.card-red h3,
        .card.card-damage h3,
        .card.card-risk h3 {
            color: #7f1d1d;
        }

        .card.card-red h1,
        .card.card-damage h1,
        .card.card-risk h1 {
            color: #dc2626;
        }

        /* Card - Slate/Gray (Downtime/Strong) */
        .card.card-slate,
        .card.card-downtime {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 2px solid #cbd5e1;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .card.card-slate h3,
        .card.card-downtime h3 {
            color: #334155;
        }

        .card.card-slate h1,
        .card.card-downtime h1 {
            color: #1e293b;
        }

        /* Card - Green (Completed/Success) */
        .card.card-green,
        .card.card-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px solid #a7f3d0;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.08);
        }

        .card.card-green h3,
        .card.card-success h3 {
            color: #065f46;
        }

        .card.card-green h1,
        .card.card-success h1 {
            color: #059669;
        }

        .card.card-yellow,
        .card.card-pending {
            background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
            border: 2px solid #fde68a;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.08);
        }

        .card.card-yellow h3,
        .card.card-pending h3 {
            color: #92400e;
        }

        .card.card-yellow h1,
        .card.card-pending h1 {
            color: #b45309;
        }

        .card.card-indigo,
        .card.card-next {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 2px solid #c7d2fe;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.08);
        }

        .card.card-indigo h3,
        .card.card-next h3 {
            color: #312e81;
        }

        .card.card-indigo h1,
        .card.card-next h1 {
            color: #4338ca;
        }

        /* ====== MODAL COMPONENT - LIVE PREVIEW ====== */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            overflow-y: auto;
        }

        .modal.show {
            display: flex;
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: #ffffff;
            margin: auto;
            padding: 32px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(15, 23, 42, 0.15);
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 16px;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #64748b;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .modal-body {
            margin-bottom: 24px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            border-top: 2px solid #e2e8f0;
            padding-top: 16px;
        }

        /* ====== SIDEBAR FIXED POSITIONING ====== */

        .table-card,
        .form-card,
        .content-card,
        .result-card,
        .chart-card,
        .info-card {
            padding: 24px;
        }

        .table-card {
            overflow: hidden;
        }

        .table-container,
        .table-card .table-scroll,
        .table-card .table-responsive,
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-container,
        .table-card .table-scroll,
        .table-card .table-responsive,
        .table-responsive {
            position: relative;
            padding: 1rem 0 0 0;
        }

        .table-container table,
        .table-card .table-scroll table,
        .table-card .table-responsive table,
        .table-responsive table {
            width: 100%;
            min-width: max-content;
            border-collapse: collapse;
        }

        .table-container table th,
        .table-container table td,
        .table-card .table-scroll table th,
        .table-card .table-scroll table td,
        .table-card .table-responsive table th,
        .table-card .table-responsive table td,
        .table-responsive table th,
        .table-responsive table td {
            padding: 1rem 0.85rem;
            white-space: nowrap;
        }

        .table-card h2,
        .form-card h2,
        .content-card h2,
        .result-card h2 {
            margin: 0 0 1rem;
            font-size: 1.125rem;
            font-weight: 800;
            color: #0f172a;
        }

        .table-card p,
        .content-card p,
        .form-card p {
            color: #64748b;
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        table th,
        table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 0.95rem;
            color: #334155;
        }

        table thead th {
            position: sticky;
            top: 0;
            background: #3B5BDB !important;
            color: #ffffff !important;
            z-index: 10;
            text-align: left;
            font-size: 0.8rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            font-weight: 700 !important;
            padding: 12px 16px;
            border: 1px solid #2D3FB5 !important;
        }

        table tbody tr {
            transition: background-color 0.2s ease;
        }

        table tbody tr:hover {
            background: #f1f5f9;
        }

        table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        table tbody tr:nth-child(even) {
            background: rgba(241, 245, 249, 0.8);
        }

        .text-truncate {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* ====== BADGE & STATUS SYSTEM - TRAFFIC LIGHT ====== */
        .status,
        .risk,
        .badge,
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        /* Green - Completed/Normal/Low Risk */
        .badge-success,
        .status.success,
        .risk.rendah,
        .badge-normal,
        .status.normal,
        .risk.normal {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #d1fae5;
        }

        /* Yellow/Amber - In Progress/Scheduled/Medium Risk */
        .badge-warning,
        .status.scheduled,
        .status.diproses,
        .risk.sedang,
        .status.warning,
        .risk.warning {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* Red - Urgent/Not Handled/High Risk */
        .badge-urgent,
        .badge-danger,
        .status.urgent,
        .status.belum-ditangani,
        .risk.tinggi,
        .risk.sangat-tinggi,
        .risk.urgent {
            background: #fef2f2;
            color: #7f1d1d;
            border: 1px solid #fecaca;
        }

        /* Blue - Info/Additional context */
        .badge-info,
        .status.info {
            background: #eff6ff;
            color: #0c4a6e;
            border: 1px solid #bae6fd;
        }

        /* Enhanced Amber for specific status */
        .badge-amber,
        .status.amber {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        /* Tailwind Badge Styles for Dynamic Badges */
        .px-2\.5 {
            padding-left: 0.625rem;
            padding-right: 0.625rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .border {
            border-width: 1px;
        }

        /* Emerald (Success) Badges */
        .bg-emerald-50 {
            background-color: #f0fdf4;
        }

        .text-emerald-700 {
            color: #15803d;
        }

        .border-emerald-200 {
            border-color: #dcfce7;
        }

        /* Amber (Warning) Badges */
        .bg-amber-50 {
            background-color: #fffbeb;
        }

        .text-amber-700 {
            color: #b45309;
        }

        .border-amber-200 {
            border-color: #fde68a;
        }

        /* Rose (Danger) Badges */
        .bg-rose-50 {
            background-color: #fff1f2;
        }

        .text-rose-700 {
            color: #be185d;
        }

        .border-rose-200 {
            border-color: #fbcfe8;
        }

        /* Red (Critical) Badges */
        .bg-red-50 {
            background-color: #fef2f2;
        }

        .text-red-700 {
            color: #b91c1c;
        }

        .border-red-200 {
            border-color: #fecaca;
        }

        /* Slate (Neutral) Badges */
        .bg-slate-50 {
            background-color: #f8fafc;
        }

        .text-slate-700 {
            color: #334155;
        }

        .border-slate-200 {
            border-color: #e2e8f0;
        }

        /* Sky Blue for Tables & Headers */
        .bg-sky-700 {
            background-color: #3B5BDB !important;
        }

        .bg-sky-700 th,
        .bg-sky-700 td {
            color: #ffffff !important;
            background-color: #3B5BDB !important;
        }
        
        thead.bg-sky-700 tr {
            background-color: #3B5BDB !important;
        }
        
        thead.bg-sky-700 th {
            background-color: #3B5BDB !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        .text-white {
            color: #ffffff;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Additional utilities for report styling */
        .table-header-dark {
            background-color: #3B5BDB !important;
            color: #ffffff !important;
        }

        .table-header-dark th {
            background-color: #3B5BDB !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 1px solid #2D3FB5 !important;
        }

        .table-row-striped tbody tr:nth-child(odd) {
            background-color: #f8fafc;
        }

        .table-row-striped tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        /* Report styling */
        .report-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .report-subtitle {
            font-size: 14px;
            color: #64748b;
            font-style: italic;
        }

        .report-period {
            font-size: 12px;
            color: #475569;
            margin-bottom: 16px;
        }

        .action-bar,
        .filter-actions,
        .button-area,
        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .button-area {
            justify-content: flex-end;
        }

        .btn-reset,
        .btn-submit,
        .btn-add,
        .btn-edit,
        .btn-delete,
        .btn-detail,
        .btn-action,
        .btn-create-schedule,
        .btn-search,
        .btn-print,
        .btn-export,
        .btn {
            border: none;
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-submit,
        .btn-add,
        .btn-create-schedule,
        .btn-action,
        .btn-primary {
            background: #0ea5e9;
            color: #ffffff;
        }

        .btn-submit:hover,
        .btn-add:hover,
        .btn-create-schedule:hover,
        .btn-action:hover,
        .btn-primary:hover {
            background: #0284c7;
        }

        .btn-reset,
        .btn-edit,
        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-reset:hover,
        .btn-edit:hover,
        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-delete,
        .btn-action.danger,
        .btn-danger {
            background: #ef4444;
            color: #ffffff;
        }

        .btn-delete:hover,
        .btn-action.danger:hover,
        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-search,
        .btn-print,
        .btn-export {
            background: #0284c7;
            color: white;
            padding: 10px 14px;
        }

        .btn-search:hover,
        .btn-print:hover,
        .btn-export:hover {
            background: #0369a1;
        }

        .btn-ghost {
            background: transparent;
            color: #475569;
            border: 1px solid transparent;
        }

        .btn-ghost:hover {
            background: #f1f5f9;
        }

        .btn-icon {
            padding: 0.8rem;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 14px;
            background: #f8fafc;
            color: #0f172a;
        }

        .btn-icon:hover {
            background: #e2e8f0;
        }

        .search-input,
        .input,
        select,
        textarea {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .search-input:focus,
        .input:focus,
        select:focus,
        textarea:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.18);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .input-group label {
            font-weight: 700;
            color: #334155;
            font-size: 14px;
        }

        .form-grid,
        .filter-grid,
        .summary-grid,
        .detail-grid,
        .info-grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .full {
            grid-column: 1 / -1;
        }

        .chart-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: 2fr 1fr;
        }

        .chart-box {
            min-height: 300px;
            width: 100%;
        }

        .info-card {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #0ea5e9;
            padding: 18px;
            border-radius: 20px;
            color: #0f172a;
        }

        .info-card h3 {
            margin: 0 0 1rem;
            font-size: 0.95rem;
            color: #0f172a;
        }

        .info-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 16px;
            margin-bottom: 0.85rem;
            font-size: 13px;
        }

        .info-label {
            font-weight: 600;
            color: #334155;
        }

        .info-value {
            color: #0f172a;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .result-item {
            background: #f8fafc;
            border-radius: 20px;
            padding: 1.25rem;
            border-left: 4px solid #0ea5e9;
        }

        .result-item.sangat-tinggi {
            border-color: #ef4444;
        }

        .result-item.tinggi {
            border-color: #f97316;
        }

        .result-item.sedang {
            border-color: #eab308;
        }

        .result-item.rendah {
            border-color: #22c55e;
        }

        .result-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 0.6rem;
        }

        .result-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .result-category {
            display: inline-flex;
            padding: 0.45rem 0.85rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .result-category.sangat-tinggi {
            background: #fee2e2;
            color: #991b1b;
        }

        .result-category.tinggi {
            background: #fed7aa;
            color: #92400e;
        }

        .result-category.sedang {
            background: #fef3c7;
            color: #92400e;
        }

        .result-category.rendah {
            background: #dcfce7;
            color: #166534;
        }

        .table-card .search-box {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .table-card .search-box .search-input {
            flex: 1;
            min-width: 200px;
        }

        .overflow-x-auto {
            overflow-x: auto;
        }

        .no-data {
            text-align: center;
            padding: 2rem 1rem;
            color: #64748b;
        }

        .toast-wrapper {
            position: fixed;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: min(100%, 420px);
            pointer-events: none;
        }

        .toast-item {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.1rem;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            overflow: hidden;
            color: #ffffff;
            background: #0ea5e9;
            animation: toast-in 0.28s ease forwards;
            transform: translateY(-10px);
            opacity: 0;
        }

        .toast-item.toast-hidden {
            animation: toast-out 0.22s ease forwards;
        }

        .toast-item .toast-icon {
            width: 2.25rem;
            min-width: 2.25rem;
            height: 2.25rem;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.18);
            font-size: 1.1rem;
        }

        .toast-item .toast-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .toast-item .toast-title {
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: capitalize;
        }

        .toast-item .toast-message {
            font-size: 0.9rem;
            line-height: 1.4;
            word-break: break-word;
        }

        .toast-item .toast-close {
            border: none;
            background: transparent;
            color: inherit;
            padding: 0;
            margin: 0;
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
            opacity: 0.7;
        }

        .toast-item .toast-close:hover {
            opacity: 1;
        }

        .toast-item.success { background: #16a34a; }
        .toast-item.error { background: #dc2626; }
        .toast-item.warning { background: #f59e0b; color: #1f2937; }
        .toast-item.info { background: #0ea5e9; }

        .input-error-message {
            display: none;
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
        }

        .input.error {
            border-color: #dc2626 !important;
            background-color: #fef2f2 !important;
        }

        .input.error + .input-error-message {
            display: block;
        }

        .input-group.error .input {
            border-color: #dc2626 !important;
            background-color: #fef2f2 !important;
        }

        .input-group.error .input-error-message {
            display: block;
        }

        @keyframes toast-in {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes toast-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-20px); }
        }

        @media (max-width: 1024px) {
            .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .chart-grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .main-area { margin-left: 260px; }
            .main-content { padding: 102px 20px 32px; }
            .card h1 { font-size: 28px; }
            .summary-box h1 { font-size: 24px; }
            .sidebar { width: 260px; min-width: 260px; }
            header.topbar { left: 260px; padding: 16px 18px; }
            .brand img { width: 38px; height: 38px; }
        }

        @media (max-width: 768px) {
            .app-shell { flex-direction: column; }
            .sidebar { width: 100%; min-width: 100%; position: fixed; top: 0; left: 0; height: 100vh; transform: translateX(-100%); opacity: 0; visibility: hidden; transition: transform 0.3s ease, opacity 0.3s ease; padding: 20px; background: #ffffff; z-index: 100; overflow-y: auto; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); margin-left: 0; }
            .sidebar.mobile-open { transform: translateX(0); opacity: 1; visibility: visible; }
            .sidebar.collapsed { width: 100%; min-width: 100%; padding: 20px; }
            .sidebar-overlay { display: block; }
            .main-area { flex: 1; width: 100%; margin-left: 0; }
            header.topbar { left: 0; right: 0; padding: 14px 16px; gap: 8px; height: 74px; }
            .main-content { padding: 98px 16px 32px; }
            .brand { gap: 8px; }
            .brand img { width: 36px; height: 36px; }
            .brand-title { font-size: 13px; }
            .brand-subtitle { font-size: 11px; }
            .cards { grid-template-columns: 1fr; gap: 16px; }
            .summary-grid { grid-template-columns: 1fr; gap: 12px; }
            .form-grid { grid-template-columns: 1fr; gap: 14px; }
            .page-title h1 { font-size: 20px; }
            .page-title p { font-size: 13px; }
            .card h1 { font-size: 24px; }
            .summary-box { padding: 14px; }
            .summary-box h3 { font-size: 12px; }
            .summary-box h1 { font-size: 20px; }
            .card, .section, .content-card, .table-container, .detail-card, .form-card, .table-card, .result-card { padding: 18px; border-radius: 20px; }
            .input-group { margin-bottom: 12px; }
            .input-group label { font-size: 12px; margin-bottom: 6px; }
            .search-input, .input, .input-group select, .input-group input, .input-group textarea { padding: 10px 12px; font-size: 13px; }
            .button-area { gap: 8px; margin-top: 16px; }
            .btn-reset, .btn-submit, .btn-add, .btn-edit, .btn-delete, .btn-detail, .btn-action, .btn-create-schedule { padding: 10px 14px; font-size: 12px; width: 100%; }
            .action-bar { gap: 8px; margin-bottom: 12px; }
            .action-bar .search-input { min-width: 100%; width: 100%; margin-right: 0; }
            .action-bar .filter-select, .action-bar .input-group select { min-width: 100%; }
            .table-card .search-box { gap: 8px; margin-bottom: 12px; }
            .table-card .search-box input { min-width: 100%; }
            .table-card .search-box select { min-width: 100%; }
            table { font-size: 12px; }
            table th, table td { padding: 8px; }
            .table-title { font-size: 16px; margin-bottom: 12px; }
            .chart-card canvas { min-height: 240px; }
            .status, .priority, .risk { padding: 4px 10px; font-size: 11px; }
            .result-grid { grid-template-columns: 1fr; gap: 12px; }
            .result-item { padding: 12px; border-left-width: 3px; }
            .result-value { font-size: 18px; }
            .info-row { grid-template-columns: 100px 1fr; gap: 10px; font-size: 12px; }
            .action-buttons { flex-wrap: wrap; }
            .action-buttons .btn-action { padding: 8px 12px; font-size: 11px; flex: 1; min-width: 70px; }
            .form-card h2, .table-card h2 { font-size: 16px; margin-bottom: 16px; }
            .form-card h3 { font-size: 12px; margin-bottom: 10px; }
            .formula-box, .info-card, .recommendation-box { padding: 12px; font-size: 12px; margin-bottom: 12px; }
            .component-table th, .component-table td { padding: 8px; font-size: 11px; }
        }

        @media (max-width: 480px) {
            header.topbar { padding: 10px 12px; gap: 6px; height: 70px; left: 0; right: 0; }
            .sidebar-toggle { width: 40px; height: 40px; }
            .brand img { width: 32px; height: 32px; }
            .brand-title { font-size: 11px; }
            .brand-subtitle { display: none; }
            .main-content { padding: 94px 12px 32px; }
            .page-title h1 { font-size: 18px; }
            .page-title p { font-size: 12px; }
            .card, .form-card, .table-card { padding: 12px; }
            .button-area { margin-top: 12px; gap: 6px; }
            .btn-reset, .btn-submit, .btn-action { padding: 8px 12px; font-size: 11px; }
            table { font-size: 11px; }
            table th, table td { padding: 6px; }
            .input-group label { font-size: 11px; }
            .search-input, .input, .input-group input, .input-group select, .input-group textarea { padding: 8px 10px; font-size: 12px; }
            .status, .priority, .risk { padding: 3px 8px; font-size: 10px; }
            .card h1 { font-size: 20px; }
            .summary-box h1 { font-size: 18px; }
            .filters { gap: 6px; }
            .result-grid { grid-template-columns: 1fr; gap: 10px; }
            .info-row { grid-template-columns: 70px 1fr; gap: 6px; font-size: 10px; }
            .action-buttons .btn-action { padding: 6px 10px; font-size: 10px; min-width: 60px; }
            .form-card h2, .table-card h2 { font-size: 14px; }
            .component-table th, .component-table td { padding: 6px; font-size: 10px; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div id="toastContainer" class="toast-wrapper" aria-live="polite" aria-atomic="true"></div>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <nav class="menu">
                @php $sidebarType = $sidebarType ?? 'admin'; @endphp

                @if ($sidebarType === 'admin')
                    <a href="/dashboard" class="menu-item {{ ($activePage ?? '') === 'dashboard' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                        </span>
                        <span class="menu-label">Dashboard</span>
                    </a>
                    <a href="/dataaset" class="menu-item {{ ($activePage ?? '') === 'dataaset' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M4 7.5V5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2.5L12 3 4 7.5z"/><path d="M4 9.5v9a1 1 0 0 0 1 1h3v-6h8v6h3a1 1 0 0 0 1-1v-9L12 12 4 9.5z"/></svg>
                        </span>
                        <span class="menu-label">Data Aset</span>
                    </a>
                    <a href="/datakerusakan" class="menu-item {{ ($activePage ?? '') === 'datakerusakan' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2a9.966 9.966 0 0 0-9 5.5 9.966 9.966 0 0 0 1.24 10.1A10 10 0 1 0 12 2zm0 13a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm1-8h-2v5h2V7z"/></svg>
                        </span>
                        <span class="menu-label">Data Kerusakan</span>
                    </a>
                    <a href="/analisisrcm" class="menu-item {{ ($activePage ?? '') === 'analisisrcm' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M4 17h2v-7H4v7zm4 0h2V8H8v9zm4 0h2v-4h-2v4zm4 0h2v-9h-2v9zm4 0h2v-2h-2v2z"/></svg>
                        </span>
                        <span class="menu-label">Analisis Risiko Kerusakan</span>
                    </a>
                    <a href="/jadwalmaintenance" class="menu-item {{ ($activePage ?? '') === 'jadwalmaintenance' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M6 2h2v2H6V2zm10 0h2v2h-2V2zM5 7h14v13H5V7zm2 3h4v4H7v-4z"/></svg>
                        </span>
                        <span class="menu-label">Jadwal Maintenance</span>
                    </a>
                    {{-- Downtime menu dinonaktifkan; data downtime sekarang berasal dari Data Kerusakan --}}
                    <a href="/estimasibiaya" class="menu-item {{ ($activePage ?? '') === 'estimasibiaya' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2a7 7 0 0 0-7 7v2a7 7 0 0 0 7 7 7 7 0 0 0 7-7V9a7 7 0 0 0-7-7zm0 2a5 5 0 0 1 5 5v2a5 5 0 0 1-10 0V9a5 5 0 0 1 5-5zm0 3a1 1 0 0 0-1 1v1.5H10v1h1v1.5h2V12h1v-1h-1V10a1 1 0 0 0-1-1z"/></svg>
                        </span>
                        <span class="menu-label">Estimasi Biaya</span>
                    </a>
                    <a href="/datateknisi" class="menu-item {{ ($activePage ?? '') === 'datateknisi' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4zm0 2c-3.33 0-6 1.67-6 4v2h12v-2c0-2.33-2.67-4-6-4z"/></svg>
                        </span>
                        <span class="menu-label">Data Teknisi</span>
                    </a>
                    <a href="/laporan" class="menu-item {{ ($activePage ?? '') === 'laporan' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M6 2h9l5 5v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm7 1.5V8h4.5L13 3.5z"/></svg>
                        </span>
                        <span class="menu-label">Laporan</span>
                    </a>
                @else
                    <a href="/dashboardteknisi" class="menu-item {{ ($activePage ?? '') === 'dashboardteknisi' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                        </span>
                        <span class="menu-label">Dashboard</span>
                    </a>
                    <a href="/inputkerusakan" class="menu-item {{ ($activePage ?? '') === 'inputkerusakan' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M4 4h16v4H4V4zm0 6h16v10H4V10zm4 2v6h2v-6H8zm4 0v6h2v-6h-2z"/></svg>
                        </span>
                        <span class="menu-label">Input Kerusakan</span>
                    </a>
                    <a href="/input-risiko-kerusakan" class="menu-item {{ ($activePage ?? '') === 'inputrisikokerusakan' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M4 4h16v4H4V4zm0 6h16v10H4V10zm4 2v6h2v-6H8zm4 0v6h2v-6h-2z"/></svg>
                        </span>
                        <span class="menu-label">Analisis Risiko Kerusakan</span>
                    </a>
                    <a href="/inputmaintenance" class="menu-item {{ ($activePage ?? '') === 'inputmaintenance' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2a5 5 0 0 0-5 5v2H5a1 1 0 0 0-1 1v8h16v-8a1 1 0 0 0-1-1h-2V7a5 5 0 0 0-5-5zm-3 7V7a3 3 0 0 1 6 0v2h-6z"/></svg>
                        </span>
                        <span class="menu-label">Input Maintenance</span>
                    </a>
                    {{-- Hidden from sidebar - Update Kondisi Mesin --}}
                    <a href="/riwayatpekerjaan" class="menu-item {{ ($activePage ?? '') === 'riwayatpekerjaan' ? 'active' : '' }}">
                        <span class="menu-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M6 2h12v2H6V2zm12 4H6v16h12V6zm-2 2v2h-8V8h8zm0 4v2h-8v-2h8zm0 4v2h-8v-2h8z"/></svg>
                        </span>
                        <span class="menu-label">Riwayat Pekerjaan</span>
                    </a>
                @endif

                <a href="/logout" class="menu-item">
                    <span class="menu-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M16 17l5-5-5-5v3H9v4h7v3zM7 4h2v16H7V4z"/></svg>
                    </span>
                    <span class="menu-label">Logout</span>
                </a>
            </nav>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" type="button" aria-label="Toggle sidebar">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
                    </button>
                    <a href="/dashboard" class="brand">
                        <img src="{{ asset('assets/logo_perumda.png') }}" alt="Logo">
                        <div class="brand-text">
                            <span class="brand-title">Sistem Preventive Maintenance</span>
                            <span class="brand-subtitle">
                                @if (($sidebarType ?? 'admin') === 'admin')
                                    Admin Dashboard
                                @else
                                    Teknisi Dashboard
                                @endif
                            </span>
                        </div>
                    </a>
                </div>
                <div class="topbar-right"></div>
            </header>

            <main class="main-content">
                {{--
                @hasSection('pageHeading')
                    <div class="page-header">
                        <div class="page-title">
                            <h1>@yield('pageHeading')</h1>
                            <p>@yield('pageDescription')</p>
                        </div>
                    </div>
                @endif
                --}}

                <div class="content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function getToastIcon(type) {
            const icons = {
                success: '✔',
                error: '✖',
                warning: '⚠',
                info: 'ℹ'
            };
            return icons[type] || icons.info;
        }

        function dismissToast(toast) {
            if (!toast || toast.classList.contains('toast-hidden')) return;
            toast.classList.add('toast-hidden');
            toast.addEventListener('animationend', () => toast.remove(), { once: true });
        }

        function showToast(message, type = 'info', duration = 4500) {
            const container = document.getElementById('toastContainer');
            if (!container || !message) return;

            const titles = { success: 'Berhasil', error: 'Kesalahan', warning: 'Perhatian', info: 'Informasi' };
            const title = titles[type] || 'Informasi';

            const toast = document.createElement('div');
            toast.className = `toast-item ${type}`;
            toast.innerHTML = `
                <div class="toast-icon">${getToastIcon(type)}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${String(message)}</div>
                </div>
                <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
            `;

            const closeButton = toast.querySelector('.toast-close');
            closeButton.addEventListener('click', () => dismissToast(toast));

            container.prepend(toast);

            const timeoutId = setTimeout(() => dismissToast(toast), duration);
            toast.addEventListener('mouseenter', () => clearTimeout(timeoutId));
            toast.addEventListener('mouseleave', () => setTimeout(() => dismissToast(toast), 1200));

            return toast;
        }

        window.showToast = showToast;
        window.toastSuccess = message => showToast(message, 'success');
        window.toastError = message => showToast(message, 'error');
        window.toastWarning = message => showToast(message, 'warning');
        window.toastInfo = message => showToast(message, 'info');

        function persistToast(message, type = 'success') {
            if (!message) return;
            try {
                sessionStorage.setItem('globalToast', JSON.stringify({ message: String(message), type }));
            } catch (error) {
                console.warn('Unable to persist toast:', error);
            }
        }

        window.persistToast = persistToast;

        function initFlashToasts() {
            const storedToast = sessionStorage.getItem('globalToast');
            if (storedToast) {
                try {
                    const parsed = JSON.parse(storedToast);
                    showToast(parsed.message, parsed.type || 'success');
                } catch (error) {
                    console.warn('Invalid persisted toast data');
                }
                sessionStorage.removeItem('globalToast');
            }

            @if(session('success'))
                showToast(@json(session('success')), 'success');
            @endif
            @if(session('error'))
                showToast(@json(session('error')), 'error');
            @endif
            @if(session('warning'))
                showToast(@json(session('warning')), 'warning');
            @endif
            @if(session('info'))
                showToast(@json(session('info')), 'info');
            @endif
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFlashToasts);
        } else {
            initFlashToasts();
        }

        const appShell = document.querySelector('.app-shell');
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        // Detect if mobile
        function isMobile() {
            return window.innerWidth <= 768;
        }

        function setDesktopSidebarState(collapsed) {
            if (!appShell || !sidebar) return;
            sidebar.classList.toggle('collapsed', collapsed);
            appShell.classList.toggle('collapsed', collapsed);
        }

        // Toggle sidebar
        function toggleSidebar() {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                sidebarOverlay.classList.toggle('active');
            } else {
                const shouldCollapse = !sidebar.classList.contains('collapsed');
                setDesktopSidebarState(shouldCollapse);
            }
        }

        // Close sidebar on mobile
        function closeSidebarMobile() {
            if (isMobile()) {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            }
        }

        // Close sidebar when clicking outside (on mobile)
        sidebarOverlay.addEventListener('click', closeSidebarMobile);

        // Close sidebar when clicking a menu item (on mobile)
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', closeSidebarMobile);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                // Desktop
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                setDesktopSidebarState(sidebar.classList.contains('collapsed'));
            }
        });

        // Attach toggle to button
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
    </script>

    <script>
        // Jamin ada container toast (posisi: tengah atas)
        (function(){
            if (!document.getElementById('toastContainer')) {
                const c = document.createElement('div');
                c.id = 'toastContainer';
                c.className = 'toast-wrapper';
                c.setAttribute('aria-live','polite');
                c.setAttribute('aria-atomic','true');
                document.body.insertBefore(c, document.body.firstChild);
            }

            // Jika showToast belum didefinisikan, buat implementasi sederhana dan andal
            if (!window.showToast) {
                window.showToast = function(message, type = 'info', duration = 4500) {
                    try {
                        const container = document.getElementById('toastContainer');
                        if (!container || !message) return null;

                        const titles = { success: 'Berhasil', error: 'Kesalahan', warning: 'Perhatian', info: 'Informasi' };
                        const icons = { success: '✔', error: '✖', warning: '⚠', info: 'ℹ' };
                        const title = titles[type] || 'Informasi';

                        const toast = document.createElement('div');
                        toast.className = `toast-item ${type}`;
                        toast.innerHTML = `
                            <div class="toast-icon">${icons[type] || icons.info}</div>
                            <div class="toast-content">
                                <div class="toast-title">${title}</div>
                                <div class="toast-message">${String(message)}</div>
                            </div>
                            <button type="button" class="toast-close" aria-label="Tutup notifikasi">&times;</button>
                        `;

                        const closeBtn = toast.querySelector('.toast-close');
                        closeBtn.addEventListener('click', () => {
                            toast.classList.add('toast-hidden');
                            toast.addEventListener('animationend', () => toast.remove(), { once: true });
                        });

                        container.prepend(toast);

                        const timeout = setTimeout(() => {
                            if (toast.parentElement) {
                                toast.classList.add('toast-hidden');
                                toast.addEventListener('animationend', () => toast.remove(), { once: true });
                            }
                        }, duration);

                        toast.addEventListener('mouseenter', () => clearTimeout(timeout));
                        toast.addEventListener('mouseleave', () => setTimeout(() => {
                            if (toast.parentElement) {
                                toast.classList.add('toast-hidden');
                                toast.addEventListener('animationend', () => toast.remove(), { once: true });
                            }
                        }, 1200));

                        return toast;
                    } catch (e) {
                        // fallback to console but never use alert
                        console.warn('showToast error', e, message);
                        return null;
                    }
                };

                window.toastSuccess = msg => window.showToast(msg, 'success');
                window.toastError = msg => window.showToast(msg, 'error');
                window.toastWarning = msg => window.showToast(msg, 'warning');
                window.toastInfo = msg => window.showToast(msg, 'info');
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
