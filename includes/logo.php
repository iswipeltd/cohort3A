<?php
// ADEEEEE Logo Component - Beautiful brilliant inline SVG logo
// Usage: include __DIR__ . '/../../includes/logo.php'; then echo LOGO_INLINE;
// Or just copy the HTML wherever needed
?>
<?php if (!defined('ADEEEEE_LOGO')) define('ADEEEEE_LOGO', true); ?>
<style>
.adeeeee-logo{display:inline-flex;align-items:center;gap:10px;}
.adeeeee-logo-mark{width:42px;height:42px;position:relative;display:flex;align-items:center;justify-content:center;}
.adeeeee-logo-mark svg{width:100%;height:100%;filter:drop-shadow(0 0 8px rgba(245,158,11,0.4));}
.adeeeee-logo-text{font-family:'Poppins',sans-serif;font-weight:800;font-size:1.5rem;letter-spacing:1px;background:linear-gradient(135deg,#f59e0b 0%,#fbbf24 30%,#fff 50%,#fbbf24 70%,#f59e0b 100%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shine 3s linear infinite;text-shadow:none;}
.adeeeee-logo-text.small{font-size:1.1rem;}
.adeeeee-logo-text.large{font-size:2rem;}
.adeeeee-logo-text.white{-webkit-text-fill-color:#fff;background:none;color:#fff;}
@keyframes shine{to{background-position:200% center;}}
.adeeeee-logo-sub{font-family:'Inter',sans-serif;font-size:0.75rem;letter-spacing:2px;text-transform:uppercase;color:#94a3b8;margin-top:-2px;}
.adeeeee-logo-sub.gold{color:#fbbf24;letter-spacing:3px;}
</style>
<?php
$LOGO_SVG = '<div class="adeeeee-logo-mark"><svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="ag" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1"/><stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1"/><stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1"/></linearGradient><filter id="aglow"><feGaussianBlur stdDeviation="2" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><polygon points="50,5 90,25 90,75 50,95 10,75 10,25" fill="url(#ag)" stroke="#fbbf24" stroke-width="1.5" filter="url(#aglow)"/><text x="50" y="62" text-anchor="middle" font-family="Poppins,sans-serif" font-weight="800" font-size="42" fill="#fff" style="text-shadow:0 2px 8px rgba(0,0,0,0.3);">A</text></svg></div>';

$LOGO_INLINE = '<div class="adeeeee-logo">' . $LOGO_SVG . '<div><div class="adeeeee-logo-text">ADEEEEE</div></div></div>';
$LOGO_INLINE_SMALL = '<div class="adeeeee-logo">' . $LOGO_SVG . '<div><div class="adeeeee-logo-text small">ADEEEEE</div></div></div>';
$LOGO_INLINE_LARGE = '<div class="adeeeee-logo">' . $LOGO_SVG . '<div><div class="adeeeee-logo-text large">ADEEEEE</div></div></div>';
$LOGO_INLINE_WHITE = '<div class="adeeeee-logo">' . $LOGO_SVG . '<div><div class="adeeeee-logo-text white">ADEEEEE</div></div></div>';
?>
