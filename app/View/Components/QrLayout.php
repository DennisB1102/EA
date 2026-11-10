<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class QrLayout extends Component
{
    public function __construct(public string $title = 'Außenprüfung') {}

    public function render(): View
    {
        return view('layouts.qr');
    }
}
