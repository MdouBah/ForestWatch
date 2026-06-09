<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    // Enregistrement manuel de dompdf (PHP CLI sans ext-xml, web LAMPP OK)
    Barryvdh\DomPDF\ServiceProvider::class,
];
