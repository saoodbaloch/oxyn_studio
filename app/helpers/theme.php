<?php

declare(strict_types=1);

function theme_var(array $theme, string $group, string $key): string
{
    return (string) ($theme[$group][$key] ?? '');
}
