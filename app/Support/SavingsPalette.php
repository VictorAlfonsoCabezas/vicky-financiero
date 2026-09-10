<?php

namespace App\Support;

class SavingsPalette
{
    public static function colors(): array
    {
        return [
            'info' => ['name' => 'Celeste', 'accent' => '#087e9a', 'background' => '#e5f6fb'],
            'success' => ['name' => 'Verde', 'accent' => '#237347', 'background' => '#eaf7ee'],
            'warning' => ['name' => 'Amarillo', 'accent' => '#916400', 'background' => '#fff8da'],
            'danger' => ['name' => 'Rojo', 'accent' => '#b33342', 'background' => '#fff0f1'],
            'primary' => ['name' => 'Azul', 'accent' => '#245bb2', 'background' => '#eaf1ff'],
            'secondary' => ['name' => 'Gris', 'accent' => '#526175', 'background' => '#f0f3f7'],
            'dark' => ['name' => 'Grafito', 'accent' => '#303b49', 'background' => '#e5e9ef'],
        ];
    }

    public static function hex($key): string
    {
        if (is_string($key) && preg_match('/\A#[0-9a-fA-F]{6}\z/', $key)) {
            return strtolower($key);
        }
        return self::colors()[$key]['accent'] ?? self::colors()['info']['accent'];
    }

    public static function style($key): string
    {
        $colors = self::colors();
        $color = $colors[$key] ?? $colors['info'];
        if (is_string($key) && preg_match('/\A#[0-9a-fA-F]{6}\z/', $key)) {
            $hex = self::hex($key);
            $rgb = sscanf($hex, '#%02x%02x%02x');
            $background = '#';
            foreach ($rgb as $channel) {
                $background .= sprintf('%02x', (int) round($channel * 0.12 + 255 * 0.88));
            }
            $color = ['accent' => $hex, 'background' => $background];
        }

        return '--account-accent:'.$color['accent'].';--account-background:'.$color['background'].';';
    }

    public static function legacyStyle($key): string
    {
        return self::style($key).'background-color:var(--account-background);color:#263449;border:1px solid var(--account-accent);';
    }
}
