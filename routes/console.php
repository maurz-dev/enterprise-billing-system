<?php

use IlluminateFoundationInspiring;
use IlluminateSupportFacadesArtisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
