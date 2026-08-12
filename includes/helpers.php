<?php
/**
 * Utilidades chicas que se reutilizan en más de un módulo.
 */

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit();
}

function formatMoney($amount): string
{
    return number_format((float) $amount, 2, '.', ',');
}
