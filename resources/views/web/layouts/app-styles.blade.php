{{-- Dynamic Styles Component --}}
<style>
    .dynamic-announcement-bar {
        background-color: {{ $announcementBgColor ?? '#f69d1c' }};
        color: {{ $announcementTextColor ?? '#000000' }} !important;
        padding: 10px 0;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.4;
    }
    .dynamic-announcement-bar * {
        color: inherit !important;
    }
    .skc-footer {
        background-color: {{ $footerBgColor ?? '#1a1a1a' }};
        color: {{ $footerTextColor ?? '#ffffff' }};
    }
    .skc-footer-contact-item {
        color: {{ $footerTextColor ?? '#ffffff' }};
        opacity: 0.8;
        margin-bottom: 12px;
    }
    .skc-footer-contact-item:last-child {
        margin-bottom: 0;
    }
    .skc-footer-contact-icon {
        margin-right: 10px;
        color: #f69d1c;
    }
</style>