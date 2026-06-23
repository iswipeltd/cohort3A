<?php
/**
 * ADEEEEE TOTP Two-Factor Authentication
 * Implements RFC 6238 Time-based One-Time Password (no external dependencies)
 */

class TOTP {
    /**
     * Generate a random base32 secret
     */
    public static function generateSecret($length = 32) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }
    
    /**
     * Get QR code URL (using Google Chart API as fallback, or data URI)
     */
    public static function getQrCodeUrl($label, $secret, $issuer = 'ADEEEEE') {
        $otpauth = 'otpauth://totp/' . rawurlencode($label) . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . rawurlencode($otpauth);
    }
    
    /**
     * Verify a TOTP code
     */
    public static function verify($secret, $code, $window = 2) {
        $secret = self::base32Decode($secret);
        if (!$secret) return false;
        
        $timeSlice = floor(time() / 30);
        
        for ($i = -$window; $i <= $window; $i++) {
            $calculated = self::getCode($secret, $timeSlice + $i);
            if (hash_equals($calculated, str_pad($code, 6, '0', STR_PAD_LEFT))) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Generate TOTP code for a given time slice
     */
    private static function getCode($secret, $timeSlice) {
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hm = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $code = (
            ((ord($hm[$offset]) & 0x7F) << 24) |
            ((ord($hm[$offset + 1]) & 0xFF) << 16) |
            ((ord($hm[$offset + 2]) & 0xFF) << 8) |
            (ord($hm[$offset + 3]) & 0xFF)
        ) % 1000000;
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Base32 decode
     */
    private static function base32Decode($input) {
        $map = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper(str_replace('=', '', $input));
        $output = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $val = strpos($map, $input[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $output;
    }
}
